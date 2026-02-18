<?php

namespace App\Http\Controllers\Admin\pamm;

use App\Services\PammService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
class ManagerOfferController extends Controller
{
    protected $oauthClient;
    protected $baseUrl;

    public function __construct(PammService $oauthClient)
    {
        $this->oauthClient = $oauthClient;
        $this->baseUrl = $this->oauthClient->getBaseUrl();
    }
    public function getManagerOffer(Request $request)
    {
        $request['manager_id'] = base64_decode($request->manager_id);
        $url = '/api/managers/' . $request['manager_id'] . '/offers';
        $headers = [];
        $response = $this->oauthClient->makeApiRequest($url, 'GET', [], $headers);
        if (isset($response['error']) && $response['error']) {
            return json_encode([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
        return json_encode([
            'status' => 'success',
            'message' => '',
            'data' => $response
        ]);
    }
    public function updateManagerOffer(Request $request)
    {
        $request['managerId'] = base64_decode($request['managerId']);
        $agentChain = [];
        $agentChainString = '';
        for ($i = 0; $i < count($request['agentChainLogin']); $i++) {
            if ($request['agentChainLogin'][$i] != '' && $request['agentChainLogin'][$i] != '') {
                $agentChain[] = $request['agentChainLogin'][$i] . ":" . $request['agentChainServer'][$i];
            }
        }
        $agentChainString = implode(",", $agentChain);
        if ($request['id'] == 0) {
            $url = '/api/managers/' . $request['managerId'] . '/offers';
        } else {
            $url = '/api/managers/offers/' . $request['id'];
        }
        $headers = [];

        $tradingInterval = ["type" => "CalendarMonths", "count" => 1];
        if ($request['tradingInterval_type'] != '' && $request['tradingInterval_count'] != '') {
            if ($request['tradingInterval_type'] == 'OnRollover') {
                $request['tradingInterval_type'] = 'Days';
                $request['tradingInterval_count'] = 0;
            }
            $tradingInterval = [
                "type" => $request['tradingInterval_type'],
                "count" => $request['tradingInterval_count'],
            ];
        }
        $performanceFees = [
            "levels" => [],
            "mode" => "Equity"
        ];
        if (!empty($request['performanceFees_level'])) {
            foreach ($request['performanceFees_level'] as $index => $level) {
                $level = $index == 0 ? 0 : $level;
                if (($level != '' && $request['performanceFees_value'][$index] != '')) {
                    $performanceFees['levels'][] = [
                        "level" => $level,
                        "value" => $request['performanceFees_value'][$index]
                    ];
                }
            }
        }
        $joinLinks = [];
        foreach ($request['joinLinkKey'] as $index => $value) {
            if (!empty($value) && !empty($request['joinLinkExpiration'][$index]) && !empty($request['joinLinkAgent'][$index])) {
                $joinLinks[] = [
                    "key" => $value ?? "",
                    "expiration" => $request['joinLinkExpiration'][$index],
                    "oneTime" => $request['joinLinkOneTime'][$index] == 'on' ? true : false,
                    "agentChain" => $request['joinLinkAgent'][$index] ?? "",
                ];
            }
        }
        $data = [
            "id" => $request['id'],
            "name" => $request['name'],
            "description" => $request['description'],
            "managerId" => $request['managerId'],
            // "managerConfiguration" => 0,
            // "currency" => "string",
            // "activeInvestmentCount" => 0,
            "isActive" => isset($request['isActive']) ? true : false,
            "settings" => [
                "tradingInterval" => $tradingInterval,
                "minDeposit" => $request['minDeposit'],
                "minWithdrawal" => $request['minWithdrawal'],
                "minInitialInvestment" => $request['minInitialInvestment'],
                "performanceFees" => $performanceFees,
                // "earlyWithdrawalFees" => [
                //     "levels" => [
                //         [
                //             "level" => 0,
                //             "value" => 0
                //         ]
                //     ]
                // ],
                // "depositFees" => [
                //     "levels" => [
                //         [
                //             "level" => 0,
                //             "value" => 0
                //         ]
                //     ]
                // ],
                // "managementFees" => [
                //     "levels" => [
                //         [
                //             "level" => 0,
                //             "value" => 0,
                //             "mode" => "Percentage"
                //         ]
                //     ]
                // ],
                // "agentCommissions" => [
                //     "agentCommissionRate" => 0,
                //     "agentCommissionDistribution" => "string",
                //     "agentCommissionRateOverride" => [
                //         "ratePerformanceFees" => 0,
                //         "rateManagementFees" => 0,
                //         "rateWithdrawalFees" => 0,
                //         "rateDepositFees" => 0,
                //         "rateEntryFees" => 0
                //     ]
                // ],
                "agentChain" => $request['offerExtendedMode'] == 'on' ? $agentChainString : $request['agentChain'],
                "joinLinks" => $joinLinks,
                // "entryFees" => [
                //     "amount" => 0,
                //     "mode" => "Percentage"
                // ],
                // "hasEntryFees" => true
            ]
        ];
        $response = $this->oauthClient->makeApiRequest($url, ($request['id'] == 0 ? 'POST' : 'PUT'), $data, $headers);

        if (isset($response['error']) && $response['error']) {
            return json_encode([
                'status' => 'error',
                'message' => $response['message']
            ]);
        }
        DB::table('pamm_offer_log')->insert([
            'manager_id' => $request->managerId,
            'offer_id' => $request->id,
            'data' => json_encode($request->response),
            'created_by' => Session::get('alogin'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return json_encode([
            'status' => 'success',
            'message' => '',
            'data' => $response
        ]);
    }
}
