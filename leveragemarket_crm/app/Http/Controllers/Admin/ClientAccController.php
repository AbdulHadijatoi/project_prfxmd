<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ClientAccController extends Controller
{
    public function live_accounts()
    {
        $requestData=$_GET;
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and liveaccount.Registered_Date >= '" . $requestData['startdate'] . " 00:00:00' AND liveaccount.Registered_Date <= '" . $requestData['enddate'] . " 23:59:59'  ";
            }
            elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and liveaccount.Registered_Date <= '" . $requestData['enddate'] . " 23:59:59' ";
            }
            elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and liveaccount.Registered_Date >= '" . $requestData['startdate'] . " 00:00:00' ";
            }
        }
		$statusCondition = " AND liveaccount.status = 'active' ";
        $roleId = session('userData')['role_id'];
        $alogin = session('alogin');
        $userGroups = explode(',', session('user_groups'));
        // Select the fields and execute the query
        $rmCondition = app('permission')->appendRolePermissionsQry('liveaccount', 'email') . " (1=1) ";
		//echo $rmCondition;
		//exit;
        $query = "select liveaccount.*,md5(aspnetusers.id) as enc_id,account_types.ac_group,sum(ib1_commission.volume) as total_lots from liveaccount
        left join ib1_commission on ib1_commission.login = liveaccount.trade_id
        left join aspnetusers on aspnetusers.email = liveaccount.email
join account_types on account_types.ac_index = liveaccount.account_type " . $rmCondition . $dateCondition. $statusCondition. "   group by liveaccount.trade_id order by id desc;
";
//print_r($query); 
//exit;
 addIpLog('admin Live Account', $roleId);
        $accounts = DB::select($query);
        return view('admin.client_accounts.live_accounts', compact("accounts"));
    }
    public function demo_accounts()
    {
        // Get session data
        $requestData=$_GET;
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and demoaccount.Registered_Date >= '" . $requestData['startdate'] . "' AND demoaccount.Registered_Date <= '" . $requestData['enddate'] . "'  ";
            }
            elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and demoaccount.Registered_Date <= '" . $requestData['enddate'] . "' ";
            }
            elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and demoaccount.Registered_Date >= '" . $requestData['startdate'] . "' ";
            }
        }
		$statusCondition = " AND demoaccount.status = 'active' "; 
        $email = session('alogin');
        $roleId = session('userData')['role_id'];
        $rmCondition = app('permission')->appendRolePermissionsQry('demoaccount', 'email') . " (1=1) ";
        $query = "select md5(aspnetusers.id) as enc_id,demoaccount.*,aspnetusers.fullname as name from demoaccount join aspnetusers on aspnetusers.email = demoaccount.email " . $rmCondition .$dateCondition. $statusCondition. "  order by id desc";
        $accounts = DB::select($query);
         addIpLog('admin demo Account', $roleId);
        return view('admin.client_accounts.demo_accounts', compact("accounts"));
    }
}
