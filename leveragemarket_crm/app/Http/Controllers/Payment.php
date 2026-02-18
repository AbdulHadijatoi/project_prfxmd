<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\PaymentLog;
use App\Models\WalletDeposit;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use App\Services\MailService as MailService;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Models\User;
use App\Models\BonusModel;
use App\Models\TradeDeposits;
use App\Models\BonusTransaction;
use App\Services\MT5Service;
use Illuminate\Support\Facades\Log;

//use App\Http\Controllers\Admin\MT5Controller;

class Payment extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MailService $mailService, MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->mt5Service = $mt5Service;
		$this->mt5Service->connect();
        $this->mailService = $mailService;
		$this->api = $this->mt5Service->getApi();
        $this->settings = settings();
    }
    public function handlePaymentResponse(Request $request)
    {
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
        $status = $request->input('status');
        $payment_id = $request->input('payment_id');
        $payment_res = json_encode($request->all());
        $paymentLog = PaymentLog::where(DB::raw('MD5(payment_id)'), $payment_id)->with('user')->first();
        if ($status == "success") {
            // Get the payment log
            if ($paymentLog && strtolower($paymentLog->payment_status) != "success") {
                // Update payment log
                $paymentLog->update([
                    'payment_res' => $payment_res,
                    'payment_status' => $status,
                ]);
                $email = $paymentLog->initiated_by;
                $amount = $paymentLog->payment_amount;
                $bonus_amount = is_null($paymentLog->bonus_amount) ? 0 : $paymentLog->bonus_amount;
                $bonus_trans_id = $paymentLog->bonus_trans_id;
                // Create a new wallet deposit
                if ($paymentLog->payment_reference_id == 'Wallet') {
                    $walletDeposit = WalletDeposit::create([
                        'email' => $email,
                        'deposit_amount' => $amount,
                        'deposit_type' => $paymentLog->payment_type,
                        'currency_type' => "USD",
                        'Status' => 1,
                    ]);
				$datalog = [
                        'email' => $email,
                        'deposit_amount' => $amount,
                        'deposit_type' => $paymentLog->payment_type,
                        'currency_type' => "USD",
                        'Status' => 1,
                     ];
                } else {
                    $walletDeposit = TradeDeposits::create([
                        'email' => $email,
                        'trade_id' => $paymentLog->payment_reference_id,
                        'deposit_amount' => $amount,
                        'deposit_type' => $paymentLog->payment_type,
                        'deposit_currency' => "USD",
						'bonus_amount'  => $bonus_amount,
						'bonus_trans_id'  => $bonus_trans_id,						
                        'Status' => 1,
                    ]);
					
					$lastInsertId = $walletDeposit->id;
					 $datalog = [
                        'description'   => 'Crypto Payment Direct Deposit to Trade Account',
							'status'        => 1,
							'did'           => $lastInsertId,
							'email'         => $email,
							'amount'        => $amount,
							'tradeId'       => $paymentLog->payment_reference_id,
							'bonus_amount'  => $bonus_amount,
							'bonus_trans_id'  => $bonus_trans_id,
                     ];
					if ($walletDeposit) {
						$requestData = new Request([
							'description'   => 'Crypto Payment Direct Deposit to Trade Account',
							'status'        => 1,
							'did'           => $lastInsertId,
							'email'         => $email,
							'amount'        => $amount,
							'tradeId'       => $paymentLog->payment_reference_id,
							'bonus_amount'  => $bonus_amount,							
						]);						
						
						$this->updateTransaction($requestData);
					}
                }
				addIpLog('Payment Response', $datalog);
                if ($walletDeposit) {
                    $this->sendSuccessEmail($email, $amount, $paymentLog, $walletDeposit->id);
                    return redirect('/wallet_deposit')->with('success', "Successfully Deposited \$$amount");
                } else {
                    return redirect('/wallet_deposit')->with('error', "Something went wrong. Please Try Again");
                }
            } else {
                return redirect('wallet_deposit')->with('error', "Payment already processed or invalid.");
            }
        } else {
            // Update payment log for failed payment
            $paymentLog->update([
                'payment_res' => $payment_res,
                'payment_status' => $status,
            ]); 
            return redirect('/wallet_deposit')->with('error', "Payment Failed: Something Went Wrong. Please try again");
        }
    }
	
	public function webhookPaytiko(Request $request)
    {
		Log::info('Paytiko Webhook', $request->all()); 
		$this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
		
		if (!$request->has('MerchantOrderId') || !$request->has('TransactionStatus')) {
			return response()->json(['error' => 'Invalid payload'], 400);
		}

		$orderId   = $request->MerchantOrderId;
		$status    = strtolower($request->TransactionStatus);		
		$signature = $request->Signature ?? null;
        $payment_res = json_encode($request->all());
		
        $paymentLog = PaymentLog::where('payment_id', $orderId)->with('user')->first();
		$email = $paymentLog->initiated_by;
		$amount = $paymentLog->payment_amount;
		$bonus_amount = is_null($paymentLog->bonus_amount) ? 0 : $paymentLog->bonus_amount;
        $bonus_trans_id = $paymentLog->bonus_trans_id;
		
		if (!$paymentLog) {
			Log::warning('Paytiko: Payment log not found', ['payment_id' => $orderId]);
			return response()->json(['error' => 'Order not found'], 200);
		}
		
		if (strtolower($paymentLog->payment_status) === 'success') {
			Log::info('Paytiko: Payment already processed', ['payment_id' => $orderId]);
			return response()->json(['status' => 'already_processed'], 200);
		}
		
		if ($status === 'success') {
			$paymentLog->update([
				'payment_res'    => json_encode($request->all()),
				'payment_status' => 'success',
			]);
			
			if ($paymentLog->payment_reference_id == 'Wallet') {
				$walletDeposit = WalletDeposit::create([
					'email' => $email,
					'deposit_amount' => $amount,
					'deposit_type' => $paymentLog->payment_type,
					'currency_type' => "USD",
					'Status' => 1,
				]);
			} else {
				$walletDeposit = TradeDeposits::create([
					'email' => $email,
					'trade_id' => $paymentLog->payment_reference_id,
					'deposit_amount' => $amount,
					'deposit_type' => $paymentLog->payment_type,
					'deposit_currency' => "USD",
					'bonus_amount'  => $bonus_amount,
					'bonus_trans_id'  => $bonus_trans_id,
					'Status' => 1,
				]);
				
				$lastInsertId = $walletDeposit->id;
				$datalog = [
                    'description'   => 'Crypto Payment Direct Deposit to Trade Account',
						'status'        => 1,
						'did'           => $lastInsertId,
						'email'         => $email,
						'amount'        => $amount,
						'tradeId'       => $paymentLog->payment_reference_id,
						'bonus_amount'  => $bonus_amount,	
                ];
				if ($walletDeposit) {
					$requestData = new Request([
						'description'   => 'Crypto Payment Direct Deposit to Trade Account',
						'status'        => 1,
						'did'           => $lastInsertId,
						'email'         => $email,
						'amount'        => $amount,
						'tradeId'       => $paymentLog->payment_reference_id,
						'bonus_amount'  => $bonus_amount,	
					]);						
					
					$this->updateTransaction($requestData);
				}
			}
			addIpLog(' Paytiko Webhook Response',  $datalog);
			if ($walletDeposit) {
				$this->sendSuccessEmail($email, $amount, $paymentLog, $walletDeposit->id);
				return redirect('/trade-deposit')->with('success', "Successfully Deposited \$$amount");
			} else {
				return redirect('/trade-deposit')->with('error', "Something went wrong. Please Try Again");
			}
		} else { 
			$reasonfail    = $request->DeclineReasonText;
			$payment_res = json_encode($request->all());
			$status = $request->TransactionStatus;
			
			$paymentLog->update([
				'payment_res' => $payment_res,
				'payment_status' => $status,
			]);
			
			return redirect('/trade-deposit')->with('error', "Payment Failed: $payment_res");
		}
	}
	
	public function handlePaytikoSuccess(Request $request)
    {		
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
        $status = 'success';
        $payment_id = $request->input('m_orderId');
        $payment_res = json_encode($request->all());
        $paymentLog = PaymentLog::where('payment_id', $payment_id)->with('user')->first();
		// Get the payment log
		if ($paymentLog && strtolower($paymentLog->payment_status) != "success") {
			// Update payment log
			$paymentLog->update([
				'payment_res' => $payment_res,
				'payment_status' => 'success',
			]);
			$email = $paymentLog->initiated_by;
			$amount = $paymentLog->payment_amount;
			$bonus_amount = is_null($paymentLog->bonus_amount) ? 0 : $paymentLog->bonus_amount;
            $bonus_trans_id = $paymentLog->bonus_trans_id;
			// Create a new wallet deposit
			if ($paymentLog->payment_reference_id == 'Wallet') {
				$walletDeposit = WalletDeposit::create([
					'email' => $email,
					'deposit_amount' => $amount,
					'deposit_type' => $paymentLog->payment_type,
					'currency_type' => "USD",
					'Status' => 1,
				]);
			} else {
				$walletDeposit = TradeDeposits::create([
					'email' => $email,
					'trade_id' => $paymentLog->payment_reference_id,
					'deposit_amount' => $amount,
					'deposit_type' => $paymentLog->payment_type,
					'deposit_currency' => "USD",
					'bonus_amount'  => $bonus_amount,
					'bonus_trans_id'  => $bonus_trans_id,
					'Status' => 1,
				]);
				
				$lastInsertId = $walletDeposit->id;
				$datalog = [
                    'description'   => 'Crypto Payment Direct Deposit to Trade Account',
						'status'        => 1,
						'did'           => $lastInsertId,
						'email'         => $email,
						'amount'        => $amount,
						'tradeId'       => $paymentLog->payment_reference_id,
						'bonus_amount'  => $bonus_amount,
                ];

				if ($walletDeposit) {
					$requestData = new Request([
						'description'   => 'Crypto Payment Direct Deposit to Trade Account',
						'status'        => 1,
						'did'           => $lastInsertId,
						'email'         => $email,
						'amount'        => $amount,
						'tradeId'       => $paymentLog->payment_reference_id,
						'bonus_amount'  => $bonus_amount,
					]);						
					
					$this->updateTransaction($requestData);
				}
			}
			addIpLog(' Paytiko Success Response',  $datalog);
			if ($walletDeposit) {
				$this->sendSuccessEmail($email, $amount, $paymentLog, $walletDeposit->id);
				return redirect('/trade-deposit')->with('success', "Successfully Deposited \$$amount");
			} else {
				return redirect('/trade-deposit')->with('error', "Something went wrong. Please Try Again");
			}
		} else {
			return redirect('trade-deposit')->with('success', "Payment is processing will update your account soon.");
		}
    }
	
	public function handlePaytikoFaild(Request $request)
    {
		// Update payment log for failed payment
		$payment_id = $request->input('m_orderId');
        $payment_res = json_encode($request->all());
        $paymentLog = PaymentLog::where('payment_id', $payment_id)->with('user')->first();
        $datalog =[
            'payment_res' => $payment_res,
			'payment_status' => 'Failure',
        ];

		$paymentLog->update([
			'payment_res' => $payment_res,
			'payment_status' => 'Failure',
		]); 
        addIpLog(' Paytiko Faild',  $datalog);
		return redirect('/trade-deposit')->with('error', "Payment Failed: Something Went Wrong. Please try again");
	}

    public function sendSuccessEmail($toEmail, $amount, $paymentLog, $lastInsertId)
    {
        $user = User::where('email', $toEmail)->first();
        $settings = settings();
        $from = $settings['email_from_address'];
        $transid = ($paymentLog->payment_reference_id=='Wallet'?"WDID":"TDID"). str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
        $emailSubject = $settings['admin_title'] . ' - Transaction Successful';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
        $content = '<div>We are pleased to inform you that your transaction has been successful.</div>
          <div><b>Transaction Details</b></div>
          <div><b>Approved Amount: </b>$' . $paymentLog->payment_amount . '</div>
          <div><b>Reference ID: </b>' . $paymentLog->payment_reference_id . '</div>
          <div><b>Transaction ID: </b>' . $transid . '</div>
          <div><b>Deposited Date: </b>' . $paymentLog->created_at . '</div>
          <div><b>Payment Type: </b>' . $paymentLog->payment_type . '</div>';
          $datalog = [
            'name' => $user->fullname,
            'site_link' => $settings['copyright_site_name_text'],
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "Transaction",
            "subtitle_right" => "Successful",
            "btn_text" => "Go To Dashboard",
          ];
        $templateVars = [
            'name' => $user->fullname,
            'site_link' => $settings['copyright_site_name_text'],
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "Transaction",
            "subtitle_right" => "Successful",
            "btn_text" => "Go To Dashboard",
        ];
              addIpLog(' Payment send SuccessEmail',  $datalog);
        $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);

    }

    public function handleMatch2PayResponse(Request $request)
    {
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
        $redirect_link = '/login';
        if ($request->session()->has('clogin')) {
            $redirect_link = '/dashboard';
        }
        Log::info('Match2PayResponse', $request->all());
        $payment_id = $request->input('payment_id');
        $paymentLog = PaymentLog::where(DB::raw('MD5(payment_id)'), $payment_id)->with('user')->first();
        $all_req = $request->all();
        if ($paymentLog) {
            $newResponse = [];
            $currentResponse = json_decode($paymentLog->payment_res, true);
            $newResponse[] = $currentResponse;
            $newResponse[] = (array) $all_req;
            $paymentLog->update([
                'payment_res' => json_encode($newResponse),
            ]);
            if ($paymentLog->log_status != 1) {

                $type = $paymentLog->payment_type;
                $transaction_type = $paymentLog->payment_reference_id;
                $status = $request->input('status');


                $payment_res = json_encode($request->all());
                if ($type == 'Crypto Payment' && $transaction_type == 'Wallet' && $status) {
                    $paymentLog->update([
                        // 'payment_res' => $payment_res,
                        'payment_status' => $status,
                    ]);
                    if (strtolower($status) == 'done') {
                        $email = $paymentLog->initiated_by;
                        $amount = $paymentLog->payment_amount;
                        $datalog = [
                            'email' => $email,
                            'deposit_amount' => $amount,
                            'deposit_type' => "Crypto Payment",
                            'currency_type' => "USD",
                            'Status' => 1,
                            'payment_log_id' => $paymentLog->payment_id
                        ];
                        $walletDeposit = WalletDeposit::create([
                            'email' => $email,
                            'deposit_amount' => $amount,
                            'deposit_type' => "Crypto Payment",
                            'currency_type' => "USD",
                            'Status' => 1,
                            'payment_log_id' => $paymentLog->payment_id
                        ]);
                        $paymentLog->update([
                            'log_status' => 1,
                        ]);
                        addIpLog(' Crypto Payment Wallet ',  $datalog);
                        if ($walletDeposit) {
                            return response()->json(
                                [
                                    'status' => true,
                                    'message' => "Successfully Deposited \$$amount To Your Wallet",
                                    'reference' => $email
                                ]
                            );
                            //  $this->sendSuccessEmail($email, $amount, $paymentLog, $walletDeposit->id);
                            //return redirect($redirect_link)->with('success', "Successfully Deposited \$$amount To Your Wallet");
                        } else {
                            return response()->json(
                                [
                                    'status' => false,
                                    'message' => "Something went wrong. Please Try Again",
                                    'reference' => $email
                                ]
                            );
                            // return redirect($redirect_link)->with('error', "Something went wrong. Please Try Again");
                        }
                    } else {
                        return response()->json(
                            [
                                'status' => true,
                                'message' => "the status changed to " . $status,
                                'reference' => json_encode($request->all())
                            ]
                        );
                    }
                } else if ($type == 'Crypto Payment' && $transaction_type == 'Trade Deposit' && $status) {
                    $trade_id = $paymentLog->trade_id;
                    $email = $paymentLog->initiated_by;
                    $amount = $paymentLog->payment_amount;
                    $bonus_id = $paymentLog->bonus_id;
                    $bonusValue = '';
                    $bonusTransId = NULL;
                    $comment = "Deposit #" . $paymentLog->payment_id;
                    $bonusComment = "Bonus Deposit";
                    $deposited_date = date('Y-m-d H:i:s');
                    if (!empty($bonus_id)) {
                        $bonusDetails = BonusModel::where('bonus_id', $bonus_id)->first();
                        if ($bonusDetails) {
                            if ($bonusDetails->bonus_type === 'percentage') {
                                $bonusValue = ($amount * $bonusDetails->bonus_value) / 100;
                            } else {
                                $bonusValue = $bonusDetails->bonus_value;
                            }
                        } else {
                            $bonusValue = 0;
                        }
                    }
                    $paymentLog->update([
                        // 'payment_res' => $payment_res,
                        'payment_status' => $status,
                    ]);
                    if (strtolower($status) == 'done') {
                        $paymentLog->update([
                            'log_status' => 1,
                        ]);
                        $errorCode = $this->api->TradeBalance($trade_id, $type = MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, $margin_check = true);
                        if ($errorCode != MTRetCode::MT_RET_OK) {
                            $error = MTRetCode::GetError($errorCode);
                            return response()->json([
                                'success' => false,
                                'message' => 'Something went wrong',
                                'error' => $error,
                            ], 400);
                        } elseif ($bonusValue > 0 && ($error_code = $this->api->TradeBalance($trade_id, $type = MTEnDealAction::DEAL_BONUS, $bonusValue, $bonusComment, $ticket, $margin_check = true)) != MTRetCode::MT_RET_OK) {
                            $error = MTRetCode::GetError($error_code);
                            return response()->json([
                                'success' => false,
                                'message' => 'Something went wrong',
                                'error' => $error,
                            ], 400);
                        }
                        if (!empty($bonus_id)) {
                            $bonusDepositCurrency = "USD";
                            $bonusDepositType = "Bonus In";
                            $bonusDescription = '';
                            $bonusTransaction = BonusTransaction::create([
                                'email' => $email,
                                'trade_id' => $trade_id,
                                'bonus_amount' => $bonusValue,
                                'bonus_type' => $bonusDepositType,
                                'bonus_id' => $bonus_id,
                                'status' => 1,
                                'adminRemark' => $bonusDescription,
                                'bonus_currency' => $bonusDepositCurrency,
                                'created_by' => $email,
                            ]);
                            $bonusTransId = $bonusTransaction->id;
                        } else {
                            $bonusTransId = 0;
                        }
                        // Insert into trade deposit

                        $datalog = [
                             'email' => $email,
                            'trade_id' => $trade_id,
                            'deposit_amount' => $amount,
                            'bonus_amount' => $bonusValue,
                            'bonus_trans_id' => $bonusTransId,
                            'deposit_type' => 'Crypto Payment',
                            'deposted_date' => $deposited_date,
                            'deposit_from' => null,
                            'deposit_proof' => '',
                            'Status' => 1,
                            'payment_log_id' => $paymentLog->payment_id
                        ];
                        $tradeDeposits = TradeDeposits::create([
                            'email' => $email,
                            'trade_id' => $trade_id,
                            'deposit_amount' => $amount,
                            'bonus_amount' => $bonusValue,
                            'bonus_trans_id' => $bonusTransId,
                            'deposit_type' => 'Crypto Payment',
                            'deposted_date' => $deposited_date,
                            'deposit_from' => null,
                            'deposit_proof' => '',
                            'Status' => 1,
                            'payment_log_id' => $paymentLog->payment_id
                        ]);
                        if ($tradeDeposits) {
                            $user = User::where('email', $email)->first();
                            $emailSubject = $this->settings['admin_title'] . ' - Fund Deposit';
                            $transid = "TDID" . str_pad($tradeDeposits->id, 4, '0', STR_PAD_LEFT);
                            $content = '<div>We are pleased to inform you that funds have been successfully deposited into your account.</div>
            <div><b>Transaction Details</b></div>
            <div><b>Approved Amount: </b>$' . $amount . '</div>';
                            if ($bonusValue > 0) {
                                $content .= '<div><b>Bonus Amount: </b>$' . $bonusValue . '</div>';
                            }
                            $content .= '<div><b>Account ID: </b>' . $trade_id . '</div>
            <div><b>Transaction ID: </b>' . $transid . '</div>
            <div><b>Deposited Date: </b>' . $deposited_date . '</div>
            <div><b>Deposit Type </b>Crypto Payment</div>';
                            $templateVars = [
                                'name' => $user->fullname,
                                'site_link' => $this->settings['copyright_site_name_text'],
                                "btn_text" => "Go To Dashboard",
                                'email' => $this->settings['email_from_address'],
                                "content" => $content,
                                "title_right" => "Fund",
                                "subtitle_right" => "Deposit"
                            ];

                            addIpLog(' Crypto Payment Trade Deposit ',  $datalog);
                            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                            return response()->json(
                                [
                                    'status' => false,
                                    'message' => "Successfully Deposited \$$amount To Your Trade Account",
                                    'reference' => $email
                                ]
                            );
                            // return redirect($redirect_link)->with('success', "Successfully Deposited \$$amount To Your Trade Account");
                        } else {
                            return response()->json(
                                [
                                    'status' => false,
                                    'message' => "Something went wrong. Please Try Again",
                                    'reference' => $email
                                ]
                            );
                            // return redirect($redirect_link)->with('error', "Something went wrong. Please Try Again");
                        }
                    } else {
                        return response()->json(
                            [
                                'status' => true,
                                'message' => "the status changed to " . $status,
                                'reference' => json_encode($request->all())
                            ]
                        );
                    }
                } else {
                    return response()->json(
                        [
                            'status' => false,
                            'message' => "invalid url",
                            'reference' => json_encode($request->all())
                        ]
                    );
                    // return redirect($redirect_link)->with('error', "Invalid URL");
                }
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'message' => "Payment Already Processed",
                        'reference' => json_encode($request->all())
                    ]
                );
                // return redirect($redirect_link)->with('error', "Payment Already Processed");
            }
        } else {
            return response()->json(
                [
                    'status' => false,
                    'message' => "Invalid Payment ID",
                    'reference' => json_encode($request->all())
                ]
            );
            // return redirect($redirect_link)->with('error', "Invalid Payment ID");
        }
    }
    public function checkPaymentStatus(Request $request)
    {
        $paymentStatus = DB::table('payment_logs')
            ->whereRaw('MD5(payment_id) = ?', [$request->payment_id])
            ->value('payment_status');
        return response()->json([
            'payment_status' => strtolower($paymentStatus)
        ]);
    }

    public function handlePayissaResponse(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $paymentRes = json_encode($request->all());
        $depositType = 'Card Payment';
        $currency = 'USD';
        $status = 'completed';
        $settings = settings();

        // Fetch Payment Log
        $log = DB::table('payment_logs')
            ->leftJoin('aspnetusers', 'payment_logs.initiated_by', '=', 'aspnetusers.email')
            ->whereRaw('md5(payment_logs.payment_id) = ?', [$paymentId])
            ->select('payment_logs.*', 'aspnetusers.fullname')
            ->first();
        if (!$log) {
            return redirect('dashboard')->with('error', "Invalid payment ID.");
        }

        if ($log->payment_status === 'completed') {
            return redirect('dashboard')->with('success', "Payment has already been processed.");
        }

        // Update payment log
        DB::table('payment_logs')
            ->whereRaw('md5(payment_id) = ?', [$paymentId])
            ->update(['payment_res' => $paymentRes, 'payment_status' => $status]);

            $datalog = [
                 'email' => $log->initiated_by,
                'deposit_amount' => $log->payment_amount,
                'deposit_type' => $depositType,
                'currency_type' => $currency,
                'Status' => 1,
                'payment_log_id' => $log->payment_id
            ];
        if ($log->payment_reference_id === 'wallet') {
            $walletDeposit = WalletDeposit::create([
                'email' => $log->initiated_by,
                'deposit_amount' => $log->payment_amount,
                'deposit_type' => $depositType,
                'currency_type' => $currency,
                'Status' => 1,
                'payment_log_id' => $log->payment_id
            ]);

            addIpLog(' Crypto Payment PayissaRespons ',  $datalog);
            $this->sendSuccessEmail($log->initiated_by, $log->payment_amount, $log, $walletDeposit->id);
            return redirect('/wallet_deposit')->with('success', 'Your payment has been processed successfully.');
        } else {
            $this->mt5Service->connect();
            $this->api = $this->mt5Service->getApi();
            $paymentStatus = 1;
            $email = $log->initiated_by;
            $trade_id = $log->payment_reference_id;
            $amount = $log->payment_amount;
            $depositedDate = $log->created_at;
            $fullname = $log->fullname;
            $comment = "";

            $errorCode = $this->api->TradeBalance($trade_id, $type = MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, $margin_check = true);
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return redirect('/trade-deposit')->with('error', $error);
            } else {
                $lastInsertId = DB::table('trade_deposit')->insertGetId([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'deposit_amount' => $amount,
                    'deposit_type' => $depositType,
                    // 'deposit_from' => $depositFrom,
                    // 'deposit_proof' => $depositProof,
                    'status' => $paymentStatus,
                    'payment_id' => $log->payment_id
                ]);
                $transId = "TDID" . str_pad($lastInsertId, 4, '0', STR_PAD_LEFT);
                $emailSubject = $this->settings['admin_title'] . ' - Transaction Successful';
                $templateVars = [
                    'name' => $fullname,
                    'site_link' => $this->settings['copyright_site_name_text'],
                    'email' => $this->settings['email_from_address'],
                    'content' => "<div>We are pleased to inform you that your payment has been successfully processed.</div>
                      <div>The amount has been deposited into your account.</div>
                      <div><b>Transaction Details</b></div>
                      <div><b>Approved Amount: </b>$" . $amount . "</div>
                      <div><b>Account ID: </b>" . $trade_id . "</div>
                      <div><b>Transaction ID: </b>" . $transId . "</div>
                      <div><b>Deposited Date: </b>" . $depositedDate . "</div>
                      <div><b>Deposit Type: </b>" . $depositType . "</div>",
                    'title_right' => "Transaction",
                    'subtitle_right' => "Successful",
                    'btn_text' => "Go To Dashboard",
                ];
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                return redirect('/trade-deposit')->with('success', 'Your payment has been processed successfully.');
            }
        }
    }
	
	
	function updateTransaction(Request $request)
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // echo'<pre>';print_r($request->all());exit;
        $settings = settings();
        $data = $request->all();		
        $description = $data['description'];
        $status = $data['status'];
        $did = $data['did'];
        $email = $data['email'];
        $amount = $data['amount'];
        $login = $data['tradeId'];
        $bonus_amount = $data['bonus_amount'] ?? 0;
        $comment = "Deposit";
		
		$emailup = '';
		if(session('alogin') == ""){
            $emailup = session("alogin");
        } else {
			$emailup = 'support@leveragemarkets.com';
		}

        if ($status == 1) {
            
            $error = null;
            $ticket = null;
            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);				
            } else if ($bonus_amount > 0 && ($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BONUS, $bonus_amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
            } else {
				
                DB::table('trade_deposit as td')
				->where('td.id', '=', $did)
				->update([
					'AdminRemark' => $description,
					'Status' => $status,
					'admin_email' => 'support@leveragemarkets.com'
				]);
                DB::table('total_balance')->insert([
                    'email' => $email,
                    'trading_deposited' => $amount,
                ]);
				
				
                $depositDetails = DB::table('trade_deposit as td')
                    ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
                    ->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
                    ->where('td.id', '=', $did)
                    ->first();
								
                if ($depositDetails->bonus_trans_id) {
                    DB::table('bonus_trans')
                        ->where('id', $depositDetails->bonus_trans_id)
                        ->update(['status' => 1]);
                }
				
                $emailSubject = $settings['admin_title'] . ' - Transaction Approved';
                $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
                $content = '<div>We are pleased to inform you that your transaction has been successfully approved. </div>
        <div>The approved amount has been deposited into your account.</div>
        <div><b>Transaction Details</b></div>
        <div><b>Approved Amount: </b>$' . $amount . '</div>';
                if ($bonus_amount > 0) {
                    $content .= '<div><b>Bonus Amount: </b>$' . $bonus_amount . '</div>';
                }
                $content .= '<div><b>Account ID: </b>' . $login . '</div>
        <div><b>Transaction ID: </b>' . $transid . '</div>
        <div><b>Deposited Date: </b>' . $depositDetails->date . '</div>
        <div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
                $templateVars = [
                    'name' => $depositDetails->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Transaction",
                    "subtitle_right" => "Approved"
                ];
                
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                //return redirect()->back()->with('success', 'Transaction Approved Successfully');
            }
        } else {
            DB::table('trade_deposit as td')
				->where('td.id', '=', $did)
				->update([
					'AdminRemark' => $description,
					'Status' => $status,
					'admin_email' => 'support@leveragemarkets.com'
				]);
            $depositDetails = DB::table('trade_deposit as td')
				->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
				->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
				->where('td.id', '=', $did)
				->first();
            $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
            $emailSubject = $settings['admin_title'] . ' - Transaction Rejected';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $content = '<div>This email to inform you that your transaction has been Rejected. </div>
                    <div><b>Transaction Details</b></div>
                    <div><b>Rejected Amount: </b>$' . $depositDetails->amount . '</div>
                    <div><b>Account ID: </b>' . $depositDetails->trade_id . '</div>
                    <div><b>Transaction ID: </b>' . $transid . '</div>
                    <div><b>Date: </b>' . $depositDetails->date . '</div>
                    <div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
            $templateVars = [
                'name' => $depositDetails->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Transaction",
                "subtitle_right" => "Rejected",
                "btn_text" => "Go To Dashboard",
            ];
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            return redirect()->back()->with('success', 'Transaction Rejected');
        }
    }
	public function walletTransactionupdate(Request $request){
		//error_reporting(E_ALL);
        //ini_set('display_errors', 1);

        // echo'<pre>';print_r($request->all());exit;
        $settings = settings();
        $data = $request->all();		
        $description = $data['description'];
        $status = $data['status'];
        $did = $data['did'];
        $email = $data['email'];
        $amount = $data['amount'];
        $login = $data['tradeId'];
        $bonus_amount = $data['bonus_amount'] ?? 0;
        $comment = "Deposit";
		
		$emailup = '';
		if(session('alogin') == ""){
            $emailup = session("alogin");
        } else {
			$emailup = 'support@leveragemarkets.com';
		}

        if ($status == 1) {
            
            $error = null;
            $ticket = null;		
			/*$error_code = $this->api->Balance(
				$login,
				$amount, // + deposit, - withdrawal
				IMTDeal::DEAL_TYPE_BALANCE,
				$comment
			);

			if ($error_code != MTRetCode::MT_RET_OK) {
				throw new Exception('MT5 balance error: ' . $error_code);
			}*/


			
            if (($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
			//if (($error_code = $this->api->UserDepositChange($login, $amount, $comment, $type=MTEnDealAction::DEAL_BALANCE)) != MTRetCode::MT_RET_OK){
                $error = MTRetCode::GetError($error_code);				
            } elseif ($bonus_amount > 0 && ($error_code = $this->api->TradeBalance($login, MTEnDealAction::DEAL_BONUS, $bonus_amount, $comment, $ticket, true)) != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($error_code);
            } else {
				
                DB::table('trade_deposit as td')
				->where('td.id', '=', $did)
				->update([
					'AdminRemark' => $description,
					'Status' => $status,
					'admin_email' => 'support@leveragemarkets.com'
				]);
                DB::table('total_balance')->insert([
                    'email' => $email,
                    'trading_deposited' => $amount,
                ]);
				
                $depositDetails = DB::table('trade_deposit as td')
                    ->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
                    ->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
                    ->where('td.id', '=', $did)
                    ->first();
								
                if ($depositDetails->bonus_trans_id) {
                    DB::table('bonus_trans')
                        ->where('id', $depositDetails->bonus_trans_id)
                        ->update(['status' => 1]);
                }
				
                $emailSubject = $settings['admin_title'] . ' - Transaction Approved';
                $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
                $content = '<div>We are pleased to inform you that your transaction has been successfully approved. </div>
				<div>The approved amount has been deposited into your account.</div>
				<div><b>Transaction Details</b></div>
				<div><b>Approved Amount: </b>$' . $amount . '</div>';
                if ($bonus_amount > 0) {
                    $content .= '<div><b>Bonus Amount: </b>$' . $bonus_amount . '</div>';
                }
                $content .= '<div><b>Account ID: </b>' . $login . '</div>
				<div><b>Transaction ID: </b>' . $transid . '</div>
				<div><b>Deposited Date: </b>' . $depositDetails->date . '</div>
				<div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
                $templateVars = [
                    'name' => $depositDetails->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "Transaction",
                    "subtitle_right" => "Approved"
                ];
                
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
                //return redirect()->back()->with('success', 'Transaction Approved Successfully');
                return true;
            }
        } else {
            DB::table('trade_deposit as td')
				->where('td.id', '=', $did)
				->update([
					'AdminRemark' => $description,
					'Status' => $status,
					'admin_email' => 'support@leveragemarkets.com'
				]);
            $depositDetails = DB::table('trade_deposit as td')
				->leftJoin('aspnetusers as ap', 'td.email', '=', 'ap.email')
				->select('td.id', 'ap.fullname', 'td.email', 'td.trade_id', 'td.deposit_amount as amount', 'td.bonus_amount', 'td.bonus_trans_id', 'td.deposted_date as date', 'td.deposit_type as type')
				->where('td.id', '=', $did)
				->first();
            $transid = "TDID" . str_pad($depositDetails->id, 4, '0', STR_PAD_LEFT);
            $emailSubject = $settings['admin_title'] . ' - Transaction Rejected';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $content = '<div>This email to inform you that your transaction has been Rejected. </div>
                    <div><b>Transaction Details</b></div>
                    <div><b>Rejected Amount: </b>$' . $depositDetails->amount . '</div>
                    <div><b>Account ID: </b>' . $depositDetails->trade_id . '</div>
                    <div><b>Transaction ID: </b>' . $transid . '</div>
                    <div><b>Date: </b>' . $depositDetails->date . '</div>
                    <div><b>Deposit Type </b>' . $depositDetails->type . '</div>';
            $templateVars = [
                'name' => $depositDetails->fullname,
                'site_link' => $settings['copyright_site_name_text'],
                'email' => $settings['email_from_address'],
                "content" => $content,
                "title_right" => "Transaction",
                "subtitle_right" => "Rejected",
                "btn_text" => "Go To Dashboard",
            ];
            $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            // return redirect()->back()->with('success', 'Transaction Rejected');
            return response()->json([
                        'status' => false,
                        'message' => 'Transaction Rejected',
                    ]);
        }
  }
 
}
