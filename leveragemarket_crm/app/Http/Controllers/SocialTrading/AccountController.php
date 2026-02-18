<?php

namespace App\Http\Controllers\SocialTrading;

use App\Http\Controllers\Controller;
use App\Models\LiveAccount;
use App\Models\STAccounts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{
    public function generateSignature($body, $path, $timestamp)
    {
        $secretKey = env("ST_SECTET");
        $requestPath = parse_url($path, PHP_URL_PATH);
        $bodyString = $body ?? "";

        $payloadString = $timestamp . $requestPath . $bodyString;
        $signature = base64_encode(hash_hmac('sha256', $payloadString, base64_decode($secretKey), true));

        return $signature;
    }
    public function accounts(Request $request)
    {
        return view("admin.social-trading.accounts");
    }

    public function getAccounts(Request $request)
    {
        if ($request->ajax()) {
            $now = now()->toIso8601String();
            $signature = $this->generateSignature("", '/api/v1/accounts', $now);
            // dd($signature);
            $response = Http::withHeaders([
                'api_key' => env("ST_API"),
                'signature' => $signature,
                'timestamp' => $now,
            ])->get('http://195.181.165.250:5100/api/v1/accounts');

            $list = $response->json();
            if ($list) {
                $list = $list["result"];
            }
            $data["data"] = $list;

            return json_encode($data);
        } else {
            return response(["status" => false, "message" => "UnAuthorized Access"], 401);
        }
    }


    public function store_user(Request $request)
    {
        if ($request->ajax()) {
            $req = $request->except("_token");
            $user = LiveAccount::where("trade_id", $req["login"])->first();
            if (!$user) {
                return response(["status" => false, "message" => $req["login"] . " - Not Exist in CRM"]);
            }
            $body = '{
                "login": "' . $req["login"] . '",
                "serverId": 1,
                "userId": ' . $req["user_id"] . ',
                "canBeLeader": ' . (($req["canBeLeader"] == 1) ? "true" : "false") . ',
                "leaderBio": "' . (($req["canBeLeader"] == 1) ? $user->email : NULL) . '",
                "leaderFeeSubscriptionAmount": ' . (isset($req["leaderFeeSubscriptionAmount"]) ? $req["leaderFeeSubscriptionAmount"] : "0") . ',
                "leaderFollowingFeePercent": ' . (isset($req["leaderFollowingFeePercent"]) ? $req["leaderFollowingFeePercent"] : "0") . ',
                "leaderFollowingMinFreeMargin": ' . (isset($req["leaderFollowingMinFreeMargin"]) ? $req["leaderFollowingMinFreeMargin"] : "0") . ',
                "leaderPerformanceFeeType": ' . (isset($req["leaderPerformanceFeeType"]) ? $req["leaderPerformanceFeeType"] : "0") . ',
                "leaderPerformanceFeePercent": ' . (isset($req["leaderPerformanceFeePercent"]) ? $req["leaderPerformanceFeePercent"] : "0") . '
            }';
            // dd($body);

            $now = now()->toIso8601String();
            $signature = $this->generateSignature($body, '/api/v1/accounts', $now);

            $apiUrl = 'http://195.181.165.250:5100/api/v1/accounts';
            $headers = [
                'api_key: ' . env("ST_API"),
                'signature: ' . $signature,
                'timestamp: ' . $now,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]));

            $response = curl_exec($ch);
            // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $res = json_decode($response);
            // dd($res);
            if (isset($res->result)) {
                STAccounts::create([
                    'login' => $req["login"],
                    'serverId' => 1,
                    'userId' => $req["userId"],
                    'canBeLeader' => $req["canBeLeader"],
                    'leaderBio' => (($req["canBeLeader"] == 1) ? $user->email : ""),
                    'leaderFeeSubscriptionAmount' => (isset($req["leaderFeeSubscriptionAmount"]) ? $req["leaderFeeSubscriptionAmount"] : 0),
                    'leaderFollowingFeePercent' => (isset($req["leaderFollowingFeePercent"]) ? $req["leaderFollowingFeePercent"] : 0),
                    'leaderFollowingMinFreeMargin' => (isset($req["leaderFollowingMinFreeMargin"]) ? $req["leaderFollowingMinFreeMargin"] : 0),
                    'leaderPerformanceFeeType' => (isset($req["leaderPerformanceFeeType"]) ? $req["leaderPerformanceFeeType"] : 0),
                    'leaderPerformanceFeePercent' => (isset($req["leaderPerformanceFeePercent"]) ? $req["leaderPerformanceFeePercent"] : 0),
                    'updated_by_type' => "admin",
                    'updated_by' => session("alogin"),
                    "st_result_id" => $res->result
                ]);
                if ($req["canBeLeader"] == 0) {

                    $body = '{
                        "leaderId": ' . $req["leader_id"] . ',
                        "followerId": ' . $req["login"] . ',
                        "mode": ' . $req["mode"] . ',
                        "modeParameter": ' . $req["modeParameter"] . ',
                        "stopLoss": ' . $req["stopLoss"] . '
                    }';
                    // dd($body);

                    $now = now()->toIso8601String();
                    $signature = $this->generateSignature($body, '/api/v1/strategies', $now);

                    $apiUrl = 'http://195.181.165.250:5100/api/v1/strategies';
                    $headers = [
                        'api_key: ' . env("ST_API"),
                        'signature: ' . $signature,
                        'timestamp: ' . $now,
                    ];

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $apiUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
                        'Content-Type: application/json',
                        'Accept: application/json',
                    ]));

                    $response = curl_exec($ch);
                    // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    $res = json_decode($response);
                    if (isset($res->result)) {

                        return response(["status" => true, "message" => $request->login . " - Account & Strategy Created."]);
                    }
                }
                return response(["status" => true, "message" => $request->login . " - Account created."]);
            } else {
                if (isset($res->error)) {
                    return response(["status" => false, "message" => $res->error]);
                }
            }
        } else {
            return response(["status" => false, "message" => "UnAuthorized Access"], 401);
        }
    }
    public function update_user(Request $request)
    {
        if ($request->ajax()) {
            $body = '{
                "password": "' . $request->st_password . '"
            }';
            // dd($body);
            $now = now()->toIso8601String();
            $signature = $this->generateSignature($body, '/api/v1/users/' . $request->user_id, $now);

            $apiUrl = 'http://195.181.165.250:5100/api/v1/users/' . $request->user_id;
            $headers = [
                'api_key: ' . env("ST_API"),
                'signature: ' . $signature,
                'timestamp: ' . $now,
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, [
                'Content-Type: application/json',
                'Accept: application/json',
            ]));

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $res = json_decode($response);
            // dd($response,$res,$httpCode);
            if ($httpCode == 200) {
                STUser::where("st_result_id", $request->user_id)->update([
                    "st_password" => $request->password,
                    "updated_by" => session("alogin"),
                ]);
                return response(["status" => true, "message" => "User Password updated."]);
            } else {
                if (isset($res->error)) {
                    return response(["status" => false, "message" => $res->error]);
                }
                return response(["status" => false, "message" => "Something went wrong"], 500);
            }
        } else {
            return response(["status" => false, "message" => "UnAuthorized Access"], 401);
        }
    }
}
