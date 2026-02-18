<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LiveAccount;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Helpers\AccountHelper;
use Illuminate\Support\Facades\DB;
use App\Models\TradeWithdrawals;
use App\Models\TradeDeposits;
use App\Models\TotalBalance;
use App\Models\IbWithdraw;
use App\Services\MailService as MailService;
use Validator;


class InternalTransfer extends Controller
{
    protected $api;
    protected $mailService;

    public function __construct(MTWebAPI $api, MailService $mailService, )
    {
        $this->api = $api;
        $this->mailService = $mailService;
    }
    public function index()
    {
        $email = auth()->user()->email;
        AccountHelper::updateLiveAndDemoAccounts($email, $this->api);
        $liveaccount_details = LiveAccount::with('accountType')
            ->where('email', $email)
			->where('status', 'active')	
            ->get();
		addIpLog('Account to Account Transfer Page', $email);
        return view('internal-transfer', compact('liveaccount_details'));
    }
    public function processTransfer(Request $request)
    {
        $settings = settings();
        $this->api->SetLoggerWriteDebug(config('constants.IS_WRITE_DEBUG_LOG'));
        $this->api->Connect(
            $settings['mt5_server_ip'],
            $settings['mt5_server_port'],
            300,
            $settings['mt5_server_web_login'],
            $settings['mt5_server_web_password']
        );
        $validated = $request->validate([
            'fromAccount' => 'required',
            'toAccount' => 'required|different:fromAccount',
            'transferable_amount' => 'required|numeric|min:1',
        ]);

        $fromAccount = $request->input('fromAccount');
        $toAccount = $request->input('toAccount');
        $transferable_amount = $request->input('transferable_amount');
        $email = auth()->user()->email;
        $ticket = NULL;

        // Withdraw from the first account
        $errorCode = $this->api->TradeBalance($fromAccount, $type = MTEnDealAction::DEAL_BALANCE, -$transferable_amount, 'withdraw', $ticket, true);
        if ($errorCode != MTRetCode::MT_RET_OK) {
            $error = MTRetCode::GetError($errorCode);
            return redirect()->back()->with('error', 'Failed to withdraw from the account.');
        } else {
            $tradewithdrawal = null;
            $tradedeposit = null;
            DB::transaction(function () use (&$tradewithdrawal, &$tradedeposit, $email, $fromAccount, $toAccount, $transferable_amount) {
                
                $errorCode = $this->api->TradeBalance($toAccount, $type = MTEnDealAction::DEAL_BALANCE, $transferable_amount, 'deposit', $ticket, true);
                if ($errorCode != MTRetCode::MT_RET_OK) {
                    $error = MTRetCode::GetError($errorCode);
                    return redirect()->back()->with('error', 'Deposit Failed.');
                } else {
					$tradewithdrawal = TradeWithdrawals::create([
						'email' => $email,
						'trade_id' => $fromAccount,
						'withdrawal_amount' => $transferable_amount,
						'withdraw_type' => 'A2A Transfer',
						'withdraw_to' => $toAccount,
						'withdraw_date' => now(),
						'Status' => 1
					]);
                    $tradedeposit = TradeDeposits::create([
                        'email' => $email,
                        'trade_id' => $toAccount,
                        'deposit_amount' => $transferable_amount,
                        'deposit_type' => 'A2A Transfer',
                        'deposit_from' => $fromAccount,
                        'Status' => 1,
                    ]);
                    TotalBalance::create([
                        'email' => $email,
                        'trade_id' => $toAccount,
                        'trading_deposited' => $transferable_amount,
                        'deposit_type' => 'A2A Transfer',
                    ]);
					
					$datalog = [
                        'email' => $email,
                        'trade_id' => $toAccount,
                        'deposit_amount' => $transferable_amount,
                        'deposit_type' => 'A2A Transfer',
                        'deposit_from' => $fromAccount
                    ];

                    addIpLog('Process Transfer', $datalog);
					
                }
            });
            if ($tradedeposit && $tradewithdrawal) {

                $from_acc = LiveAccount::where("trade_id", $fromAccount)->first();
                $to_acc = LiveAccount::where("trade_id", $toAccount)->first();

                $from = $settings['email_from_address'];
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";

                // $from_transid = "TWID" . str_pad($tradewithdrawal->id, 4, '0', STR_PAD_LEFT);
                // $emailSubject_from = $settings['admin_title'] . ' - Fund Withdrawal';

                // $to_transid = "TDID" . str_pad($tradedeposit->id, 4, '0', STR_PAD_LEFT);
                // $emailSubject_to = $settings['admin_title'] . ' - Fund Deposit';

                $from_transid = "TDTW" . $tradedeposit->id.'-'.$tradewithdrawal->id;
                $emailSubject_from = $settings['admin_title'] . ' - Account to Account Transfer';

                $content = '<div>We are pleased to inform you that funds have been successfully transferred.</div>
                <div><b>Transaction Details</b></div>
                <div><b>Amount: </b>$' . $transferable_amount . '</div>
                <div><b>From Account: </b>' . $fromAccount . '</div>
                <div><b>To Account: </b>' . $toAccount . '</div>
                <div><b>Transaction ID: </b>' . $from_transid . '</div>
                <div><b>Withdraw Date: </b>' . date("Y-m-d H:i:s") . '</div>
                <div><b>Withdraw Type </b>A2A Transfer</div>';
                $templateVars = [
                    'name' => $from_acc->name,
                    'site_link' => settings()['copyright_site_name_text'],
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "A2A",
                    "subtitle_right" => "Transfer",
                    "btn_text" => "Go To Dashboard",
                ];
                $this->mailService->sendEmail($email, $emailSubject_from, '', '', $templateVars);


//                 $content = '<div>We are pleased to inform you that funds have been successfully deposited into your account.</div>
// <div><b>Transaction Details</b></div>
// <div><b>Amount: </b>$' . $transferable_amount . '</div>
// <div><b>From Account: </b>' . $fromAccount . '</div>
// <div><b>To Account: </b>' . $toAccount . '</div>
// <div><b>Transaction ID: </b>' . $to_transid . '</div>
// <div><b>Deposited Date: </b>' . date("Y-m-d H:i:s") . '</div>
// <div><b>Deposit Type </b>Internal Transfer</div>';
//                 $templateVars = [
//                     'name' => $to_acc->name,
//                     'site_link' => $settings['copyright_site_name_text'],
//                     'email' => $settings['email_from_address'],
//                     "content" => $content,
//                     "title_right" => "Fund",
//                     "subtitle_right" => "Deposit",
//                     "btn_text" => "Go To Dashboard",
//                 ];
             //   $this->mailService->sendEmail($email, $emailSubject_to, '', '', $templateVars);

             $datalog = [
                    'name' => $from_acc->name,
                    'site_link' => settings()['copyright_site_name_text'],
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "A2A",
                    "subtitle_right" => "Transfer",
                    "btn_text" => "Go To Dashboard",
                ];

            }
        }
		addIpLog('Account to Account Transfer submit', $datalog);
        return redirect()->back()->with('success', 'Account to Account Transfer Successfully Done');
    }
	
	/*IB Commission External Transfer*/
	
	/*IB Commission Bank Withdrawal */
	public function IBBankTransfer(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'withdraw_amount' => 'required|numeric|min:1',
			'withdraw_type' => 'required|string',
			'withdraw_to' => 'required|string',
			'withdrawal_currency' => 'nullable|string',
			'amount_in_other_currency' => 'nullable|numeric',
			'adjustment_inr' => 'nullable|numeric',
		]);
		
		if ($validator->fails()) {
			return redirect()->back()
				->withErrors($validator)
				->withInput()
				->with('error', 'Validation failed. Please check your input and try again.');
		}

		try {
			// ✅ Step 2: Ensure authenticated IB user (or define $email)
			$email = auth()->user()->email ?? null;
			
			if (!$email) {
				return response()->json([
					'status' => 'error',
					'message' => 'User not authenticated '
				], 401);
			}
			
			$balance = DB::table('ib_wallet')
                ->selectRaw('SUM(ib_wallet) as wallet, SUM(ib_withdraw) as withdraw')
                ->where('email', $email)
                ->first();
				
            $availableBalance = $balance->wallet - $balance->withdraw;
			
            if ($availableBalance >= $request->withdraw_amount) {
							
				// ✅ Step 3: Create withdrawal request
				$ibbankins = IbWithdraw::create([
					'email' => $email,
					'withdraw_type' => $request->withdraw_type,
					'withdraw_amount' => $request->withdraw_amount,
					'amount_in_other_currency' => $request->amount_in_other_currency,
					'adjustment_inr' => $request->adjustment_inr,
					'withdraw_to' => $request->withdraw_to,
					'withdrawal_currency' => $request->withdrawal_currency,
					'client_bank' => $request->withdraw_to
				]);
				
				$datalog = [
					'email' => $email,
					'withdraw_type' => $request->withdraw_type,
					'withdraw_amount' => $request->withdraw_amount,
					'amount_in_other_currency' => $request->amount_in_other_currency,
					'adjustment_inr' => $request->adjustment_inr,
					'withdraw_to' => $request->withdraw_to,
					'withdrawal_currency' => $request->withdrawal_currency,
					'client_bank' => $request->withdraw_to
				];

				// ✅ Step 4: Success response
				addIpLog('IB commission withdrawal', $datalog);
				return response()->json([
					'status' => 'success',
					'message' => 'IB commission withdrawal request is pending admin approval. Once approved, the amount will be transferred to the requested bank account.'
				]);
			
			} else {
				$availableBalance = number_format($availableBalance, 2);
				return response()->json([
					'status' => 'error',
					'message' => "Insufficient IB Transferrable Balance.<br /> Available balance <b>{$availableBalance}</b>. But you requested <b>{$request->withdraw_amount}</b>"
				], 400);

			}

		} catch (\Exception $e) {
			// ❌ Step 5: Handle database or logic errors
			return response()->json([
				'status' => 'error',
				'message' => 'Something went wrong while processing your request: ' . $e->getMessage()
			], 400);
		}
	}
}
