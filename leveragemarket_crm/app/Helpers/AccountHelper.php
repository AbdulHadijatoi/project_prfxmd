<?php

namespace App\Helpers;

use DB;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Models\BonusModel;
use App\Models\BonusTransaction;

class AccountHelper
{
    public static function updateLiveAndDemoAccounts($email = "", $table = '', $api = new MTWebAPI())
    {
        if ($email == "") {
            $email = session("clogin");
        }

        $settings = settings();

        $api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        $api->Connect(
            $settings['mt5_server_ip'],
            $settings['mt5_server_port'],
            300,
            $settings['mt5_server_web_login'],
            $settings['mt5_server_web_password']
        );
        if ($table == 'tournament_liveaccount') {
            $liveAccounts = DB::table('tournament_liveaccount')
                ->where('email', $email)
                ->orderBy('id', 'desc')
                ->get();

            foreach ($liveAccounts as $account) {
                $apiResponse = $api->UserAccountGet($account->trade_id, $accountData);
                if ($apiResponse === MTRetCode::MT_RET_OK) {
                    DB::table('tournament_liveaccount')
                        ->where('trade_id', $account->trade_id)
                        ->update([
                            'Balance' => $accountData->Balance,
                            'credit' => $accountData->Credit,
                            'MarginFree' => $accountData->MarginFree,
                            'MarginLevel' => $accountData->MarginLevel,
                            'equity' => $accountData->Equity,
                        ]);
                } else {
                    // Logger::error
                }
            }
        } else {
            $liveAccounts = DB::table('liveaccount')
                ->where('email', $email)
                ->orderBy('id', 'desc')
                ->get();

            foreach ($liveAccounts as $account) {
                $apiResponse = $api->UserAccountGet($account->trade_id, $accountData);				
                if ($apiResponse === MTRetCode::MT_RET_OK) {
					
					$balance = $accountData->Balance;
					$credit  = $accountData->Credit;
					$bonusToRemove = 0;
					if ($balance < 0) {
						$bonusToRemove = abs($balance);
						$balance = 0;
					}
										
                    DB::table('liveaccount')
                        ->where('trade_id', $account->trade_id)
                        ->update([
                            'Balance' => $balance,
                            'credit' => $credit,
                            'MarginFree' => $accountData->MarginFree,
                            'MarginLevel' => $accountData->MarginLevel,
                            'equity' => $accountData->Equity,
                        ]);			
											
					/*if ($bonusToRemove > 0) {

						$bonuses = BonusTransaction::where('trade_id', $account->trade_id)
							->where('bonus_withdrawstatus', 0)
							->orderBy('bonus_date', 'asc')
							->lockForUpdate()
							->get();

						$remaining = $bonusToRemove;

						foreach ($bonuses as $bonus) {

							if ($remaining <= 0) {
								break;
							}

							$availableBonus = $bonus->bonus_amount - $bonus->bonus_withdrawamount;

							if ($availableBonus <= 0) {
								continue;
							}
							$deduct = min($availableBonus, $remaining);

							// ✅ Update bonus transaction
							$bonus->bonus_withdrawamount += $deduct;

							if ($bonus->bonus_withdrawamount >= $bonus->bonus_amount) {
								$bonus->bonus_withdrawstatus = 1; // fully consumed
							}
							$bonus->save();
							
							
							
							$remaining -= $deduct;
						}
					} */					
	
                } else {
                    // $error = MTRetCode::GetError($apiResponse);
                    // return $error;
                }
            }

            // Update Demo Accounts
            $demoAccounts = DB::table('demoaccount')
                ->where('email', $email)
                ->orderBy('id', 'desc')
                ->get();

            foreach ($demoAccounts as $account) {
                $apiResponse = $api->UserAccountGet($account->trade_id, $accountData);

                if ($apiResponse === MTRetCode::MT_RET_OK) {
                    DB::table('demoaccount')
                        ->where('trade_id', $account->trade_id)
                        ->update([
                            'Balance' => $accountData->Balance,
                            'credit' => $accountData->Credit,
                            'MarginFree' => $accountData->MarginFree,
                            'MarginLevel' => $accountData->MarginLevel,
                            'equity' => $accountData->Equity,
                        ]);
                } else {
                    // Handle API error
                    // You can log the error here if needed
                }
            }
        }
    }

    public static function getAccount($id, $table = "liveaccount", $api = new MTWebAPI())
    {
        $settings = settings();

        $api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        $api->Connect(
            $settings['mt5_server_ip'],
            $settings['mt5_server_port'],
            300,
            $settings['mt5_server_web_login'],
            $settings['mt5_server_web_password']
        );
        $liveAccount = DB::table($table)
            ->where(DB::raw('md5(trade_id)'), $id)
            ->first();
        if (!empty($liveAccount)) {
            $accountData = NULL;
            $apiResponse = $api->UserAccountGet($liveAccount->trade_id, $accountData);
            if ($apiResponse === MTRetCode::MT_RET_OK) {
                $accountData->email=$liveAccount->email;
                DB::table($table)
                    ->where('trade_id', $liveAccount->trade_id)
                    ->update([
                        'Balance' => $accountData->Balance,
                        'credit' => $accountData->Credit,
                        'MarginFree' => $accountData->MarginFree,
                        'MarginLevel' => $accountData->MarginLevel,
                        'equity' => $accountData->Equity,
                    ]);
            }
            return $accountData;
        }
    }
}
