<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ib1Commission;
use App\Models\TournamentModel;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Models\Ib1;
use App\Models\User;
use App\Models\EmployeeList;
use App\Models\KycUpdate;
use App\Models\BonusModel;
use App\Models\TournamentModel as Tournament;
use App\Models\UserGroup;
use App\Models\Promotation;
use App\Models\ClientWallets;
use App\Helpers\AccountHelper;
use App\MT5\MTRetCode;
use App\MT5\MTWebAPI;
use App\Services\MT5Service;
class AjaxController extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MTWebAPI $api)
    {
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
    }
    public function __contract()
    {
        if (!session("alogin")) {
            return response(["status" => false, "message" => "Please Login or Refresh the Page"], 401);
        }
    }

    public function index(Request $request)
    {

        if (isset($request->action)) {
            $action = $request->action;
            $id = isset($request->id) ? $request->id : null;
            $type = isset($request->type) ? $request->type : null;
            $tier = isset($request->tier) ? $request->tier : null;
            $search = isset($request->search) ? $request->search : null;
            $requestData = $request->all();
            if (!empty($requestData['startdate']))
                $requestData['startdate'] = $requestData['startdate'] . ' 00:00:00';
            if (!empty($requestData['enddate']))
                $requestData['enddate'] = $requestData['enddate'] . ' 23:59:59';
            switch ($action) {
                case 'getClientList':
                    $this->getClientList($requestData);
                    break;
                case 'getClientDetails':
                    $this->getClientDetails($requestData);
                    break;
                case 'getWalletDeposit':
                    $this->getWalletDeposit($requestData);
                    break;
                case 'getWalletWithdrawal':
                    $this->getWalletWithdrawal($requestData);
                    break;
				case 'getWalletTransfer':
                    $this->getWalletTransfer($requestData);
                    break;
                case 'getTradingDeposit':
                    $this->getTradingDeposit($requestData);
                    break;
                case 'getTradingWithdrawal':
                    $this->getTradingWithdrawal($requestData);
                    break;
                case 'getInternalTransfer':
                    $this->getInternalTransfer($requestData);
                    break;
                     case 'ibWithdrawal':
                    $this->ibWithdrawal($requestData);
                    break;
                case 'getAllTransactions':
                    $this->getAllTransactions($requestData);
                    break;
                case 'getPendingAllTransactions':
                    $this->getPendingAllTransactions($requestData);
                    break;
                case 'getPendingWalletDeposit':
                    $this->getPendingWalletDeposit($requestData);
                    break;
                case 'getPendingWalletWithdrawal':
                    $this->getPendingWalletWithdrawal($requestData);
                    break;
                case 'getPendingTradingDeposit':
                    $this->getPendingTradingDeposit($requestData);
                    break;
                case 'getPendingTradingWithdrawal':
                    $this->getPendingTradingWithdrawal($requestData);
                    break;
                case 'getPendingInternalTransfer':
                    $this->getPendingInternalTransfer($requestData);
                    break;
                case 'getKYCHistory':
                    $this->getKYCHistory($requestData);
                    break;
                case 'getBankDetails':
                    $this->getBankDetails($requestData);
                    break;
                case 'getWalletDetails':
                    $this->getWalletDetails($requestData);
                    break;
                case 'getClientWalletList':
                    $this->getClientWalletList($requestData);
                    break;
                case 'getAdminUsers':
                    $this->getAdminUsers();
                    break;
                case 'getMT5Groups':
                    $this->getMT5Groups($type);
                    break;
                case 'getIbGroups':
                    $this->getIbGroups($type);
                    break;
                case 'getIbPlans':
                    $this->getIbPlans($type);
                    break;
                case 'getMT5Category':
                    $this->getMT5Category($type);
                    break;
                case 'getRoles':
                    $this->getRoles();
                    break;
                case 'getRolePermissions':
                    $this->getRolePermisions();
                    break;
                case 'getAllTickets':
                    $this->getAllTickets();
                    break;
                case 'getOpenTickets':
                    $this->getOpenTickets();
                    break;
                case 'getClosedTickets':
                    $this->getClosedTickets();
                    break;
                case 'getRoleDetails':
                    $this->getRoleDetails($id);
                    break;
                case 'getPaymentGateways':
                    $this->getPaymentGateways();
                    break;
                case 'ibEnroll':
                    $this->ibEnroll();
                    break;
                case 'getLatestDeposit':
                    $this->getLatestDeposit($id);
                    break;
                case 'getLatestWithdrawal':
                    $this->getLatestWithdrawal($id);
                    break;
                case 'getLatestTransfer':
                    $this->getLatestTransfer($id);
                    break;
                case 'getIbUsers':
                    $this->getIbUsers($requestData);
                    break;
                case 'getPendingIbUsers':
                    $this->getPendingIbUsers($requestData);
                    break;
                case 'getAdminDetails':
                    $this->getAdminDetails($id);
                    break;
                case 'getPaymentDetails':
                    $this->getPaymentDetails($id);
                    break;
                case 'updateClientStatus':
                    $this->updateClientStatus($requestData);
                    break;
                case 'getIbList':
                    $this->getIbList($id);
                    break;
                case 'getRMbyGroup':
                    $this->getRMbyGroup($id);
                    break;
                case 'getListOfGroups':
                    $this->getListOfGroups($search);
                    break;
                case 'getListOfUsers':
                    $this->getListOfUsers($search);
                    break;
                case 'getListOfUserGroups':
                    $this->getListOfUserGroups($search);
                    break;
                case 'getListOfIBs':
                    $this->getListOfIBs($search);
                    break;
                case 'requestIB':
                    $this->requestIB($requestData);
                    break;
                case 'updateKYC':
                    $this->updateKYC($requestData);
                    break;
                case 'getBonusList':
                    $this->getBonusList();
                    break;
                case 'getBonusDetails':
                    $this->getBonusDetails($id);
                    break;
				case 'getPromoDetails':
                    $this->getPromoDetails($id);
                    break;
                case 'getTournaments':
                    $this->getTournaments();
                    break;
                case 'getTournamentDetails':
                    $this->getTournamentDetails($id);
                    break;
                case 'getTournamentLiveAccounts':
                    $this->getTournamentLiveAccounts();
                    break;
                case 'getUserGroups';
                    $this->getUserGroups();
                    break;
                case 'getUserGroupDetails':
                    $this->getUserGroupDetails($id);
                    break;
                case 'getSingleFormTransactions':
                    $this->getSingleFormTransactions($requestData);
                    break;
                case 'getAccountOrders':
                    $this->getAccountOrders($id);
                    break;
                case 'getAccountPositions':
                    $this->getAccountPositions($id);
                    break;
                case 'getAllPendingTasks':
                    $this->getAllPendingTasks($requestData);
                    break;
                case 'getLiveAccounts':
                    $this->getLiveAccounts($requestData);
                    break;
                case 'checkNotification':
                    $this->checkNotification($requestData);
                    break;
                case 'getIPLogs':
                    $this->getIPLogs();
                    break;
				
                default:
                    echo json_encode(['error' => 'Invalid function call']);
                    break;
            }
        } else {
            echo json_encode(['error' => 'No functions specified']);
        }
    }

   

    public function getListOfGroups($string)
    {

        $sql = "SELECT account_types.ac_index as id,account_types.ac_group as text from account_types left join mt5_groups on (account_types.ac_type=mt5_groups.mt5_group_id) where account_types.ac_group like '%$string%' and status = 1 and mt5_groups.is_active=1";
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getListOfGroups ', $results);
        echo json_encode($results);
    }
    public function getListOfUsers($string)
    {

        $sql = "SELECT aspnetusers.email as id,concat(aspnetusers.fullname,' [',aspnetusers.email,']') as text from aspnetusers where (aspnetusers.email like '%$string%' OR aspnetusers.fullname like '%$string%') and status = 1";
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getListOfUsers ', $results);
        echo json_encode($results);
    }
    public function getListOfIBs($string)
    {

        $sql = "SELECT aspnetusers.email as id,concat(aspnetusers.fullname,' [',aspnetusers.email,']') as text from aspnetusers
  join ib1 on ib1.email = aspnetusers.email
  where (aspnetusers.email like '%$string%' OR aspnetusers.fullname like '%$string%') and aspnetusers.status = 1 and ib1.status = 1";
        $query = DB::select($sql);
        $results = $query;
        addIpLog('getListOfIBs ', $results);
        echo json_encode($results);
    }

    public function getClientList($requestData)
    {
        $rmCondition = app('permission')->appendRolePermissionsQry('ap', 'email');
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " ap.created_at >= '" . $requestData['startdate'] . "' AND ap.created_at <= '" . $requestData['enddate'] . "' AND ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " ap.created_at <= '" . $requestData['enddate'] . "' AND ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " ap.created_at >= '" . $requestData['startdate'] . "' AND ";
            }
        }
        header('Content-Type: application/json');
        $sql = "SELECT ug.group_name as user_grp,ibs.name as ib_name,c.country_alpha,emp.username as rm_name,rm.rm_id,md5(ap.id) as enc_id,ap.fullname as fullname,ap.*,COALESCE(SUM(tb.deposit_amount), 0) as deposit_amount,COALESCE(SUM(tb.trading_deposited), 0) as trading_deposited,COALESCE(SUM(tb.trading_withdrawal), 0) as trading_withdrawal,COALESCE(SUM(tb.withdraw_amount), 0) as withdraw_amount,ib1.status as ib_status,ib1.acc_type as ib_group from aspnetusers ap
  LEFT JOIN ib1 on ib1.email = ap.email
  LEFT JOIN ib1 as ibs on ibs.email = ap.ib1
  LEFT JOIN relationship_manager rm on(ap.email =rm.user_id)
  LEFT JOIN emplist emp on(rm.rm_id =emp.email)
  LEFT JOIN countries c on(ap.country =c.country_name)
  LEFT JOIN user_groups ug on(ap.group_id =ug.user_group_id)
  LEFT JOIN total_balance tb on (ap.email=tb.email) " . $rmCondition . $dateCondition . " (1=1) group by ap.email";
        $results = DB::select($sql);
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'enc' => md5($row->email),
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'created_at' => $row->created_at,
                'created_date' => date("d-m-Y", strtotime($row->created_at)),
                'created_time' => date('H:s:i', strtotime($row->created_at)),
                'email' => $row->email,
                'phone' => $row->country_code . $row->number,
                'country' => $row->country_alpha,
                'ib' => $row->ib1,
                'ib_name' => $row->ib_name,
                'ib_status' => $row->ib_status,
                'kyc_verify' => $row->kyc_verify,
                'rm_id' => $row->rm_name ?? '',
                'rmid' => $row->rm_id ?? '',
                'ib_group' => $row->ib_group,
                'total_deposit' => $row->trading_deposited + $row->deposit_amount,
                'total_withdraw' => $row->trading_withdrawal + $row->withdraw_amount,
                'status' => $row->status,
                'email_confirmed' => $row->email_confirmed,
                'user_grp' => $row->user_grp,
                'action' => ' <a class="btn btn-sm btn-secondary me-2 edit-user d-none" data-id="' . $row->email . '"><i class="fa fa-edit"></i></a><a class="btn btn-sm btn-primary" href="/admin/client_details?id=' . md5($row->email) . '"><i class="fa fa-eye"></i></a>'
            ];
        }
         addIpLog('getClientList ', $data);
        echo json_encode(['data' => $data]);
    }
    public function getWalletDeposit($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT emplist.username as approved_name,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_deposit trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email) " . $rmCondition . $dateCondition . " order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->deposit_amount ?? 0),
                'payment_mode' => $row->deposit_type,
                'deposit_date' => $row->deposted_date,
                'approved_name' => $row->approved_name,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_deposit_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
         addIpLog('getWalletDeposit ', $data);
        echo json_encode(value: ['data' => $data]);
    }
    public function getWalletWithdrawal($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if ($fieldName == 'deposted_date')
                $fieldName = 'withdraw_date';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT emplist.username as approved_name,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_withdraw trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email)" . $rmCondition . $dateCondition . " order by trs.id desc";
       

        $results = DB::select($sql);
       
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdraw_amount ?? 0),
                'payment_mode' => $row->withdraw_type,
                'withdraw_date' => $row->withdraw_date,
                'approved_name' => $row->approved_name,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_withdrawal_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
        $rmConditionPl = app('permission')->appendRolePermissionsQry('trs', 'initiated_by') . " (1=1) ";
        $sqlPl = "SELECT md5(user.id) as enc_id, user.fullname as fullname, trs.payment_id as id, trs.payment_amount as withdraw_amount, trs.payment_type as withdraw_type, trs.payment_status, trs.initiated_by as email, trs.created_at as withdraw_date
            FROM payment_logs trs
            LEFT JOIN aspnetusers user ON user.email = trs.initiated_by
            " . $rmConditionPl . " AND trs.payment_type = 'BinancePay' AND trs.payment_reference_id = 'Wallet Withdrawal' AND trs.payment_status IN ('Pending', 'Initiated') ";
        $resultsPl = DB::select($sqlPl);
        foreach ($resultsPl as $row) {
            $data[] = [
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdraw_amount ?? 0),
                'payment_mode' => 'Binance Pay',
                'withdraw_date' => $row->withdraw_date ?? '',
                'approved_name' => null,
                'status' => '<span class="badge bg-outline-primary">Pending</span>',
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_withdrawal_details?id=pl_' . md5($row->id) . '">View</a>'
            ];
        }
        usort($data, function ($a, $b) {
            $da = $a['withdraw_date'] ?? '';
            $db = $b['withdraw_date'] ?? '';
            return strcmp($db, $da);
        });
        addIpLog('getWalletWithdrawal ', $data);
        echo json_encode(['data' => $data]);
    }
	
	
	public function getWalletTransfer($requestData)
	{
		$dateCondition = '';

		// Date Filter Condition
		if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
			$fieldName = $requestData['transfer_date']; // transfer_date or created_at or updated_at

			if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
				$dateCondition = " AND trs.$fieldName >= '" . $requestData['startdate'] . "' 
								   AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
			} elseif (!empty($requestData['enddate'])) {
				$dateCondition = " AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
			} elseif (!empty($requestData['startdate'])) {
				$dateCondition = " AND trs.$fieldName >= '" . $requestData['startdate'] . "' ";
			}
		}

		header('Content-Type: application/json');

		// FINAL SQL
		  $sql = "SELECT 
                trs.*,
                md5(trs.id) AS enc_id,
                uf.fullname AS from_fullname,
                uf.username AS from_username,
                ut.fullname AS to_fullname,
                ut.username AS to_username
            FROM wallet_totransfer trs
            LEFT JOIN aspnetusers uf 
                ON uf.email COLLATE utf8mb4_general_ci = trs.wallet_from COLLATE utf8mb4_general_ci
            LEFT JOIN aspnetusers ut 
                ON ut.email COLLATE utf8mb4_general_ci = trs.wallet_to COLLATE utf8mb4_general_ci
            WHERE 1=1
            $dateCondition
            ORDER BY trs.id DESC";

		$results = DB::select($sql);

		$data = [];

		foreach ($results as $row) {
			$data[] = [
				'wallet_from' => $row->wallet_from . "<br><small>(" . ($row->from_fullname ?? 'Unknown') . ")</small>",
				'wallet_to' => $row->wallet_to . "<br><small>(" . ($row->to_fullname ?? 'Unknown') . ")</small>",
				'transfer_amount' => number_format($row->transfer_amount, 2), // numeric for DataTable
				'transfer_date' => $row->transfer_date,
				'transfer_note' => $row->transfer_note,

				'status' => $row->status == 1 
					? '<div class="badge bg-outline-success">Success</div>'
					: ($row->status == 2
						? '<div class="badge bg-outline-danger">Rejected</div>'
						: '<div class="badge bg-outline-primary">Success</div>'
					  )
			];
		}
addIpLog('getWalletTransfer', $data);
		echo json_encode(['data' => $data]);
	}
	
    public function getTradingDeposit($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $condition = "";
        if (isset($_GET['id'])) {
            $condition = ' and trs.trade_id=' . $_GET['id'];
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT emplist.username as approved_name,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_deposit trs left join aspnetusers user on(user.email=trs.email)  left join emplist on (emplist.email=trs.admin_email)" . $rmCondition . $condition . $dateCondition . " group by trs.id order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'TDID' . sprintf("%05d", $row->id),
                'account_no' => $row->trade_id,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->deposit_amount ?? 0),
                'adj_amount' => round($row->adj_amount ?? 0),
                'deposit_type' => $row->deposit_type,
                'deposit_from' => $row->deposit_from,
                'deposit_date' => $row->deposted_date,
                'approved_name' => $row->approved_name,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => '<a href="/admin/trading_deposit_details?id=' . md5($row->id) . '" class="" style="font-size: 13px;padding: 2px 20px;"><i class="fe fe-eye fs-14 text-info"></i></a>'
            ];
        }
        addIpLog('getTradingDeposit', $data);
        echo json_encode(['data' => $data]);
    }
    public function getTradingWithdrawal($requestData)
    {
        $condition = '';
        if (isset($_GET['id'])) {
            $condition = ' and trs.trade_id=' . $_GET['id'];
        }
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if ($fieldName == 'deposted_date')
                $fieldName = 'withdraw_date';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT emplist.username as approved_name,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_withdrawal trs left join aspnetusers user on(user.email=trs.email)  left join emplist on(emplist.email=trs.admin_email)" . $rmCondition . $condition . $dateCondition . " order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'TWID' . sprintf("%05d", $row->id),
                'account_no' => $row->trade_id,
                'enc_id' => $row->enc_id,
                'email' => $row->email,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdrawal_amount ?? 0),
                'withdraw_type' => $row->withdraw_type,
                'withdraw_to' => $row->withdraw_to,
                'withdraw_date' => $row->withdraw_date,
                'approved_name' => $row->approved_name,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => '<a href="/admin/trading_withdrawal_details?id=' . md5($row->id) . '" class="" style="font-size: 13px;padding: 2px 20px;"><i class="fe fe-eye fs-14 text-info"></i></a>'
            ];
        }
        addIpLog('getTradingWithdrawal', $data);
        echo json_encode(['data' => $data]);
    }
    public function getInternalTransfer($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT md5(trs.id) as enc_id,trs.* from trade_deposit trs " . $rmCondition . $dateCondition . " trs.deposit_type = 'Internal Transfer' order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'enc_id' => $row->enc_id,
                'id' => 'ITID' . sprintf("%05d", $row->id),
                'email' => $row->email,
                'amount' => '$' . round($row->deposit_amount ?? 0),
                'transfer_from' => $row->deposit_from,
                'transfer_to' => $row->trade_id,
                'date' => $row->deposted_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/internal_transfer_details">View</a>'
            ];
        }
         addIpLog('getInternalTransfer', $data);
        echo json_encode(['data' => $data]);
    }

     public function ibWithdrawal($requestData){
            $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.$fieldName  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT * FROM ib1_withdraw ORDER BY id DESC";

        
        $query = DB::select($sql);
        $results = $query;
        
        $data = [];
       foreach ($results as $row) {
                $data[] = [
                    'enc_id' => md5($row->id), // use id directly, or encrypt if needed
                    'id' => 'ITID' . sprintf("%05d", $row->id),
                    'email' => $row->email ?? 'N/A',
					'reqamount' => '$' . number_format($row->withdraw_amount ?? 0),
                    'amount' => (($row->withdrawal_currency ?? '') === 'INR'
                        ? '₹' . number_format($row->amount_in_other_currency ?? 0)
                        : '$' . number_format($row->amount_in_other_currency ?? 0)),
                    'transfer_from' => $row->withdraw_type ?? 'N/A',
                    'transfer_to' => $row->withdraw_to ?? 'N/A',
                    'date' => $row->withdraw_date ? date('Y-m-d H:i:s', strtotime($row->withdraw_date)) : 'N/A',
                    'status' => match ($row->Status) {
                        1 => '<div class="badge bg-outline-success">Approved</div>',
                        2 => '<span class="badge bg-outline-danger">Rejected</span>',
                        default => '<span class="badge bg-outline-primary">Pending</span>',
                    },
                    'action' => '<a href="/admin/ibcomm_withdrawal_details?id=' . md5($row->id) . '" class="btn btn-sm btn-primary">View</a>',
                ];
            }
              addIpLog('ibWithdrawal', $data);
        echo json_encode(['data' => $data]);
 
    }
    public function getAllTransactions($requestData)
    {
        $condition = "";
        if (isset($_GET['id'])) {
            $condition = ' and trs.trade_id=' . $_GET['id'];
        }

        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";

        $data = [];
        header('Content-Type: application/json');

        $sql = "SELECT emplist.username as approved_name,'wallet_deposit_details' as link,'Wallet Deposit' as transaction_type,trs.deposit_amount as trs_amount,trs.deposit_type as trs_type,trs.deposted_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_deposit trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email) " . $rmCondition . $dateCondition . " order by trs.id desc";
        $results_wd = DB::select($sql);

        $sql = "SELECT emplist.username as approved_name,'trading_deposit_details' as link,'Trade Deposit' as transaction_type,trs.deposit_amount as trs_amount,trs.deposit_type as trs_type,trs.deposted_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_deposit trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email) " . $rmCondition . $condition . $dateCondition . " group by trs.id order by trs.id desc";
        $results_td = DB::select($sql);

        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if ($fieldName == 'deposted_date')
                $fieldName = 'withdraw_date';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $sql = "SELECT emplist.username as approved_name,'trading_withdrawal_details' as link,'Trade Withdrawal' as transaction_type,trs.withdrawal_amount as trs_amount,trs.withdraw_type as trs_type,trs.withdraw_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_withdrawal trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email)" . $rmCondition . $condition . $dateCondition . " order by trs.id desc";
        $results_tw = DB::select($sql);


        $sql = "SELECT emplist.username as approved_name,'wallet_withdrawal_details' as link,'Wallet Withdrawal' as transaction_type,trs.withdraw_amount as trs_amount,trs.withdraw_type as trs_type,trs.withdraw_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_withdraw trs left join aspnetusers user on(user.email=trs.email) left join emplist on(emplist.email=trs.admin_email) " . $rmCondition . $dateCondition . " order by trs.id desc";
        $results_ww = DB::select($sql);

        $results_all = array_merge($results_wd, $results_ww, $results_td, $results_tw);

        $data = [];
        foreach ($results_all as $row) {
            $data[] = [
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->trs_amount ?? 0),
                'transaction_type' => $row->transaction_type,
                'payment_mode' => $row->trs_type,
                'deposit_date' => $row->trs_date,
                'approved_name' => $row->approved_name,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/' . $row->link . '?id=' . md5($row->id) . '">View</a>'
            ];
        }
         addIpLog('getAllTransactions', $data);
        echo json_encode(value: ['data' => $data]);
    }
    public function getPendingAllTransactions($requestData)
    {
        $condition = "";
        if (isset($_GET['id'])) {
            $condition = ' and trs.trade_id=' . $_GET['id'];
        }
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = 'deposted_date';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";
        header('Content-Type: application/json');

        $sql = "SELECT 'wallet_deposit_details' as link,'Wallet Deposit' as transaction_type,trs.deposit_amount as trs_amount,trs.deposit_type as trs_type,trs.deposted_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_deposit trs left join aspnetusers user on(user.email=trs.email) " . $rmCondition . $dateCondition . " and trs.Status = 0 order by trs.id desc";
        $results_wd = DB::select($sql);

        $sql = "SELECT 'trading_deposit_details' as link,'Trade Deposit' as transaction_type,trs.deposit_amount as trs_amount,trs.deposit_type as trs_type,trs.deposted_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_deposit trs left join aspnetusers user on(user.email=trs.email)  " . $rmCondition . $condition . $dateCondition . " and  trs.Status = 0 group by trs.id order by trs.id desc";
        $results_td = DB::select($sql);

        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            $fieldName = 'withdraw_date';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $sql = "SELECT 'wallet_withdrawal_details' as link,'Wallet Withdrawal' as transaction_type,trs.withdraw_amount as trs_amount,trs.withdraw_type as trs_type,trs.withdraw_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_withdraw trs left join aspnetusers user on(user.email=trs.email) " . $rmCondition . $dateCondition . " and trs.Status = 0 order by trs.id desc";
        $results_ww = DB::select($sql);
        $sql = "SELECT 'trading_withdrawal_details' as link,'Trade Withdrawal' as transaction_type,trs.withdrawal_amount as trs_amount,trs.withdraw_type as trs_type,trs.withdraw_date as trs_date,md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_withdrawal trs left join aspnetusers user on(user.email=trs.email) " . $rmCondition . $condition . $dateCondition . " and trs.Status = 0 order by trs.id desc";
        $results_tw = DB::select($sql);

        $results_all = array_merge($results_wd, $results_ww, $results_td, $results_tw);

        $data = [];
        foreach ($results_all as $row) {
            $data[] = [
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->trs_amount ?? 0),
                'transaction_type' => $row->transaction_type,
                'payment_mode' => $row->trs_type,
                'deposit_date' => $row->trs_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/' . $row->link . '?id=' . md5($row->id) . '">View</a>'
            ];
        }
        addIpLog('getPendingAllTransactions', $data);
        echo json_encode(value: ['data' => $data]);
    }
    public function getPendingWalletDeposit($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date >= '" . $requestData['startdate'] . "' AND trs.deposted_date <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_deposit trs left join aspnetusers user on(user.email=trs.email) " . $rmCondition . $dateCondition . " trs.Status = 0 order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'WDID' . sprintf("%05d", $row->id),

                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->deposit_amount ?? 0),
                'payment_mode' => $row->deposit_type,
                'deposit_date' => $row->deposted_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_deposit_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
           addIpLog('getPendingWalletDeposit', $data);
        echo json_encode(['data' => $data]);
    }
    public function getPendingWalletWithdrawal($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date >= '" . $requestData['startdate'] . "' AND trs.withdraw_date <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT md5(user.id) as enc_id,user.fullname as fullname,trs.* from wallet_withdraw trs left join aspnetusers user on(user.email=trs.email) " . $rmCondition . $dateCondition . " trs.Status = 0 order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'WWID' . sprintf("%05d", $row->id),
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdraw_amount ?? 0),
                'payment_mode' => $row->withdraw_type,
                'withdraw_date' => $row->withdraw_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_withdrawal_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
        $rmConditionPl = app('permission')->appendRolePermissionsQry('trs', 'initiated_by') . " (1=1) ";
        $sqlPl = "SELECT trs.payment_id as id, trs.payment_amount as withdraw_amount, trs.initiated_by as email, trs.created_at as withdraw_date, md5(user.id) as enc_id, user.fullname as fullname
            FROM payment_logs trs
            LEFT JOIN aspnetusers user ON user.email = trs.initiated_by
            " . $rmConditionPl . " AND trs.payment_type = 'BinancePay' AND trs.payment_reference_id = 'Wallet Withdrawal' AND trs.payment_status IN ('Pending', 'Initiated') order by trs.payment_id desc";
        $resultsPl = DB::select($sqlPl);
        foreach ($resultsPl as $row) {
            $data[] = [
                'id' => 'PL' . sprintf("%05d", $row->id),
                'email' => $row->email,
                'enc_id' => $row->enc_id,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdraw_amount ?? 0),
                'payment_mode' => 'Binance Pay',
                'withdraw_date' => $row->withdraw_date ?? '',
                'status' => '<span class="badge bg-outline-primary">Pending</span>',
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/wallet_withdrawal_details?id=pl_' . md5($row->id) . '">View</a>'
            ];
        }
        usort($data, function ($a, $b) {
            $da = $a['withdraw_date'] ?? '';
            $db = $b['withdraw_date'] ?? '';
            return strcmp($db, $da);
        });
         addIpLog('getPendingWalletWithdrawal', $data);
        echo json_encode(['data' => $data]);
    }
    public function getPendingTradingDeposit($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date >= '" . $requestData['startdate'] . "' AND trs.deposted_date <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.deposted_date  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT trs.id as raw_erc,trs.* from trade_deposit trs " . $rmCondition . $dateCondition . " trs.Status = 0 order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'TDID' . sprintf("%05d", $row->id),
                'enc_id' => $row->raw_erc,
                'account_no' => $row->trade_id,
                'amount' => '$' . round($row->deposit_amount ?? 0),
                'adj_amount' => round($row->adj_amount ?? 0),
                'deposit_type' => $row->deposit_type,
                'deposit_from' => $row->deposit_from,
                'deposit_date' => $row->deposted_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/trading_deposit_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
         addIpLog('getPendingTradingDeposit', $data);
        echo json_encode(['data' => $data]);
    }
    public function getPendingTradingWithdrawal($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date >= '" . $requestData['startdate'] . "' AND trs.withdraw_date <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.withdraw_date  >= '" . $requestData['startdate'] . "'  and ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT md5(user.id) as enc_id,user.fullname as fullname,trs.* from trade_withdrawal trs left join aspnetusers user on(user.email=trs.email)  " . $rmCondition . $dateCondition . " trs.Status = 0 order by trs.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'TWID' . sprintf("%05d", $row->id),
                'account_no' => $row->trade_id,
                'enc_id' => $row->enc_id,
                'email' => $row->email,
                'fullname' => $row->fullname,
                'amount' => '$' . round($row->withdrawal_amount ?? 0),
                'withdraw_type' => $row->withdraw_type,
                'withdraw_to' => $row->withdraw_to,
                'withdraw_date' => $row->withdraw_date,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/trading_withdrawal_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
         addIpLog('getPendingTradingWithdrawal', $data);
        echo json_encode(['data' => $data]);
    }
    public function getPendingInternalTransfer($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.created_at >= '" . $requestData['startdate'] . "' AND trs.created_at <= '" . $requestData['enddate'] . "' and";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " trs.created_at  <= '" . $requestData['enddate'] . "' and ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " trs.created_at  >= '" . $requestData['startdate'] . "'  and ";
            }
        }

        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email');
        header('Content-Type: application/json');
        $sql = "SELECT * from internaltransfer trs " . $rmCondition . $dateCondition . " trs.Status = 0 order by trs.itIndex desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => 'ITID' . sprintf("%05d", $row->id),
                'name' => $row->clientName,
                'amount' => '$' . round($row->amount ?? 0),
                'transfer_from' => $row->TransferFromAccountId,
                'transfer_to' => $row->TransferToAccountId,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/internal_transfer_details?id=' . $row->itIndex . '">View</a>'
            ];
        }
         addIpLog('getPendingInternalTransfer', $data);
        echo json_encode(['data' => $data]);
    }

    public function getKYCHistory($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = $requestData['option'];
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " where kyc.$fieldName >= '" . $requestData['startdate'] . "' AND kyc.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " where kyc.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " where kyc.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = " left join aspnetusers user on(user.email=kyc.email) ";
        if (session('userData')['role_id'] == 2) {
            $rmCondition .= " left join relationship_manager rm on (rm.user_id=kyc.email) where rm.rm_id='" . session('alogin') . "' and";
        }
        header('Content-Type: application/json');
        $sql = "SELECT emplist.username as approved_name,emplist.email as approved_email,kyc.id as id,kyc.id as id,max(registered_date_js) as date,max(Admin_Remark_Date) as approved_date,group_concat(kyc.kyc_type) as kyc_type,
  group_concat(DISTINCT concat(kyc.kyc_type,'=',kyc.Status) SEPARATOR '#') as summary,
  kyc.email as email,sum(kyc.Status) as status,aspnetusers.fullname,md5(kyc.email) as enc_id from kyc_update kyc left join aspnetusers on aspnetusers.email = kyc.email left join emplist  on emplist.email = kyc.approved_by " . $rmCondition . $dateCondition . " group by kyc.email order by kyc.id desc";
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getKYCHistory', $results);
        echo json_encode(['data' => $results]);
    }
    public function getBankDetails($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and clientbankdetails.date >= '" . $requestData['startdate'] . "' AND clientbankdetails.date <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and clientbankdetails.date  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and clientbankdetails.date  >= '" . $requestData['startdate'] . "'  ";
            }
        }

        $rmCondition = " left join aspnetusers user on(user.email=clientbankdetails.userId) ";
        if (session('userData')['role_id'] == 2) {
            $rmCondition .= " left join relationship_manager rm on (rm.user_id=clientbankdetails.userId) where rm.rm_id='" . session('alogin') . "'";
        } else {
            $rmCondition .= " where (1)";
        }
        header('Content-Type: application/json');
        $sql = "SELECT clientbankdetails.* from clientbankdetails " . $rmCondition . $dateCondition . " order by clientbankdetails.id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->id,
                'email' => $row->userId,
                'account_name' => $row->ClientName,
                'bank_name' => $row->bankName,
                'account_no' => $row->accountNumber,
                'ifsc' => $row->code,
                'status' => $row->status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/view_bank_details?id=' . md5($row->id) . '">View</a>'
            ];
        }
          addIpLog('getBankDetails', $data);
        echo json_encode(['data' => $data]);
    }
    public function getWalletDetails($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and client_wallets.created_at>= '" . $requestData['startdate'] . "' AND client_wallets.created_at <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and client_wallets.created_at  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and client_wallets.created_at  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = " left join aspnetusers user on(user.email=client_wallets.user_id) ";
        if (session('userData')['role_id'] == 2) {
            $rmCondition .= " left join relationship_manager rm on (rm.user_id=client_wallets.user_id) where rm.rm_id='" . session('alogin') . "'";
        } else {
            $rmCondition .= " where (1)";
        }
        header('Content-Type: application/json');
        $sql = "SELECT client_wallets.* from client_wallets " . $rmCondition . $dateCondition . " order by client_wallets.client_wallet_id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->client_wallet_id,
                'email' => $row->user_id,
                'wallet_name' => $row->wallet_name,
                'currency' => $row->wallet_currency,
                'network' => $row->wallet_network,
                'address' => $row->wallet_address,
                'status' => $row->status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
                'action' => ' <a class="btn btn-sm btn-primary" href="/admin/view_wallet_details?id=' . md5($row->client_wallet_id) . '">View</a>'
            ];
        }
         addIpLog('getWalletDetails', $data);
        echo json_encode(['data' => $data]);
    }

    public function getClientWalletList($requestData)
    {
        $id = $requestData['id'] ?? null;
        if (empty($id)) {
            header('Content-Type: application/json');
            echo json_encode(['data' => []]);
            return;
        }
        $user = DB::table('aspnetusers as ap')
            ->where(DB::raw('md5(ap.id)'), $id)
            ->orWhere(DB::raw('md5(ap.email)'), $id)
            ->select('ap.email')
            ->first();
        if (!$user) {
            header('Content-Type: application/json');
            echo json_encode(['data' => []]);
            return;
        }
        $query = ClientWallets::where('user_id', $user->email);
        if (!empty($requestData['wallet_search'])) {
            $term = $requestData['wallet_search'];
            $query->where(function ($q) use ($term) {
                $q->where('wallet_name', 'like', '%' . $term . '%')
                    ->orWhere('wallet_currency', 'like', '%' . $term . '%')
                    ->orWhere('wallet_network', 'like', '%' . $term . '%')
                    ->orWhere('wallet_address', 'like', '%' . $term . '%');
            });
        }
        $wallets = $query->orderBy('client_wallet_id', 'desc')->get();
        $data = [];
        foreach ($wallets as $row) {
            $data[] = [
                'id' => 'CWA' . sprintf('%04u', $row->client_wallet_id),
                'wallet_name' => $row->wallet_name,
                'wallet_currency' => $row->wallet_currency,
                'wallet_network' => $row->wallet_network,
                'wallet_address' => $row->wallet_address,
            ];
        }
        header('Content-Type: application/json');
         addIpLog('getClientWalletList', $data);
        echo json_encode(['data' => $data]);
    }

    public function getAdminUsers()
    {
        $rmCondition = app('permission')->appendRolePermissionsQry('e', 'email', 'admin') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT e.client_index, md5(e.client_index) as enc_id,e.username, e.email, e.number, e.userRole, e.gender, e.dob, e.address, e.website, e.uid, e.company_name, e.company_address, e.company_number, e.country,e.state, e.city, e.zipcode, COUNT(pages.page_id) as permissions_count, e.status,r.role_name,r.role_id
                FROM emplist e
                LEFT JOIN permissions p ON e.role_id = p.role_id
                LEFT JOIN roles r ON e.role_id = r.role_id
                LEFT JOIN pages ON p.page_id = pages.page_id " . $rmCondition . "
                GROUP BY e.client_index";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->status = $row->status == 1 ? '<span class="badge bg-outline-success">Active</span>' : '<span class="badge bg-outline-danger">Inactive</span>';
            $dat->action = (session('userData')['role_id'] == 1 ? '<a data-id="' . $row->client_index . '" class="btn btn-sm btn-secondary update-user" data-bs-toggle="modal" data-bs-target="#updateUserModal" >Edit</a>' : '');
            $data[] = $dat;
        }
        addIpLog('getAdminUsers', $data);
        echo json_encode(['data' => $data]);
    }

    public function getMT5Category($type = "category")
    {

        header('Content-Type: application/json');
        $sql = "SELECT * from mt5_group_categories where mt5_grp_cat_type = '" . $type . "' order by mt5_grp_cat_id";
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getMT5Category',  $results);
        echo json_encode(['data' => $results]);
    }


    public function getMT5Groups($type = NULL)
    {
        header('Content-Type: application/json');
        if ($type == NULL) {
            $sql = "SELECT * from account_types order by display_priority desc";
        } else {
            $sql = "SELECT * from account_types where md5(ac_category) = '$type' order by display_priority asc";
        }
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->enc_id = md5($row->ac_index);
            $dat->ib_status = $row->ib_enabled == 1 ? '<span class="badge bg-outline-success">Active</span>' : '<span class="badge bg-outline-danger">Inactive</span>';
            $dat->acc_status = $row->status == 1 ? '<span class="badge bg-outline-success">Active</span>' : '<span class="badge bg-outline-danger">Inactive</span>';
            $data[] = $dat;
        }
         addIpLog('getMT5Groups',  $data);
        echo json_encode(['data' => $data]);
    }

    public function getIbGroups($type = NULL)
    {

        header('Content-Type: application/json');
        if ($type == NULL) {
            $sql = "SELECT account_types.*,ib_categories.ib_cat_name as ib_plan from account_types left join ib_categories on ib_categories.ib_cat_id = account_types.acc_ib_cat ";
        } else {
            $sql = "SELECT account_types.*,ib_categories.ib_cat_name as ib_plan from account_types left join ib_categories on ib_categories.ib_cat_id = account_types.acc_ib_cat where md5(acc_ib_cat) = '$type'";
        }
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getIbGroups', $results);
        echo json_encode(['data' => $results]);
    }

    public function getIbPlans($type = NULL)
    {

        header('Content-Type: application/json');
        if ($type == NULL) {
            $sql = "SELECT ib_plans.*,ib_categories.ib_cat_name as ib_plan,account_types.ac_name as ac_group from ib_plans left join account_types on account_types.ac_index = ib_plans.ib_acc_type_id left join ib_categories on ib_categories.ib_cat_id = ib_plans.ib_plan_cat_id where deleted_at is NULL";
        } else {
            $sql = "SELECT ib_plans.*,ib_categories.ib_cat_name as ib_plan,account_types.ac_name as ac_group from ib_plans left join account_types on account_types.ac_index = ib_plans.ib_acc_type_id left join ib_categories on ib_categories.ib_cat_id = ib_plans.ib_plan_cat_id where md5(ib_plan_cat_id) = '$type' and deleted_at is NULL";
        }
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getIbPlans', $results);
        echo json_encode(['data' => $results]);
    }

    public function getRoles()
    {

        header('Content-Type: application/json');
        $sql = "SELECT * from roles";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->status = $row->is_active == 1 ? '<span class="badge bg-outline-success">Active</span>' : '<span class="badge bg-outline-danger">Inactive</span>';
            $dat->action = ' <a data-id="' . $row->role_id . '" class="btn btn-sm btn-secondary me-1 update-role" href="#">Edit</a>' . ($row->is_active == 1 ? '<a class="btn btn-sm btn-danger" href="#" onclick="updateStatus(`' . $row->role_id . '`,0)">Deactivate</a>' : '<a class="btn btn-sm btn-success" href="#" onclick="updateStatus(`' . $row->role_id . '`,1)">Activate</a>');

            $data[] = $dat;
        }
        addIpLog('getRoles', $data);
        echo json_encode(['data' => $data]);
    }
    public function getRolePermisions()
    {


        header('Content-Type: application/json');
        $sql = "SELECT p.id,r.role_name,pg.pagename, p.created_at,p.updated_at from permissions p left join roles r on(p.role_id = r.role_id) left join pages pg on (p.page_id=pg.page_id)";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->action = ' <a class="btn btn-sm btn-danger disabled" href="#">DELETE</a>';
            $data[] = $dat;
        }
         addIpLog('getRolePermisions', $data);
        echo json_encode(['data' => $data]);
    }
    public function getAllTickets()
    {


        header('Content-Type: application/json');
        $sql = "SELECT * FROM  tickets";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->status = $row->Status == 'Open' ? '<span class="badge bg-outline-success">Open</span>' : '<span class="badge bg-outline-danger">Closed</span>';
            $data[] = $dat;
        }
        addIpLog('getAllTickets', $data);
        echo json_encode(['data' => $data]);
    }
    public function getOpenTickets()
    {
        header('Content-Type: application/json');
        $sql = "SELECT * FROM  tickets where Status='Open'";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->status = $row->Status == 'Open' ? '<span class="badge bg-outline-success">Open</span>' : '<span class="badge bg-outline-danger">Closed</span>';
            $data[] = $dat;
        }
         addIpLog('getOpenTickets', $data);
        echo json_encode(['data' => $data]);
    }
    public function getClosedTickets()
    {
        header('Content-Type: application/json');
        $sql = "SELECT * FROM  tickets where Status='Closed'";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->status = $row->Status == 'Open' ? '<span class="badge bg-outline-success">Open</span>' : '<span class="badge bg-outline-danger">Closed</span>';
            $data[] = $dat;
        }
         addIpLog('getClosedTickets', $data);
        echo json_encode(['data' => $data]);
    }
    public function getRoleDetails($id)
    {

        header('Content-Type: application/json');
        $sql = "SELECT * FROM  roles WHERE role_id=" . $id;
        $query = DB::select($sql);
        if (count($query)) {
            $result = $query[0];
            addIpLog('getRoleDetails',$result);
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
    }
    public function getPaymentGateways()
    {
        header('Content-Type: application/json');
        $sql = "SELECT * FROM  available_payment";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $dat = $row;
            $dat->action = '<a data-id="' . $row->id . '"  class="btn btn-sm btn-secondary me-1 update-payment" href="#">Edit</a> <a href="#" onclick="deletePayment(`' . $row->id . '`)" data-id="' . $row->id . '"  class="btn btn-sm btn-danger me-1 delete-payment" href="#">Delete</a>';
            $data[] = $dat;
        }
         addIpLog('getRoleDetails',$data);
        echo json_encode(['data' => $data]);
    }
    public function ibEnroll()
    {

        $uid = uniqid();
        $code = md5(uniqid(rand()));
        $user = $_POST["user"];
        try {
            $data = [
                 'uid' => $uid,
                'email' => $user['email'],
                'name' => $user['fullname'],
                'password' => $user['password'],
                'number' => $user['number'],
                'username' => $user['email'],
                'emailToken' => $code,
                'status' => 0
            ];
            DB::table('ib1')->insert([
                'uid' => $uid,
                'email' => $user['email'],
                'name' => $user['fullname'],
                'password' => $user['password'],
                'number' => $user['number'],
                'username' => $user['email'],
                'emailToken' => $code,
                'status' => 0
            ]);
             addIpLog('ibEnroll',$data);
            echo "true";
        } catch (Exception $e) {
            echo "false";
        }
        exit();
    }
    public function getLatestDeposit($id)
    {

        header('Content-Type: application/json');
        $sql = "SELECT * from trade_deposit where email='" . $id . "' order by id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'created_on' => $row->deposted_date,
                'from_to' => $row->trade_id ?? $row->deposit_from,
                'payment_method' => $row->deposit_type,
                'amount' => '$' . $row->deposit_amount,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
            ];
        }
         addIpLog('getLatestDeposit',$data);
        echo json_encode(['data' => $data]);
    }
    public function getLatestWithdrawal($id)
    {

        header('Content-Type: application/json');
        $sql = "SELECT * from trade_withdrawal where email='" . $id . "'  order by id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'created_on' => $row->withdraw_date,
                'from_to' => $row->withdraw_to,
                'payment_method' => $row->withdraw_type,
                'amount' => '$' . $row->withdrawal_amount,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>')
            ];
        }
        addIpLog('getLatestWithdrawal',$data);
        echo json_encode(['data' => $data]);
    }
    public function getLatestTransfer($id)
    {

        header('Content-Type: application/json');
        $sql = "SELECT * from trade_deposit where deposit_type='Internal Transfer' and email='" . $id . "'  order by id desc";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'created_on' => $row->deposted_date,
                'from' => $row->deposit_from,
                'to' => $row->trade_id,
                'amount' => '$' . $row->deposit_amount,
                'status' => $row->Status == 1 ? '<div class="badge bg-outline-success">Approved</div>' : ($row->Status == 2 ? '<span class="badge bg-outline-danger">Rejected</span>' :
                    '<span class="badge bg-outline-primary">Pending</span>'),
            ];
        }
         addIpLog('getLatestTransfer',$data);
        echo json_encode(['data' => $data]);
    }
    public function getIbUsers($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date >= '" . $requestData['startdate'] . "' AND ib1.reg_date <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('ib1', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT
    ib1.*,
    account_types.ac_name as grp,
    sum(ib_wallet.ib_wallet) as deposit,
    sum(ib_wallet.ib_withdraw) as withdraw
FROM
    ib1
LEFT JOIN
    ib_wallet
ON
    ib1.email = ib_wallet.email
LEFT JOIN account_types on account_types.ac_index = ib1.indexId " . $rmCondition . $dateCondition . "
    group by ib1.email";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->indexId,
                'enc' => md5($row->email),
                'acc_type' => $row->acc_type,
                'grp' => $row->grp,
                'name' => $row->name,
                'email' => $row->email,
                'country' => $row->country,
                'number' => $row->number,
                'date' => $row->reg_date,
                //'total_deposit' => $row->deposit ? '$' . $row->deposit : '$0',
                //'total_withdrawal' => $row->withdraw ? '$' . $row->withdraw : '$0',
				
				'total_deposit' => '$' . round($row->deposit ?? 0),
				'total_withdrawal' => '$' . round($row->withdraw ?? 0),
                'status' => $row->status
            ];
        }
         addIpLog('getIbUsers',$data);
        echo json_encode(['data' => $data]);
    }

    public function getPendingIbUsers($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date >= '" . $requestData['startdate'] . "' AND ib1.reg_date <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and ib1.reg_date  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('ib1', 'email') . " (1=1) ";
        header('Content-Type: application/json');
        $sql = "SELECT
    ib1.*,
    account_types.ac_name as grp,
    sum(ib_wallet.ib_wallet) as deposit,
    sum(ib_wallet.ib_withdraw) as withdraw
FROM
    ib1
LEFT JOIN
    ib_wallet
ON
    ib1.email = ib_wallet.email
LEFT JOIN account_types on account_types.ac_index = ib1.indexId " . $rmCondition . $dateCondition . "
and ib1.status = 0
    group by ib1.email";
        $query = DB::select($sql);
        $results = $query;
        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->indexId,
                'enc' => md5($row->email),
                'acc_type' => $row->acc_type,
                'grp' => $row->grp,
                'name' => $row->name,
                'email' => $row->email,
                'country' => $row->country,
                'number' => $row->number,
                'date' => $row->reg_date,
                'total_deposit' => $row->deposit ? '$' . $row->deposit : '$0',
                'total_withdrawal' => $row->withdraw ? '$' . $row->withdraw : '$0',
                'status' => $row->status
            ];
        }
         addIpLog('getPendingIbUsers',$data);
        echo json_encode(['data' => $data]);
    }



    public function getAdminDetails($id)
    {

        header('Content-Type: application/json');
        // $sql = "SELECT * FROM  emplist WHERE client_index=" . $id;
        // $query = DB::select($sql);
        // $result = $query[0];
        // if (!empty($result)) {
        //     $result->rm_list = $result->rmMappings()->pluck('rm_id');
        // }
        $result = EmployeeList::with('rmMappings')->find($id);
        if ($result) {
            $result->rm_list = $result->rmMappings->pluck('rm_id');
        }
        addIpLog('getAdminDetails',$result);
        echo json_encode($result);
    }
    public function getPaymentDetails($id)
    {

        header('Content-Type: application/json');
        $sql = "SELECT * FROM  available_payment WHERE id=" . $id;
        $query = DB::select($sql);
        $result = $query[0];
         addIpLog('getPaymentDetails',$result);
        echo json_encode($result);
    }
    public function updateClientStatus($data)
    {
        header('Content-Type: application/json');
 
        $email = $data['client_id'];
        $email_confirmed = isset($data['email_confirmed']) && $data['email_confirmed'] === "on" ? 1 : 0;
        $user_status = isset($data['status']) && $data['status'] === "on" ? 1 : 0;
        $kyc_verify = isset($data['kyc_verify']) && $data['kyc_verify'] === "on" ? 1 : 0;
        

        $result = DB::table('aspnetusers')
            ->select('status', 'email', 'email_confirmed', 'kyc_verify','sumsub_verify')
            ->where(DB::raw('md5(email)'), '=', $email)
            ->first();
        if($kyc_verify == 0){
            $sumsub_verify = 0;
        }else{
          $sumsub_verify= 0; 
        }
        try {
            $updated = DB::table('aspnetusers')
                ->where(DB::raw('md5(email)'), '=', $email)
                ->update([
                    'status' => $user_status,
                    'email_confirmed' => $email_confirmed,
                    'kyc_verify' => $kyc_verify,
                     'sumsub_verify' => $sumsub_verify,
                ]);

            if ($updated) {
                $data['email'] = $result->email;
                if ($result->status != $user_status) {
                    $data['field'] = 'status';
                    $data['value'] = $user_status;
                    $this->add_to_user_log($data);
                }
                if ($result->email_confirmed != $email_confirmed) {
                    $data['field'] = 'email_confirmed';
                    $data['value'] = $email_confirmed;
                    $this->add_to_user_log($data);
                }
                if ($result->kyc_verify != $kyc_verify) {
                    $data['field'] = 'kyc_verify';
                    $data['value'] = $kyc_verify;
                    $this->add_to_user_log($data);
                }
                 addIpLog('updateClientStatus',$data);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No rows updated']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function add_to_user_log($data)
    {

        DB::table('aspnetusers_log')->insert([
            'email' => $data['email'],
            'admin_email' => session('alogin'),
            'type' => $data['field'],
            'value' => $data['value']
        ]);
        
    }
    public function getIbList($id)
    {
        $ib_columns = collect(range(1, 15))->map(function ($n) {
            return "ib$n";
        })->toArray();

        $result = DB::table('aspnetusers')
            ->select($ib_columns)
            ->where(DB::raw('md5(email)'), '=', $id)
            ->first();
addIpLog('getIbList',$result);
        echo json_encode((array) $result);
    }

    public function getRMbyGroup($id)
    {

        $results = [];
        // $group_id = DB::table('aspnetusers')
        //   ->where(DB::raw('md5(email)'), '=', $id)
        //   ->value('group_id');
        // if ($group_id !== null) {
        $results = DB::table('emplist as emp')
            ->select('emp.client_index', 'emp.email', 'emp.username')
            ->where('emp.role_id', 13)
            ->get()
            ->toArray();
        // }
        addIpLog('getRMbyGroup',$results);
        echo json_encode($results);
    }

    public function getClientDetails($data)
    {
        $result = DB::table('aspnetusers')
            ->select(
                DB::raw('md5(email) as id'),
                'email',
                'password',
                'fullname',
                'country',
                'number AS telephone',
                'country_code',
                'group_id',
                'password as confirm_password',
                // DB::raw("SUBSTRING(number, 1, LOCATE(')', number)) AS country_code"),
                // DB::raw("REPLACE(SUBSTRING_INDEX(number, ')', -1), ' ', '') AS telephone")
            )
            ->where(DB::raw('md5(email)'), '=', $data['id'])
            ->first();
 addIpLog('getClientDetails',$result);
        echo json_encode((array) $result);
    }

    public function requestIB($request)
    {
        try {
            $clientId = $request['client_id'];
            $ibStatus = $request['ib_status'];
            $ibGroup = $request['ib_group'];
            $result = Ib1::whereRaw('md5(email) = ?', [$clientId])->first();
            if (!$result) {
                $user = User::whereRaw('md5(email) = ?', [$clientId])->first();
                // dd($user);
                if ($user) {
                    $ib1 = new Ib1();
                    $ib1->uid = $user->uid;
                    $ib1->email = $user->email;
                    $ib1->password = $user->password;
                    $ib1->number = $user->number;
                    $ib1->username = $user->email;
                    $ib1->name = $user->fullname;
                    $ib1->country = $user->country;
                    $ib1->emailToken = $user->emailToken;
                    $ib1->status = 1;
                    $ib1->save();
                }
            }
            $updated = Ib1::whereRaw('md5(email) = ?', [$clientId])
                ->update([
                    'status' => $ibStatus,
                    'acc_type' => $ibGroup
                ]);
                 addIpLog('requestIB',$result);
            if ($updated) {
                echo json_encode(['status' => true, 'message' => 'IB details updated successfully']);
            } else {
                echo json_encode(['status' => false, 'message' => 'Failed to update IB details']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
    }
    public function updateKYC($request)
    {
        try {
         
            $did = $request['id'];
            $email = $request['email'];
            $description = $request['description'];
            $status = $request['status'];
            $admin = session('alogin');
            
            $kyc = KycUpdate::findOrFail($did);
            $kyc->Admin_Remark = $description;
            $kyc->Status = $status;
            // $kyc->sumsub_verify = 0;
            $kyc->approved_by = $admin;
            $kyc->save();
            if ($status == 1) {
                $user = User::where('email', $email)->firstOrFail();
                $user->kyc_verify = 1;
                $user->kycdocumentRequest = 2;
                $user->save();
            }

            $user = User::where('email', $email)->firstOrFail();
                $user->kycdocumentRequest = 3;
                $user->save();
                 addIpLog('updateKYC',$kyc);
            echo json_encode(['status' => 'success', 'message' => 'User KYC Updated Successfully']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function getBonusList()
    {
        $bonuses = BonusModel::whereNull('deleted_at')->get();
         addIpLog('getBonusList',$bonuses);
        echo json_encode(['data' => $bonuses]);
    }
    public function getBonusDetails($id)
    {
        $bonus = BonusModel::find($id);

        // echo'<pre>';print_r($bonus);exit;
        if ($bonus) {
            echo json_encode($bonus);
        } else {
            return response()->json(['error' => 'Bonus not found'], 404);
        }
    }
	public function getPromoDetails($id)
    {
        $promos = Promotation::find($id);

        // echo'<pre>';print_r($promos);exit;
        if ($promos) {
            echo json_encode($promos);
        } else {
            return response()->json(['error' => 'Promotions not found'], 404);
        }
    }
    public function getTournaments()
    {
        header('Content-Type: application/json');
        $data = Tournament::all()->map(function ($tournament): TournamentModel {
            $tournament['enc_id'] = md5($tournament->id);
            return $tournament;
        });
         addIpLog('getTournaments',$data);
        echo json_encode(['data' => $data]);
    }
    public function getTournamentDetails($id)
    {
        $tournament = DB::table('tournaments')
            ->select(DB::raw('*, MD5(id) as enc_id'))
            ->whereRaw('MD5(id) = ?', [$id])
            ->first();
        if ($tournament) {
             addIpLog('getTournamentDetails',$tournament);
            echo json_encode($tournament);
        } else {
            return response()->json(['error' => 'Tournament not found'], 404);
        }
    }
    public function getTournamentLiveAccounts()
    {
        header('Content-Type: application/json');
        $data = DB::table('tournaments')
            ->select('asp.fullname', 'acc.ac_group', DB::raw('MD5(tl.trade_id) as enc_acc'), DB::raw('MD5(asp.id) as enc_id'), 'tournaments.name', 'tl.id', 'tl.trade_id', 'tl.leverage', 'tl.balance', 'tl.email')
            ->join('tournament_liveaccount as tl', 'tournaments.id', '=', 'tl.tournament_id')
            ->leftJoin('aspnetusers as asp', 'asp.email', '=', 'tl.email')
            ->leftJoin('account_types as acc', 'tl.account_type', '=', 'acc.ac_index')
            ->get()->toArray();
             addIpLog('getTournamentLiveAccounts',$data);
        echo json_encode(['data' => $data]);
    }
    public function getUserGroups()
    {
        header('Content-Type: application/json');
        // $results = UserGroup::all();
        $sql = "SELECT  ug.*, COUNT(mg.mt5_group_id) AS mt5_group_count FROM user_groups ug LEFT JOIN mt5_groups mg ON ug.user_group_id = mg.user_group_id GROUP BY ug.user_group_id";
        $query = DB::select($sql);
        $results = $query;
          addIpLog('getUserGroups',$results);
        echo json_encode(['data' => $results]);
    }
    public function getUserGroupDetails($id)
    {
        header('Content-Type: application/json');
        $result = UserGroup::find($id);
         addIpLog('getUserGroupDetails',$result);
        echo json_encode($result);
    }
    public function getListOfUserGroups($string)
    {
        $sql = "SELECT user_group_id as id, group_name as text from user_groups where status = 1";
        $query = DB::select($sql);
        $results = $query;
         addIpLog('getListOfUserGroups',$results);
        echo json_encode($results);
    }
    public function getSingleFormTransactions($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = 'created_at';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1)";
        $sql = "
    SELECT
        md5(CAST(REGEXP_REPLACE(trs.transaction_id, '[^0-9]', '') AS UNSIGNED)) as enc_transaction_id,
        trs.*,
        user.fullname,
        MD5(user.email) as enc_id,
        MD5(trs.trade_id) as enc_trade_id,
        MD5(trs.to_account) as enc_to_account
    FROM single_form_transactions as trs
    INNER JOIN aspnetusers as user
        ON trs.email = user.email
" . $dateCondition . $rmCondition;
        $transactions = DB::select($sql);
        addIpLog('getSingleFormTransactions',$transactions);
        echo json_encode(['data' => $transactions]);
    }
    public function getAccountOrders($trade_id)
    {
        $offset = 0;
        $positions = [];
        $data = [];
        $from = 'March 01,2016';
        $to = 'March 31,2080';
        if (($error_code = $this->api->DealGetTotal($trade_id, $from, $to, $total)) != MTRetCode::MT_RET_OK) {
            echo json_encode(value: ['error' => MTRetCode::GetError($error_code)]);
        }
        $offset = 0;
        $positions = [];
        if (($error_code = $this->api->DealGetPage($trade_id, $from, $to, $offset, $total, $orders)) != MTRetCode::MT_RET_OK) {
            echo json_encode(value: ['error' => MTRetCode::GetError($error_code)]);
        }
        $orders = $orders ?? [];
        addIpLog('getAccountOrders',$orders);
        echo json_encode(['data' => $orders]);
    }
    public function getAccountPositions($trade_id)
    {
        $postions = [];
        $offset = 0;
        if (($error_code = $this->api->PositionGetTotal($trade_id, $total)) != MTRetCode::MT_RET_OK) {
            echo json_encode(['error' => MTRetCode::GetError($error_code)]);
        }
        $open_order_history = $total;
        $offset = 0;
        if (($error_code = $this->api->PositionGetPage($trade_id, $offset, $total, $positions)) != MTRetCode::MT_RET_OK) {
            echo json_encode(['error' => MTRetCode::GetError($error_code)]);
        }
        $positions = $positions ?? [];
        addIpLog('getAccountPositions',$positions);
        echo json_encode(['data' => $positions]);
    }
    public function getAllPendingTasks($requestData)
    {
        $dateCondition = '';
        if (!empty($requestData['startdate']) || !empty($requestData['enddate'])) {
            $fieldName = 'created_at';
            if (!empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName >= '" . $requestData['startdate'] . "' AND trs.$fieldName <= '" . $requestData['enddate'] . "' ";
            } elseif (empty($requestData['startdate']) && !empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  <= '" . $requestData['enddate'] . "' ";
            } elseif (!empty($requestData['startdate']) && empty($requestData['enddate'])) {
                $dateCondition = " and trs.$fieldName  >= '" . $requestData['startdate'] . "'  ";
            }
        }
        $rmCondition = app('permission')->appendRolePermissionsQry('trs', 'email') . " (1=1) ";

        $data = [];
        // and  (trs.wd_status=0 OR trs.td_status=0 OR trs.ww_status=0 OR trs.tw_status=0 OR trs.ib_status=0 OR trs.kyc_status=0 OR trs.ticket_status!=4)
        header('Content-Type: application/json');
        $sql = "SELECT  md5(trs.raw_id) as enc_id,md5(trs.email) as enc_email,user.fullname,trs.*,trs.raw_type as rawtype,(CASE
                WHEN trs.transaction_type = 'ib_request' THEN ic.ib_cat_name
                ELSE trs.raw_type
            END) as raw_type from transactions_all as trs left join aspnetusers user on(user.email=trs.email) left join ib_categories ic on(trs.raw_type=ic.ib_cat_id)" . $rmCondition . $dateCondition . " and  (trs.wd_status=0 OR trs.td_status=0 OR trs.ww_status=0 OR trs.tw_status=0 OR trs.ib_status=0 OR trs.kyc_status=0 OR trs.ticket_status!=4) order by trs.created_at desc";
        $result = DB::select($sql);
         addIpLog('getAllPendingTasks',$result);
        echo json_encode(['data' => $result]);
    }
    public function getLiveAccounts($request)
    {
        $result = [];
        $term = $request['term'] ?? '';
        $result = DB::table('liveaccount')
            ->where('name', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->orWhere('trade_id', 'LIKE', "%{$term}%")
            ->get();
             addIpLog('getLiveAccounts',$result);
        echo json_encode($result);
    }
    public function checkNotification($request)
    {
        $count = DB::table('notifications')->where('pusher_id', $request['id'])->count();
        if ($count == 0) {
            DB::table('notifications')->insert([
                'pusher_id' => $request['id'],
            ]);
            echo "0";
            exit;
        }
        echo "1";
        exit;
    }

    function getIPLogs()
    {
        $result = DB::table('login_history')
            ->leftJoin('aspnetusers as user', 'login_history.email', '=', 'user.email')
             ->leftJoin('emplist as emp', 'login_history.email', '=', 'emp.email')
              ->select(
        DB::raw('COALESCE(user.fullname, emp.username) as display_name'),
        'login_history.*'
    )
            ->get();
        echo json_encode(['data' => $result]);
    }

        function getIPLogsview()
    {
        $result = DB::table('login_history')
            ->leftJoin('aspnetusers as user', 'login_history.email', '=', 'user.email')
             ->leftJoin('emplist as emp', 'login_history.email', '=', 'emp.email')
              ->select(
        DB::raw('COALESCE(user.fullname, emp.username) as display_name'),
        'login_history.*'
    )
            ->get();
        echo json_encode(['data' => $result]);
    } 

}
