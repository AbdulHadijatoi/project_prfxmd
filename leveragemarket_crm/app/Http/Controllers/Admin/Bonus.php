<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BonusModel;
use Validator;
use DB;
use App\Models\BonusLogs;
class Bonus extends Controller
{
    public function index()
    {
		$pageTitle = "Bouns";
		$bounsdata = BonusModel::orderBy('bonus_id','DESC')->get();
        return view('admin.bonus', compact('pageTitle', 'bounsdata'));
    }
    public function add_bonus()
    {
        return view('admin.bonusAdd');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bonus_name' => 'required|string|max:255',
            'bonus_code' => 'required|string|max:255',
            'bonus_desc' => 'nullable|string',
            'bonus_starts_at' => 'required|date',
            'bonus_ends_at' => 'required|date|after_or_equal:bonus_starts_at',
            'bonus_limit' => 'nullable|integer',
            'bonus_accessable' => 'required|array',
            'bonus_shows_on' => 'required|string',
            'bonus_show_list' => 'nullable|array',
            'bonus_type' => 'required|string',
            'bonus_value' => 'required|numeric',
			'bonus_images'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',

        ]);

        
		
        try {
			$bonus = new BonusModel();
			
			/*Image upload*/
			if ($request->hasFile('bonus_images')) {
				$imageName = time() . '_' . uniqid() . '.' . $request->bonus_images->extension();
				$request->bonus_images->storeAs('bonus', $imageName, 'public');
				$bonus->bonus_images = $imageName;
			}		
			
            $bonus->bonus_name = $validated['bonus_name'];
            $bonus->bonus_code = $validated['bonus_code'];
            $bonus->bonus_desc = $validated['bonus_desc'];
            $bonus->bonus_starts_at = $validated['bonus_starts_at'];
            $bonus->bonus_ends_at = $validated['bonus_ends_at'];
            $bonus->bonus_limit = $validated['bonus_limit'];
            $bonus->bonus_accessable = implode(',', $validated['bonus_accessable']);
            $bonus->bonus_shows_on = $validated['bonus_shows_on'];
            $bonus->bonus_show_list = isset($validated['bonus_show_list']) ? implode(',', $validated['bonus_show_list']) : null;
            $bonus->bonus_type = $validated['bonus_type'];
            $bonus->bonus_value = $validated['bonus_value'];
            $bonus->status = $validated['status'] ?? 0;
            $bonus->bonus_updated_by = session('alogin');
             $bonus->expiry_date = $validated['expiry_date'];
            $bonus->save();

            $bonuslogs=new BonusLogs();
            $bonuslogs->bonus_id=$bonus->bonus_id;
            $bonuslogs->data=json_encode($bonus);
            $bonuslogs->created_by=session('alogin');
            $bonuslogs->save();
            $datalogs = [
    'action'          => 'Bonus Created',
    'bonus_id'        => $bonus->bonus_id,
    'bonus_name'      => $bonus->bonus_name,
    'bonus_code'      => $bonus->bonus_code,
    'bonus_type'      => $bonus->bonus_type,
    'bonus_value'     => $bonus->bonus_value,
    'bonus_limit'     => $bonus->bonus_limit,
    'bonus_starts_at' => $bonus->bonus_starts_at,
    'bonus_ends_at'   => $bonus->bonus_ends_at,
    'expiry_date'     => $bonus->expiry_date,
    'status'          => $bonus->status,
    'created_by'      => session('alogin'),
    'ip_address'      => $request->ip(),
    'user_agent'      => $request->userAgent(),
    'timestamp'       => now(),
];
 addIpLog('create Bouns in admin ', $datalogs);
            session()->flash('success', 'Bonus Successfully Added');
            return redirect()->route('admin.bonus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bonus_name' => 'required|string|max:255',
            'bonus_code' => 'required|string|max:255',
            'bonus_desc' => 'nullable|string',
            'bonus_starts_at' => 'required|date',
            'bonus_ends_at' => 'required|date',
            'bonus_limit' => 'required|integer',
            'bonus_accessable' => 'required|array',
            'bonus_shows_on' => 'required|string',
            'bonus_type' => 'required|string',
            'bonus_value' => 'required|numeric',
			'bonus_images'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $bonus = BonusModel::findOrFail($request->input('bonus_id'));
			
			if ($request->hasFile('bonus_images')) {

				// Delete old image
				/*if ($bonus->bonus_images && Storage::disk('public')->exists('bonus/' . $bonus->bonus_images)) {
					Storage::disk('public')->delete('bonus/' . $bonus->bonus_images);
				}*/

				// Upload new image
				$imageName = time() . '_' . uniqid() . '.' . $request->bonus_images->extension();
				$request->bonus_images->storeAs('bonus', $imageName, 'public');
				$bonus->bonus_images = $imageName;
			}
			
            $bonus->bonus_name = $request->input('bonus_name');
            $bonus->bonus_code = $request->input('bonus_code');
            $bonus->bonus_desc = $request->input('bonus_desc');
            $bonus->bonus_starts_at = $request->input('bonus_starts_at');
            $bonus->bonus_ends_at = $request->input('bonus_ends_at');
            $bonus->bonus_limit = $request->input('bonus_limit');
            $bonus->bonus_accessable = implode(',', $request->input('bonus_accessable'));
            $bonus->bonus_shows_on = $request->input('bonus_shows_on');
            $bonus->bonus_show_list = $request->input('bonus_show_list') ? implode(',', $request->input('bonus_show_list')) : '';
            $bonus->bonus_type = $request->input('bonus_type');
            $bonus->bonus_value = $request->input('bonus_value');
            $bonus->status = $request->input('status', 0);
            $bonus->bonus_updated_by = session('alogin');
            $bonus->expiry_date = $request->input('expiry_date');
            $bonus->save();

            $bonuslogs=new BonusLogs();
            $bonuslogs->bonus_id=$bonus->bonus_id;
            $bonuslogs->data=json_encode($bonus);
            $bonuslogs->created_by=session('alogin');
            $bonuslogs->save();
                        $datalogs = [
    'action'          => 'Bonus update',
    'bonus_id'        => $bonus->bonus_id,
    'bonus_name'      => $bonus->bonus_name,
    'bonus_code'      => $bonus->bonus_code,
    'bonus_type'      => $bonus->bonus_type,
    'bonus_value'     => $bonus->bonus_value,
    'bonus_limit'     => $bonus->bonus_limit,
    'bonus_starts_at' => $bonus->bonus_starts_at,
    'bonus_ends_at'   => $bonus->bonus_ends_at,
    'expiry_date'     => $bonus->expiry_date,
    'status'          => $bonus->status,
    'created_by'      => session('alogin'),
    'ip_address'      => $request->ip(),
    'user_agent'      => $request->userAgent(),
    'timestamp'       => now(),
];
 addIpLog('update Bouns in admin ', $datalogs);
            session()->flash('success', 'Bonus Successfully Updated');
            return redirect()->route('admin.bonus');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }
    public function getBonus(Request $request)
    {
        $eid = session('clogin');
        $account_type = 0;
        $status = 1;
        $accountTypeQuery = DB::table('liveaccount')
            ->where('trade_id', $request->id)
            ->select('account_type')
            ->first();
        $account_type = $accountTypeQuery ? $accountTypeQuery->account_type : 0;
        $user_groups=session('user')['group_id'];
        $bonusTaken = DB::table('bonus_trans')
            ->select(DB::raw('bonus_id, count(id) as bonus_limit'))
            ->where('email', $eid)
            ->where('status', 1)
            ->groupBy('bonus_id')
            ->get();
        $bonusAvailable = DB::table('bonuses')
            ->where(function ($query) use ($eid, $account_type,$user_groups) {
                $query->whereRaw('FIND_IN_SET(?, bonus_show_list) > 0 AND bonus_shows_on = "users"', [$eid])
                    ->orWhereRaw('FIND_IN_SET(?, bonus_show_list) > 0 AND bonus_shows_on = "groups"', [$account_type])
                    ->orWhereRaw('FIND_IN_SET(?, bonus_show_list) > 0 AND bonus_shows_on = "user_groups"', [$user_groups])
                    ->orWhere('bonus_shows_on', 'all');
            })
            ->where('status', 1)
            ->whereRaw('NOW() BETWEEN bonus_starts_at AND bonus_ends_at')
            ->get();
        $limitMapping = [];
        foreach ($bonusTaken as $limit) {
            $limitMapping[$limit->bonus_id] = $limit->bonus_limit;
        }
        $filteredBonuses = $bonusAvailable->filter(function ($bonus) use ($limitMapping) {
            return !isset($limitMapping[$bonus->bonus_id]) || $bonus->bonus_limit >= $limitMapping[$bonus->bonus_id];
        });
            $datalogs = [
        'email'            => $eid,
        'trade_id'         => $request->id,
        'account_type'     => $account_type,
        'user_group_id'    => $user_groups,
        'total_available'  => $bonusAvailable->count(),
        'total_filtered'   => $filteredBonuses->count(),
        'ip'               => request()->ip(),
        'time'             => now()
    ];

    addIpLog('getBonus', $datalogs);
        return response()->json($filteredBonuses);
    }
}
