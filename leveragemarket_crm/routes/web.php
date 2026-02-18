<?php

use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\ApiAjaxController;
use App\Http\Controllers\Admin\ClientAccController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ClientResourceController;
use App\Http\Controllers\Admin\Dashboard;
use App\Http\Controllers\Admin\DDController;
use App\Http\Controllers\Admin\IBController;
use App\Http\Controllers\Admin\Kyc;
use App\Http\Controllers\Admin\Login;
use App\Http\Controllers\Admin\MFAController;
use App\Http\Controllers\Admin\MT5Controller;
use App\Http\Controllers\Admin\pamm\ManagerConfigurationController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ActionOtpController;
use App\Http\Controllers\Admin\StaffManagement;
use App\Http\Controllers\Admin\Ticket;
use App\Http\Controllers\Admin\Transaction;
use App\Http\Controllers\Admin\Bonus;
use App\Http\Controllers\Admin\PromotionsController;
use App\Http\Controllers\Admin\Tournaments;
use App\Http\Controllers\Admin\Utilities;
use App\Http\Controllers\Admin\P2PAdminController;
use App\Http\Controllers\Admin\pamm\PammController;
use App\Http\Controllers\PammController as pamm;
use App\Http\Controllers\Admin\pamm\MoneyManagerController;
use App\Http\Controllers\Admin\pamm\ManagerOfferController;
use App\Http\Controllers\Admin\pamm\ActionHandlerController;
use App\Http\Controllers\Admin\pamm\InvestmentsController;
use App\Http\Controllers\Home;
use App\Http\Controllers\Ajax;
use App\Http\Controllers\Ib;
use App\Http\Controllers\CustomerMFAController;
use App\Http\Controllers\InternalTransfer;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MT5Accounts;
use App\Http\Controllers\Payment;
use App\Http\Controllers\SocialTrading\AccountController;
use App\Http\Controllers\SocialTrading\UsersController;
use App\Http\Controllers\Tickets;
use App\Http\Controllers\TradeDeposit;
use App\Http\Controllers\TradeWithdrawal;
use App\Http\Controllers\Transactions;
use App\Http\Controllers\Users;
use App\Http\Controllers\Wallet;
use App\Http\Controllers\Tournaments as Tournament;
use App\Http\Controllers\P2PController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return 'PHP and Laravel are working!';
});


Route::get('{any}', function (Request $request, $any) {
    $requestUri = $request->getRequestUri();
    $normalizedUri = preg_replace('/\/+/', '/', $requestUri);
    $normalizedUri = preg_replace('/\.php$/', '', $normalizedUri);
    if ($requestUri !== $normalizedUri) {
        return redirect($normalizedUri, 301);
    }
    return abort(404);
})->where('any', '.*')->fallback();

Route::get('/php/artisan/{request}', function ($request) {
    dd(Artisan::call($request));
});
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login_index');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/verify-2fa', [LoginController::class, 'verify2FaAndLogin'])->name('customer.verify-2fa');
Route::get('/forgot-password', [LoginController::class, 'forgot_password']);
Route::post('/forgot-password', [LoginController::class, 'sendResetLink']);
Route::get('/register/{group_code?}', [LoginController::class, 'register'])->name('register');

Route::post('/register/{group_code?}', [LoginController::class, 'addUser']);
Route::get('/email_verify', [LoginController::class, 'verifyEmail']);
Route::get('/reset-password', [LoginController::class, 'resetPassword']);
Route::post('/reset-password', [LoginController::class, 'resetPassword']);
Route::get('/ib-ref', [Ib::class, 'ibReference'])->name('ib-ref');
Route::post('/ib-ref', [LoginController::class, 'addUser'])->name('ib-ref-post');

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/payment-response', [Payment::class, 'handlePaymentResponse'])->name('handlePaymentResponse');

Route::match(['get', 'post'], '/webhookpaytiko', [Payment::class, 'webhookPaytiko']);
Route::match(['get', 'post'], '/deposit-success', [Payment::class, 'handlePaytikoSuccess'])->name('handlePaytikoSuccess');
Route::match(['get', 'post'], '/deposit-failed', [Payment::class, 'handlePaytikoFaild'])
    ->name('handlePaytikoFaild'); 
	
Route::get('/cryptopayment-response', [Payment::class, 'handleMatch2PayResponse']);
Route::post('/cryptopayment-response', [Payment::class, 'handleMatch2PayResponse']);
Route::get('/check-payment-status', [Payment::class, 'checkPaymentStatus']);
Route::get('/payment-confirmation', [Payment::class, 'handlePayissaResponse']);


Route::get('/client-profile/{clientId}', [LoginController::class, 'clientProfile']);
Route::post('/update-popup-flag', function () {
    Session::put('version2PopupShown', true);
    return response()->json(['success' => true]);
});
Route::middleware(['auth', 'is_user'])->group(function () {
    Route::post('getOtp', [Ajax::class, 'getOtp']);
    Route::prefix('/pamm')->group(function () {
        Route::get('/investments', [pamm::class, 'investments']);
        Route::get('/investment_list', [pamm::class, 'investment_list']);
        Route::get('/get_money_managers', [MoneyManagerController::class, 'getMoneyManagers']);
        Route::post('/get_manager_offer', [ManagerOfferController::class, 'getManagerOffer']);
        Route::post('/offers_money_manager', [MoneyManagerController::class, 'offersMoneyManager']);
        Route::get('/fetchclient_investments', [InvestmentsController::class, 'fetchClientInvestments']);
        Route::post('/deposit_investments', [InvestmentsController::class, 'depositInvestments']);
        Route::post('/create_investments', [InvestmentsController::class, 'createInvestments']);

    });
    // Route::get('/', [Home::class, 'dashboard'])->name('dashboardIndex');
    Route::get('dashboard', [Home::class, 'dashboard'])->name('dashboard');
    Route::get('/view_account_details', [MT5Accounts::class, 'viewAccountDetails'])->name('view_account_details');
    Route::get('/select_account_deposit', [MT5Accounts::class, 'select_account_deposit'])->name('select_account_deposit');
    Route::post('/view_account_details', [MT5Accounts::class, 'changeMt5Password'])->name('changemt5-password');

    Route::get('/wallet', [Wallet::class, 'index'])->name('wallet');
    Route::get('/transactions', [Transactions::class, 'index'])->name('transactions');

    Route::get('/liveAccounts', [MT5Accounts::class, 'liveAccounts'])->name('liveAccounts');
    Route::get('/demoAccounts', [MT5Accounts::class, 'demoAccounts'])->name('demoAccounts');
    Route::get('/view-account-details', [MT5Accounts::class, 'viewAccountDetails'])->name('view-account-details');
    Route::get('/createLiveAccount', [MT5Accounts::class, 'showLiveAccountForm'])->name('show-live-account-form');
    Route::post('/createLiveAccount', [MT5Accounts::class, 'createLiveAccount'])->name('create-live-account');
    Route::get('/createDemoAccount', [MT5Accounts::class, 'showDemoAccountForm'])->name('show-demo-account-form');
    Route::post('/createDemoAccount', [MT5Accounts::class, 'createDemoAccount'])->name('create-demo-account');
    Route::post('/view-account-details', [MT5Accounts::class, 'changeMt5Password'])->name('change-mt5-password');
	Route::post('/updateLiveAccount', [MT5Accounts::class, 'updateLiveAccount'])->name('update-live-account'); 

    Route::get('/getLeverage', [MT5Accounts::class, 'getLeverage'])->name('get-leverage');

    Route::get('/support', [Tickets::class, 'index'])->name('supports');
    Route::post('/support', [Tickets::class, 'createTicket'])->name('support');
    Route::get('/ticket_details', [Tickets::class, 'showDetails'])->name('ticket_details');
    Route::post('/ticket_details', [Tickets::class, 'addRemark'])->name('ticket_details_store');
    
    Route::get('/ticket_followups', [Tickets::class, 'fetchFollowups'])->name('ticket_followups');

    Route::get('/ib-profile', [Ib::class, 'ib_profile'])->name('ib-profile');
    Route::get('/ib', [Ib::class, 'index'])->name('ib');
    Route::post('/ib-profile', [Ib::class, 'processTransfer'])->name('ib-profile-store');
    Route::post('/ib-enroll', [Ib::class, 'ibEnroll'])->name('ib-enroll');
    Route::get('/ib-commission-histories', [Ib::class, 'getCommissionHistory']);


    Route::get('/user-profile', [Users::class, 'profile'])->name('user-profile');
    Route::post('/profileupate', [Users::class, 'profileupate'])->name('profileupate');
    Route::get('/user/documentUpload', [Users::class, 'documentUpload']);
    Route::post('/user/documentUpload', [Users::class, 'uploadDocument']);
    Route::get('/sumsub', [Users::class, 'sumsub'])->name('sumsub');
    Route::post('/sumsub_verify', [Users::class, 'sumsub_verify'])->name('sumsub_verify');

    Route::post('/wallet/store', [Wallet::class, 'storeClientWallet'])->name('wallet.store');
    Route::post('/bank/store', [Users::class, 'storeBankDetails'])->name('bank.store');
    Route::get('/bank/delete/{enc}/{otp?}', [Users::class, 'deleteBankDetails'])->name('bank.delete');
    Route::post('/wallet/updateStatus', [Wallet::class, 'updateStatus'])->name('wallet.updateStatus');
    Route::get('/wallet_deposit', [Wallet::class, 'showDepositForm'])->name('wallet_deposit');
    Route::get('/wallet_withdrawal', [Wallet::class, 'showWithdrawalForm'])->name('wallet_withdrawal');
    Route::post('/wallet_deposit', [Wallet::class, 'deposit'])->name('wallet_deposit_store');
    Route::post('/wallet_withdrawal', [Wallet::class, 'withdrawal'])->name('wallet_withdrawal_store');
    Route::post('/wallet_payment', [Wallet::class, 'processPayment'])->name('wallet_payment');

    Route::post('/change_password', [Users::class, 'changePassword'])->name('password.change');
	Route::post('/update-profile', [Users::class, 'profileUpdate'])->name('profile.update');
	
    Route::get('/customer/mfa-setup-qr', [CustomerMFAController::class, 'getQrCode'])->name('customer.mfa-setup-qr');
    Route::post('/customer/mfa-authentication', [CustomerMFAController::class, 'verify'])->name('customer.mfa-authentication');
    Route::post('/customer/mfa-disable', [CustomerMFAController::class, 'disable'])->name('customer.mfa-disable');
    Route::post('/customer/mfa-reenable', [CustomerMFAController::class, 'reenable'])->name('customer.mfa-reenable');

    Route::get('/trade-deposit', [TradeDeposit::class, 'index'])->name('trade-deposit');
    Route::post('/trade-deposit', [TradeDeposit::class, 'deposit'])->name('trade-deposit_store');

    Route::get('/trade-withdrawal/{type?}', [TradeWithdrawal::class, 'index'])->name('trade-withdrawal');
    Route::post('/trade-withdrawal', [TradeWithdrawal::class, 'withdraw'])->name('trade-withdrawal_store');
    Route::get('/internal-transfer', [InternalTransfer::class, 'index'])->name('internal-transfer');
    Route::post('/process-transfer', [InternalTransfer::class, 'processTransfer'])->name('process-transfer_store');
    
	Route::post('/ib_bank_withdraw', [InternalTransfer::class, 'IBBankTransfer'])->name('ib_bank_withdraw');

    Route::get("/getBonus", [Bonus::class, 'getBonus'])->name('getBonus');
	
    Route::get("/tournaments", [Tournament::class, 'index'])->name('tournaments');
    Route::get("/leader-board", [Tournament::class, 'leaderBoard'])->name('leader-board');
    Route::get("/tournament_details", [Tournament::class, 'details'])->name('tournament_details');
    Route::post("/enroll_tournament", [Tournament::class, 'enroll'])->name('enroll_tournament');
    Route::get("/getTradeHistory", [Tournament::class, 'getTradeHistory']);
	
	Route::get('/activate-wallet', [Wallet::class, 'activateWallet'])->name('wallet.activate');
	Route::get('/wallet-transfer', [Wallet::class, 'walletTransfer'])->name('wallet.transfer');
	Route::post('/wallettransferto', [Wallet::class, 'wallettransferto'])->name('wallet.transferto');
	Route::get('/wallet-transcation', [Wallet::class, 'wallettranscation'])->name('wallet.transcation');
	
	/*P2P Functionality*/
	Route::get("/p2p-marketplace", [P2PController::class, 'p2pmarketplace'])->name('p2pmarketplace');
	Route::get("/p2p-myorders", [P2PController::class, 'p2pmyorders'])->name('p2pmyorders');
	Route::get("/p2p-merchant", [P2PController::class, 'p2pmerchant'])->name('p2pmerchant');
	Route::post("/p2p-merchantstore", [P2PController::class, 'p2pmerchantstore'])->name('p2pmerchantstore');
	
	Route::get('/p2p-myadslist', [P2PController::class, 'p2pmyadslist'])->name('p2pmyadslist');
	Route::get('/p2p-merchantedit/{id}', [P2PController::class, 'p2pmerchantedit'])->name('p2pmerchantedit');
	Route::post('/p2p-merchantupdate/{id}', [P2PController::class, 'p2pmerchantupdate'])->name('p2pmerchantupdate');
	Route::post('/p2p-merchantdelete/{id}', [P2PController::class, 'p2pmerchantdelete'])->name('p2pmerchantdelete');
	Route::get('/exchange-rate', [P2PController::class, 'exchangeRate']);
	
	Route::get("/p2p-buy/{marketid}", [P2PController::class, 'p2pbuy'])->name('p2pbuy');
	Route::post("/p2p-orderstore", [P2PController::class, 'p2porderstore'])->name('p2porderstore');
	Route::get("/p2p-receiveorders", [P2PController::class, 'p2preceiveorders'])->name('p2preceiveorders');
	
	/*Offers and Promotions*/
	Route::get("/offers", [Users::class, 'getOffers'])->name('offers'); 
	Route::get("/promotions", [Users::class, 'getPromotions'])->name('promotions');
	Route::get('/promoenroll', [Users::class, 'promoenroll'])->name('promo.enroll');
	Route::get('/bounsenroll', [Users::class, 'getbonusenroll'])->name('bouns.enroll');
	Route::post('/bonuspreview', [Users::class, 'bonuspreview'])->name('bonuspreview');
	Route::post('/applybonus', [Users::class, 'applybonus'])->name('applybonus');
	
	/*Webterminal Connect*/
	Route::post('/mt5/generate-token', [MT5Accounts::class, 'generateToken'])->name('mt5.generateToken');
	Route::get('/tradeonline', [MT5Accounts::class, 'tradeonline'])->name('tradeonline');
	
	
	
});

// Route::get("/admin/migration",[Login::class,'ib_migration']);

Route::prefix("/admin")->name("admin.")->group(function () {
    Route::get('/', [Login::class, 'showLoginForm']);
    Route::post('/', [Login::class, 'adminLogin']);
    Route::get('/login', [Login::class, 'showLoginForm']);
    Route::post('/login', [Login::class, 'adminLogin']);
    Route::post('getOtp', [Ajax::class, 'getOtp']);
    Route::post('validate-login-otp', [Login::class, 'validateAndRequestOtp']);
    Route::post('verify-otp', [Login::class, 'verifyOtpAndLogin']);
    Route::post('verify-2fa', [Login::class, 'verify2FaAndLogin'])->name('verify-2fa');
    Route::get('/ajax', [AjaxController::class, 'index']);
    Route::post('/ajax', [AjaxController::class, 'index']);
    Route::get('/api/ajax', [ApiAjaxController::class, 'handleRequest']);
    Route::post('/api/ajax', [ApiAjaxController::class, 'handleRequest']);
    Route::post('/api/accounts', [ApiAjaxController::class, 'liveaccountCreation']);
    Route::get('/logout', [Login::class, 'logout'])->name('logout');


    Route::get("/mfa-authentication",[MFAController::class,'index']);
    Route::post("/mfa-authentication",[MFAController::class,'verify'])->name("mfa-authentication");
	Route::get('mfa-setup-qr', [MFAController::class, 'getQrCode'])->name('mfa-setup-qr');
    Route::post('mfa-disable', [MFAController::class, 'disable'])->name('mfa-disable');
    Route::post('mfa-reenable', [MFAController::class, 'reenable'])->name('mfa-reenable');
	
	Route::delete('/group-mains/{id}', [MT5Controller::class, 'maingroups'])->name('maingroups.destroy'); 
	Route::delete('/group-category/{id}', [MT5Controller::class, 'maincategory'])->name('maincategory.destroy'); 
	Route::delete('/group-accdelete/{id}', [MT5Controller::class, 'groupdelete'])->name('groupdelete.destroy'); 
	Route::delete('/userAccountdelete/{id}', [MT5Controller::class, 'userAccountdelete'])->name('userAccountdelete.destroy');
	Route::delete('/userdemoAccountdelete/{id}', [MT5Controller::class, 'userdemoAccountdelete'])->name('userdemoAccountdelete.destroy'); 
	/*
	Route::delete('/groups/{id}', [GroupController::class, 'destroy'])->name('groups.destroy');
	Route::delete('/group-mains/{id}', [GroupMainController::class, 'destroy'])->name('groupmains.destroy');
	Route::delete('/group-categories/{id}', [GroupCategoryController::class, 'destroy'])->name('groupcategories.destroy');
	Route::delete('/group-types/{id}', [GroupTypeController::class, 'destroy'])->name('grouptypes.destroy');*/
	
	/*Promotions Modules*/
	Route::get("/promotions", [PromotionsController::class, 'index'])->name('promotions');
	Route::get("/promotionsAdd", [PromotionsController::class, 'add_promotion'])->name('promotionsAdd');
	Route::post("/promotionsStore", [PromotionsController::class, 'store'])->name('promotionsStore');
	Route::post("/promotionsUpdate", [PromotionsController::class, 'update'])->name('promotionsUpdate');
	
	Route::get("/ibclientdetails", [IBController::class, 'ibclientdetails']);

    Route::middleware(['is_admin', 'check.permissions'])->group(function () {
        Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
		Route::get('/resend-email-verification', [ClientController::class, 'resendemail'])->name('resend-email-verification');
        Route::get('/transactions/{id}', [Transaction::class, 'index']);
        Route::get('/transactions/pending/{id}', [Transaction::class, 'pending']);

        Route::get('/pending_tasks', [Transaction::class, 'pending_tasks']);


        Route::get('/client_list', [ClientController::class, 'index'])->name('client_list');
        Route::get('/client_details', [ClientController::class, 'clientDetails'])->name('client_details');
        Route::post('/updateIB', [ClientController::class, 'updateIB'])->name('updateIB');
        Route::post('/updateRM', [ClientController::class, 'updateRM'])->name('updateRM');
        Route::post('/addUser', [ClientController::class, 'addUser'])->name('addUser');
        Route::post('/updateUser', [ClientController::class, 'updateUser'])->name('updateUser');
        Route::post('/sendPasswordResetLink', [ClientController::class, 'sendPasswordResetLink'])->name('sendPasswordResetLink');
        Route::post('/updateAccLimit', [ClientController::class, 'updateAccLimit'])->name('updateAccLimit');
        Route::get('/ip_activity', [ClientController::class, 'activityLog']);
          Route::get('/ip_activityview', [ClientController::class, 'activityLogview']);

        Route::prefix("/dd")->group(function () {
            Route::get("/users", [DDController::class, 'users']);
            Route::get("/stusers", [UsersController::class, 'getddUsers']);
            Route::get("/mt5", [UsersController::class, 'mt5']);
        });

        Route::get('/kyc_history', [ClientResourceController::class, 'kycHistory']);
        Route::get('/kyc_details', [ClientResourceController::class, 'kycDetails']);
        Route::get('/bank_details', [ClientResourceController::class, 'bankDetails']);
        Route::get('/view_bank_details', [ClientResourceController::class, 'viewBankDetails']);
        Route::get('/wallet_details', [ClientResourceController::class, 'walletDetails']);
        Route::get('/view_wallet_details', [ClientResourceController::class, 'viewWalletDetails']);


        Route::get('/roles', [StaffManagement::class, 'roles']);
        Route::get('/rm_dashboard', [StaffManagement::class, 'rmDashboard'])->name('rm_dashboard');
        Route::post('/roles', [StaffManagement::class, 'addRole'])->name('roles');
        Route::post('/update_roles', [StaffManagement::class, 'updateRole'])->name('update_roles');
        Route::post('/update_role_status', [StaffManagement::class, 'updateRoleStatus'])->name('update_role_status');
        Route::post('/update_role_permissions', [StaffManagement::class, 'updateRolePermissions'])->name('update_role_permissions');
        Route::post('/save_user', [StaffManagement::class, 'saveUser'])->name('saveUser');
        Route::post('/save_user_group', [StaffManagement::class, 'saveUserGroup'])->name('saveUserGroup');

        Route::get('/role_permissions', [StaffManagement::class, 'rolePermissions']);
        Route::get('/admin_users', [StaffManagement::class, 'adminUsers']);
        Route::get('/user_groups', [StaffManagement::class, 'userGroups']);

        Route::post('/action-otp/request', [ActionOtpController::class, 'requestActionOtp']);
        Route::post('/action-otp/verify', [ActionOtpController::class, 'verifyActionOtp']);
        
        Route::get('/permissionsList', [StaffManagement::class, 'permissionsList'])->name('permissionsList');

        Route::post('/addTicket', [Ticket::class, 'addTicket'])->name('addTicket');
        Route::post('/assignTicket', [Ticket::class, 'assignTicket'])->name('assignTicket');
        Route::post('/updateStatus', [Ticket::class, 'updateStatus'])->name('updateStatus');
        Route::match(['get', 'post'], '/all_tickets', [Ticket::class, 'tickets'])->name('all_tickets');
        Route::match(['get', 'post'], '/open_tickets', [Ticket::class, 'tickets'])->name('open_tickets');
        Route::match(['get', 'post'], '/closed_tickets', [Ticket::class, 'tickets'])->name('closed_tickets');
        Route::get('/ticket_details', [Ticket::class, 'showDetails'])->name('ticket_details');
        Route::post('/ticket_details', [Ticket::class, 'addRemark'])->name('ticket_details_store');
        Route::get('/ticket_followups', [Tickets::class, 'fetchFollowups'])->name('ticket_followups');

        Route::post('/updateKyc', [Kyc::class, 'updateKyc'])->name('updateKyc');

        Route::get('/wallet_deposit_details', [Transaction::class, 'wallet_deposit_details']);
        Route::post('/wallet_deposit_details', [Transaction::class, 'update_wallet_deposit']);
        Route::get('/wallet_withdrawal_details', [Transaction::class, 'wallet_withdrawal_details']);
        Route::post('/wallet_withdrawal_details', [Transaction::class, 'update_wallet_withdrawal']);
        Route::get('/trading_deposit_details', [Transaction::class, 'trading_deposit_details']);
        Route::get('/trading_withdrawal_details', [Transaction::class, 'trading_withdrawal_details']);
        Route::get('/internal_transfer_details', [Transaction::class, 'internal_transfer_details']);
        Route::get('/ib_withdrawal_details', [Transaction::class, 'ib_withdrawal_details']);
		
		Route::get('/ibcomm_withdrawal_details', [Transaction::class, 'ibcomm_withdrawal_details']);
		Route::post("/ibupdateWithdrawal", [Transaction::class, 'ibupdateWithdrawal'])->name('ibupdateWithdrawal');


        Route::prefix('/clientAccounts')->group(function () {
            Route::get("/liveAccounts", [ClientAccController::class, 'live_accounts']);
            Route::get("/demoAccounts", [ClientAccController::class, 'demo_accounts']);
        });

        Route::prefix('/ui_settings')->group(function () {
            Route::get('/', [SettingsController::class, 'index']);
            Route::post('/', [SettingsController::class, 'store']);
        });
        Route::prefix('/update_password')->group(function () {
            Route::get('/', [SettingsController::class, 'update_password']);
            Route::post('/', [SettingsController::class, 'store_password'])->name('update_password');
        });

        Route::get("/ibdashboard", [IBController::class, 'index']);
        Route::get("/iblist", [IBController::class, 'list']);
        Route::get("/iblist_active", [IBController::class, 'list_active']);
        Route::get("/ib_settings", [IBController::class, 'ib_settings']);
        Route::get("/ibCommission", [IBController::class, 'ibCommission']);
        Route::post("/ibCommission", [IBController::class, 'updateIbPlan']);
        Route::get("/ibCommissionEdit/{planId}/{accType}", [IBController::class, 'ibCommissionEdit']);
        Route::post("/ibCommissionEdit/{planId}/{accType}", [IBController::class, 'ibCommissionEdit']);

        Route::get("/mt5_groups", [MT5Controller::class, 'index']);

        Route::get("/view_account_details", [MT5Controller::class, 'view']);
        Route::post("/updatePassword", [MT5Controller::class, 'updatePassword'])->name('updatePassword');
        Route::post("/updateAccountDetails", [MT5Controller::class, 'updateAccountDetails'])->name('updateAccountDetails');
        Route::post("/depositToAccount", [MT5Controller::class, 'depositToAccount'])->name('depositToAccount');
        Route::post("/withdrawFromAccount", [MT5Controller::class, 'withdrawFromAccount'])->name('withdrawFromAccount');
        Route::post("/bonusToAccount", [MT5Controller::class, 'bonusToAccount'])->name('bonusToAccount');
        Route::post("/updateTransaction", [MT5Controller::class, 'updateTransaction'])->name('updateTransaction');
           Route::post("/adminwalletupdate", [MT5Controller::class, 'adminwalletupdate'])->name('adminwalletupdate');
            Route::post("/admingetOtp", [MT5Controller::class, 'admingetOtp'])->name('admingetOtp');
             Route::post("/updatewalletwithdrawal", [MT5Controller::class, 'updatewalletwithdrawal'])->name('updatewalletwithdrawal');
            Route::post("/verifyAdminOtp", [MT5Controller::class, 'verifyAdminOtp'])->name('verifyAdminOtp');
        Route::post("/updateWithdrawal", [MT5Controller::class, 'updateWithdrawal'])->name('updateWithdrawal');
        Route::post("/mapMt5Account", [MT5Controller::class, 'mapMt5Account']);
        Route::post("/userAccountGet", [MT5Controller::class, 'userAccountGet']);
		

        Route::get("/search", [SearchController::class, 'index']);

        Route::get("/bonus", [Bonus::class, 'index'])->name('bonus');
        Route::get("/bonusAdd", [Bonus::class, 'add_bonus'])->name('bonusAdd');
        Route::post("/bonusStore", [Bonus::class, 'store'])->name('bonusStore');
        Route::post("/bonusUpdate", [Bonus::class, 'update'])->name('bonusUpdate');

        Route::get("/tournaments", [Tournaments::class, 'index'])->name('tournaments');
        Route::post("/tournaments", [Tournaments::class, 'store'])->name('tournaments_post');
        Route::post("/updateTournament", [Tournaments::class, 'update'])->name('updateTournament');
        Route::get("/tournament_liveaccounts", [Tournaments::class, 'liveaccounts']);
        Route::get("/tournament_account_details", [Tournaments::class, 'account_details']);
        Route::get("/single_form_transactions", [Utilities::class, 'index']);
        Route::get("/live_acc_excluded", [Utilities::class, 'excludes']);
        Route::post("/single_form_transactions", [Utilities::class, 'singleFormTransaction'])->name('singleFormTransaction');
        Route::get("/getUtilityAccounts", [Utilities::class, 'getUtilityAccounts'])->name('getUtilityAccounts');


        Route::get("/live_acc_excluded", [MT5Controller::class, 'live_acc_excluded']);
        Route::post("/live_acc_excluded", [MT5Controller::class, 'live_acc_excluded_store']);


        Route::prefix("/social-trading")->group(function () {
            Route::prefix("/listing")->group(function () {
                Route::get("/users", [UsersController::class, 'users']);
                Route::get("/accounts", [AccountController::class, 'accounts']);
            });

            Route::prefix("/store")->group(function () {
                Route::post("/user", [UsersController::class, 'store_user'])->name("user");
                Route::post("/account", [AccountController::class, 'store_user'])->name("account");
            })->name("store");

            Route::prefix("/update")->group(function () {
                Route::post("/user", [UsersController::class, 'update_user'])->name("Updateuser");
            })->name("update");

            Route::prefix("/getList")->group(function () {
                Route::get("/users", [UsersController::class, 'getUsers']);
                Route::get("/accounts", [AccountController::class, 'getAccounts']);
            });
        })->name("social-trading");

        Route::prefix('/pamm')->group(function () {
            Route::get('/action', [PammController::class, 'handleRequest']);
            Route::get('/fetch_client_investments', [InvestmentsController::class, 'fetchClientInvestments']);

            Route::get('/investments', [PammController::class, 'investments'])->name('admin.pamm.investments');
            Route::get('/mm_configuration', [PammController::class, 'mmConfiguration'])->name('admin.pamm.mm_configuration');
            Route::get('/money_manager_list', [PammController::class, 'moneyManagerList'])->name('admin.pamm.money_manager_list');

            Route::get('/get_money_managers', [MoneyManagerController::class, 'getMoneyManagers']);
            Route::post('/create_money_manager', [MoneyManagerController::class, 'createMoneyManager']);
            Route::get('/money_manager_details', [MoneyManagerController::class, 'detailsMoneyManager']);

            Route::post('/investments_money_manager', [MoneyManagerController::class, 'investmentsMoneyManager']);
            Route::post('/offers_money_manager', [MoneyManagerController::class, 'offersMoneyManager']);
            Route::post('/transactions_money_manager', [MoneyManagerController::class, 'transactionsMoneyManager']);
            Route::post('/requests_money_manager', [MoneyManagerController::class, 'requestsMoneyManager']);
            Route::post('/get_manager_offer', [ManagerOfferController::class, 'getManagerOffer']);
            Route::post('/update_money_manager', [MoneyManagerController::class, 'updateMoneyManager']);
            Route::post('/update_manager_offer', [ManagerOfferController::class, 'updateManagerOffer']);

            Route::get('/get_investments', [InvestmentsController::class, 'getInvestments']);


            Route::post('/create_investments', [InvestmentsController::class, 'createInvestments']);
            Route::post('/deposit_investments', [InvestmentsController::class, 'depositInvestments']);

            Route::get('/get_manager_configuration', [ManagerConfigurationController::class, 'getManagerConfiguration']);
            Route::get('/fetchclient_investments', [InvestmentsController::class, 'fetchClientInvestments']);
            Route::post('/requestactions_investments', [InvestmentsController::class, 'requestActionsInvestments']);


        });
		
		Route::prefix('/p2p')->name('p2p.')->group(function () {
			Route::get('/cryptoindex', [P2PAdminController::class, 'cryptoindex'])->name('cryptoindex');
			Route::post('/cryptostore', [P2PAdminController::class, 'cryptostore'])->name('cryptostore');
			Route::post('/cryptoupdate/{id}', [P2PAdminController::class, 'cryptoupdate'])->name('cryptoupdate');
			Route::get('/merchataccount', [P2PAdminController::class, 'merchantacclist'])->name('merchataccount');
		}); 
    });
});
