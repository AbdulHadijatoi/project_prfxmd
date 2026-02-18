<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\IbWallet;
use App\Models\LiveAccount;
use App\Models\IbClientList;
use App\Models\Ib1;
use App\Models\IbPlanDetails;
use App\Models\ClientBankDetails;
use App\Models\TradeDeposits;
use App\MT5\MTWebAPI;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Models\Ib1Commission;
use App\Services\MT5Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\AccountHelper;
use App\Models\Country;
use App\Models\IbWithdraw;
use App\Models\UserGroup;
use Exception;
use Illuminate\Support\Facades\Cache;
class Ib extends Controller
{
    protected $mt5Service;
    protected $api;
    public function __construct(MT5Service $mt5Service)
    {
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
    }
    public function index()
    {

        $user = auth()->user();		
		$pageTitle = "IB Partnership";
        $email = auth()->user()->email;
        $ib_result = Ib1::where('email', $email)->first();
        if ($ib_result && $ib_result->status == 1) {
            return redirect("/ib-profile");
        }
		addIpLog('IB View Page', $email);
        return view('ib', compact('ib_result', 'user','pageTitle'));
    }
    public function ibEnroll(Request $request)
    {
        if ($request->isMethod('post')) {
            $uid = uniqid();
            $code = md5(uniqid(rand()));
            $user = session('user');
			// $path = $request->file('document')->store('ib_docs', 'public');
            try {
				$datalog = [
					'uid' => $uid,
                    'email' => $user['email'],
                    'name' => $user['fullname'],
                    'password' => $user['password'],
                    'number' => $user['number'],
                    'username' => $user['email'],
                    'emailToken' => $code,
					'ibExp' => $request->ibExp,
					'partnershipModel' => $request->partnershipModel,
					'regions' => $request->regions,
					'clientType' => $request->clientType,
					'clientsbring' => $request->clientsbring,
					'turnover' => $request->turnover,
					'deposits' => $request->deposits,
					'website' => $request->website,
					'channels' => $request->channels,
					'budget' => $request->budget,
					'languagePref' => $request->languagePref,
					'referral' => $request->referral,
					'documentFile' => '',
					'termsaccept' => $request->termsaccept,
                    'status' => 0,
				];

                Ib1::create([
                    'uid' => $uid,
                    'email' => $user['email'],
                    'name' => $user['fullname'],
                    'password' => $user['password'],
                    'number' => $user['number'],
                    'username' => $user['email'],
                    'emailToken' => $code,
					'ibExp' => $request->ibExp,
					'partnershipModel' => $request->partnershipModel,
					'regions' => $request->regions,
					'clientType' => $request->clientType,
					'clientsbring' => $request->clientsbring,
					'turnover' => $request->turnover,
					'deposits' => $request->deposits,
					'website' => $request->website,
					'channels' => $request->channels,
					'budget' => $request->budget,
					'languagePref' => $request->languagePref,
					'referral' => $request->referral,
					'documentFile' => '',
					'termsaccept' => $request->termsaccept,
                    'status' => 0,
                ]);		
				addIpLog('IB Create Page', $datalog);
                return response()->json(['status' => 'true']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
            }
        }
        return response()->json(['status' => 'false', 'message' => 'Invalid request method']);
    }

    public function ib_profile()
	{
		ini_set('memory_limit', '512M');
		set_time_limit(0);
	
		$ibEmail  = auth()->user()->email;		
		$email  = auth()->user()->email;	
		
		try {
			AccountHelper::updateLiveAndDemoAccounts($ibEmail, $this->api);
		} catch (\Throwable $e) {
			Log::error('Account sync failed', ['error' => $e->getMessage()]);
		}
		
		/* -------------------------------------------------
		  1. Fetch IB trade accounts
		------------------------------------------------- */
		$tradeAccounts = DB::table('liveaccount')
			->where('status', 'active')
			->where('is_excluded', 0)
			->where(function ($q) use ($ibEmail) {
				for ($i = 1; $i <= 15; $i++) {
					$q->orWhere("ib{$i}", $ibEmail);
				}
			})
			->select('trade_id', 'email')
			->get();

		$start = \Carbon\Carbon::create(2025, 12, 1, 0, 0, 0, 'UTC')->timestamp;
		$end   = \Carbon\Carbon::create(2035, 12, 31, 23, 59, 59, 'UTC')->timestamp;

		foreach ($tradeAccounts as $acc) {
			try {
				$login     = (int) $acc->trade_id;
				$clientEmail = $acc->email;

				// 👇 SAME AS: COUNT(ib1_commission.id)
				$offset = DB::table('ib1_commission')
					->where('login', $login)
					->count();
					
				if (($error_code = $this->api->UserAccountGet($login, $accounts)) != MTRetCode::MT_RET_OK) {
					session()->flash('error', value: 'MT5 ' . $login . ': ' . MTRetCode::GetError($error_code));
					Log::warning('MT5 UserAccountGet failed', ['login' => $login, 'error' => MTRetCode::GetError($error_code) ]);
					continue;
				}
				if (($error_code = $this->api->UserGet($login, $account)) != MTRetCode::MT_RET_OK) {
					Log::warning('MT5 UserGet failed', ['login' => $login, 'error' => MTRetCode::GetError($error_code) ]);
					continue;
				}
				
				if (($error_code = $this->api->HistoryGetTotal($login, $start, $end, $total)) != MTRetCode::MT_RET_OK) {
					Log::warning('MT5 HistoryGetTotal failed', ['login' => $login, 'error' => MTRetCode::GetError($error_code) ]);
					continue;
				}
				
				if (($error_code = $this->api->HistoryGetPage($login, $start, $end, $offset, $total, $orders)) != MTRetCode::MT_RET_OK) {
					Log::warning('MT5 HistoryGetPage failed', ['login' => $login, 'error' => MTRetCode::GetError($error_code) ]);
					continue;
				}
				
				if (empty($orders) || !is_iterable($orders)) {
					Log::info('MT5: No orders found', [
						'login' => $login,
						'orders' => $orders
					]);
					continue; // VERY IMPORTANT
				}

				foreach ($orders as $record) {
					if (!in_array($record->Type, [0, 1])) continue;

					$volume = $record->VolumeInitial * 0.0001; 
					$time_closed = gmdate("Y-m-d H:i:s", $record->TimeDone);
					
					$datalog = [
						'user_id'     => $clientEmail,
							'login'       => $record->Login,
							'position_id' => $record->ExpertPositionID,
							'order_type'  => $record->Type,
							'init_volume' => $record->VolumeInitial,
							'symbol'      => $record->Symbol ?? '',
							'volume'      => $volume,
							'time_closed' => $time_closed,
							'status'      => 0,
							'updated_at'  => now(),
					];
					DB::table('ib1_commission')->updateOrInsert(
						['order_id' => $record->Order],
						[
							'user_id'     => $clientEmail,
							'login'       => $record->Login,
							'position_id' => $record->ExpertPositionID,
							'order_type'  => $record->Type,
							'init_volume' => $record->VolumeInitial,
							'symbol'      => $record->Symbol ?? '',
							'volume'      => $volume,
							'time_closed' => $time_closed,
							'status'      => 0,
							'updated_at'  => now(),
						]
					);
				}

				addIpLog('IB Profile Page', $datalog);
			} catch (\Throwable $e) {
				Log::error('MT5 Sync Error', [
					'login' => $login,
					'error' => $e->getMessage()
				]);
				continue;
			}
		}
		
		// 3. Check User IB level and update the commission to wallet

		$ib = Ib1::where('email', $ibEmail)
			->whereNotNull('acc_type')
			->where('status', 1)
			->first();

		if (!$ib) {
			return redirect()->route('ib');
		}

		$plan_id = $ib->acc_type;

		if ($plan_id) {

			/* -------------------------------
			 | Load IB Plan
			 --------------------------------*/
			$ibPlans = IbPlanDetails::where('ib_plan_id', $plan_id)
				->where('status', 1)
				->whereNull('deleted_at')
				->get();

			$ib_acc_plans = [];

			foreach ($ibPlans as $plan) {
				$accType = (int) $plan->acc_type;
				$levelId = (int) $plan->level_id;

				for ($d = 1; $d <= $levelId; $d++) {
					$ib_acc_plans[$accType][$levelId]["d{$d}"] = (float) $plan["d{$d}"];
				}
			}

			DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

			/* -------------------------------
			 | Fetch pending orders
			 --------------------------------*/
			$client_live_accs = DB::table('ib1_commission')
				->join('liveaccount', 'liveaccount.trade_id', '=', 'ib1_commission.login')
				->join('aspnetusers', 'aspnetusers.email', '=', 'ib1_commission.user_id')
				->leftJoin('ib_wallet', function ($join) {
					$join->on('ib_wallet.order_id', '=', 'ib1_commission.order_id');
				})
				->whereNull('ib_wallet.order_id')
				->where('ib1_commission.status', 0)
				->whereIn('ib1_commission.order_type', [0, 1])
				->where('liveaccount.status', 'active')
				->where('liveaccount.is_excluded', 0)
				->where('aspnetusers.status', 1)
				->whereColumn('ib1_commission.position_id', '!=', 'ib1_commission.order_id')
				->select(
					'ib1_commission.*',
					'aspnetusers.email as client_email',
					'liveaccount.account_type',
					'liveaccount.ib1',
					'liveaccount.ib2',
					'liveaccount.ib3',
					'liveaccount.ib4',
					'liveaccount.ib5',
					'liveaccount.ib6',
					'liveaccount.ib7',
					'liveaccount.ib8',
					'liveaccount.ib9',
					'liveaccount.ib10',
					'liveaccount.ib11',
					'liveaccount.ib12',
					'liveaccount.ib13',
					'liveaccount.ib14',
					'liveaccount.ib15'
				)
				->groupBy('ib1_commission.order_id')
				->orderByDesc('ib1_commission.id')
				->get();

			/* -------------------------------
			 | Generate Wallet Commission
			 --------------------------------*/
			foreach ($client_live_accs as $order) {

				$accountType = (int) $order->account_type;

				if (!isset($ib_acc_plans[$accountType])) {
					continue;
				}

				// 🔥 USE ONLY MAX PLAN LEVEL
				$maxPlanLevel = max(array_keys($ib_acc_plans[$accountType]));
				$dxRates = $ib_acc_plans[$accountType][$maxPlanLevel];

				// Build IB chain (1 → 15)
				$ibChain = [];
				for ($i = 1; $i <= 15; $i++) {
					$col = "ib{$i}";
					if (empty($order->$col) || $order->$col === 'noIB') {
						break;
					}
					$ibChain[$i] = $order->$col;
				}

				foreach ($dxRates as $dx => $rate) {

					if ($rate <= 0) continue;

					$ibIndex = (int) substr($dx, 1);

					if (!isset($ibChain[$ibIndex])) continue;

					// Duplicate protection
					$exists = IbWallet::where('order_id', $order->order_id)
						->where('email', $ibChain[$ibIndex])
						->exists();

					if ($exists) continue;

					$walletAmount = round($rate * $order->volume, 2);
					$ib_level_name = "IB Level " . $maxPlanLevel . " - D" . $ibIndex;
					
					$datalog = [
						'email'             => $ibChain[$ibIndex],
						'trade_id'          => $order->login,
						'order_id'          => $order->order_id,
						'ib_wallet'         => $walletAmount,
						'comission_per_lot' => $rate,
						'ib_level'          => $ib_level_name,
						'remark'            => $order->client_email,];
					IbWallet::create([
						'email'             => $ibChain[$ibIndex],
						'trade_id'          => $order->login,
						'order_id'          => $order->order_id,
						'ib_wallet'         => $walletAmount,
						'comission_per_lot' => $rate,
						'ib_level'          => $ib_level_name,
						'remark'            => $order->client_email,
					]);
					addIpLog('IB Wallet Page', $datalog);
				}
			}
		}


		//4. Summary data send the view pages
		$ib_clients_total = User::where(function ($query) use ($ibEmail) {
			for ($i = 1; $i <= 15; $i++) {
				$query->orWhere("ib{$i}", $ibEmail);
			}
		})->distinct('email')->count('email');

		$ib_wallet_raw = IbWallet::where('email', $ibEmail)
			->selectRaw('SUM(ib_wallet) as wallet, SUM(ib_withdraw) as withdraw')
			->first();

		$ib_wallet = $ib_wallet_raw ? $ib_wallet_raw->wallet - $ib_wallet_raw->withdraw : 0.00;

		$live_accs = LiveAccount::where('email', $ibEmail)
			->where('status', 'active')
			->orderBy('id', 'desc')
			->get();

		/*$ib_clients = [];
		for ($i = 1; $i <= 7; $i++) {
			$ib_clients[$i] = IbClientList::where("ib$i", $ibEmail)->get();
		}*/
		
		/* Live accounts per user */
		$liveAccountsSub = DB::table('liveaccount')
			->select('email', DB::raw('COUNT(id) as liveaccounts'))
			->groupBy('email');

		/* Total deposit per user */
		$depositSub = DB::table('trade_deposit')
			->select('email', DB::raw('SUM(deposit_amount) as total_deposit'))
			->where('Status', 1)
			->groupBy('email');
		
		/*Total Withdraw per user*/
		$withdrawalSub = DB::table('trade_withdrawal')
			->select('email', DB::raw('SUM(withdrawal_amount) as total_withdrawal'))
			->where('Status', 1)
			->groupBy('email');
		
		$ib_clients = [];

		for ($level = 1; $level <= 7; $level++) {

			$ib_clients[$level] = User::query()
				->leftJoinSub($liveAccountsSub, 'la', function ($join) {
					$join->on('la.email', '=', 'aspnetusers.email');
				})
				->leftJoinSub($depositSub, 'td', function ($join) {
					$join->on('td.email', '=', 'aspnetusers.email');
				})
				->leftJoinSub($withdrawalSub, 'tw', function ($join) {
					$join->on('tw.email', '=', 'aspnetusers.email');
				})
				->where("aspnetusers.ib{$level}", $ibEmail)
				->select([
					'aspnetusers.id',
					'aspnetusers.fullname',
					'aspnetusers.email',
					'aspnetusers.gender',
					'aspnetusers.profile_image',
					'aspnetusers.email_confirmed',

					DB::raw('COALESCE(la.liveaccounts, 0) as liveaccounts'),
					DB::raw('COALESCE(td.total_deposit, 0) as total_deposit'),
					DB::raw('COALESCE(tw.total_withdrawal, 0) as total_withdrawal'),
				])
				->orderBy('aspnetusers.created_at', 'DESC')
				->get();
		}
			
		$externalliveaccs = LiveAccount::where('liveaccount.ib1', $ibEmail)
			->where('status', 'active')
			->orderBy('liveaccount.id', 'desc')
			->get();
				
		$bank_details = ClientBankDetails::where('userId', $ibEmail)->get() ?? [];
		
		$withdrawallow = IbWithdraw::where('email', $ibEmail)->where('status', 0)->count();
		
		/*Transfer History*/
			
		$tradetransfer = IbWallet::where('email', $ibEmail)
			->whereNotNull('ib_withdraw')
			->where('ib_withdraw', '!=', 0)
			->whereNotNull('trade_id')
			->selectRaw('
				ib_withdraw AS amount,
				trade_id AS account,
				created_at as transdate,
				"Trade Transfer" as transfer_type,
				"Completed" as Status
			')
			->get();

		$banktransfer = IbWithdraw::where('email', $ibEmail)
			->selectRaw('
				withdraw_amount AS amount,
				withdraw_to AS account,
				withdraw_date as transdate,
				"Bank Transfer" as transfer_type,
				Status
			')
			->get();

		$histories = collect($tradetransfer)
			->concat($banktransfer)
			->sortByDesc(function ($item) {
				return \Carbon\Carbon::parse($item->transdate);
			})
			->values();

		return view('ib-profile', compact('ib_clients_total', 'ib_wallet', 'live_accs', 'ib_clients', 'histories', 'withdrawallow', 'ib_wallet_raw', 'externalliveaccs', 'bank_details'));
	}

    // public function ib_profile()
    // {
        
    //     $email = auth()->user()->email;
    
    //     AccountHelper::updateLiveAndDemoAccounts($email, $this->api);
    //     // Get IB user data
    //     $ib = Ib1::where('email', session('clogin'))
    //         ->whereNotNull('acc_type')
    //         ->where('status', 1)
    //         ->first();
    
    //     if (!$ib) {
    //         return redirect()->route('ib');
    //     }
    
    //     $plan_id = $ib->acc_type;
    
    //     // Cache IB plan configuration
    //     $ib_acc_plans = Cache::remember("ib_plans_{$plan_id}", 300, function () use ($plan_id) {
    //         $plans = IbPlanDetails::where('ib_plan_id', $plan_id)
    //             ->where('status', 1)
    //             ->whereNull('deleted_at')
    //             ->get();
    
    //         $ib_acc_plans = [];
    //         foreach ($plans as $plan) {
    //             $ib_acc_plans[$plan->acc_type][$plan->level_id] = [];
    //             for ($i = 1; $i <= $plan->level_id; $i++) {
    //                 $ib_acc_plans[$plan->acc_type][$plan->level_id]["d$i"] = $plan["d$i"];
    //             }
    //         }
    //         return $ib_acc_plans;
    //     });
    
    //     // Cache total IB clients count
    //     $ib_clients_total = Cache::remember("ib_clients_total_{$email}", 300, function () use ($email) {
    //         return User::where(function ($query) use ($email) {
    //             for ($i = 1; $i <= 15; $i++) {
    //                 $query->orWhere("ib{$i}", $email);
    //             }
    //         })->distinct()->count('email');
    //     });
   
    //     // Cache wallet values
    //     $ib_wallet_raw = Cache::remember("ib_wallet_raw_{$email}", 300, function () use ($email) {
    //         return IbWallet::where('email', $email)
    //             ->selectRaw('SUM(ib_wallet) as wallet, SUM(ib_withdraw) as withdraw')
    //             ->first();
    //     });
    
    //     $ib_wallet = $ib_wallet_raw ? $ib_wallet_raw->wallet - $ib_wallet_raw->withdraw : 0.00;
    
    //     // Paginated live accounts
    //     $live_accs = LiveAccount::where('email', $email)
    //         ->orderBy('id', 'desc')
    //         ->paginate(20);
    // //  echo'<pre>';print_r($email);exit;
    //     // Optimize IB clients (1 query instead of 7)
    //     $ib_clients_raw = IbClientList::where(function ($query) use ($email) {
    //         for ($i = 1; $i <= 7; $i++) {
    //             $query->orWhere("ib{$i}", $email);
    //         }
    //     })->limit(700)->get(); // Optional limit
    
    //     $ib_clients = [];
    //     for ($i = 1; $i <= 7; $i++) {
    //         $ib_clients[$i] = $ib_clients_raw->filter(fn($client) => $client["ib{$i}"] === $email)->values();
    //     }
    
    //     // Cache commission history
    //     $histories = Cache::remember("ib_histories_{$email}", 300, function () use ($email) {
    //         return IbWallet::leftJoin('ib1_commission', 'ib_wallet.order_id', '=', 'ib1_commission.order_id')
    //             ->where('ib_wallet.email', $email)
    //             ->select('ib_wallet.*', 'ib1_commission.volume')
    //             ->orderByDesc('ib_wallet.id')
    //             ->limit(100)
    //             ->get();
    //     });
    
    //     return view('ib-profile', compact(
    //         'ib_clients_total',
    //         'ib_wallet',
    //         'live_accs',
    //         'ib_clients',
    //         'histories',
    //         'ib_wallet_raw'
    //     ));
    // }

//     public function ib_profile()
//     {
//         $email = auth()->user()->email;

//         AccountHelper::updateLiveAndDemoAccounts($email, $this->api);
//         $ib = Ib1::where('email', session('clogin'))
//             ->whereNotNull('acc_type')
//             ->where('status', 1)
//             ->first();
// // echo'<pre>';print_r(session('clogin'));exit;
//         // dd($ib);
//         if (!$ib) {
//             return redirect()->route('ib');
//         }
//         $plan_id = $ib->acc_type;
//         $ib_email = $email;
//         if ($plan_id) {
//             $ibPlans = IbPlanDetails::where('ib_plan_id', $plan_id)
//                 ->where('status', 1)
//                 ->whereNull('deleted_at')
//                 ->get()
//                 ->toArray();
//             $ib_acc_plans = [];

//             foreach ($ibPlans as $plan) {
//                 $ib_acc_plans[$plan['acc_type']][$plan['level_id']] = [];
//                 for ($i = 1; $i <= $plan['level_id']; $i++) {
//                     $ib_acc_plans[$plan['acc_type']][$plan['level_id']]["d$i"] = $plan["d$i"];
//                 }
//             }

//             for ($i = 1; $i <= 15; $i++) {
//                 DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

//                 $client_live_accs = DB::table('ib1_commission')
//                     ->join('liveaccount', 'liveaccount.trade_id', '=', 'ib1_commission.login')
//                     ->join('aspnetusers', 'aspnetusers.email', '=', 'ib1_commission.user_id')
//                     ->leftJoin('ib_wallet', function ($join) use ($ib_email) {
//                         $join->on('ib_wallet.order_id', '=', 'ib1_commission.order_id')
//                             ->where('ib_wallet.email', '=', $ib_email);
//                     })
//                     ->select(
//                         'aspnetusers.email as client_email',
//                         'liveaccount.ib1',
//                         'liveaccount.ib2',
//                         'liveaccount.ib3',
//                         'liveaccount.ib4',
//                         'liveaccount.ib5',
//                         'liveaccount.ib6',
//                         'liveaccount.ib7',
//                         'liveaccount.ib8',
//                         'liveaccount.ib9',
//                         'liveaccount.ib10',
//                         'liveaccount.ib11',
//                         'liveaccount.ib12',
//                         'liveaccount.ib13',
//                         'liveaccount.ib14',
//                         'liveaccount.ib15',
//                         'ib1_commission.*',
//                         'liveaccount.account_type'
//                     )
//                     ->where('ib1_commission.status', 0)
//                     ->whereNull('ib_wallet.order_id')
//                     ->where('aspnetusers.status', 1)
//                     ->where('liveaccount.is_excluded',0)
//                     ->whereIn('ib1_commission.order_type', [0, 1])
//                     ->where('liveaccount.ib' . $i, '=', $ib_email)
//                     ->groupBy(
//                         'ib1_commission.order_id'
//                     )
//                     ->orderByDesc('ib1_commission.id')
//                     ->get();
//             //dd($client_live_accs);
//                 foreach ($client_live_accs as $ca) {
//                     // $ib_level = collect(range(1, 15))->takeWhile(fn($iter) => $ca->{'ib' . $iter} !== null)->count();
//                     $ib_level = 0;
//                     $is_break = 0;
//                     for ($iter = 1; $iter <= 15; $iter++) {
//                         if ($is_break == 0) {
//                             $ib_stage = "ib" . $iter;
//                             if ($ca->$ib_stage != NULL && $ca->$ib_stage != "" && $ca->$ib_stage != "noIB") {
//                                 $ib_level = $ib_level + 1;
//                             } else {
//                                 $is_break = 1;
//                             }
//                         }
//                     }
                   

//                     $commission = $ib_acc_plans[$ca->account_type][$ib_level]["d$i"] ?? null;
// //  dd($commission);
//                     if ($commission) {
//                         $raw_com = $commission;
//                         $ib_level_name = "IB Level" . $ib_level . " - D" . $i;
//                         $ib_wallet = ((float) $commission / 2) * $ca->volume;
//                         IbWallet::create([
//                             'ib_wallet' => $ib_wallet,
//                             'email' => $ib_email,
//                             'trade_id' => $ca->login,
//                             'order_id' => $ca->order_id,
//                             'remark' => $ca->client_email,
//                             'ib_level' => $ib_level_name,
//                             'comission_per_lot' => $raw_com
//                         ]);
//                     }
//                 }
//             }
//         }
 
//         $ib_clients_total = NULL;
//         $ib_wallet_raw = NULL;
//         $live_accs = [];
//         $ib_clients = [];
//         $ib_clients_total = User::where(function ($query) use ($email) {
//             for ($i = 1; $i <= 15; $i++) {
//                 $query->orWhere("ib{$i}", $email);
//             }
//         })->distinct('email')->count('email');

//         $ib_wallet_raw = IbWallet::where('email', $email)
//             ->selectRaw('SUM(ib_wallet) as wallet, SUM(ib_withdraw) as withdraw')
//             ->first();
//         $ib_wallet = 0.00;
//         if ($ib_wallet_raw) {
//             $ib_wallet = $ib_wallet_raw->wallet - $ib_wallet_raw->withdraw;
//         }
//         $live_accs = LiveAccount::where('email', $email)
//             ->orderBy('id', 'desc')
//             ->get();
//         for ($i = 1; $i <= 7; $i++) {
//             $ib_clients[$i] = IbClientList::where("ib$i", $email)->get();
//         }
//         // $histories = IbWallet::where('email', $email)->get();
//         // $histories = IbWallet::where('ib_wallet.email', $email)
//         //     ->leftjoin('ib1_commission', 'ib_wallet.order_id', '=', 'ib1_commission.order_id')
//         //     ->select('ib_wallet.*', 'ib1_commission.volume')
//         //     ->groupBy('ib_wallet.order_id')
//         //     ->get();
//         $histories = [];
// 	// dd($ib_wallet);
//         return view('ib-profile', compact('ib_clients_total', 'ib_wallet', 'live_accs', 'ib_clients', 'histories', 'ib_wallet_raw'));
//     }
    public function ibReference(Request $request)
    {
        if ($request->has('refercode')) {
            $refercode = $request->query('refercode');
            $decodedEmail = base64_decode($refercode);

            // Fetch the IB record using Eloquent or DB facade
            $result = DB::table('ib1')->where('email', $decodedEmail)->first();

            if ($result) {
                // Encode the IB details as required
                $ib1 = base64_encode($result->email);
                $ib2 = base64_encode($result->ib1);
                $ib3 = base64_encode($result->ib2);
                $ib4 = base64_encode($result->ib3);
                $ib5 = base64_encode($result->ib4);
                $ib6 = base64_encode($result->ib5);
                $ib7 = base64_encode($result->ib6);
                $ib8 = base64_encode($result->ib7);
                $ib9 = base64_encode($result->ib8);
                $ib10 = base64_encode($result->ib9);
                $ib11 = base64_encode($result->ib10);
                $ib12 = base64_encode($result->ib11);
                $ib13 = base64_encode($result->ib12);
                $ib14 = base64_encode($result->ib13);
                $ib15 = base64_encode($result->ib14);
                $countries = Country::all();
                $user_groups = UserGroup::where("is_visible", 1)->get();
                return view('auth.ib-ref', compact('countries', 'user_groups'));
            } else {
                return redirect()->route('register')->with('error', 'Invalid Refer Code');
            }
        } else {
            return redirect()->route('register')->with('error', 'Invalid Refer Code');
        }
    }
    public function processTransfer(Request $request)
    {
        if ($request->has('transfer')) {
            $amount = $request->input('amount');			
            $tradeId = $request->input('tradeId');
			
			$checkliveacc = LiveAccount::where('trade_id', $tradeId)->first();
			$email = $checkliveacc->email;			
            $useremail = auth()->user()->email;
			
            $balance = DB::table('ib_wallet')
                ->selectRaw('SUM(ib_wallet) as wallet, SUM(ib_withdraw) as withdraw')
                ->where('email', $useremail)
                ->first();
				
            $availableBalance = $balance->wallet - $balance->withdraw;
			
            if ($availableBalance >= $amount) {

                if (!$availableBalance || !$amount || !$tradeId) {
                    alert()->warning("Invalid Request", "Please Select / Enter valid values");
                    return redirect()->back();
                }

                $comment = 'IB Comm. - Dep';
                $ticket = null;
                $errorCode = $this->api->TradeBalance($tradeId, $type = MTEnDealAction::DEAL_BALANCE, $amount, $comment, $ticket, true);

                if ($errorCode != MTRetCode::MT_RET_OK) {
                    $error = MTRetCode::GetError($errorCode);
                    return redirect()->back()->with('error', 'Something went wrong on Deposit. ' . $error);
                } else {

				$datalogs = [
					'email' => $email,
                        'trade_id' => $tradeId,
                        'deposit_amount' => $amount,
                        'deposit_type' => 'IB Withdraw',
                        'deposit_from' => 'IB Commission',
                        'status' => 1
				];
                    // Insert into trade_deposit.
                    TradeDeposits::create([
                        'email' => $email,
                        'trade_id' => $tradeId,
                        'deposit_amount' => $amount,
                        'deposit_type' => 'IB Withdraw',
                        'deposit_from' => 'IB Commission',
                        'status' => 1
                    ]);
                    // Insert into ib_wallet for withdrawal.
                    IbWallet::create([
                        'email' => $useremail,
						'trade_id' => $tradeId,
                        'ib_withdraw' => $amount,
                        'remark' => 'IB Comm. Withdrawl'
                    ]);
					addIpLog(' IB Process Transfer', $datalogs);
					$checkliveacc->Balance = $checkliveacc->Balance + $amount;
					$checkliveacc->save();
					
                    return redirect()->back()->with('success', 'IB Balance is Transferred to ' . $tradeId);
                }
            } else {

                return redirect()->back()->with('error', 'Insufficient IB Transferrable Balance');
            }
        }
    }
    public function calculateIbCommission(Request $request)
    {
        $email = session('clogin');
        $ib = Ib1::where('email', session('clogin'))
            ->whereNotNull('acc_type')
            ->where('status', 1)
            ->first();
        $plan_id = $ib->acc_type;
        $ib_email = $email;
        if ($plan_id) {
            $ibPlans = IbPlanDetails::where('ib_plan_id', $plan_id)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get()
                ->toArray();
            $ib_acc_plans = [];
            foreach ($ibPlans as $plan) {
                $ib_acc_plans[$plan['acc_type']][$plan['level_id']] = [];
                for ($i = 1; $i <= $plan['level_id']; $i++) {
                    $ib_acc_plans[$plan['acc_type']][$plan['level_id']]["d$i"] = $plan["d$i"];
                }
            }
            for ($i = 1; $i <= 15; $i++) {
                DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

                $client_live_accs = DB::table('ib1_commission')
                    ->join('liveaccount', 'liveaccount.trade_id', '=', 'ib1_commission.login')
                    ->join('aspnetusers', 'aspnetusers.email', '=', 'ib1_commission.user_id')
                    ->leftJoin('ib_wallet', function ($join) use ($ib_email) {
                        $join->on('ib_wallet.order_id', '=', 'ib1_commission.order_id')
                            ->where('ib_wallet.email', '=', $ib_email);
                    })
                    ->select(
                        'aspnetusers.email as client_email',
                        'aspnetusers.ib1',
                        'aspnetusers.ib2',
                        'aspnetusers.ib3',
                        'aspnetusers.ib4',
                        'aspnetusers.ib5',
                        'aspnetusers.ib6',
                        'aspnetusers.ib7',
                        'aspnetusers.ib8',
                        'aspnetusers.ib9',
                        'aspnetusers.ib10',
                        'aspnetusers.ib11',
                        'aspnetusers.ib12',
                        'aspnetusers.ib13',
                        'aspnetusers.ib14',
                        'aspnetusers.ib15',
                        'ib1_commission.*',
                        'liveaccount.account_type'
                    )
                    ->where('ib1_commission.status', 0)
                    ->whereNull('ib_wallet.order_id')
                    ->where('aspnetusers.status', 1)
                    ->where('ib1_commission.order_type', 1)
                    //->whereIn('ib1_commission.order_type', [0, 1])
                    ->where('liveaccount.is_excluded', 0)
					->where('liveaccount.status', 'active')
                    ->where('aspnetusers.ib' . $i, '=', $ib_email)
                    ->groupBy(
                        'ib1_commission.order_id'
                    )
                    ->orderByDesc('ib1_commission.id')
                    ->get();
                foreach ($client_live_accs as $ca) {
                    $ib_level = collect(range(1, 15))->takeWhile(fn($iter) => $ca->{'ib' . $iter} !== null)->count();
                    $commission = $ib_acc_plans[$ca->account_type][$ib_level]["d$i"] ?? null;
                    if ($commission) {
                        $ib_level_name = "IB Level $ib_level - D$i";
                        $ib_wallet = ((float) $commission / 2) * $ca->volume;
						$datalogs = [
                        'ib_wallet' => $ib_wallet,
                            'email' => $ib_email,
                            'trade_id' => $ca->login,
                            'order_id' => $ca->order_id,
                            'remark' => $ca->client_email,
                            'ib_level' => $ib_level_name,
						];
                        IbWallet::create([
                            'ib_wallet' => $ib_wallet,
                            'email' => $ib_email,
                            'trade_id' => $ca->login,
                            'order_id' => $ca->order_id,
                            'remark' => $ca->client_email,
                            'ib_level' => $ib_level_name,
                        ]);

						addIpLog(' IB calculateIb Commission', $datalogs);
                    }
                }
            }
        }
    }
    public function getCommissionHistory()
    {
        header('Content-Type: application/json');
        $email = session('clogin');
        $histories = IbWallet::where('ib_wallet.email', $email)
            ->leftjoin('ib1_commission', 'ib_wallet.order_id', '=', 'ib1_commission.order_id')
            ->select('ib_wallet.*', 'ib1_commission.volume','ib1_commission.order_type')
			//->where('ib1_commission.order_type', 1)
			->whereIn('ib1_commission.order_type', [0, 1])
            ->groupBy('ib_wallet.order_id')
			->orderBy('ib1_commission.time_closed', 'desc')
            ->get();
			echo'<pre>';print_r($histories);exit;
        echo json_encode(value: ['data' => $histories]);
    }
}
