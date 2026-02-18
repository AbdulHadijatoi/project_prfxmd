<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\LiveAccount;
use App\Models\BonusTransaction;
use App\Services\MT5Service;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Services\MailService as MailService;
use App\Services\Logging\AccountLogger;
use DB;


class Mt5NegativeBalanceProtection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt5:negative-balance-protection';
    protected $description = 'Negative balance protection: balance → bonus → close positions';
	protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MailService $mailService)
    {
        parent::__construct();
		$this->mailService = $mailService;
        $this->mt5Service = $mt5Service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->mt5Service->connect();
            $this->api = $this->mt5Service->getApi();
        } catch (\Throwable $e) {
            Log::error('MT5 connection failed: ' . $e->getMessage());
            return;
        }		
		
		$accounts = LiveAccount::where('status', 'active')->where('processing', 0)->get();		
		foreach ($accounts as $account) {
			$account->update(['processing' => 1]);
            try {
				$tradeId = $account->trade_id;
				//$tradeId = 495082;
				$mt5Acc  = null;
				
				/** GET MT5 ACCOUNT */
                if (($ret = $this->api->UserAccountGet($tradeId, $mt5Acc)) !== MTRetCode::MT_RET_OK) {
                    AccountLogger::log($tradeId, 'UserAccountGet failed', ['ret' => $ret]);
                    continue;
                }
				
				$balance = (float) $mt5Acc->Balance;
				$credit  = (float) $mt5Acc->Credit;
				$profit  = (float) ($mt5Acc->Profit ?? 0);
				$equity  = (float) $mt5Acc->Equity;
				
				AccountLogger::log($tradeId, 'current status', [
					'balance' => $balance,
					'credit'  => $credit,
					'equity'  => $equity,
					'profit'  => $profit,
				]);
				
				$totalFunds = $balance + $credit;
				
				if ($equity >= $totalFunds) {
					$this->syncLiveAccount($account, $balance, $credit, $equity);
					continue;
				}
				
				/** LOSS CALCULATION */
				$loss = ($balance + $credit) - $equity;

				/** APPLY LOSS TO BALANCE */
				$balanceAfter = $balance - $loss;

				if ($balanceAfter < 0) {
					/** BALANCE NEGATIVE → ZERO IT */
					$remainingLoss = abs($balanceAfter);
					$balanceAfter = 0;
					$comment = 'Negative step out';
					$ticket = NULL;
					$upbalance = -abs($balance);
					$errorCode = $this->api->TradeBalance($tradeId, $type = MTEnDealAction::DEAL_BALANCE, $upbalance, $comment, $ticket, $margin_check = true);
					
					//$errorCode = $this->api->TradeBalance($tradeId, $type = MTEnDealAction::DEAL_SO_COMPENSATION, 0, $comment, $ticket, $margin_check = true);
					
					AccountLogger::log($tradeId, 'MT5 Update to negative balance', [
						'balance' => $upbalance
					]);

					/** APPLY REMAINING LOSS TO CREDIT */
					$reduced = $this->reduceBonus($tradeId, $remainingLoss);
					$creditAfter = max(0, $credit - $remainingLoss);

					if ($creditAfter < 0) {
						$creditAfter = 0;
					}
				} else {
					$creditAfter = $credit;
				}

				/** UPDATE BONUS TABLE */
				BonusTransaction::where('trade_id', $tradeId)
					->where('status', 1)
					->update(['bonus_amount' => $creditAfter]);

				/** FINAL EQUITY (PROTECT NEGATIVE) */
				$equityAfter = max(0, $balanceAfter + $creditAfter);

				/** UPDATE LIVE ACCOUNT TABLE */
				$this->syncLiveAccount(
					$account,
					$balanceAfter,
					$creditAfter,
					$equityAfter
				);

				/** CLOSE POSITIONS IF BOTH ZERO */
				if ($balanceAfter == 0 && $creditAfter == 0) {
					$this->closeAllPositions($tradeId);
				}								

            } catch (\Throwable $e) {
                Log::error("MT5 Protection Error ({$account->trade_id}): " . $e->getMessage());
            }
			$account->update(['processing' => 0]);
        }
    }
	
	/**
     * Reduce bonus based on remaining loss
     */
    private function reduceBonus(int $tradeId, float $loss)
    {
        DB::transaction(function () use ($tradeId, &$loss) {

            $bonuses = BonusTransaction::where('trade_id', $tradeId)
                ->where('status', 1)
                ->orderBy('id', 'ASC')
                ->lockForUpdate()
                ->get();

            foreach ($bonuses as $bonus) {
                if ($loss <= 0) break;

                if ($bonus->bonus_amount <= $loss) {
                    $loss -= $bonus->bonus_amount;

                    $bonus->update([
                        'bonus_amount' => 0,
                        'status' => 0
                    ]);

                    AccountLogger::log($tradeId, 'Bonus fully consumed', [
                        'bonus_id' => $bonus->id
                    ]);
                } else {
                    $bonus->update([
                        'bonus_amount' => $bonus->bonus_amount - $loss
                    ]);

                    AccountLogger::log($tradeId, 'Bonus partially consumed', [
                        'bonus_id' => $bonus->id,
                        'remaining_bonus' => $bonus->bonus_amount - $loss
                    ]);

                    $loss = 0;
                }
				
				$ticket = null;
				$bonusamount = -abs($loss);
				$comment = 'Bonus Out';
				if (($error_code = $this->api->TradeBalance($tradeId, MTEnDealAction::DEAL_BONUS, $bonusamount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
					Log::info('MT5: Bonus Reduce ', [
						'tradeId' => $tradeId,
						'error' => MTRetCode::GetError($error_code)
					]);
				}
				
				AccountLogger::log($tradeId, 'MT5 Update to bonus balance', [
					'bonusamount' => $bonusamount
				]);
            }
        });
    }
    /**
     * Close all open MT5 positions
     */
    private function closeAllPositions(int $login)
    {
        $positions = null;

        if ($this->api->PositionGet($login, $positions) !== MTRetCode::MT_RET_OK) {
            AccountLogger::log($login, 'PositionGet failed');
            return;
        }

        foreach ($positions as $position) {
            try {
                $this->api->PositionClose($position['Position']);
                AccountLogger::log($login, 'Position closed', [
                    'position' => $position['Position']
                ]);
            } catch (\Throwable $e) {
                Log::error("Failed closing position {$position['Position']} | {$e->getMessage()}");
            }
        }
    }
	
	private function syncLiveAccount(
		LiveAccount $account,
		float $balance,
		float $credit,
		float $equity
	) {
		$account->update([
			'balance' => round($balance, 2),
			'credit'  => round($credit, 2),
			'equity'  => round($equity, 2),
		]);

		AccountLogger::log($account->trade_id, 'Live account synced', [
			'balance' => $balance,
			'credit'  => $credit,
			'equity'  => $equity
		]);
	}
}
