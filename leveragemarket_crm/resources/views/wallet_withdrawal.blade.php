@extends('layouts.crm.crm')
@section('styles')
    <style>
        .otp-req:not([disabled]):hover {
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
        }
        .otp-req[disabled] {
            opacity: 0.4;
        }
    </style>
@endsection
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Wallet</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <ul class="nav nav-tabs checkout-tabs mb-0" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation"><a class="nav-link " id="ecomtab-tab-1"
                                        href="/wallet_deposit" role="tab" aria-controls="ecomtab-1" aria-selected="true"
                                        tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-credit-card"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Deposit</h6>
                                            </div>
                                        </div>
                                    </a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link active" href="/wallet_withdrawal"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-dollar-sign"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Withdraw</h6>
                                            </div>
                                        </div>
                                    </a></li>
								<!--<li class="nav-item" role="presentation"><a class="nav-link " href="/wallet-transfer"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-shuffle"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Client to Client Transfer</h6>
                                            </div>
                                        </div>
                                    </a></li>-->
								<li class="nav-item" role="presentation"><a class="nav-link" href="/wallet-transcation"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="ti ti-file-invoice"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Transcation</h6>
                                            </div>
                                        </div>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div>
                            <div>
                                <div class="row">
								@if ($user->kyc_verify > 0)
                                    <div class="col-12">
                                        <div class="row">
										@if($wallet_balance == 0)
											<div class="col-xl-12">
                                                <div class="card">
                                                    <div class="card-body ">
                                                        <h6 class="text-danger">Wallet balance not available. Top up your balance.</h6>
                                                    </div>
												</div>
											</div>
										@else
                                            <div class="col-xl-8">
                                                <div class="card">
                                                    <div class="card-body border-bottom">
                                                        <h6>CREATE WITHDRAW TICKET</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="divider my-4"><span>SELECT WITHDRAW METHOD</span></div>
                                                    </div>
                                                    <div class="row g-1">
                                                        <div class="col-md-3 col-lg-4 col-xl-4">
                                                            <div class="address-check border rounded">
                                                                <div class="form-check"><input type="radio"
                                                                        name="withdraw_type"
                                                                        class="form-check-input input-primary wallet-withdraw"
                                                                        value="1" data-type="Wallet_Withdrawal"><label
                                                                        class="form-check-label d-block"
                                                                        for="payopn-check-1"><span
                                                                            class="card-body p-2 d-block"><span
                                                                                class="h6 f-w-500 mb-1 d-block">CRYPTO
                                                                                WITHDRAWAL</span><span
                                                                                class="d-flex align-items-center"><span
                                                                                    class="f-10 badge bg-light-success me-1">CRYPTO
                                                                                    WALLET</span>
                                                                                <span class="ti ti-currency-bitcoin"></span>
                                                                            </span></span></label></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 col-lg-4 col-xl-4">
                                                            <div class="address-check border rounded">
                                                                <div class="form-check"><input type="radio"
                                                                        name="withdraw_type"
                                                                        class="form-check-input input-primary wallet-withdraw"
                                                                        value="1" data-type="Bank_Withdrawal"><label
                                                                        class="form-check-label d-block"
                                                                        for="payopn-check-1"><span
                                                                            class="card-body p-2 d-block"><span
                                                                                class="h6 f-w-500 mb-1 d-block">BANK
                                                                                WITHDRAWAL</span><span
                                                                                class="d-flex align-items-center"><span
                                                                                    class="f-10 badge bg-light-success me-1">BANK
                                                                                    WITHDRAWAL</span><img
                                                                                    src="/assets/images/bank.png"
                                                                                    alt="img"
                                                                                    class="img-fluid ms-1 wid-25"></span></span></label>
                                                                </div>
                                                            </div>
                                                        </div>
														<div class="col-md-3 col-lg-4 col-xl-4">
                                                            <div class="address-check border rounded">
                                                                <div class="form-check"><input type="radio"
                                                                        name="withdraw_type"
                                                                        class="form-check-input input-primary wallet-withdraw"
                                                                        value="1" data-type="External_Withdrawal"><label
                                                                        class="form-check-label d-block"
                                                                        for="payopn-check-1"><span
                                                                            class="card-body p-2 d-block"><span
                                                                                class="h6 f-w-500 mb-1 d-block">External Transfer</span><span
                                                                                class="d-flex align-items-center"><span
                                                                                    class="f-10 badge bg-light-success me-1">CLIENT TO CLIENT</span></span></span></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if (!empty(config('services.binance.api_key')))
                                                            <div class="col-md-3 col-lg-4 col-xl-4">
                                                                <div class="address-check border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="withdraw_type"
                                                                            class="form-check-input input-primary wallet-withdraw"
                                                                            id="option_binancepay_withdraw" value="1" data-type="BinancePay_Withdrawal">
                                                                        <label class="form-check-label d-block" for="option_binancepay_withdraw">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span class="d-flex flex-wrap align-items-center justify-content-between">
                                                                                    <span>Crypto Withdrawal (Binance Pay)</span>
                                                                                    <span>
                                                                                        <span class="h6 f-w-500 mb-1 d-block">
                                                                                            <img src="/assets/images/binancepay.png" alt="Binance Pay" style="height: 30px;" onerror="this.style.display='none'">
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="divider my-4"><span>WITHDRAW DETAILS</span></div>
                                                    <div class="wallet-withdrawal Wallet_Withdrawal">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material withdrawForm">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="withdraw_type"
                                                                    class="withdraw-type" value="Wallet_Withdrawal">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            SELECT WALLET ACCOUNT:
                                                                            <small class="text-muted d-block">
                                                                                Please select the Wallet account to which
                                                                                you wish to transfer your funds
                                                                            </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            @if (count($client_wallets) == 0)
                                                                                <div class="form-group">
                                                                                    <button type="button"
                                                                                        class="btn btn-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#addWalletModal">
                                                                                        <i class="ti ti-plus f-18"></i> Add
                                                                                        Wallet Information
                                                                                    </button>
                                                                                </div>
                                                                            @else
                                                                                <select name="client_bank" required
                                                                                    id="wallet-account-select"
                                                                                    class="form-control fill"
                                                                                    style="color:black;">
                                                                                    @foreach ($client_wallets as $bank)
                                                                                        <option
                                                                                            value="{{ $bank->client_wallet_id }}"
                                                                                            data-address="{{ $bank->wallet_address ?? '' }}">
                                                                                            {{ $bank->wallet_name }} /
                                                                                            {{ $bank->wallet_currency }} /
                                                                                            {{ $bank->wallet_network }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div id="wallet-address-hint" class="mt-2 small text-muted border border-success rounded p-2" style="display: none;">
                                                                                    <span class="d-flex align-items-center">
                                                                                        <i class="ti ti-circle-check text-success me-2 f-20"></i>
                                                                                        <span><span class="text-muted">Address:</span> <span id="wallet-address-value"></span></span>
                                                                                    </span>
                                                                                </div>
                                                                                
                                                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                                                                    <div id="wallet-address-confirm-wrap" class="form-check mb-0" style="display: none;">
                                                                                        <input type="checkbox" class="form-check-input" id="wallet-address-confirm">
                                                                                        <label class="form-check-label small" for="wallet-address-confirm" style=" cursor: pointer;">I confirm that the wallet address is correct.</label>
                                                                                    </div>
                                                                                    <small data-bs-toggle="modal"
                                                                                        data-bs-target="#addWalletModal"
                                                                                        style="color: var(--primary-color); cursor: pointer;">
                                                                                        + Add another wallet
                                                                                    </small>
                                                                                    
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            Your Wallet Balance:
                                                                            <small class="text-muted d-block">(USD)</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <input type="number"
                                                                                    name="wallet_balance"
                                                                                    value="{{ $wallet_balance }}" readonly
                                                                                    class="form-control fill wallet_balance">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER AMOUNT:
                                                                            <small class="text-muted d-block">Please enter
                                                                                the amount that you need to withdraw</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">$</span>
                                                                                <input type="number" class="form-control withdraw_amount"
                                                                                    name="withdraw_amount" min="10"
                                                                                    aria-label="Amount (to the nearest dollar)"
                                                                                    @if (count($client_wallets) > 0) required @endif>
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-lg-4 col-form-label">Verification
                                                                            OTP :
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text otp-req" data-type="Wallet_Withdrawal">Send OTP</span>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    name="otp" disabled required data-type="Wallet_Withdrawal">
                                                                                <span class="input-group-text">
                                                                                    <i class="feather icon-info mb-auto mt-auto"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="You will receive OTP on your registered email address"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if (count($client_wallets) > 0)
                                                                <div class="row">
                                                                    <div class="col-lg-4"></div>
                                                                    <div class="col-lg-8">
                                                                        <div class="row g-1">
                                                                            <input type="submit" name="wallet_withdraw"
                                                                                class="btn btn-primary col-12"
                                                                                value="Withdraw From Wallet">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </form>
                                                    </div>

                                                    <div class="wallet-withdrawal USDT_Withdrawal" style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material withdrawForm"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="withdraw_type"
                                                                    class="withdraw-type" value="USDT_Withdrawal">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER AMOUNT:
                                                                            <small class="text-muted d-block">Please enter
                                                                                the amount that you need to transfer</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">$</span>
                                                                                <input type="number" class="form-control withdraw_amount"
                                                                                    name="withdraw_amount"
                                                                                    aria-label="Amount (to the nearest dollar)"
                                                                                    required min="10">
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER WALLET ID:
                                                                            <small class="text-muted d-block">Please enter
                                                                                your Wallet ID</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <input type="text" class="form-control"
                                                                                    name="wallet_id"
                                                                                    aria-label="Enter your Wallet ID"
                                                                                    required>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            UPLOAD USDT WALLET QR CODE:
                                                                            <small class="text-muted d-block">Upload your
                                                                                Wallet QR CODE</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <input type="file"
                                                                                accept="application/pdf,image/png,image/jpeg,image/jpg"
                                                                                class="form-control" required
                                                                                name="wallet_qr">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-lg-4 col-form-label">Verification
                                                                            OTP :
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span
                                                                                    class="input-group-text otp-req" data-type="USDT_Withdrawal">Send
                                                                                    OTP</span>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    name="otp" disabled required data-type="USDT_Withdrawal">
                                                                                <span class="input-group-text">
                                                                                    <i class="feather icon-info mb-auto mt-auto"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="You will receive OTP on your registered email address"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-4"></div>
                                                                <div class="col-lg-8">
                                                                    <div class="row g-1">
                                                                        <input type="submit" name="usdt_withdraw"
                                                                            class="btn btn-primary col-12"
                                                                            value="Withdraw From Wallet">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="wallet-withdrawal Other_Withdrawal" style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material withdrawForm">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="withdraw_type"
                                                                    class="withdraw-type" value="Other_Withdrawal">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER AMOUNT:
                                                                            <small class="text-muted d-block">
                                                                                Please enter the amount that you need to
                                                                                transfer
                                                                            </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">$</span>
                                                                                <input type="number" class="form-control withdraw_amount"
                                                                                    aria-label="Amount (to the nearest dollar)"
                                                                                    required name="withdraw_amount" min="10">
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            CLIENT NOTE:
                                                                            <small class="text-muted d-block"></small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <input type="text" class="form-control"
                                                                                    aria-label="Client Note" required
                                                                                    name="client_note">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-lg-4 col-form-label">Verification
                                                                            OTP :
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span
                                                                                    class="input-group-text otp-req" data-type="Other_Withdrawal">Send
                                                                                    OTP</span>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    name="otp" disabled required data-type="Other_Withdrawal">
                                                                                <span class="input-group-text">
                                                                                    <i class="feather icon-info mb-auto mt-auto"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="You will receive OTP on your registered email address"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-lg-4"></div>
                                                                <div class="col-lg-8">
                                                                    <div class="row g-1">
                                                                        <input type="submit" name="other_withdraw"
                                                                            class="btn btn-primary col-12"
                                                                            value="Withdraw From Wallet">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="wallet-withdrawal BinancePay_Withdrawal" style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material withdrawForm">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="withdraw_type" value="Binance Pay">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            SELECT WALLET ACCOUNT:
                                                                            <small class="text-muted d-block">
                                                                                Please select the Wallet account to which
                                                                                you wish to transfer your funds
                                                                            </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            @if (count($client_wallets) == 0)
                                                                                <div class="form-group">
                                                                                    <button type="button"
                                                                                        class="btn btn-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#addWalletModal">
                                                                                        <i class="ti ti-plus f-18"></i> Add
                                                                                        Wallet Information
                                                                                    </button>
                                                                                </div>
                                                                            @else
                                                                                <select name="client_bank" required
                                                                                    id="binancepay-wallet-account-select"
                                                                                    class="form-control fill binancepay-wallet-select"
                                                                                    style="color:black;">
                                                                                    @foreach ($client_wallets as $bank)
                                                                                        <option
                                                                                            value="{{ $bank->client_wallet_id }}"
                                                                                            data-address="{{ $bank->wallet_address ?? '' }}">
                                                                                            {{ $bank->wallet_name }} /
                                                                                            {{ $bank->wallet_currency }} /
                                                                                            {{ $bank->wallet_network }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div id="binancepay-wallet-address-hint" class="mt-2 small text-muted border border-success rounded p-2" style="display: none;">
                                                                                    <span class="d-flex align-items-center">
                                                                                        <i class="ti ti-circle-check text-success me-2 f-20"></i>
                                                                                        <span><span class="text-muted">Address:</span> <span id="binancepay-wallet-address-value"></span></span>
                                                                                    </span>
                                                                                </div>
                                                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                                                                    <div id="binancepay-wallet-address-confirm-wrap" class="form-check mb-0" style="display: none;">
                                                                                        <input type="checkbox" class="form-check-input" id="binancepay-wallet-address-confirm">
                                                                                        <label class="form-check-label small" for="binancepay-wallet-address-confirm" style="cursor: pointer;">I confirm that the wallet address is correct.</label>
                                                                                    </div>
                                                                                    <small data-bs-toggle="modal"
                                                                                        data-bs-target="#addWalletModal"
                                                                                        style="color: var(--primary-color); cursor: pointer;">
                                                                                        + Add another wallet
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            Your Wallet Balance:
                                                                            <small class="text-muted d-block">(USD)</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <input type="number"
                                                                                    name="wallet_balance"
                                                                                    value="{{ $wallet_balance }}" readonly
                                                                                    class="form-control fill wallet_balance">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER AMOUNT:
                                                                            <small class="text-muted d-block">Please enter
                                                                                the amount that you need to withdraw</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">$</span>
                                                                                <input type="number" class="form-control withdraw_amount"
                                                                                    name="withdraw_amount" min="10"
                                                                                    aria-label="Amount (to the nearest dollar)"
                                                                                    @if (count($client_wallets) > 0) required @endif>
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">Verification OTP :</label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text otp-req" data-type="Binance Pay">Send OTP</span>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    name="otp" disabled required data-type="Binance Pay">
                                                                                <span class="input-group-text">
                                                                                    <i class="feather icon-info mb-auto mt-auto"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="You will receive OTP on your registered email address"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if (count($client_wallets) > 0)
                                                                <div class="row">
                                                                    <div class="col-lg-4"></div>
                                                                    <div class="col-lg-8">
                                                                        <div class="row g-1">
                                                                            <input type="submit" name="wallet_withdraw"
                                                                                class="btn btn-primary col-12"
                                                                                value="Withdraw From Wallet">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </form>
                                                    </div>
                                                    <div class="wallet-withdrawal Bank_Withdrawal" style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material withdrawForm">
                                                            @csrf
                                                            <div class="row">
                                                                <input type="hidden" name="withdraw_type"
                                                                    class="withdraw-type" value="Bank_Withdrawal">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            SELECT ACCOUNT:
                                                                            <small class="text-muted d-block">
                                                                                Please select the Wallet account to which
                                                                                you wish to transfer your funds
                                                                            </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            @if (count($client_banks) == 0)
                                                                                <div class="form-group">
                                                                                    <button type="button"
                                                                                        class="btn btn-primary"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#addBankModal2">
                                                                                        <i class="ti ti-plus f-18"></i> Add
                                                                                        Bank Details
                                                                                    </button>
                                                                                </div>
                                                                            @else
                                                                                <select name="client_bank" required
                                                                                    id="bank-account-select"
                                                                                    class="form-control fill"
                                                                                    style="color:black;">
                                                                                    @foreach ($client_banks as $bank)
                                                                                        <option
                                                                                            value="{{ $bank->id }}"
                                                                                            data-account-number="{{ $bank->accountNumber ?? '' }}">
                                                                                            {{ $bank->accountNumber }}/
                                                                                            {{ $bank->ClientName }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                <div id="bank-account-hint" class="mt-2 small text-muted border border-success rounded p-2" style="display: none;">
                                                                                    <span class="d-flex align-items-center">
                                                                                        <i class="ti ti-circle-check text-success me-2 f-20"></i>
                                                                                        <span><span class="text-muted">Account number:</span> <span id="bank-account-value"></span></span>
                                                                                    </span>
                                                                                </div>
                                                                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mt-2">
                                                                                    
                                                                                    <div id="bank-account-confirm-wrap" class="form-check mb-0" style="display: none;">
                                                                                        <input type="checkbox" class="form-check-input" id="bank-account-confirm">
                                                                                        <label class="form-check-label small" for="bank-account-confirm" style="cursor: pointer;">I confirm that the account number is correct.</label>
                                                                                    </div>
                                                                                    <small data-bs-toggle="modal"
                                                                                        data-bs-target="#addBankModal2"
                                                                                        style="color: var(--primary-color); cursor: pointer;">
                                                                                        + Add Another Account
                                                                                    </small>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            Your Wallet Balance:
                                                                            <small class="text-muted d-block">(USD)</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <input type="number"
                                                                                    name="wallet_balance"
                                                                                    value="{{ $wallet_balance }}" readonly
                                                                                    class="form-control fill wallet_balance">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">
                                                                            ENTER AMOUNT:
                                                                            <small class="text-muted d-block">Please enter
                                                                                the amount that you need to withdraw</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">$</span>
                                                                                <input type="number" class="form-control withdraw_amount"
                                                                                    name="withdraw_amount" min="10"
                                                                                    aria-label="Amount (to the nearest dollar)"
                                                                                    @if (count($client_banks) > 0) required @endif>
                                                                                <span class="input-group-text">.00</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <div class="form-group row">
                                                                        <label
                                                                            class="col-lg-4 col-form-label">Verification
                                                                            OTP :
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span
                                                                                    class="input-group-text otp-req" data-type="Bank_Withdrawal">Send
                                                                                    OTP</span>
                                                                                <input type="number"
                                                                                    class="form-control"
                                                                                    name="otp" disabled required data-type="Bank_Withdrawal">
                                                                                <span class="input-group-text">
                                                                                    <i class="feather icon-info mb-auto mt-auto"
                                                                                        data-bs-toggle="tooltip"
                                                                                        data-bs-placement="top"
                                                                                        title="You will receive OTP on your registered email address"></i>
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if (count($client_banks) > 0)
                                                                <div class="row">
                                                                    <div class="col-lg-4"></div>
                                                                    <div class="col-lg-8">
                                                                        <div class="row g-1">
                                                                            <input type="submit" name="wallet_withdraw"
                                                                                class="btn btn-primary col-12"
                                                                                value="Withdraw From Wallet">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </form>
                                                    </div>
													
													<div class="wallet-withdrawal External_Withdrawal" style="display:none">
														<form method="post" style="padding:10px;" class="md-float-material form-material" action="{{ route('wallet.transferto') }}" id="wallettransferto" >
															<input type="hidden" name="walletBalance" id="walletBalance" class="form-control" value="{{ $walletBalance }}" />
															<div class="col-12 mt-2">
																<div class="form-group row">
																	<label class="col-lg-4 col-form-label">
																		Your Wallet Balance:
																		<small class="text-muted d-block">(USD)</small>
																	</label>
																	<div class="col-lg-8">
																		<div class="input-group mb-3">
																			<input type="number"
																				name="wallet_balance"
																				value="{{ $wallet_balance }}" readonly
																				class="form-control fill wallet_balance">
																		</div>
																	</div>
																</div>
																
																<div class="form-group row">
																	<label class="col-lg-4 col-form-label">Transfer To
																		:<small class="text-muted d-block"> Please
																			Enter the transfer user email address </small></label>
																	<div class="col-lg-8">
																		<input type="email" name="transfer_emailto" class="form-control" required placeholder="example@gmail.com" />
																	</div>
																</div>
																<!--<div class="form-group row"><label
																		class="col-lg-4 col-form-label">DEPOSIT
																		CURRENCY
																		:<small class="text-muted d-block"> Please
																			select the currency you wish to use for
																			the payment </small></label>
																	<input type="hidden" name="currency"
																		value="USD">
																	<input class="deposit_type" type="hidden"
																		name="deposit_type" value="Bank-Deposit">
																	<div class="col-lg-8"><select
																			class="form-select" id="currencyType"
																			disabled name="currencyType">
																			<option value="USD" selected>USD
																			</option>
																		</select></div>
																</div>-->
																<div class="form-group row"><label
																		class="col-lg-4 col-form-label">ENTER
																		AMOUNT :<small class="text-muted d-block">
																			Please enter the amount to be deposited
																			in selected
																			currency</small></label>
																	<div class="col-lg-8">
																		<div class="input-group mb-3"><span
																				class="input-group-text currency-type">USD</span><input
																				type="number"
																				class="form-control wallet-amount"
																				aria-label="Amount"
																				name="wallet_amount" required>
																		</div>
																	</div>
																</div>
																<!--<div class="form-group row"><label
																		class="col-lg-4 col-form-label">AMOUNT IN
																		USD :<small class="text-muted d-block">
																			Deposit amount in USD </small></label>
																	<div class="col-lg-8">
																		<div class="input-group mb-3"><span
																				class="input-group-text">USD</span><input
																				type="text"
																				class="form-control wallet-amount-usd"
																				aria-label="Amount"
																				disabled=""></div>
																	</div>
																</div>-->
																<div class="">
																	<div class="row">
																		<div class="col-lg-4"></div>
																		<div class="col-lg-8">
																			<div class="row g-1">
																				<input type="submit" name="add_wallet" class="btn btn-primary col-12" value="Transfer Payment" />
																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</form>													
													</div>													
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div class="card coupon-card bg-primary">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div
                                                                class="col-8 d-flex flex-column align-items-start justify-content-center">
                                                                <h3 class="text-white f-w-500">Fuel Your Trading Journey
                                                                </h3>
                                                                <span class="f-16 py-2 text-white">Deposit now and unlock
                                                                    the gateway to global markets.</span>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <img src="/assets/images/fund_now.png" alt="img"
                                                                    class="img-fluid wid-110">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>MT5 ACCOUNTS SUMMARY</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($liveaccount_details as $liveaccount)
                                                                <li class="list-group-item">
                                                                    <div class="media align-items-start">
                                                                        <span class="h4 mb-0 d-block f-w-500 pb-0">
                                                                            <img src="/assets/images/mt5.png"
                                                                                alt="user-image" class="wid-25 me-1 ms-1">
                                                                        </span>
                                                                        <div class="media-body mx-2">
                                                                            <h5 class="mb-1">
                                                                                <span
                                                                                    class="h4 mb-0 d-block f-w-500 pb-0">{{ $liveaccount->trade_id }}</span>
                                                                            </h5>
                                                                            <p class="text-sm mb-2">
                                                                                <span class="text-muted">ACCOUNT CATEGORY
                                                                                    :</span> ECN
                                                                            </p>
                                                                            <div class="border-top border-dashed">
                                                                                <p class="mb-1 mt-2">
                                                                                    <span class="text-muted">LEVERAGE
                                                                                        :</span>
                                                                                    {{ $liveaccount->leverage }}
                                                                                    <span class="text-muted">| CREDIT
                                                                                        :</span> $0.0000
                                                                                    <span class="text-muted">| EQUITY :
                                                                                        ${{ $liveaccount->equity }}</span>
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-shrink-0">
                                                                            <h4 class="f-w-500">
                                                                                ${{ $liveaccount->Balance }}</h4>
                                                                            <p class="text-muted text-sm mb-2 text-end">
                                                                                Balance</p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">$0.0000</h4>
                                                                </div>
                                                                <span class="text-muted">TOTAL CREDIT</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">${{ $totals->equity }}</h4>
                                                                </div>
                                                                <span class="text-muted">TOTAL EQUITY</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-body py-2">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item px-0">
                                                                <div class="float-end">
                                                                    <h3 class="mb-0 fw-medium">${{ $totals->balance }}
                                                                    </h3>
                                                                </div>
                                                                <h5 class="mb-0 d-inline-block">TOTAL BALANCE</h5>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
										@endif
                                        </div>
                                    </div>
									@else
										
									<div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
											<div class="row p-3">
												<div class="card-body text-center">
													<div class="text-center me-4"><a href="/transactions/deposit#"><img
																src="/assets/images/doc_upload.png" class="w-25" alt="img"></a></div>
													<h6 class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">Your KYC is Awaiting
														Approval!
													</h6>
													<a href="/user-profile#kyc" id="verify-user-kyc-disabled" class="mt-3">
														<button class="btn btn-outline-primary"><span class="text-truncate">Verify Now To
																Proceed</span></button>
													</a>
												</div>
											</div>
										</div>
								@endif
									
                                </div>
								
								
								
                            </div>

                        </div>
                    </div>
                    <!---->
                </div>
            </div>
        </div>
    </div>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        </script>
    @endif
    <script>
		$('.withdrawForm').on('submit', function(e) {
			const $form = $(this);
			
            const $btn = $form.find('input[type="submit"]');
			
			let balance = parseFloat($('.wallet_balance').val());
            let amount = parseFloat($('.withdraw_amount').val());

			// Balance Check
			if (amount > balance) {
				e.preventDefault();
				Swal.fire({
					icon: 'error',
					title: 'Insufficient Wallet Balance',
					html: '<b>Wallet Balance:</b> $' + balance +
						'<br><b>Entered Amount:</b> $' + amount,
					confirmButtonText: 'OK'
				});
				return false;
			}
			// Confirmation checkbox (wallet address / bank account)
			var withdrawType = $form.find('input[name="withdraw_type"]').val();
			if (withdrawType === 'Wallet_Withdrawal') {
				if (!$('#wallet-address-confirm').is(':checked')) {
					e.preventDefault();
					Swal.fire({
						icon: 'warning',
						title: 'Confirmation required',
						text: 'Please confirm that the wallet address is correct.'
					});
					return false;
				}
			}
			if (withdrawType === 'Bank_Withdrawal') {
				if (!$('#bank-account-confirm').is(':checked')) {
					e.preventDefault();
					Swal.fire({
						icon: 'warning',
						title: 'Confirmation required',
						text: 'Please confirm that the account number is correct.'
					});
					return false;
				}
			}
			if (withdrawType === 'Binance Pay') {
				if (!$('#binancepay-wallet-address-confirm').is(':checked')) {
					e.preventDefault();
					Swal.fire({
						icon: 'warning',
						title: 'Confirmation required',
						text: 'Please confirm that the wallet address is correct.'
					});
					return false;
				}
			}
			e.preventDefault();
			
			$btn.html(
				'<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Please wait...'
			);
			$btn.prop('disabled', true);
			// SweetAlert - Waiting Message
			Swal.fire({
				title: "Processing...",
				text: "Your wallet withdrawal request has been sent for admin approval. Once approved, you will be notified via your registered email ID.",
				icon: "info",
				allowOutsideClick: false,
				allowEscapeKey: false,
				showConfirmButton: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			// Submit after slight delay to show UI
			setTimeout(() => {
				//$form.off('submit').submit();
				$form[0].submit();
			}, 500);
		});
		
        $(".otp-req").click(function(e) {
            e.preventDefault();
			
			let balance = parseFloat($('.wallet_balance').val());
            let amount = parseFloat($('.withdraw_amount').val());

			// Balance Check
			if (amount > balance) {
				e.preventDefault();
				Swal.fire({
					icon: 'error',
					title: 'Insufficient Wallet Balance',
					html: '<b>Wallet Balance:</b> $' + balance +
						'<br><b>Entered Amount:</b> $' + amount,
					confirmButtonText: 'OK'
				});
				return false;
			}
			
            var type= $(this).closest("form").find("[name='withdraw_type']").val();
            if($(this).attr("disabled")){
                return true;
            }
			
            $.ajax({
                url: "/getOtp",
                type: "POST",
                data: {
                    "action": "getPOotp",
                    "type": type
                },
                beforeSend: function() {
                    $(".otp-req[data-type='" + type + "']").attr("disabled", "true");
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    if (data.success == true) {
                        $("[name='otp'][data-type='" + type + "']").removeAttr("disabled");
                        Swal.fire({
                            icon: 'success',
                            title: "Please check the Email and Put the OTP here",
                            text: "Kindly check in SPAM/JUNK folder for second case. Otherwise try again after sometime."
                        }).then((val) => {
                            $("[name='otp'][data-type='" + type + "']")[0].focus();
                        });
                    } else {
                        $(".otp-req[data-type='" + type + "']").attr("disabled", "true");
                        Swal.fire({
                            icon: 'warning',
                            title: data.message,
                        })
                    }
                }
            });
        });
		
		$(document).ready(function () {

			$("#wallettransferto").on("submit", function (e) {
				e.preventDefault(); 

				let form = this;
				let walletBalance = parseFloat($("#walletBalance").val());
				let enteredAmount = parseFloat($(".wallet-amount").val());
				let emailTo = $("input[name='transfer_emailto']").val();

				// Validate email
				if (!emailTo || !emailTo.includes("@")) {
					Swal.fire("Invalid Email", "Please enter a valid email address.", "warning");
					return false;
				}

				// Validate amount
				if (!enteredAmount || enteredAmount <= 0) {
					Swal.fire("Invalid Amount", "Please enter a valid transfer amount.", "warning");
					return false;
				}

				// Amount greater than balance
				if (enteredAmount > walletBalance) {
					Swal.fire({
						icon: "error",
						title: "Insufficient Balance",
						html: 'Your wallet balance is <b>$'+walletBalance+' </b>.<br>You cannot transfer <b>$'+enteredAmount+'</b>.'
					});
					return false;
				}

				// Confirm Transfer
				Swal.fire({
					title: "Confirm Transfer?",
					html: 'Transfer <b>$'+enteredAmount+'</b> to <b>'+emailTo+'</b>?<br>This will go for admin approval.',
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Yes, Proceed",
					cancelButtonText: "Cancel"
				}).then((result) => {
					if (result.isConfirmed) {
						// AJAX SUBMIT
						$.ajax({
							url: "{{ route('wallet.transferto') }}", 
							method: "POST",
							data: $(form).serialize(),
							headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },

							beforeSend: function () {
								Swal.fire({
									title: "Processing...",
									text: "Please wait",
									allowOutsideClick: false,
									didOpen: () => Swal.showLoading()
								});
							},

							success: function (response) {
								Swal.close();

								if (response.status === "success") {
									Swal.fire({
										icon: "success",
										title: "Transfer Submitted!",
										text: "Amount Transferred to mentioned wallet.",
									}).then(() => {
										location.reload();
									});
								}

								else {
									Swal.fire({
										icon: "error",
										title: "Error",
										text: response.message
									});
								}
							},

							error: function () {
								Swal.fire("Error", "Something went wrong.", "error");
							}
						});
					}
				});
			});
		});

		// Wallet address hint below wallet account dropdown (Crypto withdrawal)
		function updateWalletAddressHint() {
			var $select = $('#wallet-account-select');
			var $hint = $('#wallet-address-hint');
			var $confirmWrap = $('#wallet-address-confirm-wrap');
			var $value = $('#wallet-address-value');
			if ($select.length && $hint.length) {
				var address = $select.find('option:selected').attr('data-address') || '';
				if (address) {
					$value.text(address);
					$hint.show();
					$confirmWrap.show();
				} else {
					$hint.hide();
					$confirmWrap.hide();
					$('#wallet-address-confirm').prop('checked', false);
				}
			}
		}
		$('#wallet-account-select').on('change', updateWalletAddressHint);
		$(document).ready(updateWalletAddressHint);

		// Binance Pay wallet address hint (same as Crypto withdrawal)
		function updateBinancePayWalletAddressHint() {
			var $select = $('#binancepay-wallet-account-select');
			var $hint = $('#binancepay-wallet-address-hint');
			var $confirmWrap = $('#binancepay-wallet-address-confirm-wrap');
			var $value = $('#binancepay-wallet-address-value');
			if ($select.length && $hint.length) {
				var address = $select.find('option:selected').attr('data-address') || '';
				if (address) {
					$value.text(address);
					$hint.show();
					$confirmWrap.show();
				} else {
					$hint.hide();
					$confirmWrap.hide();
					$('#binancepay-wallet-address-confirm').prop('checked', false);
				}
			}
		}
		$('#binancepay-wallet-account-select').on('change', updateBinancePayWalletAddressHint);
		$(document).ready(updateBinancePayWalletAddressHint);

		// Bank account number hint below bank account dropdown (Bank withdrawal)
		function updateBankAccountHint() {
			var $select = $('#bank-account-select');
			var $hint = $('#bank-account-hint');
			var $confirmWrap = $('#bank-account-confirm-wrap');
			var $value = $('#bank-account-value');
			if ($select.length && $hint.length) {
				var accountNumber = $select.find('option:selected').attr('data-account-number') || '';
				if (accountNumber) {
					$value.text(accountNumber);
					$hint.show();
					$confirmWrap.show();
				} else {
					$hint.hide();
					$confirmWrap.hide();
					$('#bank-account-confirm').prop('checked', false);
				}
			}
		}
		$('#bank-account-select').on('change', updateBankAccountHint);
		$(document).ready(updateBankAccountHint);
    </script>
	@include('pgi_cryptoChill')
@endsection
