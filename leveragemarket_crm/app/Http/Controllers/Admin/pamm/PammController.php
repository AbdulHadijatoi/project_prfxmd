<?php

namespace App\Http\Controllers\Admin\pamm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller\Admin\pamm\MoneyManagerController;


class PammController extends Controller
{
    public function investments()
    {
        return view('admin.pamm.investments');
    }

    public function mmConfiguration()
    {
        return view('admin.pamm.mm_configuration');
    }
    public function moneyManagerList()
    {
        return view('admin.pamm.money_manager_list');
    }
    // public function handleRequest(Request $request)
    // {
    //     $action = $request->input('action');
    //     if (!$action) {
    //         return Response::json(['status' => 'error', 'message' => 'No action specified'], 400);
    //     }
    //     switch ($action) {
    //         case 'fetch_client_investments':
    //             $controller = 'InvestmentsController';
    //             $method = 'fetchClientInvestments';
    //             break;
    //         case 'get_money_managers':
    //             $controller = 'MoneyManagerController';
    //             $method = 'getMoneyManagers';
    //             break;
    //         case 'get_investments':
    //             $controller = 'InvestmentsController';
    //             $method = 'getInvestments';
    //             break;

    //         default:
    //             return Response::json(['status' => 'error', 'message' => 'Invalid action'], 400);
    //     }
    //     $controller = 'App\\Http\\Controllers\\Admin\\pamm\\' . $controller;
    //     if (class_exists($controller) && method_exists($controller, $method)) {
    //         $instance = App::make($controller);
    //         return $instance->$method($request);
    //     }

    //     return Response::json(['status' => 'error', 'message' => 'Controller or method does not exist'], 400);
    // }
}
