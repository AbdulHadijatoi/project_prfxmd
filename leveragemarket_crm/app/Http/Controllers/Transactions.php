<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradeDeposits;
use App\Models\TradeWithdrawals;
use App\Models\InternalTransfer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class Transactions extends Controller
{
    public function index(Request $request)
    {
        $email = $email = auth()->user()->email;
        $deposit_history = TradeDeposits::with('liveAccount.accountType')
            ->where('email', $email)
            ->where('deposit_type', '!=', 'Internal Transfer')
            ->orderBy('id', 'desc')
            ->get();

        // Fetching withdrawal history
        $withdrawal_history = TradeWithdrawals::where('email', $email)
            ->where('withdraw_type', '!=', 'Internal Transfer')
            ->orderBy('id', 'desc')
            ->get();

        // Fetching internal transfers
        $internal_transfer = InternalTransfer::where('email', $email)
            ->whereIn('type', ['Internal Transfer'])
            ->orderBy('raw_id', 'desc')
            ->get();
			
		// -----------------------------
		// DATE FILTER LOGIC
		// -----------------------------
		$from = $request->from;
		$to   = $request->to;

		if ($request->filter == 'today') {
			$from = Carbon::today();
			$to   = Carbon::today()->endOfDay();
		}

		if ($request->filter == 'week') {
			$from = Carbon::now()->startOfWeek();
			$to   = Carbon::now()->endOfWeek();
		}

		if ($request->filter == 'month') {
			$from = Carbon::now()->startOfMonth();
			$to   = Carbon::now()->endOfMonth();
		}

		$deposit = TradeDeposits::select([
				'id',
				'trade_id',
				DB::raw('deposit_amount as amount'),
				'Status',
				DB::raw('deposted_date as created_at'),
				DB::raw("deposit_type as particulars"),
				DB::raw("0 as debit"),
				DB::raw("deposit_amount as credit")
			])
			->where('email', $email);

		$withdraw = TradeWithdrawals::select([
				'id',
				'trade_id',
				DB::raw('withdrawal_amount as amount'),
				'Status',
				DB::raw('withdraw_date as created_at'),
				DB::raw("withdraw_type as particulars"),
				DB::raw("withdrawal_amount as debit"),
				DB::raw("0 as credit")
			])
			->where('email', $email);

		$ledgerQuery = $deposit->unionAll($withdraw);
		$baseLedger = DB::query()->fromSub($ledgerQuery, 'ledger');
		if ($from && $to) {
			$baseLedger->whereBetween('created_at', [$from, $to]);
		}
					
		$totalCredit = TradeDeposits::where('email', $email)
			->where('deposit_type', '!=', 'A2A Transfer')
			->sum('deposit_amount');
			
		$totalDebit = TradeWithdrawals::where('email', $email)
			->where('withdraw_type', '!=', 'A2A Transfer')
			->sum('withdrawal_amount');
			
		$a2aTotal = TradeWithdrawals::where('email', $email)
			->where('withdraw_type', 'A2A Transfer')
			->when($from && $to, fn($q)=>
				$q->whereBetween('withdraw_date', [$from, $to])
			)
			->sum('withdrawal_amount');
			
		$ledger = $baseLedger
			->orderByDesc('created_at')
			->paginate(25)
			->withQueryString();
		$datalog = [
			'email' => $email,
			'from' => $from,
			'to' => $to,
			'filter' => $request->filter 
		];
		addIpLog('Payment Transactions View', $datalog);
        return view('transactions', [
			'ledger' => $ledger,
			'totalDebit' => $totalDebit,
			'totalCredit' => $totalCredit,
			'a2aTotal' => $a2aTotal
		]);
    }
}
