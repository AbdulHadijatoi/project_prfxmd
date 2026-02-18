<?php

namespace App\Http\Controllers\SocialTrading;

use App\Http\Controllers\Controller;
use App\Models\LiveAccount;
use App\Models\STUser;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;


class UsersController extends Controller
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
    public function users(Request $request)
    {
        return view("admin.social-trading.users");
    }

    public function getUsers(Request $request)
    {
        if ($request->ajax() || true) {
            $now = now()->toIso8601String();
            $signature = $this->generateSignature("", '/api/v1/users', $now);
            // dd($signature);
            $response = Http::withHeaders([
                'api_key' => env("ST_API"),
                'signature' => $signature,
                'timestamp' => $now,
            ])->get('http://195.181.165.250:5100/api/v1/users');

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
    public function getddUsers(Request $request)
    {
        if ($request->ajax()) {
            $term = NULL;
            if($request->term){
                $term = $request->term;
            }
            $now = now()->toIso8601String();
            $signature = $this->generateSignature("", '/api/v1/users', $now);
            // dd($signature);
            $response = Http::withHeaders([
                'api_key' => env("ST_API"),
                'signature' => $signature,
                'timestamp' => $now,
            ])->get('http://195.181.165.250:5100/api/v1/users');

            $list = $response->json();
            if ($list) {
                $list = $list["result"];
            }
            if($term != NULL){
                $src_list = [];
                foreach($list as $l){
                    if(str_contains($l["login"], $term)){
                        $src_list[] = $l;
                    }
                }
                $list = $src_list;
            }
            $data["data"] = $list;
            return json_encode($data);
        } else {
            return response(["status" => false, "message" => "UnAuthorized Access"], 401);
        }
    }

    public function store_user(Request $request)
    {
        // dd($request->all());
        if ($request->ajax()) {
            $email = NULL;
            if($request->user_id){
                $user = User::find($request->user_id);
                $email = $user->email;
            }
            else {
                return response(["status" => false, "message" => "User does not exist in CRM"]);
            }
            $body = '{
            "login": "' . $email . '",
            "password": "' . $request->password . '",
            "role": ' . $request->role . '}';

            $now = now()->toIso8601String();
            $signature = $this->generateSignature($body, '/api/v1/users', $now);

            $apiUrl = 'http://195.181.165.250:5100/api/v1/users';
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
                STUser::create([
                    "user_id" => $request->user_id,
                    "st_username" => $email,
                    "st_password" => $request->password,
                    "st_role" => $request->role,
                    "st_result_id" => $res->result,
                    "updated_by" => session("alogin"),
                ]);
                return response(["status" => true, "message" => $email . " user created."]);
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
            $signature = $this->generateSignature($body, '/api/v1/users/'.$request->user_id, $now);

            $apiUrl = 'http://195.181.165.250:5100/api/v1/users/'.$request->user_id;
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
                STUser::where("st_result_id",$request->user_id)->update([
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
