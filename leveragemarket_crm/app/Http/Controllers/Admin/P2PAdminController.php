<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Services\PusherService;
use App\Models\Crypto;
use App\Models\Cryptohistory;
use App\Models\P2PMerchant;
use App\Models\P2POrders;
use App\Models\P2POrdershistory;

class P2PAdminController extends Controller
{
	protected $settings;
    protected $pusherService;

    public function __construct(PusherService $pusherService)
    {
        $this->settings = settings();
        $this->pusherService = $pusherService;
    }
    public function cryptoindex(Request $request)
    {
        $cryptos = Crypto::orderBy('id')->get();
		$editCrypto = null;
		if ($request->id) {
			$editCrypto = Crypto::find($request->id);
		}
        return view('admin.p2p.cryptoindex', compact('cryptos', 'editCrypto'));
    }
	
	public function cryptostore(Request $request){
		$request->validate([
            'symbol' => 'nullable|unique:cryptos,symbol',
            'name' => 'required|unique:cryptos,name',
            'icon' => 'nullable|image|mimes:png,jpg,svg,webp|max:2048'
        ]);

        $fileName = null;

        if ($request->hasFile('icon')) {
            $fileName = time().'_'.$request->icon->getClientOriginalName();
            $request->icon->storeAs('cryptos', $fileName, 'public');
        }
$datalogs = [ 'symbol' => $request->symbol,
            'name'   => $request->name,
            'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
            'icon'   => $fileName,
			'status'   => $request->status,];
        $crypto = Crypto::create([
            'symbol' => $request->symbol,
            'name'   => $request->name,
            'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
            'icon'   => $fileName,
			'status'   => $request->status,
        ]);
		
		/*Create the Histroy value currency*/
		Cryptohistory::create([
            'cryptoid' => $crypto->id,
            'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
        ]);
		addIpLog('cryptostore request', $datalogs);
        return redirect()->route('admin.p2p.cryptoindex')->with('success', 'Crypto Added Successfully!');
	}
	
	public function cryptoupdate(Request $request, $id)
	{
		$crypto = Crypto::findOrFail($id);

		$request->validate([
			'symbol' => 'nullable|unique:cryptos,symbol,' . $crypto->id,
			'name' => 'required|unique:cryptos,symbol,' . $crypto->id,
			'icon' => 'nullable|image|mimes:png,jpg,svg,webp|max:2048'
		]);

		$fileName = $crypto->icon;

		if ($request->hasFile('icon')) {
			$fileName = time().'_'.$request->icon->getClientOriginalName();
			$request->icon->storeAs('cryptos', $fileName, 'public');
		}
$datalogs = ['symbol' => $request->symbol,
			'name'   => $request->name,
			'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
			'icon'   => $fileName,
			'status'   => $request->status,];
		$crypto->update([
			'symbol' => $request->symbol,
			'name'   => $request->name,
			'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
			'icon'   => $fileName,
			'status'   => $request->status,
		]);
		
		Cryptohistory::create([
            'cryptoid' => $id,
            'minprice'   => $request->minprice,
            'maxprice'   => $request->maxprice,
            'defaultprice'   => $request->defaultprice,
        ]);
			addIpLog(' Update cryptostore request', $datalogs);
		return redirect()->route('admin.p2p.cryptoindex')->with('success', 'Crypto Updated Successfully!');
	}
	
	public function merchantacclist(Request $request)
	{
		$merchantlist = P2PMerchant::orderBy('id', 'DESC')->get();
        return view('admin.p2p.merchataccount', compact('merchantlist'));
		
	}
	
	
}
