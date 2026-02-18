<?php

namespace App\Http\Controllers\Admin\pamm;

use App\Services\PammService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class MoneyManagerController extends Controller
{
    protected $oauthClient;
    protected $baseUrl;

    public function __construct(PammService $oauthClient)
    {
        $this->oauthClient = $oauthClient;
        $this->baseUrl = $this->oauthClient->getBaseUrl();
    }

    public function createMoneyManager(Request $request)
    {
        $password = '';
        $name = '';
        $data = [];
        if ($request->account_type == 'new') {
            $name = $request->name;
            $password = $request->password;
            if (!$this->validatePassword($password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one digit, and one special character.'
                ]);
            }

            $data = [
                'serverId' => 1,
                'ownerId' => $request->owner_id,
                'name' => $name,
                'password' => $password
            ];
        } else {
            $liveaccount_details = DB::table('liveaccount')
                ->where('trade_id', $request->trade_id)
                ->first();

            $data = [
                'serverId' => 1,
                'ownerId' => $request->owner_id,
                'accountId' => $request->trade_id ?? '',
                'name' => $liveaccount_details->name ?? ''
            ];
        }

        $url = '/api/managers';
        $response = $this->oauthClient->makeApiRequest($url, 'POST', $data, []);
        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }

        $status = $response['status'] == 'Active' ? 1 : 0;
       $datalog =[
              'manager_id' => $response['id'],
            'owner_id' => $response['ownerId'],
            'trade_id' => $response['accountId'],
            'name' => $response['name'],
            'password' => $password,
            'status' => $status,
            'api_response' => json_encode($response),
            'created_by' => Session::get('alogin'),
        ];
        DB::table('pamm_manager')->insert([
            'manager_id' => $response['id'],
            'owner_id' => $response['ownerId'],
            'trade_id' => $response['accountId'],
            'name' => $response['name'],
            'password' => $password,
            'status' => $status,
            'api_response' => json_encode($response),
            'created_by' => Session::get('alogin'),
        ]);

 
addIpLog('Offer Money Manager', $datalog);
        return response()->json([
            'status' => 'success',
            'message' => 'Money Manager Created Successfully'
        ]);
    }

    public function getMoneyManagers(Request  $request)
    {
        $response = $this->oauthClient->makeApiRequest('/api/managers', 'GET', [], []);
        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => $response['items']
        ]);
    }

    public function detailsMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $url = '/api/managers/' . $id;
        $manager_details = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        $url = '/api/managerConfigurations';
        $managerConfig = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        return view('admin.pamm.money_manager_details', compact('manager_details', 'managerConfig'));

    }

    public function summaryMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $url = '/api/managers/' . $id;
        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        return response()->json($response);
    }

    public function investmentsMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $url = '/api/managers/' . $id . '/investments';
        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $response['items']
        ]);
    }

    public function offersMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $email = auth()->user()->email;
        $url = '/api/managers/' . $id . '/offers';
        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
 addIpLog('Offer View', $email);
        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function requestsMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $filter = "managerId eq " . $id;
        $email = auth()->user()->email;
        $url = $this->baseUrl."/api/requests?" . http_build_query(['$filter' => $filter]);
        $response = $this->oauthClient->makeCurlRequest($url, 'GET', [], []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
 addIpLog('Offer Request', $url);
        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function transactionsMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $filter = "managerId eq " . $id;
        $email = auth()->user()->email;
        $url = $this->baseUrl."/api/transactions?" . http_build_query(['$filter' => $filter]);
        $response = $this->oauthClient->makeCurlRequest($url, 'GET', [], []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
 addIpLog('Transactions Money Offer', $url);
        return response()->json([
            'status' => 'success',
            'data' => $response
        ]);
    }

    public function fetchMoneyManager()
    {
        $managers = DB::table('pamm_manager')->get();

        return response()->json([
            'status' => 'success',
            'data' => $managers
        ]);
    }

    public function updateMoneyManager(Request $request)
    {
        $id = base64_decode($request->id);
        $url = '/api/managers/' . $id;
        $data = [
            "isPublic" => $request->isPublic,
            "configurationId" => $request->configuration,
            "description" => $request->description,
            "settings" => [
                "nameOverride" => $request->name,
            ]
        ];
        $response = $this->oauthClient->makeApiRequest($url, 'PUT', $data, []);
        if (isset($response['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => "Something Went Wrong. Please Try Again"
            ]);
        }

        $is_public = $response['isPublic'] == 'true' ? 1 : 0;
        $datalog = [
             'manager_id' => $response['id'],
            'name' => $request->name??'',
            'description' => $request->description??'',
            'configuration' => $request->configuration??'',
            'is_public' => $is_public,
            'created_by' => Session::get('alogin'),
        ];

        DB::table('pamm_manager_log')->insert([
            'manager_id' => $response['id'],
            'name' => $request->name??'',
            'description' => $request->description??'',
            'configuration' => $request->configuration??'',
            'is_public' => $is_public,
            'created_by' => Session::get('alogin'),
        ]);

        addIpLog('update Money Manager', $datalog);
        return response()->json([
            'status' => 'success',
            'message' => 'Money Manager Updated Successfully'
        ]);
    }

    private function validatePassword($password)
    {
        $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/';
        return preg_match($pattern, $password);
    }
}
