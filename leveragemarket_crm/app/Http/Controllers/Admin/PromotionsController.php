<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotation;
use Validator;
use DB;

class PromotionsController extends Controller
{
    public function index()
    {
		$pageTitle = "Promotions";
		$promodata = Promotation::orderBy('promo_id','DESC')->get();
        return view('admin.promotions', compact('pageTitle', 'promodata'));
    }
    public function add_promotion()
    {
		$userGroups = json_decode(session("userData")["user_group_id"]);
		$acc_types = DB::table('account_types as ac')
            ->leftJoin('mt5_groups as m', 'ac.ac_type', '=', 'm.mt5_group_id')
            ->select('ac.*')
            ->where('m.mt5_group_type', 'live')
            ->where('ac.status', 1)
            ->where('m.is_active', 1)
            ->whereIn("ac.user_group_id", $userGroups)
            ->get();
                 $datalogs = [
        'action'          => 'Add Promotion Page Viewed',
        'total_acc_types' => $acc_types->count(),
        'user_groups'     => $userGroups,
        'viewed_by'       => session('alogin'),
        'role_id'         => session('userData.role_id') ?? null,
        'ip_address'      => request()->ip(),
        'user_agent'      => request()->userAgent(),
        'timestamp'       => now(),
    ];

    addIpLog('View Add Promotion Page in Admin', $datalogs);
        return view('admin.promoAdd', compact('acc_types'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'promo_name' => 'required|string|max:255',
            'promo_starts_at' => 'required|date',
            'promo_ends_at' => 'required|date|after_or_equal:bonus_starts_at',
			'promo_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_desc' => 'nullable|string',
            'promo_url' => 'required|string',
            'status' => 'nullable|boolean',
             'expiry_date' => 'nullable|date',
        ]);
        try {
            $promo = new Promotation();
			
			/*Image upload*/
			if ($request->hasFile('promo_image')) {
				$imageName = time() . '_' . uniqid() . '.' . $request->promo_image->extension();
				$request->promo_image->storeAs('promo', $imageName, 'public');
				$promo->promo_image = $imageName;
			}
			
            $promo->promo_name = $validated['promo_name'];
            $promo->promo_code = $request->input('promo_code');
            $promo->promo_url = $validated['promo_url'];
            $promo->promo_desc = $validated['promo_desc'];
            $promo->promo_starts_at = $validated['promo_starts_at'];
            $promo->promo_ends_at = $validated['promo_ends_at'];
            $promo->promo_groups = implode(',', $request->input('promo_groups'));
            $promo->promo_apply_for = $request->input('promo_apply_for');
            $promo->status = $validated['status'] ?? 0;
            $promo->promo_updated_by = session('alogin');
            $promo->expiry_date =  $validated['expiry_date'];

            $promo->save();
        // ✅ Proper Data Logs
        $datalogs = [
            'action'       => 'Promotion Created',
            'promo_id'     => $promo->id ?? null,
            'promo_name'   => $promo->promo_name,
            'promo_url'    => $promo->promo_url,
            'status'       => $promo->status,
            'created_by'   => session('alogin'),
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'timestamp'    => now(),
        ];
        addIpLog('Promotion Create in Admin', $datalogs);
            session()->flash('success', 'Promotions Successfully Added');
            return redirect()->route('admin.promotions');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function update(Request $request)
    {

       
        $validator = Validator::make($request->all(), [
            'promo_name' => 'required|string|max:255',
            'promo_starts_at' => 'required|date',
            'promo_ends_at' => 'required|date|after_or_equal:bonus_starts_at',
			'promo_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'promo_desc' => 'nullable|string',
            'promo_url' => 'required|string',
            'status' => 'nullable|boolean',
            'expiry_date' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
			$promo = Promotation::findOrFail($request->input('promo_id'));
			if ($request->hasFile('promo_image')) {

				// Delete old image
				/*if ($promo->promo_image && Storage::disk('public')->exists('promo/' . $promo->promo_image)) {
					Storage::disk('public')->delete('promo/' . $promo->promo_image);
				}*/

				// Upload new image
				$imageName = time() . '_' . uniqid() . '.' . $request->promo_image->extension();
				$request->promo_image->storeAs('promo', $imageName, 'public');
				$promo->promo_image = $imageName;
			}
			
			$promo->promo_name = $request->input('promo_name');
			$promo->promo_url = $request->input('promo_url');
            $promo->promo_desc = $request->input('promo_desc');
            $promo->promo_starts_at = $request->input('promo_starts_at');
            $promo->promo_ends_at = $request->input('promo_ends_at');
            $promo->status = $request->input('status') ?? 0;
            $promo->promo_updated_by = session('alogin');
             $promo->expiry_date = $request->input('expiry_date') ;
            $promo->save();
  // ✅ Proper Data Logs for Update
        $datalogs = [
            'action'       => 'Promotion Updated',
            'promo_id'     => $promo->id,
           
            'new_data'     => $promo->toArray(),
            'updated_by'   => session('alogin'),
            'ip_address'   => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'timestamp'    => now(),
        ];
        addIpLog('Promotion Update in Admin', $datalogs);

            session()->flash('success', 'Promotions Successfully Updated');
            return redirect()->route('admin.promotions');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }
    }
}
