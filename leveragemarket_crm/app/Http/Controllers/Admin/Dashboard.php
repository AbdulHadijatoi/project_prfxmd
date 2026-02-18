<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradeDeposits;
use App\Models\TradeWithdrawals;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\User;
use App\Models\LiveAccount;
use App\Models\Ib1;
use Illuminate\Support\Facades\DB;


class Dashboard extends Controller
{
    public function index()
    {
        // $rmCondition = '';
        // if (session('userData')['role_id'] != 1) {
        //     $rmCondition .= " left join aspnetusers user on(user.email=trs.email) ";
        // } else {
        //     $rmCondition .= " where (1) and ";
        // }
        // if (session('userData')['role_id'] == 2) {
        //     $rmCondition .= "  left join relationship_manager rm on(rm.user_id=trs.email) where rm.rm_id='" . session('alogin') . "' and ";
        // }

        // $userCondition = " ";
        // if (session('userData')['role_id'] != 1) {
        //     if (session('userData')['role_id'] == 2) {
        //         $userCondition = "  left join relationship_manager rm on(rm.user_id=asp.email) where rm.rm_id='" . session('alogin') . "'";
        //     }
        // }

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        $userCondition = app('permission')->appendRolePermissionsQry('asp', 'email') . " (1=1)";

        $sql = "SELECT COALESCE(SUM(trs.deposit_amount), 0) AS deposit FROM trade_deposit trs" . $rmCondition . " trs.Status = 1 AND trs.deposit_type NOT LIKE '%Internal Transfer%' AND trs.deposit_type NOT LIKE '%CRM%' ";
        $trade_deposit = DB::select($sql)[0];
		
		$sql = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.status=1 and trs.withdraw_type NOT LIKE '%Internal Transfer%' AND trs.withdraw_type NOT LIKE '%CRM%'";
        $trade_withdrawal = DB::select($sql)[0];
		
		$sql_interdr = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.Status=1 and trs.deposit_type LIKE '%Internal Transfer%'";
        $internal_deposit = DB::select($sql_interdr)[0];
		
		$sql_intercrm = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.Status=1 and trs.deposit_type LIKE '%CRM%'";
        $crm_deposit = DB::select($sql_intercrm)[0];
		
		$sql_interdep = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.Status=1 and trs.withdraw_type LIKE '%Internal Transfer%'";
        $internal_withdrawal = DB::select($sql_interdep)[0];
		
		$sql_withcrm = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.Status=1 and trs.withdraw_type LIKE '%CRM%'";
        $crm_withdrawal = DB::select($sql_withcrm)[0];
		
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs " . $rmCondition . " trs.Status=1";
        $wallet_deposit = DB::select($sql)[0];

        $sql = "select COALESCE(SUM(trs.withdraw_amount), 0) as withdraw from wallet_withdraw  trs" . $rmCondition . " trs.Status=1";
        $wallet_withdrawal = DB::select($sql)[0];


        /*Daily Deposits & Withdraw******************************/
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.status=1 and trs.deposit_type NOT LIKE '%Internal Transfer%' and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $trade_deposit_daily = DB::select($sql)[0];
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs " . $rmCondition . " trs. status=1 and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $wallet_deposit_daily = DB::select($sql)[0];
		$sql_inter = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.status=1 and trs.deposit_type LIKE '%Internal Transfer%' and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $internal_deposit_daily = DB::select($sql_inter)[0];
		
        $sql = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.status=1 and trs.withdraw_type NOT LIKE '%Internal Transfer%' and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $trade_withdrawal_daily= DB::select($sql)[0];
        $sql = "select COALESCE(SUM(trs.withdraw_amount), 0) as withdraw from wallet_withdraw  trs" . $rmCondition . " trs.status=1 and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $wallet_withdrawal_daily = DB::select($sql)[0];
		$sql_interdaily = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.status=1 and trs.withdraw_type LIKE '%Internal Transfer%' and date(trs.Js_Admin_Remark_Date)='".date("Y-m-d")."'";
        $internal_withdrawal_daily = DB::select($sql_interdaily)[0];
        /*Daily Deposits & Withdraw******************************/


        /*Weekly Deposits & Withdraw******************************/
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.status=1 and trs.deposit_type NOT LIKE '%Internal Transfer%'  AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0) ";
        $trade_deposit_weekly = DB::select($sql)[0];
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs " . $rmCondition . " trs. status=1 AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0) ";
        $wallet_deposit_weekly = DB::select($sql)[0];
		$sql_interweek = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs" . $rmCondition . " trs.status=1 and trs.deposit_type LIKE '%Internal Transfer%'  AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0) ";
        $internal_deposit_weekly = DB::select($sql_interweek)[0];
		
        $sql = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.status=1 and trs.withdraw_type NOT LIKE '%Internal Transfer%' AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0)";
        $trade_withdrawal_weekly= DB::select($sql)[0];
        $sql = "select COALESCE(SUM(trs.withdraw_amount), 0) as withdraw from wallet_withdraw  trs" . $rmCondition . " trs.status=1  AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0)";
        $wallet_withdrawal_weekly = DB::select($sql)[0];
		$sql_withweek = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs" . $rmCondition . " trs.status=1 and trs.withdraw_type LIKE '%Internal Transfer%' AND YEARWEEK(trs.Js_Admin_Remark_Date, 0) = YEARWEEK(CURDATE(), 0)";
        $internal_withdrawal_weekly= DB::select($sql_withweek)[0];
        /*Weekly Deposits & Withdraw******************************/



        $sql = "SELECT count(*) as counts from wallet_deposits trs " . $rmCondition . " trs.Status = 0";
        $pending_wd = DB::select($sql)[0];

        $sql = "SELECT count(*) as counts from trade_deposit trs " . $rmCondition . " trs.Status = 0";
        $pending_td = DB::select($sql)[0];

        $sql = "SELECT count(*) as counts from trade_withdrawal trs " . $rmCondition . " trs.Status = 0";
        $pending_tw = DB::select($sql)[0];

        $sql = "SELECT count(*) as counts from wallet_withdraw  trs " . $rmCondition . " trs.Status = 0";
        $pending_ww = DB::select($sql)[0];

        $sql = "SELECT count(*) as counts from ib1 trs " . $rmCondition . " trs.status = 0";
        $pending_ib = DB::select($sql)[0];


        $sql = "SELECT count(*) as counts from aspnetusers trs " . $rmCondition . " trs.wallet_enabled = 1";
        $wallet_users = DB::select($sql)[0];

        $sql = "SELECT
            SUM(CASE WHEN asp.status = 0 THEN 1 ELSE 0 END) AS inactive_users,
            SUM(CASE WHEN asp.status = 1 THEN 1 ELSE 0 END) AS active_users
        FROM aspnetusers asp" . $userCondition;
        $total_clients = DB::select($sql)[0];


        $eid = session('alogin');
        $sql = "SELECT trs.* from wallet_deposits trs " . $rmCondition . " (trs.status=0) order by trs.raw_id desc limit 10";
        // echo "<!-- ".$sql." --->";
        $results = DB::select($sql);

        $sql = "SELECT trs.* from wallet_withdraws trs " . $rmCondition . " (trs.status=0) order by trs.raw_id desc limit 10";
        $wallet_withdraws = DB::select($sql);
		
		/*Balance/Equity/Profile and loss*/
		$tradeAccountpl = DB::table('aspnetusers as user')
			->leftJoin('liveaccount as liacc', 'user.email', '=', 'liacc.email')
			->where('liacc.status', 'active')
			->selectRaw('
				COALESCE(SUM(liacc.Balance), 0) AS total_balance,
				COALESCE(SUM(liacc.equity), 0) AS total_equity,
				COALESCE(SUM(GREATEST(COALESCE(liacc.equity,0) - COALESCE(liacc.Balance,0), 0)), 0) AS total_profit,
				COALESCE(SUM(GREATEST(COALESCE(liacc.Balance,0) - COALESCE(liacc.equity,0), 0)), 0) AS total_loss
			')
			->get();
			
		/*Account types count*/
		$dataacctype = DB::table('account_types as at')
			->leftJoin('liveaccount as la', 'la.account_type', '=', 'at.ac_index')
			->select(
				'at.ac_name as type',
				DB::raw("COUNT(CASE WHEN at.ac_index BETWEEN 1 AND 5 THEN la.id END) as funded"),
				DB::raw("COUNT(CASE WHEN at.ac_index BETWEEN 6 AND 10 THEN la.id END) as demo")
			)
			->groupBy('at.ac_name')
			->get();
		
		/*Range Amounts*/
		$dataaccountrange = DB::table('liveaccount')
			->selectRaw("
				SUM(CASE WHEN Balance BETWEEN 0 AND 1 THEN 1 ELSE 0 END) as zero_accounts,
				SUM(CASE WHEN Balance BETWEEN 1 AND 100 THEN 1 ELSE 0 END) as range_1_100,
				SUM(CASE WHEN Balance BETWEEN 101 AND 500 THEN 1 ELSE 0 END) as range_101_500,
				SUM(CASE WHEN Balance BETWEEN 501 AND 1000 THEN 1 ELSE 0 END) as range_501_1000,
				SUM(CASE WHEN Balance BETWEEN 1001 AND 10000 THEN 1 ELSE 0 END) as range_1001_10000,
				SUM(CASE WHEN Balance BETWEEN 10001 AND 25000 THEN 1 ELSE 0 END) as range_10001_25000,
				SUM(CASE WHEN Balance > 25000 THEN 1 ELSE 0 END) as range_25001_plus,
				COUNT(*) as total_accounts
			")
			->first();

        return view('admin.dashboard', [
            'trade_deposit' => $trade_deposit,
            'internal_deposit' => $internal_deposit,
            'crm_deposit' => $crm_deposit,
            'trade_withdrawal' => $trade_withdrawal,
            'wallet_deposit' => $wallet_deposit,
            'wallet_withdrawal' => $wallet_withdrawal,
            'internal_withdrawal' => $internal_withdrawal,
			'crm_withdrawal' => $crm_withdrawal,
            'trade_deposit_daily' => $trade_deposit_daily,
            'trade_withdrawal_daily' => $trade_withdrawal_daily,
            'internal_deposit_daily' => $internal_deposit_daily,
            'wallet_deposit_daily' => $wallet_deposit_daily,
            'wallet_withdrawal_daily' => $wallet_withdrawal_daily,
            'internal_withdrawal_daily' => $internal_withdrawal_daily,
            'trade_deposit_weekly' => $trade_deposit_weekly,
            'trade_withdrawal_weekly' => $trade_withdrawal_weekly,
            'internal_deposit_weekly' => $internal_deposit_weekly,
            'wallet_deposit_weekly' => $wallet_deposit_weekly,
            'wallet_withdrawal_weekly' => $wallet_withdrawal_weekly,
            'internal_withdrawal_weekly' => $internal_withdrawal_weekly,
            'pending_wd' => $pending_wd,
            'pending_td' => $pending_td,
            'pending_tw' => $pending_tw,
            'pending_ww' => $pending_ww,
            'pending_ib' => $pending_ib,
            'wallet_users' => $wallet_users,
            'total_clients' => $total_clients,
            'rmCondition' => $rmCondition,
            'results' => $results,
            'wallet_withdraws' => $wallet_withdraws,
			'tradeAccountpl' => $tradeAccountpl[0],
			'dataacctype' => $dataacctype,
			'dataaccountrange' => $dataaccountrange
        ]);

    }
}
