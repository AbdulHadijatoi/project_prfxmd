@extends('layouts.crm.crm')
@section('content')
    <style>
        .banner-slider,
        .bonus-slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(18, 163, 0, 0.15);
            background: #fff;
        }

        .slider-container {
            position: relative;
            width: 100%;
            max-height: 231px;
            overflow: hidden;
        }

        .slider-images {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .slider-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
        }

        .slider-image.active {
            opacity: 1;
            position: relative;
        }

        .slider-image img {
            width: 100%;
            /* height: 100%; */
            object-fit: cover;
            border-radius: 20px;
            display: block;
        }

        .banner-slider .slider-image img {
            max-height: 262px;
        }

        .bonus-slider .slider-image img {
            max-height: 450px;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(18, 163, 0, 0.9);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            font-size: 28px;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 300;
            line-height: 1;
            padding: 0;
        }

        .slider-btn.small {
            width: 35px;
            height: 35px;
            font-size: 22px;
        }

        .slider-btn:hover {
            background: #12a300;
            transform: translateY(-50%) scale(1.1);
            box-shadow: 0 5px 15px rgba(18, 163, 0, 0.4);
        }

        .prev-btn {
            left: 15px;
        }

        .next-btn {
            right: 15px;
        }

        /* Move dots inside the image area */
        .slider-dots {
            position: absolute;
            bottom: 15px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(255, 255, 255, 0.0);
            padding: 0;
            margin: 0;
            border-radius: 0;
            z-index: 20;
        }


        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid #12a300;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .dot.active {
            background: #12a300;
            width: 32px;
            border-radius: 6px;
            transform: scale(1);
        }


        .dot:hover {
            background: rgba(18, 163, 0, 0.5);
            transform: scale(1.2);
        }

        .bonus-slider {
            position: relative;
        }


        .bonus-label {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #12a300;
            padding: 6px 14px;
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            border-radius: 20px;
            z-index: 25;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
        }


        @media (max-width: 768px) {
            .banner-slider .slider-image img {
                height: 300px;
            }

            .bonus-slider .slider-image img {
                height: 250px;
            }

            .slider-btn {
                width: 35px;
                height: 35px;
                font-size: 22px;
            }

            .slider-btn.small {
                width: 28px;
                height: 28px;
                font-size: 18px;
            }

            .prev-btn {
                left: 10px;
            }

            .next-btn {
                right: 10px;
            }
        }

        @media (max-width: 576px) {

            .banner-slider .slider-image img,
            .bonus-slider .slider-image img {
                height: 220px;
            }

            .banner-slider,
            .bonus-slider {
                border-radius: 15px;
            }

            .slider-image img {
                border-radius: 15px;
            }
        }

        @media (min-width: 1500px) and (max-width: 1999px) {
            .slider-container {
                max-height: none;
                height: 350px!important;
            }

            .slider-image img {
                height: 100%;
                object-fit: fill;
            }

            .banner-slider .slider-image img {
                max-height: none !important;
            }
        }


        @media (min-width: 2000px) {
            .slider-container {
                max-height: none;
                height: 420px;
            }

            .slider-image img {
                height: 100%;
                object-fit: fill;
            }


            .banner-slider .slider-image img {
                max-height: none !important;
            }
        }
    </style>
    <!--<div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <h3 class="modal-title" id="upgradeModalLabel">Upgrade Alert!</h3>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
                </div>
                <div class="modal-body  text-center">
                    <b>Dear Valued Client,</b>
                    <p>We've Updated the Leverage Markets CRM to give you a better experience, improved performance, and
                        enhanced
                        security.</p>
                    <p><a id="exploreUpdatesBtn" data-bs-dismiss="modal" href="#"
                            class="btn bg-primary text-white">Explore the Updates</a></p>
                    @if ($ibResult)
                        <b>Please note, to continue earning commissions, ensure you're using the latest referral link
                            provided in your account.
                        </b>
                    @endif
                    <p class="mt-2">Thank you for your continued trust in us!</p>
                    <div class="d-flex form-check justify-content-center">
                        <input class="form-check-input" type="checkbox" id="dontShowAgain">
                        <label class="ms-1 form-check-label" for="dontShowAgain">
                            Don't show this again
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div class="pc-container">
        <div class="pc-content">
            <div class="row mb-4">
                @include('trading-view')
            </div>


            <div class="row">
                <div class="col-md-6 col-lg-3">
                    {{-- <div class="card bg-gray-800 dropbox-card">
                        <a href="{{ route('wallet_deposit') }}" class="text-decoration-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="text-white">Wallet </h5>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2 mt-2">
                                <div>
                                    <div class="avtar avtar-s"><svg class="pc-icon">
                                            <use xlink:href="#custom-security-safe"></use>
                                        </svg></div>
                                </div>
                                @if (auth()->user() && (auth()->user()->wallet_enabled == 0 || auth()->user()->wallet_enabled == null))
                                    <div>
                                        <button class="btn btn-sm btn-outline-light bg-transparent activate-wallet"
                                            type="button"><i class="ti ti-plus me-2"></i><!---->
                                            Activate Wallet</button>
                                    </div>
                                @else
                                    <div>
                                        <h2 class="text-center text-white">${{ $walletBalance }}</h2>
                                    </div>
                                @endif
                            </div><a href="#"><small class="text-white">Fund Now</small></a>
                        </div>
                        </a>
                    </div> --}}

             <div class="card bg-gray-800 dropbox-card">

                @if (auth()->user() && auth()->user()->wallet_enabled == 1)
                    <a href="{{ route('wallet_deposit') }}" class="text-decoration-none">
                @endif

                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="text-white">Wallet</h5>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2 mt-2">
                            <div>
                                <div class="avtar avtar-s">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-security-safe"></use>
                                    </svg>
                                </div>
                            </div>

                            @if (auth()->user() && (auth()->user()->wallet_enabled == 0 || auth()->user()->wallet_enabled == null))
                                <div>
                                    <button class="btn btn-sm btn-outline-light bg-transparent activate-wallet"
                                        type="button">
                                        <i class="ti ti-plus me-2"></i>
                                        Activate Wallet
                                    </button>
                                </div>
                            @else
                                <div>
                                    <h2 class="text-center text-white">${{ $walletBalance }}</h2>
                                </div>
                            @endif
                        </div>

                        <small class="text-white">Fund Now</small>
                    </div>

                @if (auth()->user() && auth()->user()->wallet_enabled == 1)
                    </a>
                @endif

            </div>
                </div>
				<div class="col-md-6 col-lg-3">
                    <a href="{{ route('wallet.transcation') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="feather icon-credit-card f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Deposit</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
                            <h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalDeposit }}</h4>
                        </div>
                    </div>
                    </a>
                </div>
				
				<div class="col-md-6 col-lg-3">
                    <a href="{{ route('wallet.transcation') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="ti ti-wallet f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Withdrawal</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
                            <h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalWithdrawal }}</h4>
                        </div>
                    </div>
                    </a>
                </div>
				
				<div class="col-md-6 col-lg-3">
                    <a href="{{ route('wallet.transcation') }}" class="text-decoration-none">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="feather icon-shuffle f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Transfer</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
                            <h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalA2ATransfer }}</h4>
                        </div>
                    </div>
                     </a>
                </div>
				
                <!--<div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="ti ti-wallet f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Wallet</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
							{{-- <div class="d-flex align-items-center justify-content-between mt-1 mb-2">
								<div class="">
									<h4 class="mb-1 f-w-400">${{ $totalDeposit }}</h4>
									<p class="text-muted mb-0">Trade Deposit</p>
								</div>
								<div>
									<h4 class="mb-1 f-w-400">${{ $totalWalletDeposit }}</h4>
									<p class="text-muted mb-0">Wallet Deposit</p>
								</div>
							</div> --}}

                            <div class="d-flex align-items-center justify-content-between mt-1 mb-2">
                                <div>
									<h4 class="mb-1 f-w-400">${{ $totalWalletDeposit }}</h4>
									<p class="text-muted mb-0">Deposit</p>
								</div>
								<div>
									<h4 class="mb-1 f-w-400">${{ $totalWalletWithdrawal }}</h4>
									<p class="text-muted mb-0">Withdraw</p>
								</div>
								
							</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="ti ti-wallet-off f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Trade</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
							<div class="d-flex align-items-center justify-content-between mb-2 mt-1 ">
                                <div class="">
									<h4 class="mb-1 f-w-400">${{ $totalDeposit }}</h4>
									<p class="text-muted mb-0">Deposit</p>
								</div>
								<div>
									<h4 class="mb-1 f-w-400">${{ $totalWithdrawal }}</h4>
									<p class="text-muted mb-0">Withdraw</p>
								</div>
								
							</div>						
                        </div>
                    </div>
                </div>
				
				<div class="col-md-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
							<div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avtar avtar-s bg-light-primary">
									<i class="feather icon-shuffle f-24"></i>
                                </div>
								<div>
									<h4 class="text-center">Transfer</h4>
								</div>
                            </div>
							<hr class="p-0 m-0" style=""/>
							<div class="d-flex align-items-center justify-content-between mb-2 mt-1 ">
								<div>
									<h4 class="mb-1 f-w-400">${{ $totalA2ATransfer }}</h4>
									{{-- <p class="text-muted mb-0">A2A</p> --}}
									<p class="text-muted mb-0"><small>Internal Transfer</small></p>
								</div>
								<div>
									<h4 class="mb-1 f-w-400">${{ $totalC2CTransfer }} </h4>
									{{-- <p class="text-muted mb-0">C2C</p> --}}
									<p class="text-muted mb-0"><small>External Transfer</small></p>
								</div>
							</div>						
                        </div>
                    </div>
                </div>  -->
				
				
                <!--<div class="col-md-6 col-lg-3"><a href="/dashboard">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="avtar avtar-s bg-light-primary"><i class="ti ti-shield-check f-18"></i>
                                    </div>
                                    <div class="dropdown"><a
                                            class="avtar avtar-s btn-link-secondary dropdown-toggle arrow-none"
                                            href="/dashboard" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false"><i class="ti ti-dots-vertical f-18"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end"><a href="/liveAccounts"
                                                class="dropdown-item">View
                                                Accounts </a></div>
                                    </div>
                                </div>
                                <h3 class="mb-0 f-w-400">{{ $liveAccounts }}</h3>
                                <p class="text-muted mb-0">Live MT5 Accounts</p>
                            </div>
                        </div>
                    </a></div>-->
            </div>

            <div class="row">
                <div class="col-md-12 col-lg-9">
                    <div class="card">
                        <div class="card-body border-bottom pb-0">

                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <h3 class="mb-0">My Trading Accounts</h3>

                                <div class="d-flex gap-2 mt-2 mt-md-0">
                                    <a href="/createLiveAccount" class="btn btn-outline-primary">
                                        Open Live Account
                                    </a>
                                    <a href="/createDemoAccount" class="btn btn-outline-primary">
                                        Open Demo Account
                                    </a>
                                </div>
                            </div>

                            <ul class="nav nav-tabs analytics-tab" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation"><button class="nav-link active"
                                        id="analytics-tab-1" data-bs-toggle="tab" data-bs-target="#analytics-tab-1-pane"
                                        type="button" role="tab" aria-controls="analytics-tab-1-pane"
                                        aria-selected="true">Live Accounts ({{ $liveAccounts }})</button></li>
                                <li class="nav-item" role="presentation"><button class="nav-link" id="analytics-tab-2"
                                        data-bs-toggle="tab" data-bs-target="#analytics-tab-2-pane" type="button"
                                        role="tab" aria-controls="analytics-tab-2-pane" aria-selected="false">Demo
                                        Accounts</button></li>
                                @if ($tournamentAccountDetails->isNotEmpty())
                                    <li class="nav-item" role="presentation"><button class="nav-link"
                                            id="analytics-tab-3" data-bs-toggle="tab"
                                            data-bs-target="#analytics-tab-3-pane" type="button" role="tab"
                                            aria-controls="analytics-tab-3-pane" aria-selected="false">Tournament
                                            Accounts</button></li>
                                @endif
                            </ul>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="analytics-tab-1-pane" role="tabpanel"
                                aria-labelledby="analytics-tab-1" tabindex="0">
                                @if ($liveAccountDetails->isNotEmpty())
                                    <div>
                                        <div class="table-responsive ps-2">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Leverage</th>
                                                        <th class="text-end">Balance</th>
                                                        <th class="text-end">Equity</th>
                                                        <th class="text-end"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($liveAccountDetails as $liveAccount)
                                                        <tr>
                                                            <td>
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto pe-0">
                                                                        <img src="/assets/images/mt5.png" alt="user-image"
                                                                            class="wid-50 hei-50 rounded">
                                                                    </div>
                                                                    <div class="col">
                                                                        <h4 class="mb-0 ms-2">
                                                                            <span
                                                                                class="text-truncate w-100">{{ $liveAccount->trade_id }}</span>
                                                                        </h4>
                                                                        <div class="d-flex justify-content-between ms-2">
                                                                            <div>
                                                                                <div class="fw-bold text-muted">
                                                                                    {{ $liveAccount->accountType->ac_name }}
                                                                                </div>
                                                                                {{-- <div class="mb-2 fw-normal">
                                                                                {{ $liveAccount->accountType->ac_group }}</div> --}}
                                                                            </div>
                                                                        </div>
                                                                        {{-- <p class="text-muted ms-2 f-12 mb-0">
                                                                        <span
                                                                            class="text-truncate w-100">{{ $liveAccount->account_type }}</span>
                                                                    </p> --}}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="f-w-400 f-16">{{ $liveAccount->leverage }}</td>
                                                            <td class="text-end f-w-400 f-16">$
                                                                {{ $liveAccount->balance ?? '0.00' }}</td>
                                                            <td class="text-end f-w-400 f-16">$ {{ $liveAccount->equity }}
                                                            </td>
                                                            <td class="text-end f-w-200">
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('view-account-details', ['type' => 'live', 'id' => $liveAccount->trade_id]) }}"
                                                                        class="btn btn-sm btn-outline-secondary d-grid me-2">
                                                                        <span>View <svg class="pc-icon">
                                                                                <use xlink:href="#custom-login"></use>
                                                                            </svg></span>
                                                                    </a>
                                                                    <a href="/trade-deposit"
                                                                        class="btn btn-sm btn-outline-secondary d-grid me-2">
                                                                        <span>Deposit <i
                                                                                class="ti ti-database-import"></i></span>
                                                                    </a>
                                                                    <a href="{{ route('tradeonline', ['login' => $liveAccount->trade_id, 'password' => $liveAccount->trader_pwd]) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-secondary d-grid">
                                                                        <span class="">Trade <i
                                                                                class="ti ti-chart-line"></i></span>
                                                                    </a>
                                                                    <input type="hidden" id="acc_login"
                                                                        value="{{ $liveAccount->trade_id }}" />
                                                                    <input type="hidden" id="acc_password"
                                                                        value="{{ $liveAccount->trader_pwd }}" />

                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <div class="p-5 m-3">
                                            <div class="auth-main">
                                                <div class="card-body">
                                                    <div class="text-center me-4">
                                                        <a href="/dashboard"><img src="/assets/images/no-data.jpg"
                                                                class="w-25" alt="img"></a>
                                                    </div>
                                                    <h6 class="text-center text-secondary f-w-400 mb-0 f-16">No Live
                                                        Accounts
                                                        Found</h6>
                                                </div>
                                            </div>
                                            <?php
                                            /* <a href="/createLiveAccount" class="d-grid">
                                                <button class="btn btn-outline-primary">
                                                    <span class="text-truncate w-100">Create new Live Account</span>
                                                </button>
                                            </a> */
                                            ?>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="analytics-tab-2-pane" role="tabpanel"
                                aria-labelledby="analytics-tab-2" tabindex="0">
                                @if ($demoAccountDetails->isNotEmpty())
                                    <div>
                                        <div class="table-responsive ps-2">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Leverage</th>
                                                        <th class="text-end">Balance</th>
                                                        <th class="text-end">Equity</th>
                                                        <th class="text-end"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($demoAccountDetails as $demoAccount)
                                                        <tr>
                                                            <td>
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto pe-0">
                                                                        <img src="/assets/images/mt5.png" alt="user-image"
                                                                            class="wid-50 hei-50 rounded">
                                                                    </div>
                                                                    <div class="col">
                                                                        <h4 class="mb-2 ms-2">
                                                                            <span
                                                                                class="text-truncate w-100">{{ $demoAccount->trade_id }}</span>
                                                                        </h4>
                                                                        <div class="d-flex justify-content-between ms-2"
                                                                            style="font-size:10px">
                                                                            <div>
                                                                                <div class="fw-bold">
                                                                                    {{ $demoAccount->accountType->ac_name }}
                                                                                </div>
                                                                                {{-- <div class="mb-2 fw-normal">
                                                                                {{ $demoAccount->accountType->ac_group }}</div> --}}
                                                                            </div>
                                                                        </div>
                                                                        {{-- <p class="text-muted ms-2 f-12 mb-0">
                                                                        <span
                                                                            class="text-truncate w-100">{{ $demoAccount->account_type }}</span>
                                                                    </p> --}}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="f-w-400 f-16">{{ $demoAccount->leverage }}</td>
                                                            <td class="text-end f-w-400 f-16">$
                                                                {{ $demoAccount->balance ?? '0.00' }}</td>
                                                            <td class="text-end f-w-400 f-16">$ {{ $demoAccount->equity }}
                                                            </td>
                                                            <td class="text-end f-w-200">
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('view-account-details', ['type' => 'demo', 'id' => $demoAccount->trade_id]) }}"
                                                                        class="btn btn-sm btn-outline-secondary d-grid me-2">
                                                                        <span>View <svg class="pc-icon">
                                                                                <use xlink:href="#custom-login"></use>
                                                                            </svg></span>
                                                                    </a>

                                                                    <a href="{{ route('tradeonline', ['login' => $demoAccount->trade_id, 'password' => $demoAccount->trader_pwd]) }}"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-outline-secondary d-grid">
                                                                        <span class="">Trade <i
                                                                                class="ti ti-chart-line"></i></span>
                                                                    </a>
                                                                    <input type="hidden" id="acc_login"
                                                                        value="{{ $demoAccount->trade_id }}" />
                                                                    <input type="hidden" id="acc_password"
                                                                        value="{{ $demoAccount->trader_pwd }}" />
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <div class="p-5 m-3">
                                            <div class="auth-main">
                                                <div class="card-body">
                                                    <div class="text-center me-4">
                                                        <a href="{{ route('dashboard') }}"><img
                                                                src="/assets/images/no-data.jpg" class="w-25"
                                                                alt="img"></a>
                                                    </div>
                                                    <h6 class="text-center text-secondary f-w-400 mb-0 f-16">No Demo
                                                        Accounts
                                                        Found</h6>
                                                </div>
                                            </div>
                                            <a href="{{ route('show-demo-account-form') }}" class="d-grid">
                                                <button class="btn btn-outline-primary">
                                                    <span class="text-truncate w-100">Create new Demo Account</span>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="tab-pane fade" id="analytics-tab-3-pane" role="tabpanel"
                                aria-labelledby="analytics-tab-2" tabindex="0">
                                @if ($tournamentAccountDetails->isNotEmpty())
                                    <div>
                                        <div class="table-responsive ps-2">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th></th>
                                                        <th>Leverage</th>
                                                        <th class="text-end">Balance</th>
                                                        <th class="text-end">Equity</th>
                                                        <th class="text-end"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($tournamentAccountDetails as $tAccount)
                                                        <tr>
                                                            <td>
                                                                <div class="row align-items-center">
                                                                    <div class="col-auto pe-0">
                                                                        <img src="/assets/images/mt5.png" alt="user-image"
                                                                            class="wid-50 hei-50 rounded">
                                                                    </div>
                                                                    <div class="col">
                                                                        <h4 class="mb-2 ms-2">
                                                                            <span
                                                                                class="text-truncate w-100">{{ $tAccount->trade_id }}</span>
                                                                        </h4>
                                                                        <p class="text-muted ms-2 f-12 mb-0">
                                                                            <span
                                                                                class="text-truncate w-100">{{ $tAccount->account_type }}</span>
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="f-w-400 f-16">{{ $tAccount->leverage }}</td>
                                                            <td class="text-end f-w-400 f-16">$
                                                                {{ $tAccount->balance ?? '0.00' }}</td>
                                                            <td class="text-end f-w-400 f-16">$ {{ $tAccount->equity }}
                                                            </td>
                                                            <td class="text-end f-w-200">
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ route('view-account-details', ['type' => 'tournament', 'id' => $tAccount->trade_id]) }}"
                                                                        class="btn btn-sm btn-outline-secondary d-grid me-2">
                                                                        <span>View <svg class="pc-icon">
                                                                                <use xlink:href="#custom-login"></use>
                                                                            </svg></span>
                                                                    </a>
                                                                    <a href="javascript:void(0);"
                                                                        class="btn btn-sm btn-outline-secondary d-grid">
                                                                        <span class="">Web Termial <i
                                                                                class="ti ti-database-import"></i></span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @else
                                    <div>
                                        <div class="p-5 m-3">
                                            <div class="auth-main">
                                                <div class="card-body">
                                                    <div class="text-center me-4">
                                                        <a href="{{ route('dashboard') }}"><img
                                                                src="/assets/images/empty.png" class="w-25"
                                                                alt="img"></a>
                                                    </div>
                                                    <h6 class="text-center text-secondary f-w-400 mb-0 f-16">No Demo
                                                        Accounts
                                                        Found</h6>
                                                </div>
                                            </div>
                                            <a href="{{ route('show-demo-account-form') }}" class="d-grid">
                                                <button class="btn btn-outline-primary">
                                                    <span class="text-truncate w-100">Create new Demo Account</span>
                                                </button>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer"></div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="/dashboard">
                        <div class="card">
                            <div class="card-body p-3"><a href="{{ url('/trade-deposit') }}"
                                    class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-white text-opacity-75"></p>
                                        <h4 class="mb-0 text-black">Quick Deposit</h4>
                                    </div>
                                    <div class="avtar bg-light-primary"><i class="ti ti-bolt f-18"></i></div>
                                </a></div>
                        </div>
                    </a><a href="/dashboard">
                        <div class="card">
                            <div class="card-body p-3"><a href="/trade-withdrawal"
                                    class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-white text-opacity-75"></p>
                                        <h4 class="mb-0 text-black">Quick Withdraw</h4>
                                    </div>
                                    <div class="avtar bg-light-primary"><i class="ti ti-bolt f-18"></i></div>
                                </a></div>
                        </div>
                    </a>
                    @php
                        $ib = $ibResult ? '/ib-profile' : '/ib';
                    @endphp
                    <a href="{{ $ib }}" class="">
                        <div class="card bg-primary available-balance-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="mb-0 text-white">Introducing Broker</h4>
                                        <p class="mb-0 text-white text-opacity-75">View Profile</p>
                                    </div>
                                    <div class="avatar">
                                        <i class="ti ti-award f-18"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            {{-- Slider Start --}}
            @include('layouts.crm.offerspromo')
            {{-- Slider End --}}
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
        //  localStorage.removeItem('version2PopupShown');
        if (!localStorage.getItem('version2PopupShown')) {
            window.onload = function() {
                $('#upgradeModal').modal('show');
                $('#dontShowAgain').on('change', function() {
                    if (this.checked) {
                        localStorage.setItem('version2PopupShown', 'true');
                    } else {
                        localStorage.removeItem('version2PopupShown');
                    }
                });
            };
        }

        $("#deposit_link1").on("click", function(e) {
            e.preventDefault(); // stop navigation
            $("#depositNoticeModal").modal("show");
        });

        $(".activate-wallet").click(function() {
            Swal.fire({
                icon: "info",
                title: "You want to activate wallet ?",
                text: "Note: You can revert at any time with your profile settings.",
                showConfirmButton: true,
                confirmButtonText: "Yes",
                showCancelButton: true,
                cancelButtonText: "Cancel"
            }).then((val) => {
                if (val.isConfirmed) {
                    $.ajax({
                        url: "{{ route('wallet.activate') }}",
                        method: "GET",
                        data: {
                            activate_wallet: "enable"
                        },
                        beforeSend: function() {
                            Swal.fire({
                                showConfirmButton: false,
                                showCancelButton: false,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: function() {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(data) {
                            Swal.close();
                            if (data === true || data === "true") {
                                Swal.fire({
                                    title: "Your wallet is activated now!",
                                    text: "Let's get started seamless journey with wallet. Kindly logout and Login again.",
                                    icon: "success"
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>

    {{-- Slider Script --}}
    <script>
        class ImageSlider {
            constructor(sliderId, dotsContainerId, prevBtnId, nextBtnId, autoPlayInterval = 3000) {
                this.slider = document.getElementById(sliderId);
                this.dotsContainer = document.getElementById(dotsContainerId);
                this.prevBtn = document.getElementById(prevBtnId);
                this.nextBtn = document.getElementById(nextBtnId);
                this.autoPlayInterval = autoPlayInterval;
                this.currentIndex = 0;
                this.slides = this.slider.querySelectorAll('.slider-image');
                this.totalSlides = this.slides.length;

                this.init();
            }

            init() {
                this.createDots();
                this.attachEventListeners();
                this.startAutoPlay();
            }

            createDots() {
                if (!this.dotsContainer) return;

                for (let i = 0; i < this.totalSlides; i++) {
                    const dot = document.createElement('button');
                    dot.classList.add('dot');
                    if (i === 0) dot.classList.add('active');
                    dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                    dot.addEventListener('click', () => this.goToSlide(i));
                    this.dotsContainer.appendChild(dot);
                }
            }

            attachEventListeners() {
                if (this.prevBtn) {
                    this.prevBtn.addEventListener('click', () => this.prevSlide());
                }
                if (this.nextBtn) {
                    this.nextBtn.addEventListener('click', () => this.nextSlide());
                }
            }

            goToSlide(index) {
                this.currentIndex = (index + this.totalSlides) % this.totalSlides;
                this.updateSlides();
                this.resetAutoPlay();
            }

            nextSlide() {
                this.currentIndex = (this.currentIndex + 1) % this.totalSlides;
                this.updateSlides();
                this.resetAutoPlay();
            }

            prevSlide() {
                this.currentIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
                this.updateSlides();
                this.resetAutoPlay();
            }

            updateSlides() {
                this.slides.forEach((slide, index) => {
                    slide.classList.toggle('active', index === this.currentIndex);
                });

                if (this.dotsContainer) {
                    const dots = this.dotsContainer.querySelectorAll('.dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === this.currentIndex);
                    });
                }
            }

            startAutoPlay() {
                this.autoPlayTimer = setInterval(() => {
                    this.nextSlide();
                }, this.autoPlayInterval);
            }

            resetAutoPlay() {
                clearInterval(this.autoPlayTimer);
                this.startAutoPlay();
            }

            destroy() {
                clearInterval(this.autoPlayTimer);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const bannerSlider = new ImageSlider(
                'bannerSlider',
                'bannerDots',
                'bannerPrev',
                'bannerNext',
                3000
            );

            const bonusSlider = new ImageSlider(
                'bonusSlider',
                null,
                'bonusPrev',
                'bonusNext',
                2500
            );
        });
    </script>

@endsection
