<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class Bonus extends Controller
{
    public function getBonus(Request $request)
    {
        if ($request->has('type') && $request->type == 'getbonus') {
            $eid = session('clogin');
            $account_type = 0;
            $status = 1;

            $accountTypeQuery = DB::table('liveaccount')
                ->where('trade_id', $request->id)
                ->select('account_type')
                ->first();
            $account_type = $accountTypeQuery ? $accountTypeQuery->account_type : 0;
            $bonusTaken = DB::table('bonus_trans')
                ->select(DB::raw('bonus_id, count(id) as bonus_limit'))
                ->where('email', $eid)
                ->where('status', 1)
                ->groupBy('bonus_id')
                ->get();

            $bonusAvailable = DB::table('bonuses')
                ->where(function ($query) use ($eid, $account_type) {
                    $query->whereRaw('FIND_IN_SET(?, bonus_show_list) > 0 AND bonus_shows_on = "users"', [$eid])
                        ->orWhereRaw('FIND_IN_SET(?, bonus_show_list) > 0 AND bonus_shows_on = "groups"', [$account_type])
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
addIpLog('Wallet Transfer request', $account_type);
            return response()->json($filteredBonuses);
        }
        return response()->json(['error' => 'Invalid request type'], 400);
    }
	
	
}
