<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\WalletDeposit;
use App\Models\WalletWithdraw;
use App\Models\TotalBalance;
use App\Models\LiveAccount;
use App\Models\DemoAccount;
use App\Models\Promotation;
use App\Models\BonusModel;
use App\Models\Ib1;
use App\Models\TournamentLiveAccount;
use DB;
use Carbon\Carbon;

class Home extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function dashboard()
    {
		$user = auth()->user();		
		$email = $user->email;
		$group_id = $user->group_id;
		
        $walletBalance = $this->getWalletBalance($email);
		$totalWalletDeposit = $this->getWalletDeposit($email);
        $totalDeposit = $this->getTotalDeposit($email);
        $totalWithdrawal = $this->getTotalWithdrawal($email);
        $totalWalletWithdrawal = $this->getWalletWithdrawal($email);        
		$totalA2ATransfer = $this->getA2ATransfer($email);
        $totalC2CTransfer = $this->getC2CTransfer($email);
        $liveAccounts = $this->getLiveAccountCount($email);
        $liveAccountDetails = $this->getLiveAccountDetails($email);
        $demoAccountDetails = $this->getDemoAccountDetails($email);
        $tournamentAccountDetails = $this->getTournamentAccountDetails($email);
        $ibResult = $this->getIb1Details($email);
		$today = Carbon::today();
		$promodata = Promotation::where('status', 1)
			->whereDate('promo_starts_at', '<=', $today)
			->whereDate('promo_ends_at', '>=', $today)
			->orderBy('promo_id', 'DESC')
			->get();
		
		/*Get my bonus details*/
		$accountTypes = DB::table('account_types')
			->where('user_group_id', $group_id)
			->pluck('ac_index');

		$acIndexes = $accountTypes->unique()->toArray();
		$bonusesdata = BonusModel::where('status', 1)
			->where(function ($q) use ($email, $acIndexes) {   // <-- FIXED

			// 1️⃣ All Users
			$q->where('bonus_shows_on', 'all')

			// 2️⃣ User Email Based Bonus
			->orWhere(function ($q2) use ($email) {
				$q2->where('bonus_shows_on', 'users')
				   ->whereRaw("FIND_IN_SET(?, bonus_show_list)", [$email]);
			})

			// 3️⃣ Group / Account-Type Based Bonus
			->orWhere(function ($q3) use ($acIndexes) {
				if (!empty($acIndexes)) {
					foreach ($acIndexes as $idx) {
						$q3->orWhereRaw("FIND_IN_SET(?, bonus_show_list)", [$idx]);
					}
				}
			});
		})
		->whereDate('bonus_starts_at', '<=', now())
		->whereDate('bonus_ends_at', '>=', now())
		->orderBy('bonus_id', 'DESC')
		->get();
        $datalogs = [
             'walletBalance' => $walletBalance,
            'totalDeposit' => $totalDeposit,
            'totalWalletDeposit' => $totalWalletDeposit,
            'totalWithdrawal' => $totalWithdrawal,
            'totalWalletWithdrawal' => $totalWalletWithdrawal,
            'totalA2ATransfer' => $totalA2ATransfer,
            'totalC2CTransfer' => $totalC2CTransfer,
            'liveAccounts' => $liveAccounts,
            'liveAccountDetails' => $liveAccountDetails,
            'demoAccountDetails' => $demoAccountDetails,
            'tournamentAccountDetails' => $tournamentAccountDetails,
            'ibResult' => $ibResult,
            'promodata' => $promodata,
            'bonusesdata' => $bonusesdata,
            'user'=> $user
        ];

			addIpLog('Dashboard Logs', $datalogs);	
        return view('dashboard', [
            'walletBalance' => $walletBalance,
            'totalDeposit' => $totalDeposit,
            'totalWalletDeposit' => $totalWalletDeposit,
            'totalWithdrawal' => $totalWithdrawal,
            'totalWalletWithdrawal' => $totalWalletWithdrawal,
            'totalA2ATransfer' => $totalA2ATransfer,
            'totalC2CTransfer' => $totalC2CTransfer,
            'liveAccounts' => $liveAccounts,
            'liveAccountDetails' => $liveAccountDetails,
            'demoAccountDetails' => $demoAccountDetails,
            'tournamentAccountDetails' => $tournamentAccountDetails,
            'ibResult' => $ibResult,
            'promodata' => $promodata,
            'bonusesdata' => $bonusesdata,
            'user'=> $user
        ]);
    }
    public function getWalletBalance($email)
    {
        $totalDeposit = WalletDeposit::where('email', $email)
            ->where('status', 1)
            ->sum('deposit_amount');
        $totalWithdraw = WalletWithdraw::where('email', $email)
            ->whereIn('status', [0,1])
            ->sum('withdraw_amount');
        $walletBalance = $totalDeposit - $totalWithdraw;
        addIpLog('WalletBalance Home', $walletBalance);
        return $walletBalance;
    }
    public function getTotalDeposit($email)
    {
        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from trade_deposit trs WHERE trs.status=1 and trs.email='" . $email . "' and trs.deposit_type NOT IN('Wallet Transfer', 'Wallet Payments', 'W2A Deposit', 'A2A Transfer')";
        $trade_deposit = DB::select($sql)[0];

        $sql = "select COALESCE(SUM(trs.deposit_amount), 0) as deposit from wallet_deposit trs WHERE trs. status=1 and trs.email='" . $email . "' and trs.deposit_type NOT IN('Wallet Transfer', 'A2A Transfer', 'A2W withdraw', 'A2W Deposit')";
        $wallet_deposit = DB::select($sql)[0];

        $totalDeposit = $trade_deposit->deposit + $wallet_deposit->deposit;
        addIpLog('TotalDeposit Home', $totalDeposit);
        return $totalDeposit;
    }
	
	public function getWalletDeposit($email){
		$wallet_deposit = DB::table('wallet_deposit as trs')
			->select(DB::raw('COALESCE(SUM(trs.deposit_amount),0) as deposit'))
			->whereNotIn('trs.deposit_type', ['A2A Transfer', 'A2W withdraw', 'A2W Deposit'])
			->where('trs.email', $email)
			->where('trs.status', 1)
			->get();
               addIpLog('WalletDeposit Home', $wallet_deposit);		
		return $wallet_deposit[0]->deposit;
     
	}
	
    public function getTotalWithdrawal($email)
    {
        $sql = "select COALESCE(SUM(trs.withdrawal_amount), 0) as withdraw from trade_withdrawal trs WHERE trs.status=1 and  trs.email='" . $email . "' and  trs.withdraw_type NOT IN('Wallet Withdrawal', 'A2W Deposit', 'A2A Transfer')";
        $trade_withdrawal = DB::select($sql)[0]; 
		
		$wallet_withdrawal = DB::table('wallet_withdraw as trs')
			->select(DB::raw('COALESCE(SUM(trs.withdraw_amount),0) as withdraw'))
			->where('trs.email', $email)
			->whereIn('trs.status', [1])
			->whereIn('trs.withdraw_type', ['Wallet Withdrawal', 'External Withdrawal'])
			->get();

        $totalWithdrawal = 0 + $wallet_withdrawal[0]->withdraw;
        addIpLog('TotalWithdrawal Home', $totalWithdrawal);	
        return $totalWithdrawal;
    }
	
	public function getWalletWithdrawal($email){
		$wallet_withdrawal = DB::table('wallet_withdraw as trs')
			->select(DB::raw('COALESCE(SUM(trs.withdraw_amount),0) as withdraw'))
			->where('trs.email', $email)
			->whereNotIn('trs.withdraw_type', ['W2A Deposit'])
			->whereIn('trs.status', [1])
			->get();
		return $wallet_withdrawal[0]->withdraw;
	}
	
	public function getA2ATransfer($email){
		$a2a_transfer = DB::table('trade_withdrawal as trs')
			->select(DB::raw('COALESCE(SUM(trs.withdrawal_amount),0) as a2awithdraw'))
			->whereIn('trs.withdraw_type', ['A2A Transfer', 'W2A Deposit', 'A2W withdraw'])
			->where('trs.email', $email)
			->where('trs.status', 1)
			->get();
		return $a2a_transfer[0]->a2awithdraw;
	}
	
	public function getC2CTransfer($email){
		$c2c_transfer = DB::table('wallet_totransfer as trs')
			->select(DB::raw('COALESCE(SUM(trs.transfer_amount),0) as c2cwithdraw'))
			->where('trs.wallet_from', $email)
			->where('trs.status', 'Success')
			->get();
		return $c2c_transfer[0]->c2cwithdraw;
	}	
	
    public function getLiveAccountCount($email)
    {
        $liveAccountsCount = LiveAccount::where('email', $email)->where('status', 'active')->count();
        return $liveAccountsCount;
    }
    public function getLiveAccountDetails($email)
    {
		
		$user = Auth::user();

		return LiveAccount::with('accountType:ac_index,ac_name,ac_group')
			->where('email', $user->email)
			->where('status', 'active')
			->orderByDesc('id')
			->get(['leverage', 'currency', 'balance', 'equity', 'id as id', 'trade_id', 'tradePlatform', 'Registered_Date', 'account_type']);
		
        /*$liveaccount_details = LiveAccount::with(['accountType:ac_index,ac_name,ac_group'])
            ->where('email', $email)
			->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get(['leverage', 'currency', 'balance', 'equity', 'id as id', 'trade_id', 'tradePlatform', 'Registered_Date', 'account_type']);
        return $liveaccount_details;*/
    }
    public function getDemoAccountDetails($email)
    {
        $demoaccount_details = DemoAccount::with(['accountType:ac_index,ac_name,ac_group'])
            ->where('email', $email)
			->where('status', 'active')
            ->orderBy('id', 'desc')
            ->get(['leverage', 'currency', 'balance', 'equity', 'id as id', 'trade_id', 'tradePlatform', 'Registered_Date', 'account_type']);
        return $demoaccount_details;
    }
    public function getTournamentAccountDetails($email)
    {
        $tournamentaccount_details = TournamentLiveAccount::with('accountType')
            ->where('email', $email)
            ->orderBy('id', 'desc')
            ->get(['leverage', 'currency', 'balance', 'equity', 'id as id', 'trade_id', 'tradePlatform', 'Registered_Date']);
        return $tournamentaccount_details;
    }
    public function getIb1Details($email)
    {
        $ibResult = Ib1::where('email', $email)
            ->where('status', 1)
            ->first();
        return $ibResult;
    }
}
