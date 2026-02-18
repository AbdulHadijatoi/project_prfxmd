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

class Payment extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MailService $mailService, MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->mt5Service = $mt5Service;
        $this->mailService = $mailService;
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
                // Create a new wallet deposit
                if ($paymentLog->payment_reference_id == 'Wallet') {
                    $walletDeposit = WalletDeposit::create([
                        'email' => $email,
                        'deposit_amount' => $amount,
                        'deposit_type' => "Now Payment",
                        'currency_type' => "USD",
                        'Status' => 1,
                    ]);
                } else {
                    $walletDeposit = TradeDeposits::create([
                        'email' => $email,
                        'trade_id' => $paymentLog->payment_reference_id,
                        'deposit_amount' => $amount,
                        'deposit_type' => "Now Payment",
                        'deposit_currency' => "USD",
                        'Status' => 0,
                    ]);
                }

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
        $templateVars = [
            'name' => $user->fullname,
            'site_link' => $settings['copyright_site_name_text'],
            'email' => $settings['email_from_address'],
            "content" => $content,
            "title_right" => "Transaction",
            "subtitle_right" => "Successful",
            "btn_text" => "Go To Dashboard",
        ];
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

        if ($log->payment_reference_id === 'wallet') {
            $walletDeposit = WalletDeposit::create([
                'email' => $log->initiated_by,
                'deposit_amount' => $log->payment_amount,
                'deposit_type' => $depositType,
                'currency_type' => $currency,
                'Status' => 1,
                'payment_log_id' => $log->payment_id
            ]);
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


}
