<?php

namespace App\Http\Controllers\Admin;
use App\Helpers\AccountHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TournamentModel as Tournament;
use DB;
use App\MT5\MTWebAPI;
use App\Services\MT5Service;

class Tournaments extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MTWebAPI $api)
    {
        // $this->mt5Service = $mt5Service;
        // $this->mt5Service->connect();
        // $this->api = $this->mt5Service->getApi();
    }
    public function index()
    {
        $acc_priority = DB::table('account_types')
            ->whereNotNull('display_priority')
            ->where('inquiry_status',2)
            ->get();
        return view('admin.tournaments', compact('acc_priority'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'entry_fee' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'email_description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'shows_on' => 'required|string',
            'shows_list' => 'nullable|array',
            'status' => 'nullable|boolean',
            'send_notification' => 'nullable|boolean',
            'image' => 'required|mimes:jpeg,png|max:10240',
            'account_type' => 'required|numeric',
            'leverage' => 'required'
        ]);
        $path = '';
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tournaments', $fileName, 'public');
        }

        $datalogs = [
            'name' => $validated['name'],
            'date' => $validated['date'],
            'entry_fee' => $validated['entry_fee'],
            'description' => $validated['desc'] ?? null,
            'email_description' => $validated['email_description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'shows_on' => $validated['shows_on'],
            'shows_list' => isset($validated['shows_list']) ? implode(',', $validated['shows_list']) : null,
            'status' => $validated['status'] ?? false,
            'send_notification' => $validated['send_notification'] ?? false,
            'created_by' => session('alogin'),
            'image' => $path,
            'account_type' => $validated['account_type'],
            'leverage' => $validated['leverage']
        ];
        Tournament::create([
            'name' => $validated['name'],
            'date' => $validated['date'],
            'entry_fee' => $validated['entry_fee'],
            'description' => $validated['desc'] ?? null,
            'email_description' => $validated['email_description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'shows_on' => $validated['shows_on'],
            'shows_list' => isset($validated['shows_list']) ? implode(',', $validated['shows_list']) : null,
            'status' => $validated['status'] ?? false,
            'send_notification' => $validated['send_notification'] ?? false,
            'created_by' => session('alogin'),
            'image' => $path,
            'account_type' => $validated['account_type'],
            'leverage' => $validated['leverage']
        ]);

        addIpLog('Tournaments Saved', $datalogs);
        return redirect()->back()->with('success', 'Tournament created successfully!');
    }

    public function update(Request $request)
    {
        $md5_id = $request->input('enc_id');
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'entry_fee' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'email_description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'shows_on' => 'required|string',
            'shows_list' => 'nullable|array',
            'status' => 'nullable|boolean',
            'send_notification' => 'nullable|boolean',
            'image' => 'mimes:jpeg,png|max:10240',
            'account_type' => 'required',
            'leverage' => 'required|numeric'
        ]);
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('tournaments', $fileName, 'public');
        }
        $tournament = Tournament::whereRaw('MD5(id) = ?', [$md5_id])->first();
        $datalogs = [
            'name' => $validated['name'],
            'date' => $validated['date'],
            'entry_fee' => $validated['entry_fee'],
            'description' => $validated['desc'] ?? null,
            'email_description' => $validated['email_description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'shows_on' => $validated['shows_on'],
            'shows_list' => isset($validated['shows_list']) ? implode(',', $validated['shows_list']) : null,
            'status' => $validated['status'] ?? false,
            'send_notification' => $validated['send_notification'] ?? false,
            'updated_by' => session('alogin'),
            'account_type' => $validated['account_type'],
            'leverage' => $validated['leverage'],
        ];
        $updateData = [
            'name' => $validated['name'],
            'date' => $validated['date'],
            'entry_fee' => $validated['entry_fee'],
            'description' => $validated['desc'] ?? null,
            'email_description' => $validated['email_description'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'shows_on' => $validated['shows_on'],
            'shows_list' => isset($validated['shows_list']) ? implode(',', $validated['shows_list']) : null,
            'status' => $validated['status'] ?? false,
            'send_notification' => $validated['send_notification'] ?? false,
            'updated_by' => session('alogin'),
            'account_type' => $validated['account_type'],
            'leverage' => $validated['leverage'],
        ];

        if (isset($path)) {
            $updateData['image'] = $path;
        }

        $tournament->update($updateData);
         addIpLog('Tournaments Update', $datalogs);
        return redirect()->back()->with('success', 'Tournament updated successfully!');
    }
    public function liveaccounts(Request $request)
    {
        return view('admin.tournament_liveaccounts');
    }

    public function account_details(Request $request)
    {
        $trade_id = $request->query('id');
        AccountHelper::updateLiveAndDemoAccounts($trade_id,'tournament_liveaccount');
        $type = "live";
        $sql = "select tournament_liveaccount.*,aspnetusers.fullname,account_types.ac_group from tournament_liveaccount
left join account_types on account_types.ac_index = tournament_liveaccount.account_type
left join aspnetusers on aspnetusers.email = tournament_liveaccount.email where md5(tournament_liveaccount.trade_id)='" . $trade_id . "'";
        $query = DB::select($sql);
        $getUser = isset($query[0]) ? $query[0] : [];
        if (!$getUser) {
            alert()->error("Account does not exist or has been deleted. Please try again.");
            return redirect("/admin/dashboard");
        }
        $total_deposit = DB::table('trade_deposit')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', 1)
            ->sum('deposit_amount');
        $unapproved_deposit = DB::table('trade_deposit')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', '!=', 1)
            ->sum('deposit_amount');
        $total_withdrawal = DB::table('trade_withdrawal')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', 1)
            ->sum('withdrawal_amount');
        $unapproved_withdrawal = DB::table('trade_withdrawal')
            ->where(DB::raw('MD5(trade_id)'), $trade_id)
            ->where('status', '!=', 1)
            ->sum('withdrawal_amount');
        $bonus_trans = [];
        $account_types = DB::table('account_types')->where('status', 1)->get();
        $account = AccountHelper::getAccount($trade_id,'tournament_liveaccount');
        // $account=[];
        return view("admin.tournament_account_details", [
            "id" => $trade_id,
            "getUser" => $getUser,
            "account" => $account,
            'total_deposit' => $total_deposit,
            'unapprove_deposit' => $unapproved_deposit,
            'total_withdrawl' => $total_withdrawal,
            'unapprove_withdrawl' => $unapproved_withdrawal,
            'bonus_trans' => $bonus_trans,
            'account_types' => $account_types,
            'type' => $type,
            'title' => 'MT5 Account Details'
        ]);
    }
}
