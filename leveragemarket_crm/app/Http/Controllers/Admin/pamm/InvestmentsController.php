<?php

namespace App\Http\Controllers\Admin\pamm;

use App\Services\PammService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class InvestmentsController extends Controller
{
    private $oauthClient;
    private $baseUrl;

    public function __construct(PammService $oauthClient)
    {
        $this->oauthClient = $oauthClient;
        $this->baseUrl = $this->oauthClient->getBaseUrl();
    }

    public function getInvestments()
    {
        $response = $this->oauthClient->makeApiRequest('/api/investments', 'GET', [], []);
        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $response['items']
        ]);
    }

    public function createInvestments(Request $request)
    {
        $request['manager_id'] = base64_decode($request->manager_id);

        $accountDetails = DB::table('liveaccount')
            ->where('trade_id', $request->owner_id)
            ->first();

        $request['trade_password'] = $accountDetails->trader_pwd ?? '';

        $data = [
            'ownerId' => $request->owner_id,
            'ownerServerId' => 1,
            'managerId' => $request->manager_id,
            'offerId' => $request->offer_id,
            'investment' => $request->amount,
            'password' => $request->trade_password,
        ];

        $url =  '/api/register/investor';
        $response = $this->oauthClient->makeApiRequest($url, 'POST', $data);

        if (isset($response['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }

        DB::table('pamm_investment')->insert([
            'investor_id' => $response['id'],
            'manager_id' => $response['managerId'],
            'offer_id' => $request->offer_id,
            'trade_id' => $request->owner_id,
            'password' => $request->trade_password,
            'api_response' => json_encode($response),
            'created_by' => session('clogin', session('alogin'))
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Investment Created Successfully'
        ]);
    }

    public function fetchClientInvestments(Request $request)
    {
        $results = [];
        $liveaccounts = DB::table('liveaccount')
            ->where('email', session('clogin'))
            ->get();
        foreach ($liveaccounts as $liveaccount) {
            $param = rawurlencode('ownerId eq ' . $liveaccount->trade_id);
            $url = $this->baseUrl . '/api/investments/optimized?$filter=' . $param;
            $headers = [];
            $response = $this->oauthClient->makeCurlRequest($url, 'GET', [], $headers);
            if (isset($response['items'])) {
                foreach ($response['items'] as $investment) {
                    $investment['searchparams']=$liveaccount->trade_id;
                    $results[] = $investment;
                }
            } else if (isset($response['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $response['message']
                ]);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $results
        ]);
    }

    public function requestActionsInvestments(Request $request)
    {
        $request['managerId'] = base64_decode($request->managerId);
        $data = [
            'managerId' => $request->managerId,
            'requestIds' => [$request->requestIds],
            'action' => $request->request_action,
            'comment' => $request->comment,
        ];
        // $url = '/api/requestActions';
        // $response = $this->oauthClient->makeApiRequest($url, 'POST', $data);
        $url = $this->baseUrl . '/api/requestActions';
        $headers = [];
        $response = $this->oauthClient->makeCurlRequest($url, 'POST', $data, $headers);
        if (isset($response['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message'],
                'response' => json_encode($response)
            ]);
        } else if (isset($response['errors']) && !empty($response['errors'])) {
            return response()->json([
                'status' => 'error',
                'message' => $response['errors'][0],
                'response' => json_encode($response)
            ]);
        }
        $login = session('clogin') ?? session('alogin');
        DB::table('pamm_actions')->insert([
            'action_id' => $request->requestIds??'',
            'manager_id' => $request->managerId??'',
            'action_type' => 'request',
            'status' => $request->request_action??'',
            'api_response' => json_encode($response),
            'created_by' => $login
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status Updated Successfully',
            'response' => json_encode($response)
        ]);
    }

    public function depositInvestments(Request $request)
    {
        $request['investmentId'] = base64_decode($request->investmentId);
        $request['managerId'] = base64_decode($request->managerId);
        $data = [
            'investmentId' => $request->investmentId,
            'requestType' => 'Deposit',
            'amount' => $request->amount,
            'isConfirmed' => true,
        ];
        $url = $request->isManagerOwned == 'true'
            ?'/api/managers/' . $request->managerId . '/requests'
            :'/api/requests';
        $response = $this->oauthClient->makeApiRequest($url, 'POST', $data);
        if (isset($response['error'])) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
        $login = session('clogin') ?? session('alogin');
        DB::table('pamm_deposits')->insert([
            'investor_id' => $request->investmentId??'',
            'manager_id' => $request->managerId??'',
            'amount' => $request->amount??'',
            'api_response' => json_encode($response),
            'created_by' => $login
        ]);

        $data = ['managerId' => $request->managerId];
        $url =  '/api/rollovers';
        $this->oauthClient->makeApiRequest($url, 'POST', $data);

        return response()->json([
            'status' => 'success',
            'message' => 'Fund Deposited Successfully',
            'response' => json_encode($response)
        ]);
    }
}
