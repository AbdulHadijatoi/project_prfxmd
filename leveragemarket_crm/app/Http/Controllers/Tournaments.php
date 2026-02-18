<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TournamentLiveAccount;
use Illuminate\Http\Request;
use DB;
use App\Models\TournamentModel as Tournament;
use App\Services\MT5Service;
use App\MT5\MTRetCode;
use App\MT5\MTEnDealAction;
use App\Models\TournamentLiveAccount as LiveAccount;
use App\Models\User;
use App\Services\MailService as MailService;
use App\Models\TournamentOrders as Orders;
use App\Models\UserGroup;

class Tournaments extends Controller
{
    protected $api;
    protected $mailService;
    protected $mt5Service;
    public function __construct(MT5Service $mt5Service, MailService $mailService)
    {
        $this->mailService = $mailService;
        $this->mt5Service = $mt5Service;
        $this->mt5Service->connect();
        $this->api = $this->mt5Service->getApi();
    }
    public function index()
    {
        $eid = session('clogin');
        $user_groups = UserGroup::find(session('user')['group_id']);
        $user_group_id=$user_groups['user_group_id'];

        $account_type = 0;
        $status = 1;
        $account_types = DB::table('liveaccount')
            ->where('email', $eid)
            ->distinct()
            ->pluck('account_type')
            ->toArray();
        $tournaments = DB::table('tournaments')
            ->select('tournaments.*', 'tournament_liveaccount.trade_id')
            ->leftjoin('tournament_liveaccount', 'tournaments.id', '=', 'tournament_liveaccount.tournament_id')
            ->where(function ($query) use ($eid, $account_types,$user_group_id) {
                $query->whereRaw('FIND_IN_SET(?, tournaments.shows_list) > 0 AND tournaments.shows_on = "users"', [$eid])
                    ->orWhere(function ($subQuery) use ($account_types) {
                        foreach ($account_types as $account_type) {
                            $subQuery->orWhereRaw('FIND_IN_SET(?, tournaments.shows_list) > 0 AND tournaments.shows_on = "groups"', [$account_type]);
                        }
                    })
                    ->orWhere(function ($subQuery) use ($user_group_id) {
                        $subQuery->whereRaw('FIND_IN_SET(?, tournaments.shows_list) > 0 AND tournaments.shows_on = "user_groups"', [$user_group_id]);
                    })
                    ->orWhere('tournaments.shows_on', 'all');
            })
            ->where('tournaments.status', 1)
            ->whereRaw('NOW() BETWEEN tournaments.starts_at AND tournaments.ends_at')
            ->orderBy('tournaments.id', 'desc')
            ->get();
        return view('tournaments', compact('tournaments'));
    }
    public function details(Request $request)
    {
        $id = $request->input('id');
        $tournament = DB::table('tournaments')
            ->select('tournaments.*', 'tournament_liveaccount.trade_id')
            ->leftJoin('tournament_liveaccount', 'tournaments.id', '=', 'tournament_liveaccount.tournament_id')
            ->whereRaw('MD5(tournaments.id) = ?', [$id])
            ->first();
        return view('tournament_details', compact('tournament'));
    }
    public function enroll(Request $request)
    {
        $id = $request->input('id');

        $tournament = DB::table('tournaments')
            ->leftJoin('account_types', 'tournaments.account_type', '=', 'account_types.ac_index')
            ->whereRaw('MD5(tournaments.id) = ?', [$id])
            ->first();

        $settings = settings();
        $user = User::where('email', session('clogin'))->firstOrFail();

        $enrolled = LiveAccount::where('tournament_id', $tournament->id)->first();
        if ($enrolled) {
            return response()->json([
                'message' => 'Already enrolled in the tournament',
            ], 400);
        }


        $new_user = $this->api->UserCreate();
        $new_user->MainPassword = $this->generatePassword();
        $new_user->Group = $tournament->ac_group;
        $new_user->Leverage = $tournament->leverage;
        $new_user->ZipCode = $user->zipcode;
        $new_user->Country = $user->country;
        $new_user->State = $user->state;
        $new_user->City = $user->city;
        $new_user->Address = $user->address;
        $new_user->Phone = $user->number;
        $new_user->Currency = 'USD';
        $new_user->Status = 1;
        $new_user->Company = $settings['mt5_company_name'];
        $new_user->Name = $user->fullname;
        $new_user->Email = session('clogin');
        $new_user->LeadSource = ($user->ib1 == 'noIB') ? "" : $user->ib1;
        $new_user->PhonePassword = $this->generatePassword();
        $new_user->InvestPassword = $this->generatePassword();
        $new_user->Login = $this->generateRandomNumber();
        $response = $this->CreateAccount($new_user, $user_server, 'Live');
        $datalog = [
            'tournament_id' => $tournament->id,
                'email' => $new_user->Email,
                'name' => $new_user->Name,
                'trade_id' => $new_user->Login,
                'account_type' => $tournament->account_type,
                'leverage' => $new_user->Leverage,
                'currency' => $new_user->Currency,
                'trader_pwd' => $new_user->MainPassword,
                'invester_pwd' => $new_user->InvestPassword,
                'phone_pwd' => $new_user->PhonePassword,
                'ib1' => $new_user->LeadSource,
                'ib2' => $user->ib2,
                'ib3' => $user->ib3,
                'ib4' => $user->ib4,
                'ib5' => $user->ib5,
                'ib6' => $user->ib6,
                'ib7' => $user->ib7,
                'ib8' => $user->ib8,
                'ib9' => $user->ib9,
                'ib10' => $user->ib10,
                'ib11' => $user->ib11,
                'ib12' => $user->ib12,
                'ib13' => $user->ib13,
                'ib14' => $user->ib14,
                'ib15' => $user->ib15
        ];
        if ($response['status']) {
            LiveAccount::create([
                'tournament_id' => $tournament->id,
                'email' => $new_user->Email,
                'name' => $new_user->Name,
                'trade_id' => $new_user->Login,
                'account_type' => $tournament->account_type,
                'leverage' => $new_user->Leverage,
                'currency' => $new_user->Currency,
                'trader_pwd' => $new_user->MainPassword,
                'invester_pwd' => $new_user->InvestPassword,
                'phone_pwd' => $new_user->PhonePassword,
                'ib1' => $new_user->LeadSource,
                'ib2' => $user->ib2,
                'ib3' => $user->ib3,
                'ib4' => $user->ib4,
                'ib5' => $user->ib5,
                'ib6' => $user->ib6,
                'ib7' => $user->ib7,
                'ib8' => $user->ib8,
                'ib9' => $user->ib9,
                'ib10' => $user->ib10,
                'ib11' => $user->ib11,
                'ib12' => $user->ib12,
                'ib13' => $user->ib13,
                'ib14' => $user->ib14,
                'ib15' => $user->ib15
            ]);

            addIpLog('Tournaments details', $datalog);
            if ($tournament->send_notification == 1) {
                $this->sendMail($new_user, 'Live', $tournament->email_description);
            }
            return response()->json(['success' => 'Enrolled Successfully']);
        } else {
            return response()->json([
                'message' => 'Error enrolling tournament',
            ], 400);
        }
    }


    function CreateAccount($user, &$user_server, $type)
    {
        $settings = settings();
        if (!$this->api->IsConnected()) {
            $errorCode = $this->api->Connect(
                $settings['mt5_server_ip'],
                $settings['mt5_server_port'],
                300,
                $settings['mt5_server_web_login'],
                $settings['mt5_server_web_password']
            );
            if ($errorCode != MTRetCode::MT_RET_OK) {
                $error = MTRetCode::GetError($errorCode);
                return ["status" => false, "message" => $error];
            }
        }
        if (($error_code = $this->api->UserAdd($user, $user_server)) != MTRetCode::MT_RET_OK) {
            $error = MTRetCode::GetError($error_code);
            return ["status" => false, "message" => $error];
        } else {
            return ["status" => true, "message" => $type . " Account Created Successfully"];
        }
    }

    public function sendMail($new_user, $type, $email_content)
    {
        $settings = settings();
        $toEmail = $new_user->Email;
        $from = $settings['email_from_address'];
        $emailSubject = $settings['admin_title'] . ' - ' . $type . ' Account Details';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From:' . $settings['admin_title'] . '<' . $from . '>' . "\r\n";
        $content = '<div>
        <span>Your Live
            MT5 account has been set up,
            and you are all ready to
            dive into the dynamic world
            of trading.</span>
    </div>
    <div><span></span></div>';
        if (!empty($email_content)) {
            $content = $email_content;
        }
        $content .= '
    <div>
        <span>Here’s your MT5 Account
            Details</span>
    </div></br><div style="font-size:20px;"><b>ACCOUNT ID: </b>' . $new_user->Login . '</div>
          </br><div style="font-size:20px;"><b>MASTER PASSWORD: </b>' . $new_user->MainPassword . '</div>
          </br><div style="font-size:20px;"><b>INVESTOR PASSWORD: </b>' . $new_user->InvestPassword . '</div>
          </br><div style="font-size:20px;"><b>LEVERAGE: </b>' . $new_user->Leverage . '</div>
          </br><div style="font-size:20px;"><b>MT5 SERVER: </b>' . $settings['mt5_company_name'] . '</div>';
          $datalog = [
             'name' => $new_user->Name,
            'type' => $type,
            'trade_id' => $new_user->Login,
            'trader_pwd' => $new_user->MainPassword,
            'investor_pwd' => $new_user->InvestPassword,
            'leverage' => "1:" . $new_user->Leverage,
            'server_name' => $settings['mt5_company_name'],
            'email' => $settings['email_from_address'],
            "title_right" => "Get Started With",
            "subtitle_right" => "New " . $type . " MT5 Account",
            "content" => $content
          ];
        $templateVars = [
            'name' => $new_user->Name,
            'type' => $type,
            'trade_id' => $new_user->Login,
            'trader_pwd' => $new_user->MainPassword,
            'investor_pwd' => $new_user->InvestPassword,
            'leverage' => "1:" . $new_user->Leverage,
            'server_name' => $settings['mt5_company_name'],
            'email' => $settings['email_from_address'],
            "title_right" => "Get Started With",
            "subtitle_right" => "New " . $type . " MT5 Account",
            "content" => $content
        ];
         addIpLog('Tournaments Send mail ', $datalog);
        $this->mailService->sendEmail($toEmail, $emailSubject, $headers, '', $templateVars);

    }
    public function generatePassword($length = 9)
    {
        // Define character pools
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#';
        // Ensure at least one character from each pool is included
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        $password .= $specialChars[rand(0, strlen($specialChars) - 1)];
        // Combine all pools for the remaining characters
        $allCharacters = $uppercase . $lowercase . $numbers . $specialChars;
        // Generate the remaining characters
        for ($i = 4; $i < $length; $i++) {
            $password .= $allCharacters[rand(0, strlen($allCharacters) - 1)];
        }
        // Shuffle the password to avoid predictable patterns
        $password = str_shuffle($password);
        return $password;
    }
    function generateRandomNumber($length = 6)
    {
        $min = pow(10, $length - 1); // Minimum value for an 8-digit number (10000000)
        $max = pow(10, $length) - 1;  // Maximum value for an 8-digit number (99999999)
        return rand($min, $max);
    }
    public function getTradeHistory(Request $request)
    {
        $login = $request->input('trade_id');
        $from = 'September 01,2024';
        $to = 'March 31,2080';
        $total = 0;
        if (($error_code = $this->api->PositionGetTotal($login, $total)) != MTRetCode::MT_RET_OK) {
            return response()->json(['error' => MTRetCode::GetError($error_code)]);
        }
        $open_order_history = $total;
        $offset = 0;
        $positions = [];
        if (($error_code = $this->api->PositionGetPage($login, $offset, $total, $positions)) != MTRetCode::MT_RET_OK) {
            return response()->json(['error' => MTRetCode::GetError($error_code)]);
        }

        echo json_encode(['data' => $positions]);
    }

    public function leaderBoard()
    {
        $this->updateHistory();
        $leaderboard = Orders::with('user')->select(
            'email',
            DB::raw('SUM(profit) as profit'))
            ->groupBy('email')
            ->orderBy('profit', 'desc')
            ->limit(10)
            ->get();
        return view('leaderboard', compact('leaderboard'));
    }
    public function updateHistory()
    {
        $from = 'September 01,2024';
        $to = 'March 31,2080';
        $total = 0;
        $positions = [];
        $eid = session('clogin');
        $tournaments = TournamentLiveAccount::all();
        foreach ($tournaments as $tournament) {
            $login = $tournament->trade_id;
            if (($error_code = $this->api->DealGetTotal($login, $from, $to, $total)) != MTRetCode::MT_RET_OK) {
                continue;
            }
            $open_order_history = $total;
            $offset = 0;
            $positions = [];
            if (($error_code = $this->api->DealGetPage($login, $from, $to, $offset, $total, $orders)) != MTRetCode::MT_RET_OK) {
                continue;
            }
            // dd($orders);
            if ($orders) {
                foreach ($orders as $order) {
                    if (in_array($order->Action, [0, 1])) {
                        $time = gmdate("Y-m-d H:i:s", $order->Time);
                        Orders::updateOrCreate(
                            [
                                'order_id' => $order->Order,
                            ],
                            [
                                'tournament_id' => $tournament->tournament_id,
                                'email' => $eid,
                                'action' => $order->Action,
                                'login' => $order->Login,
                                'deal_id' => $order->Deal,
                                'symbol' => $order->Symbol,
                                'time' => $time,
                                'lot' => $order->Volume,
                                'contract_size' => $order->ContractSize,
                                'profit' => $order->Profit,
                            ]
                        );
                    }
                }
            }
        }
    }

}
