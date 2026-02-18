<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Exception;

class IBController extends Controller
{

    public function index(Request $request)
    {
        $userRoleId = $request->session()->get('userData.role_id');
        $alogin = $request->session()->get('alogin');
        $rmCondition = app('permission')->appendRolePermissionsQry('ib', 'email')." (1=1)";

        $query = DB::table('ib1 as ib')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');
        if ($userRoleId == 2) {
            $query->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }
        $query=$query->toSql().$rmCondition;
        $totalIb = count(DB::select($query));


        $query = DB::table('ib1 as ib')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');
        if ($userRoleId == 2) {
            $query->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }
        $query=$query->toSql().$rmCondition ." and ib.status=1";
        $activeIbCount = count(DB::select($query));


        $totalClients = DB::table('ib1 as ib')
            ->join('aspnetusers as t2', 'ib.email', '=', 't2.ib1')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');

        if ($userRoleId == 2) {
            $totalClients->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }
        $totalClients=$totalClients->toSql().$rmCondition;
        $totalClientsCount = count(DB::select($totalClients));


        $pendingKyc = DB::table('ib1 as ib')
            ->join('kyc_update as kyc', 'ib.email', '=', 'kyc.email')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');

        if ($userRoleId == 2) {
            $pendingKyc->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }

        $pendingKyc=$pendingKyc->toSql().$rmCondition."  and kyc.Status=0";;
        $pendingKycCount = count(DB::select($pendingKyc));


        $ibInternal = DB::table('ib_internal as ib')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');

        if ($userRoleId == 2) {
            $ibInternal->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }

        $ibInternal=$ibInternal->toSql().$rmCondition;
        $ibInternalCount = count(DB::select($ibInternal));

        $ibPendingTrans = DB::table('ib_internal as ib')
            ->leftJoin('aspnetusers as user', 'user.email', '=', 'ib.email');

        if ($userRoleId == 2) {
            $ibPendingTrans->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }
        $ibPendingTrans=$ibPendingTrans->toSql().$rmCondition;
        $ibPendingTrans=DB::select($ibPendingTrans);


        $qry = DB::table('ib1 as ib')
            ->join('kyc_update as kyc', 'ib.email', '=', 'kyc.email');
        if ($userRoleId == 2) {
            $qry->leftJoin('relationship_manager as rm', 'ib.email', '=', 'rm.user_id')
                ->where('rm.rm_id', $alogin);
        }
        $qry=$qry->toSql().$rmCondition."  and kyc.Status=0";
        $kycpending=DB::select($qry);
        $datalogs = [
    'action'           => 'IB Dashboard Viewed',
    'role_id'          => $userRoleId,
    'admin_id'         => $alogin,
    'total_ib'         => $totalIb,
    'active_ib'        => $activeIbCount,
    'total_clients'    => $totalClientsCount,
    'pending_kyc'      => $pendingKycCount,
    'ib_internal'      => $ibInternalCount,
    'pending_trans_ib' => count($ibPendingTrans),
    'ip_address'       => $request->ip(),
    'user_agent'       => $request->userAgent(),
    'timestamp'        => now(),
];

addIpLog('IB Dashboard Viewed', $datalogs);
 
        return view('admin.ib.ibdashboard', [
            'total_ib' => $totalIb,
            'active_ib' => $activeIbCount,
            'total_clients' => $totalClientsCount,
            'pending_kyc' => $pendingKycCount,
            'ib_internal' => $ibInternalCount,
            'ibPendingTrans' => $ibPendingTrans,
            'kycpending' => $kycpending
        ]);
    }

    public function list()
    {
        $accGroups = DB::table('ib_plan_details')
            ->select('ib_categories.ib_cat_name', 'ib_plan_details.ib_plan_id')
            ->leftJoin('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->where('ib_plan_details.status', 1)
            ->groupBy('ib_plan_details.ib_plan_id')
            ->get(); // Use get() to retrieve results
        // dd($accGroups);
        return view("admin.ib.iblist", ["acc_groups" => $accGroups]);
    }
    public function list_active()
    {
        $accGroups = DB::table('ib_plan_details')
            ->select('ib_categories.ib_cat_name', 'ib_plan_details.ib_plan_id')
            ->leftJoin('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->where('ib_plan_details.status', 1)
            ->groupBy('ib_plan_details.ib_plan_id')
            ->get(); // Use get() to r
          $datalogs = [
        'action'        => 'Active IB List Plans Viewed',
        'total_plans'   => $accGroups->count(),
        'plan_ids'      => $accGroups->pluck('ib_plan_id')->toArray(),
        'viewed_by'     => session('alogin'),
        'role_id'       => session('userData.role_id') ?? null,
        'ip_address'    => request()->ip(),
        'user_agent'    => request()->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('View Active IB Plans in Admin', $datalogs);
        return view("admin.ib.iblist_active", ["acc_groups" => $accGroups]);

    }

    public function ib_settings()
    {
        // Get activeType if it's set
        $activeType = request()->get('activeType');

        // Step 1: Query for categories with counts of distinct account types
        $results = DB::table('ib_categories')
            ->select('ib_categories.*', DB::raw('count(DISTINCT ib_plan_details.acc_type) as count'))
            ->leftJoin('ib_plan_details', function ($join) {
                $join->on('ib_plan_details.ib_plan_id', '=', 'ib_categories.ib_cat_id')
                    ->whereNull('ib_plan_details.deleted_at');
            })
            ->groupBy('ib_categories.ib_cat_id')
            ->orderBy('ib_categories.ib_cat_id')
            ->get();

        // Step 2: Query for ib_plan_details with account types and category names
        $plans = DB::table('ib_plan_details')
            ->select(
                'ib_plan_details.*',
                'account_types.ac_group',
                'ib_categories.ib_cat_name',
                DB::raw('count(*) as count')
            )
            ->join('account_types', 'account_types.ac_index', '=', 'ib_plan_details.acc_type')
            ->join('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->whereNull('ib_plan_details.deleted_at');

        // Apply the filter if `activeType` is not null
        if ($activeType !== null) {
            $plans->where(DB::raw('md5(ib_categories.ib_cat_id)'), '=', $activeType);
        }

        // Group and execute the query for plans
        $plans = $plans
            ->groupBy('ib_plan_details.ib_plan_id', 'ib_plan_details.acc_type')
            ->get();

        // Step 3: Query for all account types
        $groups = DB::table('account_types')->get();

        // Combine all results into a single array if you want to return or process them together
        $data = [
            'results' => $results,
            'plans' => $plans,
            'groups' => $groups,
            'activeType' => $activeType
        ];
        $datalogs = [
             'results' => $results,
            'plans' => $plans,
            'groups' => $groups,
            'activeType' => $activeType
        ];
 addIpLog('IB Plan Data settings Admin', $datalogs);
        return view("admin.ib.ib_settings", $data);
    }

    public function ibCommission()
    {
        $ibCategories = DB::table('ib_categories')->get();

        $accountTypes = DB::table('account_types')
            ->orderBy('ac_index', 'desc')
            ->get();
            $datalogs = [
                'ibCategories' => $ibCategories,
            'accountTypes' => $accountTypes,
            ];
             addIpLog('IB commissions Admin', $datalogs);
        return view("admin.ib.ibCommission", [
            'ibCategories' => $ibCategories,
            'accountTypes' => $accountTypes,
        ]);
    }

    public function updateIbPlan(Request $request)
    {

        $ib_plan_id = $request->input('ib_plan_id');
        $acc_type = $request->input('acc_type');
        $status = $request->input('status');
        $levels = $request->input('level');
        $email = $request->session()->get('alogin');

        try {

            DB::beginTransaction();

            DB::table('ib_plan_details')
                ->where('ib_plan_id', $ib_plan_id)
                ->where('acc_type', $acc_type)
                ->update(['deleted_at' => now()]);

            foreach ($levels as $key => $divs) {
            $datalogs = [
                 'ib_plan_id' => $ib_plan_id,
                    'acc_type' => $acc_type,
                    'level_id' => $key,
                    'updated_by' => $email,
            ];   
            
            $data = [
                    'ib_plan_id' => $ib_plan_id,
                    'acc_type' => $acc_type,
                    'level_id' => $key,
                    'updated_by' => $email,
                ];

                foreach ($divs as $d => $val) {
                    $data[$d] = $val;
                }
                DB::table('ib_plan_details')->insert($data);
            }

            DB::commit();
addIpLog('IB Update plan Admin', $datalogs);
            alert()->success("IB Plan Successfully Updated");
            return redirect("/admin/ib_settings");
        } catch (Exception $e) {

            DB::rollBack();
            alert()->error("Failed to update IB Plan", "Please try again or Contact Support.");
            return redirect("/admin/ib_settings");
        }
    }

    public function ibCommissionEdit($planId, $accType, Request $request)
    {
        // Retrieve selected IB plan details
        $selected = DB::table('ib_plan_details')
            ->join('account_types', 'account_types.ac_index', '=', 'ib_plan_details.acc_type')
            ->join('ib_categories', 'ib_categories.ib_cat_id', '=', 'ib_plan_details.ib_plan_id')
            ->whereNull('ib_plan_details.deleted_at')
            ->where(DB::raw('md5(ib_plan_details.ib_plan_id)'), $planId)
            ->where(DB::raw('md5(ib_plan_details.acc_type)'), $accType)
            ->select('ib_plan_details.*', 'account_types.ac_group', 'ib_categories.ib_cat_name', DB::raw('count(*) as count'))
            ->groupBy('ib_plan_details.ib_plan_id', 'ib_plan_details.acc_type')
            ->first();

        // dd($selected,$request->all());
        // If the form is submitted (for example, via POST request)
        if ($request->isMethod('post') && $request->has('action')) {
            $ibPlanId = $request->input('ib_plan_id');
            $accType = $request->input('acc_type');
            $level = $request->input('level');
            $email = $request->session()->get('alogin');

            // Update existing plan details (soft delete by setting `deleted_at`)
            DB::table('ib_plan_details')
                ->where('ib_plan_id', $ibPlanId)
                ->where('acc_type', $accType)
                ->update(['deleted_at' => now()]);

            // Insert new plan details
            foreach ($level as $key => $divs) {
                $datalogs = [
                     $data['ib_plan_id'] = $ibPlanId,
                $data['acc_type'] = $accType,
                $data['level_id'] = $key,
                $data['updated_by'] = $email,
                ];
                $data = [];
                foreach ($divs as $d => $val) {
                    $data[$d] = $val;
                }
                $data['ib_plan_id'] = $ibPlanId;
                $data['acc_type'] = $accType;
                $data['level_id'] = $key;
                $data['updated_by'] = $email;

                // Insert new record into ib_plan_details
                DB::table('ib_plan_details')->insert($data);
            }
addIpLog('ib Commission Edit', $datalogs);
            // Return a success message using SweetAlert or any preferred method
            return redirect("/admin/ib_settings")->with('success', 'IB Plan Successfully Updated');
        }

        // Retrieve all IB Categories
        $ibCategories = DB::table('ib_categories')->get();

        // Retrieve all Account Types ordered by `ac_index` in descending order
        $accountTypes = DB::table('account_types')
            ->orderBy('ac_index', 'desc')
            ->get();
        // dd($selected);
        // Return data to the view
        return view('admin.ib.ibCommissionEdit', [
            'selected' => $selected,
            'ibCategories' => $ibCategories,
            'groups' => $accountTypes,
            'planId' => $planId,
            'accType' => $accType,
        ]);
    }
	
	public function ibclientdetails(Request $request)
    {
		$ibid = request('ibid');
		$user = DB::table('aspnetusers as ap')
            ->leftJoin('ib1', 'ib1.email', '=', 'ap.email')
            ->leftJoin('user_groups', 'user_groups.user_group_id', '=', 'ap.group_id')
            ->select('ap.*', 'ib1.status as ib_status', 'ib1.acc_type as ib_group', 'user_groups.group_name as user_group_name')
            ->where(DB::raw('md5(ap.id)'), $ibid)
            ->orWhere(DB::raw('md5(ap.email)'), $ibid)
            ->first();
		$ibDetails = DB::table('ib1 as ib')
			->where(DB::raw('md5(ib.indexId)'), $ibid)
            ->orWhere(DB::raw('md5(ib.email)'), $ibid)
            ->first();
		$country_code = DB::table('countries')
			->where('country_name', $user->country)
			->first();
		$rm_details = DB::table('relationship_manager as rm')
			->leftJoin('emplist as emp', 'rm.rm_id', '=', 'emp.email')
			->select('emp.username')
			->where('rm.user_id', $user->email)
			->first();
          // ✅ Add Proper View Log
if ($user) {

    $datalogs = [
        'action'        => 'IB Client Details Viewed',
        'client_id'     => $user->id ?? null,
        'client_email'  => $user->email ?? null,
        'ib_status'     => $user->ib_status ?? null,
        'ib_group'      => $user->ib_group ?? null,
        'country'       => $user->country ?? null,
        'rm_username'   => $rm_details->username ?? null,
        'viewed_by'     => session('alogin'),
        'role_id'       => session('userData.role_id') ?? null,
        'ip_address'    => $request->ip(),
        'user_agent'    => $request->userAgent(),
        'timestamp'     => now(),
    ];

    addIpLog('View IB Client Details in Admin', $datalogs);
}

		return view('admin.ib.ibclientdetails', [
            'user' => $user,
            'ibDetails' => $ibDetails,
            'country_code' => $country_code
        ]);
	}
}
