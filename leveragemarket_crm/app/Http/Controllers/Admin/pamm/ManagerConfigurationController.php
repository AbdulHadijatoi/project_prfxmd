<?php

namespace App\Http\Controllers\Admin\Pamm;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PammService;
use App\Http\Controllers\Controller;

class ManagerConfigurationController extends Controller
{
    private $oauthClient;
    private $baseUrl;

    public function __construct(PammService $oauthClient)
    {
        $this->oauthClient = $oauthClient;
        $this->baseUrl = $this->oauthClient->getBaseUrl();
    }

    public function getManagerConfiguration(Request $request)
    {
        $url = isset($request->id)
            ? '/api/managerConfigurations/' . $request->id
            : '/api/managerConfigurations';
        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => '',
            'data' => $response
        ]);
    }

    public function fetchManagerConfiguration(Request $request)
    {
        $url = isset($request->id)
            ? $this->baseUrl . '/api/managerConfigurations/' . $request->id
            : $this->baseUrl . '/api/managerConfigurations';

        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], []);

        return view('pamm.configuration_view', ['data' => $response]);
    }

    public function updateManagerConfiguration(Request $request)
    {
        $data = $request->except('action');
        $url = $this->baseUrl . '/api/managerConfigurations/' . $request->id;

        $response = $this->oauthClient->makeApiRequest($url, 'PUT', $data, []);

        if (isset($response['error']) && $response['error']) {
            return response()->json([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Configuration Updated Successfully',
            'data' => $response
        ]);
    }
}
