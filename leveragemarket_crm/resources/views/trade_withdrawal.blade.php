@extends('layouts.crm.crm')
@section('styles')
    <style>
        .modal-body::-webkit-scrollbar {
            width: 15px;
        }
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
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <iframe id="checkoutIframe" style="width: 100vw; height: 100vh;border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Fund</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            @include('sub_header')
                        </div>
                    </div>
					@if($liveaccount_details && $liveaccount_details->isNotEmpty())
                    <div class="tab-content">
                        <div>
                            <div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <?php if ($existing) { ?>
                                            <div class="col-xl-8">
                                                <div class="px-5 table-responsive">
                                                    <div class="auth-main">
                                                        <div class="card-body">
                                                            <div class="text-center me-4">
                                                                <svg version="1.1" id="Layer_1"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                                                                    y="0px" width="250px" height="200px"
                                                                    viewBox="0 0 250 200"
                                                                    style="enable-background:new 0 0 250 200;"
                                                                    xml:space="preserve">
                                                                    <style type="text/css">
                                                                        .st0 {
                                                                            fill: #D0EFF7;
                                                                        }

                                                                        .st1 {
                                                                            fill: none;
                                                                            stroke: #0069AA;
                                                                            stroke-width: 4.8368;
                                                                            stroke-linecap: round;
                                                                            stroke-linejoin: round;
                                                                        }

                                                                        .st2 {
                                                                            fill: #0F3D47;
                                                                        }

                                                                        .st3 {
                                                                            fill: #0069AA;
                                                                        }

                                                                        .st4 {
                                                                            fill: #2B78B1;
                                                                        }

                                                                        .st5 {
                                                                            fill: none;
                                                                            stroke: #0F3D47;
                                                                            stroke-width: 4.8368;
                                                                            stroke-linecap: round;
                                                                            stroke-linejoin: round;
                                                                        }

                                                                        .st6 {
                                                                            fill: #0F3D47;
                                                                            stroke: #0F3D47;
                                                                            stroke-miterlimit: 10;
                                                                        }

                                                                        .st7 {
                                                                            fill:
                                                                                <?=$settings['sidebar_color'] ?>;
                                                                            stroke: #5A6771;
                                                                            stroke-width: 0.7045;
                                                                            stroke-miterlimit: 10;
                                                                        }

                                                                        .st8 {
                                                                            fill: #EEEEEE;
                                                                        }

                                                                        .st9 {
                                                                            fill: none;
                                                                            stroke: #333333;
                                                                            stroke-width: 3.1704;
                                                                            stroke-linecap: round;
                                                                            stroke-linejoin: round;
                                                                            stroke-miterlimit: 10;
                                                                        }

                                                                        .st10 {
                                                                            fill: #FFFFFF;
                                                                        }

                                                                        .st11 {
                                                                            fill: none;
                                                                            stroke: #0F3D47;
                                                                            stroke-width: 3.1704;
                                                                            stroke-linecap: round;
                                                                            stroke-linejoin: round;
                                                                            stroke-miterlimit: 10;
                                                                        }
                                                                    </style>
                                                                    <g>
                                                                        <circle class="st0" cx="125" cy="100"
                                                                            r="85.5" />
                                                                        <g>
                                                                            <line class="st1" x1="122.3"
                                                                                y1="43.7" x2="133.8"
                                                                                y2="43.7" />
                                                                            <line class="st1" x1="128.1"
                                                                                y1="38" x2="128.1"
                                                                                y2="49.5" />
                                                                        </g>
                                                                        <path class="st2"
                                                                            d="M146.2,35c0-1.6-1.3-3-3-3c-1.6,0-3,1.3-3,3c0,1.6,1.3,3,3,3S146.2,36.7,146.2,35z" />
                                                                        <g>
                                                                            <path class="st2" d="M198.1,158.8c-0.5,2.2,1,4.4,3.2,4.9s4.4-0.9,4.9-3.2c0.5-2.2-1-4.4-3.2-4.9
                                  C200.8,155.2,198.6,156.6,198.1,158.8z" />
                                                                            <path class="st3" d="M205.2,147.4c-0.3,1.2,0.5,2.4,1.7,2.6c1.2,0.3,2.4-0.5,2.6-1.7c0.3-1.2-0.5-2.4-1.7-2.6
                                  C206.7,145.4,205.5,146.2,205.2,147.4z" />
                                                                        </g>
                                                                        <g>
                                                                            <g>
                                                                                <g>
                                                                                    <path class="st4" d="M183.6,146.6H65c-4.1,0-7.4-3.3-7.4-7.4V75.2c0-4.1,3.3-7.4,7.4-7.4h118.6c4.1,0,7.4,3.3,7.4,7.4v63.9
                                      C191.1,143.2,187.7,146.6,183.6,146.6z" />
                                                                                    <path class="st5" d="M183.6,146.6H65c-4.1,0-7.4-3.3-7.4-7.4V75.2c0-4.1,3.3-7.4,7.4-7.4h118.6c4.1,0,7.4,3.3,7.4,7.4v63.9
                                      C191.1,143.2,187.7,146.6,183.6,146.6z" />
                                                                                    <rect x="57.6" y="84.8" class="st6"
                                                                                        width="133.5" height="20.3" />
                                                                                    <polygon class="st5"
                                                                                        points="191.1,105.1 124.3,105.1 57.6,105.1 57.6,95 57.6,84.8 124.3,84.8 191.1,84.8 191.1,95 				" />
                                                                                    <g>
                                                                                        <line class="st5" x1="69.8"
                                                                                            y1="119.1" x2="145.8"
                                                                                            y2="119.1" />
                                                                                        <line class="st5" x1="92.9"
                                                                                            y1="132.5" x2="128.4"
                                                                                            y2="132.5" />
                                                                                        <line class="st5" x1="69.8"
                                                                                            y1="132.5" x2="83.2"
                                                                                            y2="132.5" />
                                                                                    </g>
                                                                                </g>
                                                                            </g>
                                                                            <g>
                                                                                <g>
                                                                                    <path class="st7" d="M198.7,103.8h-41.4c-4.3,0-7.7-3.5-7.7-7.7V61.8c0-4.3,3.5-7.7,7.7-7.7h41.4c4.3,0,7.7,3.5,7.7,7.7v34.3
                                      C206.4,100.4,202.9,103.8,198.7,103.8z" />
                                                                                    <g>
                                                                                        <path class="st8" d="M183.7,74c0-3.2-2.6-5.8-5.9-5.7c-3,0.1-5.5,2.6-5.5,5.7c0,2.2,1.2,4.1,3.1,5.1v9.6c0,0.6,0.5,1.1,1.1,1.1
                                        h3.1c0.6,0,1.1-0.5,1.1-1.1V79C182.4,78.1,183.7,76.2,183.7,74z" />
                                                                                        <path class="st9" d="M183.7,74c0-3.2-2.6-5.8-5.9-5.7c-3,0.1-5.5,2.6-5.5,5.7c0,2.2,1.2,4.1,3.1,5.1v9.6c0,0.6,0.5,1.1,1.1,1.1
                                        h3.1c0.6,0,1.1-0.5,1.1-1.1V79C182.4,78.1,183.7,76.2,183.7,74z" />
                                                                                    </g>
                                                                                    <path class="st10" d="M198,47.3c0-11.1-9-20-20-20l0,0c-11.1,0-20,9-20,20v6.8h7.1v-6.8c0-7.1,5.8-13,13-13c7.1,0,13,5.8,13,13
                                      v6.8h7.1V47.3z" />
                                                                                    <path class="st11" d="M198,47.3c0-11.1-9-20-20-20l0,0c-11.1,0-20,9-20,20v6.8h7.1v-6.8c0-7.1,5.8-13,13-13c7.1,0,13,5.8,13,13
                                      v6.8h7.1V47.3z" />
                                                                                    <path class="st11" d="M198.7,103.8h-41.4c-4.3,0-7.7-3.5-7.7-7.7V61.8c0-4.3,3.5-7.7,7.7-7.7h41.4c4.3,0,7.7,3.5,7.7,7.7v34.3
                                      C206.4,100.4,202.9,103.8,198.7,103.8z" />
                                                                                </g>
                                                                            </g>
                                                                        </g>
                                                                    </g>
                                                                </svg>
                                                            </div>
                                                            <h6 class="text-center text-secondary f-w-400 mb-0 f-16 mt-2">A
                                                                withdrawal request is
                                                                already pending. Please wait for confirmation or reach out
                                                                to support before creating
                                                                another request.</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } else{ ?>
                                            <div class="col-xl-8">
                                                <div class="card">
                                                    <div class="card-body border-bottom">
                                                        <h6>CREATE WITHDRAW TICKET</h6>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="divider my-4"><span>SELECT MT5 ACCOUNT</span></div>
                                                        <div class="row g-1">
                                                            @foreach ($liveaccount_details as $liveaccount)
                                                                <div class="col-md-3 col-lg-4 col-xl-4">
                                                                    <div class="address-check border rounded">
                                                                        <div class="form-check paycard">
                                                                            <input
                                                                                id="liveaccount{{ $liveaccount->trade_id }}"
                                                                                type="radio" name="live-account"
                                                                                class="select-liveaccount form-check-input input-primary"
                                                                                data-balance="{{ $liveaccount->Balance }}"
                                                                                value="{{ $liveaccount->trade_id }}">
                                                                            <label class="form-check-label d-block"
                                                                                required>
                                                                                <div class="p-1 my-1">
                                                                                    <span class="row">
                                                                                        <span class="col-6 mt-1">
                                                                                            <span
                                                                                                class="h5 mb-0 d-block f-w-500 pb-0 f-14">
                                                                                                <img src="{{ asset('assets/images/mt5.png') }}"
                                                                                                    alt="user-image"
                                                                                                    class="wid-25 me-1 ms-1">
                                                                                                {{ $liveaccount->trade_id }}
                                                                                            </span>
                                                                                        </span>
                                                                                        <span
                                                                                            class="col-6 text-end mb-0 pb-0 pe-3">
                                                                                            <span
                                                                                                class="h5 mb-0 d-block f-w-500">
                                                                                                ${{ $liveaccount->Balance ?? '0.0000' }}
                                                                                            </span>
                                                                                            <span
                                                                                                class="text-muted mb-0 f-10">Current
                                                                                                Balance</span>
                                                                                        </span>
																						<span class="text-muted mb-0 f-12 f-w-100"><b>{{ $liveaccount->accountType->ac_name }}</b></span>
                                                                                    </span>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="divider my-4"><span>SELECT WITHDRAW METHOD</span>
                                                        </div>
                                                        <div class="row g-1">
                                                            @if ($walletenabled)
                                                                <div class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                                                    <div
                                                                        class="address-check trade-withdraw-type border rounded">
                                                                        <div class="form-check">
                                                                            <input type="radio" name="withdraw_type"
                                                                                class="form-check-input input-primary tradefund-deposit wallet-withdraw"
                                                                                id="wallet_withdraw"
                                                                                value="Wallet Transfer"
                                                                                data-type="Wallet-Transfer" checked>
                                                                            <label class="form-check-label d-block"
                                                                                for="wallet_withdraw">
                                                                                <span class="card-body p-2 d-block">
                                                                                    <span
                                                                                        class="d-flex align-items-center">
                                                                                        <span>
                                                                                            <span
                                                                                                class="h6 f-w-500 mb-1 d-block">Wallets</span>
                                                                                            <span
                                                                                                class="f-10 text-muted">Wallet
                                                                                                Transfer</span>
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (!empty($user_groups['agent_account']) && $user_groups['agent_status'] == 1)
                                                                <div class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                                                    <div
                                                                        class="address-check trade-withdraw-type border rounded">
                                                                        <div class="form-check">
                                                                            <input type="radio" name="withdraw_type"
                                                                                class="form-check-input input-primary tradefund-deposit"
                                                                                id="agent_withdraw"
                                                                                value="Agent Withdrawal"
                                                                                data-type="Agent-Withdrawal" checked>
                                                                            <label class="form-check-label d-block"
                                                                                for="agent_withdraw">
                                                                                <span class="card-body p-2 d-block">
                                                                                    <span
                                                                                        class="d-flex align-items-center">
                                                                                        <span>
                                                                                            <span
                                                                                                class="h6 f-w-500 mb-1 d-block">Agent
                                                                                                Withdrawal</span>
                                                                                            <span
                                                                                                class="f-10 text-muted">Agent
                                                                                                Account Withdrawal</span>
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (!empty($user_groups['crypto_payment_api']) && $user_groups['crypto_payment_withdraw'] == 1)
                                                                <div class="col-md-6 col-lg-6 col-xl-6 d-none col-sm-12">
                                                                    <div
                                                                        class="address-check trade-withdraw-type border rounded">
                                                                        <div class="form-check">
                                                                            <input type="radio" name="withdraw_type"
                                                                                class="form-check-input input-primary tradefund-deposit"
                                                                                id="match2pay" value="Crypto Payment"
                                                                                data-type="match2pay" checked>
                                                                            <label class="form-check-label d-block"
                                                                                for="match2pay">
                                                                                <span class="card-body p-2 d-block">
                                                                                    <span
                                                                                        class="d-flex align-items-center">
                                                                                        <span>
                                                                                            <span
                                                                                                class="h6 f-w-500 mb-1 d-block">Crypto
                                                                                                Wallet</span>
                                                                                            <span
                                                                                                class="f-10 text-muted">Crypto
                                                                                                Wallet</span>
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            @if (!empty($bank_details))
                                                                <!--<div class="col-md-6 col-lg-6 col-xl-6 col-sm-12" >
                                                                    <div
                                                                        class="address-check trade-withdraw-type border rounded">
                                                                        <div class="form-check"><input type="radio"
                                                                                name="withdraw_type"
                                                                                class="form-check-input wallet-withdraw input-primary tradefund-deposit"
                                                                                id="bank_withdraw"
                                                                                value="Other Withdrawal"
                                                                                data-type="Bank-Withdrawal"><label
                                                                                class="form-check-label d-block"
                                                                                for="bank_withdraw"><span
                                                                                    class="card-body p-2 d-block"><span
                                                                                        class="h6 f-w-500 mb-1 d-block">BANK
                                                                                        WITHDRAWAL</span><span
                                                                                        class="d-flex align-items-center"><span
                                                                                            class="f-10 badge bg-light-success me-3">WITHDRAW
                                                                                            TO BANK ACCOUNT</span><img
                                                                                            src="/assets/images/bank.png"
                                                                                            alt="img"
                                                                                            class="img-fluid ms-1 wid-25"></span></span></label>
                                                                        </div>
                                                                    </div>
                                                                </div> -->
                                                            @endif
                                                            <!--<div class="col-md-6 col-lg-6 col-xl-6 col-sm-12">
                                                                <div
                                                                    class="address-check trade-withdraw-type border rounded">
                                                                    <div class="form-check"><input type="radio"
                                                                            name="withdraw_type"
                                                                            class="form-check-input input-primary tradefund-deposit wallet-withdraw"
                                                                            id="usdt_withdraw" value="Other Withdrawal"
                                                                            data-type="Other-Withdrawal" checked><label
                                                                            class="form-check-label d-block"
                                                                            for="usdt_withdraw"><span
                                                                                class="card-body p-2 d-block"><span
                                                                                    class="h6 f-w-500 mb-1 d-block">USDT
                                                                                    WITHDRAWAL</span><span
                                                                                    class="d-flex align-items-center"><span
                                                                                        class="f-10 badge bg-light-success me-3">USDT-TRC20</span><img
                                                                                        src="/assets/images/trc.png"
                                                                                        alt="img"
                                                                                        class="img-fluid ms-1 wid-25"></span></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>-->

                                                        </div>
                                                        <div class="divider my-4"><span>WITHDRAW DETAILS</span></div>
                                                        <div id="walletwithdrawal"
                                                            class="wallet-withdrawal  Wallet-Transfer"
                                                            style="display:none">
                                                            <form method="post" style="padding:10px;"
                                                                class="md-float-material form-material tradeWithdrawalForm"
                                                                enctype="multipart/form-data" id="tradeWithdrawalForm">
                                                                @csrf
                                                                <input type="hidden" name="user[email]"
                                                                    value="{{ session('clogin') }}" required
                                                                    class="form-control fill">
                                                                <input type="hidden" name="trade_id" value=""
                                                                    class="user_trade_id form-control fill" readonly
                                                                    required>
                                                                <input type="hidden" name="withdraw_type"
                                                                    value="Wallet Withdrawal">

                                                                <div class="row">
                                                                    <div class="col-12 mt-2">
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label">ENTER
                                                                                AMOUNT:
                                                                                <small class="text-muted d-block">Please
                                                                                    enter the amount that you need to
                                                                                    transfer</small>
                                                                            </label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
                                                                                    <span class="input-group-text">$</span>
                                                                                    <input type="number" step="1"
                                                                                        class="form-control"
                                                                                        min="10"
                                                                                        name="withdraw_amount"
                                                                                        aria-label="Amount (to the nearest dollar)"
                                                                                        required>
                                                                                    <span
                                                                                        class="input-group-text">.00</span>
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
                                                                                        class="input-group-text otp-req" data-type="Wallet Withdrawal">Send
                                                                                        OTP</span>
                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        name="otp" disabled required data-type="Wallet Withdrawal">
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
                                                                            <input type="submit" name="fund_add"
                                                                                class="btn btn-primary col-12"
                                                                                value="Withdraw From Trade Account">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div id="bankwithdrawal" class="wallet-withdrawal Bank-Withdrawal"
                                                            style="display:none">
                                                            <form method="post" enctype="multipart/form-data"
                                                                style="padding:10px;"
                                                                class="md-float-material form-material tradeWithdrawalForm">
                                                                <input hidden name="user[email]"
                                                                    value="{{ session('clogin') }}" min="10"
                                                                    type="email" required=""
                                                                    class="form-control fill">
                                                                <input class="user_trade_id" type="hidden"
                                                                    name="trade_id" value=""
                                                                    class="form-control fill" readonly="" required>
                                                                <input id="adjustment_inr" type="hidden"
                                                                    name="adjustment_inr" value=""
                                                                    class="form-control fill" readonly="" required>
                                                                <input type="hidden" name="withdraw_type"
                                                                    value="Bank Withdrawal">
                                                                <div class="row">
                                                                    <div class="col-12 mt-2">
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">SELECT BANK
                                                                                ACCOUNT
                                                                                :<small class="text-muted d-block"> Please
                                                                                    select the bank account to which you
                                                                                    wish
                                                                                    to transfer your funds </small></label>
                                                                            <div class="col-lg-8">
                                                                                <select name="withdraw_to" required
                                                                                    class="form-control fill" >
                                                                                    <?php foreach ($bank_details as $details): ?>
                                                                                    <option
                                                                                        value="<?= $details->accountNumber ?>">
                                                                                        <?= $details->accountNumber ?>
                                                                                    </option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">WITHDRAWAL
                                                                                CURRENCY
                                                                                :<small class="text-muted d-block"> Please
                                                                                    select the currency you wish to use for
                                                                                    the
                                                                                    withdrawal </small></label>
                                                                            <div class="col-lg-8"><select
                                                                                    class="form-select"
                                                                                    id="withdrawal_currency"
                                                                                    name="withdrawal_currency" required>
                                                                                    <option value="INR">INR</option>
                                                                                    <option value="USD">USD</option>
                                                                                </select></div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">ENTER
                                                                                AMOUNT IN USD
                                                                                :<small class="text-muted d-block"> Please
                                                                                    enter the amount that you need to
                                                                                    transfer</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text">USD</span><input
                                                                                        type="number"
                                                                                        class="form-control"
                                                                                        step="1"
                                                                                        name="withdraw_amount"
                                                                                        min="10"
                                                                                        id="withdraw_amount_usd"
                                                                                        aria-label="Amount (to the nearest dollar)"
                                                                                        required><span
                                                                                        class="input-group-text">.00</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">AMOUNT IN
                                                                                <span
                                                                                    class="convertCurrencyText">INR</span>
                                                                                :<small class="text-muted d-block">
                                                                                    Withdrawal
                                                                                    Amount in Selected
                                                                                    Currency</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text convertCurrencyText">INR</span><input
                                                                                        name="amount_in_other_currency"
                                                                                        id="amount_in_other_currency"
                                                                                        type="text"
                                                                                        class="form-control fill"
                                                                                        aria-label="Amount"
                                                                                        readonly="true" required>
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
                                                                                        class="input-group-text otp-req" data-type="Bank Withdrawal">Send
                                                                                        OTP</span>
                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        name="otp" disabled required data-type="Bank Withdrawal">
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
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1"><input type="submit"
                                                                                    name="fund_add"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="Withdraw From Trade Account">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div id="usdtwithdrawal"
                                                            class="wallet-withdrawal Other-Withdrawal"
                                                            style="display:none">
                                                            <form method="post" style="padding:10px;"
                                                                class="md-float-material form-material tradeWithdrawalForm"
                                                                enctype="multipart/form-data">
                                                                <input hidden name="user[email]"
                                                                    value="{{ session('clogin') }}" min="10"
                                                                    type="email" required=""
                                                                    class="form-control fill">
                                                                <input class="user_trade_id" type="hidden"
                                                                    name="trade_id" value=""
                                                                    class="form-control fill" readonly="" required>
                                                                <input type="hidden" name="withdraw_type"
                                                                    value="USDT Withdrawal">
                                                                <div class="row">
                                                                    <div class="col-12 mt-2">

                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">ENTER
                                                                                AMOUNT
                                                                                :<small class="text-muted d-block"> Please
                                                                                    enter the amount that you need to
                                                                                    transfer</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text">$</span><input
                                                                                        type="number" step="1"
                                                                                        class="form-control"
                                                                                        name="withdraw_amount"
                                                                                        min="10"
                                                                                        aria-label="Amount (to the nearest dollar)"
                                                                                        required=""><span
                                                                                        class="input-group-text">.00</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">ENTER
                                                                                WALLET ID
                                                                                :<small class="text-muted d-block"> Please
                                                                                    enter your Wallet ID</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><input
                                                                                        type="text"
                                                                                        class="form-control"
                                                                                        name="withdraw_to"
                                                                                        aria-label="Enter your Wallet ID"
                                                                                        required></div>
                                                                            </div>
                                                                        </div><!---->
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">UPLOAD USDT
                                                                                WALLET
                                                                                QR
                                                                                CODE :<small class="text-muted d-block">
                                                                                    Upload your Wallet QR CODE
                                                                                </small></label>
                                                                            <div class="col-lg-8"><input type="file"
                                                                                    accept="application/pdf,image/png,image/jpeg,image/jpg"
                                                                                    class="form-control" required
                                                                                    name="wallet_qr"><!----><!----></div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1">
                                                                                <!---->
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
                                                                                        class="input-group-text otp-req" data-type="USDT Withdrawal">Send
                                                                                        OTP</span>
                                                                                    <input type="number"
                                                                                        class="form-control"
                                                                                        name="otp" disabled required data-type="USDT Withdrawal">
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
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1"><input type="submit"
                                                                                    name="fund_add"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="Withdraw From Trade Account">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php }?>
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
                                                                <img src="{{ asset('assets/images/fund_now.png') }}"
                                                                    alt="img" class="img-fluid wid-110">
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
                                                            @php $total = 0; @endphp
                                                            @foreach ($liveaccount_details as $liveaccount)
                                                                <li class="list-group-item">
                                                                    <div class="media align-items-start">
                                                                        <span class="h4 mb-0 d-block f-w-500 pb-0">
                                                                            <img src="{{ asset('assets/images/mt5.png') }}"
                                                                                alt="user-image" class="wid-25 me-1 ms-1">
                                                                        </span>
                                                                        <div class="media-body mx-2">
                                                                            <h5 class="mb-1">
                                                                                <span
                                                                                    class="h4 mb-0 d-block f-w-500 pb-0">{{ $liveaccount->trade_id }}</span>
                                                                            </h5>
                                                                            <p class="text-sm mb-2">
                                                                                <span class="text-muted">ACCOUNT
                                                                                    CATEGORY:</span>
                                                                                {{ $liveaccount->group_name }}
                                                                            </p>
                                                                            <div class="border-top border-dashed">
                                                                                <p class="mb-1 mt-2">
                                                                                    <span
                                                                                        class="text-muted">LEVERAGE:</span>
                                                                                    {{ $liveaccount->leverage }}<br>
                                                                                    <span class="text-muted">CREDIT:</span>
                                                                                    ${{ $liveaccount->credit }}<br>
                                                                                    <span class="text-muted">EQUITY:</span>
                                                                                    ${{ $liveaccount->equity }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-shrink-0">
                                                                            <h4 class="f-w-500">
                                                                                ${{ $liveaccount->Balance }}
                                                                            </h4>
                                                                            <p class="text-muted text-sm mb-2 text-end">
                                                                                Balance</p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @php $total += $liveaccount->credit; @endphp
                                                            @endforeach
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">$ 0.0000</h4>
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					@else
					<div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
						<div class="row p-3">
							<div class="card-body justify-content-center">
								<div class="text-center me-4"><a href="{{ route('create-live-account') }}"><img
											src="/assets/images/noaccfound.png" class="w-25" alt="img"></a></div>
								<h6 class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">No live account founded!
								</h6>
								<a href="{{ route('create-live-account') }}" class="d-grid w-25 m-auto">
									<button class="btn btn-primary">
										<span class="text-truncate w-100">Create new Live Account</span>
									</button>
								</a>
							</div>	
						</div>	
					</div>	
					
					@endif
					
                </div>
            </div>
        </div>
    </div>
    <script>
        var withdraw_type = @json($type ?? '');
        if (withdraw_type != '') {
            setTimeout(function() {
                $("#" + withdraw_type).click();
            }, 2000);
        }
        $("#withdrawal_currency,#withdraw_amount_usd").on("keyup change", function(e) {
            var fromCurrency = $("#withdrawal_currency").val();
            $(".convertCurrencyText").html(fromCurrency.toLocaleUpperCase());
            var toCurrency = "usd";
            var currency_amt = $("#withdraw_amount_usd").val();
            if (currency_amt != '') {
                var usd_amt = convertCurrency(toCurrency, fromCurrency, currency_amt)
            }
        });

        function convertCurrency(fromCurrency, toCurrency, amount) {
            if (fromCurrency && toCurrency && amount) {
                const apiKey = '0f061f933a8359addf8aac5c';
                const url = `https://v6.exchangerate-api.com/v6/${apiKey}/pair/${fromCurrency}/${toCurrency}/${amount}`;
                $(".convertCurrency").html(toCurrency.toLocaleUpperCase());
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        if (data.result === "success") {
                            if (toCurrency == 'INR') {
                                var inr_adj_amount = parseFloat(<?= $settings['inr_adj_amount_withdrawal'] ?>);
                                var rate = data.conversion_rate;
                                var rate_inr = rate - parseFloat(inr_adj_amount);
                                var result = parseFloat(amount * rate_inr).toFixed(2);
                                var adjustment = (data.conversion_result - result);
                                $("#adjustment_inr").val(adjustment);
                                console.log(fromCurrency, toCurrency, amount, data.conversion_rate, data
                                    .conversion_result, result, adjustment);
                            } else {
                                var result = data.conversion_result;
                            }
                            $("#amount_in_other_currency").val(result);
                        } else {
                            $("#amount_in_other_currency").val("0.00");
                            swal.fire({
                                icon: "info",
                                title: "Something went wrong on Currency convertion.",
                                text: "Please try again after sometimes or Contact support."
                            });
                        }
                    },
                    error: function() {
                        // $("#result").text('Error: Something went wrong.');
                        swal.fire({
                            icon: "info",
                            title: "Something went wrong on Currency convertion.",
                            text: "Please try again after sometimes or Contact support."
                        });
                    }
                });
            }
        }

        $(".tradeWithdrawalForm").submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('trade-withdrawal') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.success
                    }).then(() => {
                        window.location.reload(true);
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: xhr.responseJSON.message
                    });
                }
            });
        });
        window.addEventListener('beforeunload', function(e) {
            if ($('#checkoutModal').hasClass('show')) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        $('#match2payForm').on('submit', function(event) {
            event.preventDefault();
            var actionUrl = $(this).attr('action');
            var formData = $(this).serialize();
            console.log(formData);
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#checkoutIframe').attr('src', response.checkoutUrl);
                        $('#checkoutIframe').attr('data-paymentid', response.paymentId);
                        var myModal = new bootstrap.Modal(document.getElementById(
                            'checkoutModal'), {
                            backdrop: 'static',
                            keyboard: false
                        });
                        myModal.show();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            showConfirmButton: true
                        });
                    }
                },
                error: function(xhr) {
                    $('#responseMessage').html(
                        '<p style="color:red;">An error occurred. Please try again later.</p>'
                    );
                    console.error('Error:', xhr);
                }
            });
        });
        $('.select-liveaccount').change(function() {
            var maxAmount = $(this).data('balance');
            $('input[name="withdraw_amount"]').attr('max', maxAmount);
        });
        $('.select-liveaccount').trigger('change');
        $(".otp-req").click(function(e) {
            e.preventDefault();
            var type=$(this).attr("data-type");
            if($(this).attr("disabled")){
                return true;
            }
            $.ajax({
                url: "/getOtp",
                type: "POST",
                data: {
                    "action": "getPOotp",
                    "type":type
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
    </script>
@endsection
