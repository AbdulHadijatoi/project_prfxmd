<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\AccountHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TournamentModel as Tournament;
use DB;
use App\MT5\MTWebAPI;
use App\Services\MT5Service;
use App\Services\MailService as MailService;
use App\Models\TradeDeposits;
use App\MT5\MTEnDealAction;
use App\MT5\MTRetCode;
use App\Models\LiveAccount;
use App\Models\TradeWithdrawals;
use App\Models\User;
use App\Models\BonusTrans;
use App\Models\IbWallet;
use App\Models\Ib1;

class Utilities extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MailService $mailService, MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
        $this->mailService = $mailService;
    }
    public function index()
    {
        $live_accounts = LiveAccount::all();
        return view("admin.utilities", compact('live_accounts'));
    }
    public function excludes()
    {
        $live_accounts = LiveAccount::all();
        return view("admin.utilities", compact('live_accounts'));
    }
    public function singleFormTransaction(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required',
            'amount' => 'nullable',
            'description' => 'required',
        ]);
        $ret_status = "true";
        $amount = $request->input('amount');
        $description = $request->input('description');
        $type = $request->input('type');
        $deposit_currency = 'USD';
        $email = $request->input('email');
        $trade_id = $request->input(key: 'trade_id');
        $to_account = $request->input('to_account');
        if (empty($email)) {
            $liveaccount = LiveAccount::where('trade_id', $trade_id)->first();
            $email = $liveaccount->email;
        }
        $user = User::where('email', $email)->first();
        if ($type == 'deposit') {
            $deposit_type = 'CRM';
            $ticket = null;
            $db_trans = DB::transaction(function () use ($ret_status,$email, $trade_id, $amount, $ticket, $deposit_type, $description, $deposit_currency, &$transid) {
            $datalogs = [
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'deposit_amount' => $amount,
                    'deposit_type' => $deposit_type,
                    'status' => 1,
                    'AdminRemark' => $description,
                    'deposit_currency' => $deposit_currency,
                    'created_by' => session('alogin')
            ];   
            $tradeDeposit = TradeDeposits::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'deposit_amount' => $amount,
                    'deposit_type' => $deposit_type,
                    'status' => 1,
                    'AdminRemark' => $description,
                    'deposit_currency' => $deposit_currency,
                    'created_by' => session('alogin')
                ]);
                $transid = "TDID" . str_pad($tradeDeposit->id, 4, '0', STR_PAD_LEFT);
                $comment = 'CRM Deposited #' . $transid;
                $error_code = $this->api->TradeBalance($trade_id, MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true);
                if ($error_code !== MTRetCode::MT_RET_OK) {
                    $ret_status = MTRetCode::GetError($error_code);
                    return $ret_status;
                }
                addIpLog('deposit request', $datalogs);
                return "true";
            });
        } else if ($type == 'withdrawal') {
            $tw_amount = abs($amount) * -1;
            $withdraw_type = 'CRM';
            $ticket = null;
            $db_trans = DB::transaction(function () use ($ret_status,$email, $trade_id, $amount, $withdraw_type, $description, $ticket, $tw_amount, &$transid) {
            $datalogs = [  'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => $withdraw_type,
                    'AdminRemark' => $description,
                    'Status' => 1,
                    'created_by' => session('alogin')];  
            $deposit_details = TradeWithdrawals::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => $withdraw_type,
                    'AdminRemark' => $description,
                    'Status' => 1,
                    'created_by' => session('alogin')
                ]);
                $transid = "TWID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $comment = 'CRM Withdrawal #' . $transid;
                $error_code = $this->api->TradeBalance($trade_id, MTEnDealAction::DEAL_BALANCE, $tw_amount, $comment, $ticket);
                if ($error_code !== MTRetCode::MT_RET_OK) {
                    $ret_status = MTRetCode::GetError($error_code);
                    return $ret_status;
                }
                addIpLog('withdrawal request', $datalogs);

                return "true";

            });
        } else if ($type == 'bonus_in' || $type == 'bonus_out') {
            $deposit_type = $type === 'bonus_in' ? 'Bonus In' : 'Bonus Out';
            $amount = $type === 'bonus_in' ? $amount : -1 * $amount;
            $ticket = null;
            $db_trans = DB::transaction(function () use ($ret_status,$email, $trade_id, $amount, $deposit_type, $description, $deposit_currency, $ticket, &$transid) {
            $datalogs = [
                 'email' => $email,
                    'trade_id' => $trade_id,
                    'bonus_amount' => $amount,
                    'bonus_type' => $deposit_type,
                    'status' => 1,
                    'admin_remark' => $description,
                    'bonus_currency' => $deposit_currency,
                    'created_by' => session('alogin')
            ];   
            $deposit_details = BonusTrans::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'bonus_amount' => $amount,
                    'bonus_type' => $deposit_type,
                    'status' => 1,
                    'admin_remark' => $description,
                    'bonus_currency' => $deposit_currency,
                    'created_by' => session('alogin')
                ]);
                $transid = "BTID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $description = $description . " #" . $transid;
                $error_code = $this->api->TradeBalance($trade_id, MTEnDealAction::DEAL_BONUS, $amount, $description, $ticket, true);
                if ($error_code !== MTRetCode::MT_RET_OK) {
                    $ret_status = MTRetCode::GetError($error_code);
                    return $ret_status;
                }
                addIpLog('bonus_in  Request', $datalogs);
                return "true";

            });
        } else if ($type == 'internal_transfer') {
            $transid = null;
            $ticket = null;
            $db_trans = DB::transaction(function () use ($ret_status,$email, $trade_id, $to_account, $amount, $ticket, $description, &$transid) {
                $datalogs = [
                     'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => 'Internal Transfer',
                    'withdraw_to' => $to_account,
                    'withdraw_date' => now(),
                    'Status' => 1,
                    'AdminRemark' => $description
                ];
            $withdrawal = TradeWithdrawals::create([
                    'email' => $email,
                    'trade_id' => $trade_id,
                    'withdrawal_amount' => $amount,
                    'withdraw_type' => 'Internal Transfer',
                    'withdraw_to' => $to_account,
                    'withdraw_date' => now(),
                    'Status' => 1,
                    'AdminRemark' => $description
                ]);
                $transid = "TWID" . str_pad($withdrawal->id, 4, '0', STR_PAD_LEFT);
                $errorCode = $this->api->TradeBalance($trade_id, MTEnDealAction::DEAL_BALANCE, -$amount, 'withdraw #' . $transid, $ticket, true);
                if ($errorCode != MTRetCode::MT_RET_OK) {
                    $error = MTRetCode::GetError($errorCode);
                    $ret_status = MTRetCode::GetError($errorCode);
                    return $ret_status;
                }
           
                $trade_deposit = TradeDeposits::create([
                    'email' => $email,
                    'trade_id' => $to_account,
                    'deposit_amount' => $amount,
                    'deposit_type' => 'Internal Transfer',
                    'deposit_from' => $trade_id,
                    'status' => 1,
                    'AdminRemark' => $description
                ]);
                $transid = "ITID" . str_pad($trade_deposit->id, 4, '0', STR_PAD_LEFT);
                $errorCode = $this->api->TradeBalance($to_account, MTEnDealAction::DEAL_BALANCE, $amount, 'deposit #' . $transid, $ticket, true);
                if ($errorCode != MTRetCode::MT_RET_OK) {
                    $error = MTRetCode::GetError($errorCode);
                    $ret_status = MTRetCode::GetError($errorCode);
                    return $ret_status;
                }

                addIpLog('internal_transfer Request', $datalogs);
                return "true";

            });
        } else if ($type == 'ib_withdrawal') {
            $email_exists = Ib1::where('email', $email)->count();
            if ($email_exists > 0) {
                $datalogs = [
                    'ib_withdraw' => $amount,
                    'email' => $email,
                    'trade_id' => '',
                    'order_id' => '',
                    'remark' => $description,
                    'ib_level' => 0,
                    'admin_id' => session('alogin')
                ];
                $ib_wallet = IbWallet::create([
                    'ib_withdraw' => $amount,
                    'email' => $email,
                    'trade_id' => '',
                    'order_id' => '',
                    'remark' => $description,
                    'ib_level' => 0,
                    'admin_id' => session('alogin')
                ]);
                 addIpLog('ib_withdrawa', $datalogs);
                $transid = "IWID" . str_pad($ib_wallet->id, 4, '0', STR_PAD_LEFT);
                return "true";

            } else {
                $ret_status = "Invalid Email";
                return $ret_status;
            }

        }
        // dd($db_trans);
        if ($db_trans == "true") {
            if (!in_array($type, ['internal_transfer'])) {
                $settings = settings();
                $emailSubject = $settings['admin_title'] . ' - ' . ucwords(str_replace("_", " ", $type));
                $content = '<div>We are pleased to inform you that your transaction has been completed successfully.</div>
          <div><b>Transaction Details</b></div>
          <div><b>Amount: </b>$' . $amount . '</div>
          <div><b>Type: </b>' . ucwords(str_replace("_", " ", $type)) . '</div>
          <div><b>Account ID: </b>' . $trade_id . '</div>
          <div><b>Transaction ID: </b>' . $transid . '</div>
          <div><b>Deposited Date: </b>' . date("Y-m-d H:i:s") . '</div>';
                $templateVars = [
                    'name' => $user->fullname,
                    'site_link' => settings()['copyright_site_name_text'],
                    "btn_text" => "Go To Dashboard",
                    'email' => settings()['email_from_address'],
                    "content" => $content,
                    "title_right" => "",
                    "subtitle_right" => ucwords(str_replace("_", " ", $type))
                ];
                $this->mailService->sendEmail($email, $emailSubject, '', '', $templateVars);
            }

            $datalogs = [
                'type' => $type,
                'email' => $email,
                'trade_id' => $trade_id,
                'to_account' => $to_account,
                'transaction_id' => $transid,
                'amount' => $amount,
                'status' => 1,
                'description' => $description,
                'created_by' => session('alogin')
            ];
            DB::table('single_form_transactions')->insert([
                'type' => $type,
                'email' => $email,
                'trade_id' => $trade_id,
                'to_account' => $to_account,
                'transaction_id' => $transid,
                'amount' => $amount,
                'status' => 1,
                'description' => $description,
                'created_by' => session('alogin')
            ]);
             addIpLog('single_form_transactions Request', $datalogs);
            return redirect()->back()->with('success', 'Transaction Successful');
        }
        else {
            alert()->warning("Transaction Failed",$db_trans);
            return redirect()->back();

        }


    }
    public function getUtilityAccounts(Request $request)
    {
        $email = $request->email;
        $userGroups = json_decode(session("userData")["user_group_id"]);
        if (!empty($email)) {
            $live_accounts = LiveAccount::select(DB::raw("liveaccount.*"))->join("account_types", "account_types.ac_index", "=", "liveaccount.account_type")->whereIn("account_types.user_group_id", $userGroups)->where('email', 'like', '%' . $email . '%');
            if ($request->term) {
                $live_accounts = $live_accounts->whereAny([
                    'email',
                    'name',
                    'trade_id',
                ], 'like', '%' . $request->term . '%');
            }
            $live_accounts = $live_accounts->get();
        } else {
            $live_accounts = LiveAccount::select(DB::raw("liveaccount.*"))->join("account_types", "account_types.ac_index", "=", "liveaccount.account_type")->whereIn("account_types.user_group_id", $userGroups)->whereAny([
                'email',
                'name',
                'trade_id',
            ], 'like', '%' . $request->term . '%')->get();
        }
        return response()->json($live_accounts);
    }
}
