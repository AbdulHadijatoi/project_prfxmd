<?php
namespace App\Http\Controllers\admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\WalletWithdraw;
use App\Models\WalletDeposit;
use App\Models\TotalBalance;
use App\Models\IbWithdraw;
use App\Models\IbWallet;
use App\Models\PaymentLog;
use App\Models\ClientWallets;
use App\Services\MailService as MailService;
use App\Services\PayoutService;

class Transaction extends Controller
{
    protected $mailService;
    protected $payoutService;

    public function __construct(MailService $mailService, PayoutService $payoutService)
    {
        $this->mailService = $mailService;
        $this->payoutService = $payoutService;
    }
    public function index(Request $request)
    {
        if (!isset($request->id)) {
            return redirect('admin/dashboard');
        }
        $id = $request->id;
         $datalogs = [
        'action'      => 'Transactions Page Viewed',
        'reference_id'=> $id,
        'status'      => 'success',
        'admin_email' => session('alogin'),
        'role_id'     => session('userData.role_id') ?? null,
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('Transactions Page Viewed', $datalogs);
        return view('admin.transactions', compact('id'));


    }
    public function pending(Request $request)
    {
        if (!isset($request->id)) {
            return redirect('admin/dashboard');
        }
        $id = $request->id;
        return view('admin.pending_transactions', compact('id'));
    }
    public function wallet_deposit_details(Request $request)
    {
        if (request()->has('id') && !empty(request()->id)) {
            $details = DB::table('wallet_deposit as wd')
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_email', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                // ->leftJoin('total_balance as tb', 'u.email', '=', 'tb.email')
                ->when(session('userData.role_id') == 2, function ($query) {
                    $query->join('relationship_manager as rm', 'wd.email', '=', 'rm.user_id')
                        ->where('rm.rm_id', session('alogin'));
                })
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->selectRaw("
                    wd.*, u.fullname, u.number, ib1.name as parent_ib,
                    ib1.email as parent_ib_email, r.rm_id, emp.username as rm_name,emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
              $datalogs = [
                'action'          => 'Client Wallet Deposit Details Viewed',
               
                'status'          => 'not_found',
                'admin_email'     => session('alogin'),
                'role_id'         => session('userData.role_id') ?? null,
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->userAgent(),
                'timestamp'       => now(),
            ];
            addIpLog('Client Wallet Deposit Details Viewed', $datalogs);
            return view('admin.wallet_deposit_details', compact('details'));
        }
    }
    public function wallet_withdrawal_details(Request $request)
    {
        if (request()->has('id') && !empty(request()->id)) {
            $id = request()->id;
            if (str_starts_with($id, 'pl_')) {
                $hash = substr($id, 3);
                $paymentLog = PaymentLog::whereRaw('md5(payment_id) = ?', [$hash])->first();
                if (!$paymentLog || !in_array($paymentLog->payment_status, ['Pending', 'Initiated'])) {
                    return view('admin.wallet_withdrawal_details', ['details' => null]);
                }
                $user = \App\Models\User::where('email', $paymentLog->initiated_by)->first();
                $walletName = $walletCurrency = $walletNetwork = $walletAddress = '';
                if ($paymentLog->payment_to && is_numeric($paymentLog->payment_to)) {
                    $cw = ClientWallets::where('client_wallet_id', $paymentLog->payment_to)->first();
                    if ($cw) {
                        $walletName = $cw->wallet_name ?? '';
                        $walletCurrency = $cw->wallet_currency ?? '';
                        $walletNetwork = $cw->wallet_network ?? '';
                        $walletAddress = $cw->wallet_address ?? '';
                    }
                } else {
                    $walletAddress = $paymentLog->payment_to ?? '';
                }
                $details = (object) [
                    'id' => $paymentLog->payment_id,
                    'email' => $paymentLog->initiated_by,
                    'fullname' => $user ? $user->fullname : $paymentLog->initiated_by,
                    'number' => $user ? $user->number : '',
                    'withdraw_amount' => $paymentLog->payment_amount,
                    'withdraw_type' => 'Now Payment (Pending)',
                    'withdraw_date' => $paymentLog->created_at ?? now(),
                    'Status' => 0,
                    'wallet_name' => $walletName,
                    'wallet_currency' => $walletCurrency,
                    'wallet_network' => $walletNetwork,
                    'wallet_address' => $walletAddress,
                    'currency_type' => '',
                    'approved_name' => null,
                    'transaction_id' => null,
                    'AdminRemark' => null,
                    'Js_Admin_Remark_Date' => null,
                    'parent_ib' => null,
                    'parent_ib_email' => null,
                    'rm_id' => null,
                    'rm_name' => null,
                    'bankName' => null,
                    'accountNumber' => null,
                    'ClientName' => null,
                    'code' => null,
                    'swift_code' => null,
                ];
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
                $datalogs = [
                    'action' => 'Client Wallet Withdrawal Details Viewed (Payment Log)',
                    'withdraw_id' => $paymentLog->payment_id,
                    'email' => $details->email,
                    'amount' => $details->withdraw_amount,
                    'status' => 'success',
                    'admin_email' => session('alogin'),
                    'role_id' => session('userData.role_id') ?? null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'timestamp' => now(),
                ];
                addIpLog('Client Wallet Withdrawal Details Viewed', $datalogs);
                return view('admin.wallet_withdrawal_details', compact('details'));
            }
            DB::enableQueryLog();
            $details = DB::table('wallet_withdraw as wd')
                ->leftJoin('clientbankdetails as cbd', 'wd.client_bank', '=', 'cbd.id')
                ->leftJoin('client_wallets as cw', 'wd.wallet_id', '=', 'cw.client_wallet_id')
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_email', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                ->when(session('userData.role_id') == 2, function ($query) {
                    $query->join('relationship_manager as rm', 'wd.email', '=', 'rm.user_id')
                        ->where('rm.rm_id', session('alogin'));
                })
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->selectRaw("
                    cbd.bankName, cbd.branch, cbd.bankDetails, cbd.accountNumber, cbd.code, cbd.swift_code, cbd.ClientName,
                    cw.wallet_name, cw.wallet_currency, cw.wallet_network, cw.wallet_address, cbd.code, cbd.swift_code, cbd.ClientName,
                    wd.*, u.fullname, u.number, ib1.name as parent_ib,
                    ib1.email as parent_ib_email, r.rm_id, emp.username as rm_name, '' as currency_type,emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
             $datalogs = [
                'action'          => 'Client Wallet Withdrawal Details Viewed',
                'withdraw_id'     => $details->id ?? null,
                'email'           => $details->email ?? null,
                'amount'          => $details->withdraw_amount ?? null,
                'status'          => 'success',
                'admin_email'     => session('alogin'),
                'role_id'         => session('userData.role_id') ?? null,
                'ip_address'      => request()->ip(),
                'user_agent'      => request()->userAgent(),
                'timestamp'       => now(),
            ];
            addIpLog('Client Wallet Withdrawal Details Viewed', $datalogs);
            return view('admin.wallet_withdrawal_details', compact('details'));
        }
    }
    public function trading_deposit_details(Request $request)
    {
        
        if (request()->has('id')) {
            $details = DB::table('trade_deposit as wd')
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                // ->leftJoin('total_balance as tb', 'u.email', '=', 'tb.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_email', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                ->when(session('userData.role_id') == 2, function ($query) {
                    $query->join('relationship_manager as rm', 'wd.email', '=', 'rm.user_id')
                        ->where('rm.rm_id', session('alogin'));
                })
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->selectRaw("
                    wd.*, u.fullname, u.number, u.email, ib1.name as parent_ib,
                    ib1.email as parent_ib_email, r.rm_id, emp.username as rm_name,emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
                                  $datalogs = [
        'action'      => 'trading deposit details Viewed',
        'status'      => 'invalid_request',
        'admin_email' => session('alogin'),
        'role_id'     => session('userData.role_id') ?? null,
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('trading deposit details Viewed', $datalogs);
            return view('admin.trading_deposit_details', compact('details'));
        }
    }
    public function trading_withdrawal_details(Request $request)
    {
        if (request()->has('id') && !empty(request()->id)) {
            $details = DB::table('trade_withdrawal as wd')
                ->leftJoin('clientbankdetails', function ($join) {
                    $join->on('clientbankdetails.accountNumber', '=', 'wd.withdraw_to')
                        ->on('clientbankdetails.userId', '=', 'wd.email');
                })
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                ->leftJoin('liveaccount as la', 'la.trade_id', '=', 'wd.agent_account')
                // ->leftJoin('total_balance as tb', 'u.email', '=', 'tb.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_email', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->where(DB::raw('md5(wd.id)'), request()->id)
                ->selectRaw("
                    wd.*, u.fullname, u.number, u.email,
                    ib1.name as parent_ib, ib1.email as parent_ib_email,
                    r.rm_id, emp.username as rm_name,
                    clientbankdetails.ClientName as account_holder_name,
                    clientbankdetails.accountNumber as bank_account_no,
                    clientbankdetails.code as ifsc_code,
                    clientbankdetails.swift_code as swift_code,
                    clientbankdetails.bankName as bank_name,
                    la.name as agent_name,la.email as agent_email,
                    emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
                      $datalogs = [
        'action'      => 'trading withdrawal details Viewed',
        'status'      => 'invalid_request',
        'admin_email' => session('alogin'),
        'role_id'     => session('userData.role_id') ?? null,
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('trading withdrawal details Viewed', $datalogs);
            return view('admin.trading_withdrawal_details', compact('details'));
        }
    }
    public function internal_transfer_details(Request $request)
    {
        if (request()->has('id')) {
            $details = DB::table('trade_deposit as wd')
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_email', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                ->when(session('userData.role_id') == 2, function ($query) {
                    $query->join('relationship_manager as rm', 'wd.email', '=', 'rm.user_id')
                        ->where('rm.rm_id', session('alogin'));
                })
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->selectRaw("
                    wd.*, u.fullname, u.number, u.email, ib1.name as parent_ib,
                    ib1.email as parent_ib_email, r.rm_id, emp.username as rm_name,'' as deposit_currency_amount,'' as deposit_currency_in_usd,emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
             $datalogs = [
        'action'      => 'Internal Transfer Details Viewed',
        'status'      => 'invalid_request',
        'admin_email' => session('alogin'),
        'role_id'     => session('userData.role_id') ?? null,
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('Internal Transfer Details Viewed', $datalogs);
            return view('admin.internal_transfer_details', compact('details'));
        }
    }
    public function ib_withdrawal_details(Request $request)
    {
        if (request()->has('id') && !empty(request()->id)) {


            $details = DB::table('ib_wallet as wd')
                ->leftJoin('aspnetusers as u', 'wd.email', '=', 'u.email')
                ->leftJoin('relationship_manager as r', 'wd.email', '=', 'r.user_id')
                ->leftJoin('emplist as emp', 'r.rm_id', '=', 'emp.email')
                ->leftJoin('emplist', 'wd.admin_id', '=', 'emplist.email')
                ->leftJoin('ib1', 'u.ib1', '=', 'ib1.email')
                ->where(function ($query) {
                    $id = request()->id;
                    $query->where(DB::raw('md5(wd.id)'), $id);
                })
                ->where(DB::raw('md5(wd.id)'), request()->id)
                ->selectRaw("
                    wd.*, u.fullname, u.number, u.email,
                    ib1.name as parent_ib, ib1.email as parent_ib_email,
                    r.rm_id, emp.username as rm_name,
                    emplist.username as approved_name
                ")
                ->groupBy('u.email')
                ->first();
            if ($details) {
                $details->totalDeposit = $this->getTotalDeposit($details->email);
                $details->totalWithdrawal = $this->getTotalWithdrawal($details->email);
            }
               $datalogs = [
        'action'      => 'IB Wallet Withdrawal Details Viewed',
        'status'      => 'invalid_request',
        'admin_email' => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('IB Wallet Withdrawal Details Viewed', $datalogs);
            return view('admin.ib_withdrawal_details', compact('details'));
        }
    }
    public function update_wallet_withdrawal(Request $request)
    {
        $settings = settings();
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|integer',
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);
        $description = $validatedData['description'];
        $status = $validatedData['status'];
        $email = $validatedData['email'];
        $depositAmount = $validatedData['amount'];
        $did = $request->input('id') ?? $request->query('id');
        $transaction_id = $request->input('transaction_id');

        if ($did && str_starts_with($did, 'pl_')) {
            $hash = substr($did, 3);
            $paymentLog = PaymentLog::whereRaw('md5(payment_id) = ?', [$hash])->first();
            if (!$paymentLog || $paymentLog->initiated_by !== $email) {
                return redirect()->back()->with('error', 'Transaction Not Found');
            }
            if ($status == 1) {
                try {
                    $this->payoutService->approveWithdrawal($paymentLog->payment_id, true, $transaction_id, $description);
                    return redirect()->back()->with('success', 'Withdrawal Approved Successfully');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Payout failed: ' . $e->getMessage());
                }
            }
            $this->payoutService->rejectWithdrawal($paymentLog->payment_id, $description);
            return redirect()->back()->with('success', 'Withdrawal Rejected Successfully');
        }

        $transaction = WalletWithdraw::whereRaw('md5(id) = ?', [$did])->first();
        if ($transaction) {
            $transaction->AdminRemark = $description;
            $transaction->Status = $status;
            $transaction->admin_email = session('alogin');
            $transaction->transaction_id = $transaction_id;
            $transaction->save();
            if ($status == 1) {
                TotalBalance::create([
                    'email' => $email,
                    'withdraw_amount' => $depositAmount,
                ]);
                $deposit_details = WalletWithdraw::with('user')
                    ->whereRaw('md5(id) = ?', [$did])
                    ->first();
                $from = $settings['email_from_address'];
                $transid = "WWID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
                $emailSubject = $settings['admin_title'] . ' - Wallet Withdrawal';
                $content = '<div>We are pleased to inform you that your withdrawal has been successfully approved.</div>
                            <div><b>Transaction Details</b></div>
                            <div><b>Approved Amount: </b>$' . $deposit_details->withdraw_amount . '</div>
                            <div><b>Transaction ID: </b>' . $transid . '</div>
                            <div><b>Withdrawal Date: </b>' . $deposit_details->withdraw_date . '</div>
                            <div><b>Withdrawal Type: </b>' . $deposit_details->withdraw_type . '</div>';
               
               $datalogs = [
                  $description = $validatedData['description'],
        $status = $validatedData['status'],
        $email = $validatedData['email'],
        $depositAmount = $validatedData['amount'],
        $did = $request->input('id'),
               ];
                            $templateVars = [
                    'name' => $deposit_details->user->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    'email' => $settings['email_from_address'],
                    'content' => $content,
                    'title_right' => 'Wallet',
                    'subtitle_right' => 'Withdrawal',
                    'btn_text' => 'Go To Dashboard',
                ];
                addIpLog('update wallet withdrawal', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
                return redirect()->back()->with('success', 'Withdrawal Approved Successfully');
            }
            return redirect()->back()->with('success', 'Withdrawal Rejected Successfully');
        } else {
            return redirect()->back()->with('error', 'Transaction Not Found');
        }
    }
    public function update_wallet_deposit(Request $request)
    {
       
        $settings = settings();
        $validatedData = $request->validate([
            'description' => 'required|string|max:255',
            'status' => 'required|integer',
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);
        $description = $validatedData['description'];
        $status = $validatedData['status'];
        $email = $validatedData['email'];
        $depositAmount = $validatedData['amount'];
        $did = $request->input('id');
        $transaction_id = $request->input('transaction_id');
        $transaction = WalletDeposit::whereRaw('md5(id) = ?', [$did])->first();
        if ($transaction) {
            $transaction->AdminRemark = $description;
            $transaction->Status = $status;
            $transaction->admin_email = session('alogin');
            //$transaction->wallet_id=$transaction_id;
            $transaction->save();
            if ($status == 1) {
                TotalBalance::create([
                    'email' => $email,
                    'withdraw_amount' => $depositAmount,
                ]);
                $deposit_details = WalletDeposit::with('user')
                    ->whereRaw('md5(id) = ?', [$did])
                    ->first();
                $from = $settings['email_from_address'];
                $transid = "WDID" . str_pad($deposit_details->id, 4, '0', STR_PAD_LEFT);
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
                $emailSubject = $settings['admin_title'] . ' - Wallet Deposit';
                $content = '<div>We are pleased to inform you that your deposit has been successfully approved.</div>
                            <div><b>Transaction Details</b></div>
                            <div><b>Approved Amount: </b>$' . $deposit_details->deposit_amount . '</div>
                            <div><b>Transaction ID: </b>' . $transid . '</div>
                            <div><b>Deposit Date: </b>' . $deposit_details->deposted_date . '</div>
                            <div><b>Deposit Type: </b>' . $deposit_details->deposit_type . '</div>';
               $datalogs = [
                 $description = $validatedData['description'],
        $status = $validatedData['status'],
        $email = $validatedData['email'],
        $depositAmount = $validatedData['amount'],
        $did = $request->input('id'),
        $transaction_id = $request->input('transaction_id'),
               ];
               
                            $templateVars = [
                    'name' => $deposit_details->user->fullname,
                    'site_link' => $settings['copyright_site_name_text'],
                    'email' => $settings['email_from_address'],
                    'content' => $content,
                    'title_right' => 'Wallet',
                    'subtitle_right' => 'Deposit',
                    'btn_text' => 'Go To Dashboard',
                ];
                 addIpLog('WalletDeposit Viewed', $datalogs);
                $this->mailService->sendEmail($email, $emailSubject, $headers, '', $templateVars);
                return redirect()->back()->with('success', 'Deposit Approved Successfully');
            }
            return redirect()->back()->with('success', 'Deposit Rejected Successfully');
        } else {
            return redirect()->back()->with('error', 'Transaction Not Found');
        }
    }

    public function pending_tasks()
    {
        $acc_groups = DB::table('ib_plan_details')
            ->leftJoin('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->where('ib_plan_details.status', 1)
            ->select(DB::raw('ib_categories.ib_cat_name,ib_plan_details.ib_plan_id'))
            ->groupBy('ib_plan_details.ib_plan_id')
            ->get();
               $datalogs = [
            'action'        => 'Pending Tasks Viewed',
            'status'        => 'success',
            'record_count'  => $acc_groups->count(),
            'admin_email'   => session('alogin'),
            'ip_address'    => request()->ip(),
            'user_agent'    => request()->userAgent(),
            'timestamp'     => now(),
        ];
        addIpLog('Pending Tasks Viewed', $datalogs);
        return view('admin.tasks', compact('acc_groups'));
    }
    public function getTotalDeposit($email)
    {
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs WHERE trs.status=1 and trs.email='" . $email . "' and trs.deposit_type NOT IN('Wallet Transfer')";
        $trade_deposit = DB::select($sql)[0];

        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs WHERE trs. status=1 and trs.email='" . $email . "'";
        $wallet_deposit = DB::select($sql)[0];

        $totalDeposit = $trade_deposit->deposit + $wallet_deposit->deposit;
         addIpLog('getTotalDeposit',$totalDeposit);
        return round($totalDeposit ?? 0);
    }
    public function getTotalWithdrawal($email)
    {
        $sql = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs WHERE trs.status=1 and  trs.email='" . $email . "' and  trs.withdraw_type NOT IN('Wallet Withdrawal')";
        $trade_withdrawal = DB::select($sql)[0];

        $sql = "select COALESCE(SUM(trs.withdraw_amount), 0) as withdraw from wallet_withdraw  trs WHERE trs.status=1 and trs.email='" . $email . "'";
        $wallet_withdrawal = DB::select($sql)[0];

        $totalWithdrawal = $trade_withdrawal->withdraw + $wallet_withdrawal->withdraw;
        addIpLog('get Total Withdrawals',$totalWithdrawal);
        return round($totalWithdrawal ?? 0);
    }
	
	public function ibcomm_withdrawal_details(Request $request)
    {
		if (request()->has('id') && !empty(request()->id)) {
			$reqid = request()->id;
			$details = IbWithdraw::join('aspnetusers', 'ib1_withdraw.email', '=', 'aspnetusers.email')
				->select(
					'ib1_withdraw.*',
					'aspnetusers.fullname',
					'aspnetusers.number'
				)
				->whereRaw('md5(ib1_withdraw.id) = ?', [$reqid])
				->first();
                 $datalogs = [
            'action'          => 'IB Withdrawal Details Viewed',
            'withdraw_id_md5' => $reqid,
            'withdraw_id'     => $details->id,
            'email'           => $details->email,
            'amount'          => $details->amount ?? null,
            'status'          => 'success',
            'admin_email'     => session('alogin'),
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'timestamp'       => now(),
        ];
                 addIpLog('IB Withdrawal Details', $datalogs);
			return view('admin.ibcomm_withdrawal_details', compact('details'));		
					
		}
	}
	
	public function ibupdateWithdrawal(Request $request){
		if(request()->has('did') && !empty(request()->did)) {
		
			$data = request()->all();
			$transaction_id = $data['transaction_id'];
			$description = $data['description'];
			$status = $data['status'];
			$did = $data['did'];
			$email = $data['email'];
			$amount = $data['amount'];
			$withdraw_amount = $data['usdamount'];
			
			DB::table('ib1_withdraw')
				->whereRaw('md5(id) = ?', [$did])
				->update([
					'AdminRemark' => $description,
					'Status' => $status,
					'Js_Admin_Remark_Date' => now(),
					'transaction_id' => $transaction_id,
					'admin_email' => session('alogin'),
				]);

			if ($status == 1) {
				IbWallet::create([
					'email' => $email,
					'ib_withdraw' => $withdraw_amount,
					'remark' => 'IB Comm. Bank Withdrawal',
				]);
				$msg = 'Bank Withdrawal Approved Successfully';
			} else {
				$msg = 'Bank Withdrawal Rejected Successfully';
			}
            $datalogs = [
        'action'      => 'IB Withdrawal Update',
        'email' => $email,
        'ib_withdraw' => $withdraw_amount,
        'transaction_id' => $transaction_id,
        'status'      => 'invalid_request',
        'admin_email' => session('alogin'),
        'ip_address'  => request()->ip(),
        'user_agent'  => request()->userAgent(),
        'timestamp'   => now(),
    ];
    addIpLog('IB Withdrawal Update', $datalogs);
			return redirect()->back()->with('success', $msg);
		}
		return redirect()->back()->with('error', 'Something went Wrong!!');
	}
}
