<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="icon" href="{{ asset($settings['favicon']) }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description"
        content="Your partner in profitable trading. Trade forex, commodities, indices, and cryptocurrencies with low spreads and fast execution">
    <meta name="keywords"
        content="forex broker, forex trading, commodities trading, indices trading, cryptocurrencies trading, low spreads, fast execution">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['admin_title'] }} - Client Portal</title>
    <script src="{{ asset('assets/js/vuejs-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/vue-simple-search-dropdown.min.js') }}"></script>
    <link rel="stylesheet" crossorigin="anonymous" href="{{ asset('assets/css/main.css?v=244.1') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/icon-fonts/feather/feather-v2.css?v=5') }}">
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css" />
    <link rel="stylesheet" crossorigin="anonymous" href="{{ asset('assets/css/custom.css?v=4.4') }}">

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
    @if (!View::hasSection('noDatatable'))
        <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
        <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
        {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script> --}}
        {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script> --}}
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.6/pdfmake.min.js"></script> --}}
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script> --}}
        {{-- <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script> --}}
        {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script> --}}
    @endif
    <script src="{{ asset('assets1/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    {{-- <script src="https://www.google.com/recaptcha/enterprise.js?render=6LdTmaUqAAAAAGBu-9SdfdyYJi4lTDaIIiB7uftf"> --}}
            <script>
document.addEventListener('contextmenu', function (e) {
    e.preventDefault();
});

document.addEventListener('keydown', function (e) {

    // Disable F12
    if (e.key === "F12") {
        e.preventDefault();
    }

    // Disable Ctrl+Shift+I
    if (e.ctrlKey && e.shiftKey && e.key === "I") {
        e.preventDefault();
    }

    // Disable Ctrl+Shift+J
    if (e.ctrlKey && e.shiftKey && e.key === "J") {
        e.preventDefault();
    }

    // Disable Ctrl+U (View Source)
    if (e.ctrlKey && e.key === "u") {
        e.preventDefault();
    }

    // Disable Ctrl+Shift+C
    if (e.ctrlKey && e.shiftKey && e.key === "C") {
        e.preventDefault();
    }
});

(function () {

    let triggered = false;

    setInterval(function () {
        if (window.outerWidth - window.innerWidth > 160 ||
            window.outerHeight - window.innerHeight > 160) {

            if (!triggered) {
                triggered = true;
                window.location.href = "{{ route('logout') }}";
            }

        }
    }, 1000);

})();
</script>
    @yield('styles')
    <style>
        .modal-body {
            scrollbar-color: var(--bs-gray-300) #f1f1f1;
            scrollbar-width: thin;
        }

        .pc-sidebar .navbar-content {
            overflow-y: scroll;
        }

        body .swal2-container {
            z-index: 999999999999999999 !important;
        }

        a.btn.btn-outline-light {
            color: var(--bs-primary);
        }

        button.close {
            background: none;
            border: none;
            font-weight: bold;
        }

        :root,
        [data-pc-preset=preset-7],
        [data-pc-preset=preset-7] * {
            --primary-color: {{ $settings['sidebar_color'] }};
            --bs-btn-active-bg: {{ $settings['sidebar_color'] }};
            --bs-primary: {{ $settings['sidebar_color'] }};
            --bs-btn-bg: #fff !important;
            --bs-btn-hover-bg: {{ $settings['sidebar_color'] }} !important;
            --bs-link-color-rgb: {{ $settings['sidebar_color'] }} !important;
            --bs-primary-rgb: {{ hexToRGB($settings['sidebar_color']) }} !important;
            --primary-rgb: {{ hexToRGB($settings['sidebar_color']) }} !important;
        }

        :root [data-pc-theme="dark"],
        :root [data-pc-theme="dark"] * {
            --bs-primary: #fff;
            --bs-btn-bg: transparent !important;
            --bs-black-rgb: 255, 255, 255 !important;
            --pc-sidebar-active-color: #fff;
            --bs-blue: var(--bs-primary);
            --bs-primary-rgb: 229, 138, 0;
            --bs-primary-light: #fcf3e6;
            --bs-link-color: var(--bs-primary);
            --bs-link-color-rgb: 229, 138, 0;
            --bs-link-hover-color: var(--bs-primary);
            --bs-link-hover-color-rgb: to-rgb(shift-color($ pc-primary, $ link-shade-percentage));
            --dt-row-selected: 229, 138, 0;
            --bs-btn-disabled-bg: #000000;
        }

        [data-pc-theme="dark"] .pc-sidebar .pc-badge {
            color: #ff0000;
        }

        .pc-sidebar .pc-badge{
            background: #ff0000!important; 
        }

        [data-pc-theme=dark] .card {
            --bs-white-rgb: var(--primary-color);
        }

        [data-pc-theme="dark"] .checkout-tabs .nav-item.show .nav-link .avtar,
        [data-pc-theme="dark"] .checkout-tabs .nav-link.active .avtar {
            background-color: var(--primary-color) !important;
        }

        [data-pc-preset=preset-7] .btn-primary {
            --bs-btn-color: var(--primary-color);
        }

        [data-pc-preset=preset-7][data-pc-theme="dark"] .link-primary {
            color: {{ $settings['sidebar_color'] }} !important;
        }

        [data-pc-preset=preset-7][data-pc-theme="dark"] div:where(.swal2-container) button:where(.swal2-styled) {
            color: black !important;
        }

        .platform-download {
            transition-duration: 400ms;
        }

        .platform-download:hover {
            box-shadow: 0px 6px 6px 2px #0000004d;
            transition-duration: 400ms;
        }

        .icon-show-paswd {
            border-top-left-radius: unset;
            border-bottom-left-radius: unset;
            cursor: pointer;
            border: 1px solid #bec8d0 !important;
        }

        .otp-request:not([disabled]):hover {
            background-color: var(--primary-color);
            color: white;
            cursor: pointer;
        }

        .otp-request[disabled] {
            opacity: 0.4;
        }
    </style>

    <style>
        /* #vertical-marquee {
            height: 40px;
            overflow: hidden;
            position: relative;
        }

        #vertical-marquee a {
            display: block;
            padding: 5px 0;
            text-decoration: none;
            white-space: nowrap;
        }

        .marquee-icon {
            font-size: 20px;
            margin-right: 8px;
        }

        .marquee-text {
            font-size: 18px;
            font-weight: 600;
            background: linear-gradient(90deg, #12a300, #1cfa00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        } */

        .marquee-container {
            width: 100%;
            max-width: 871px;
            /* desktop */
            overflow: hidden;
            position: relative;
            white-space: nowrap;
        }

        /* Track animation */
        .marquee-track {
            display: flex;
            width: max-content;
            animation: marquee 18s linear infinite;
        }

        .marquee-container:hover .marquee-track {
            animation-play-state: paused;
        }

        /* Content */
        .marquee-content {
            display: flex;
        }

        .marquee-content span {
            margin-right: 25px;
            font-size: 18px;
            font-weight: 600;

            background: linear-gradient(90deg, #12a300, #1cfa00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Animation keyframes */
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* -----------------------------
   RESPONSIVE BREAKPOINTS
--------------------------------*/

        /* Tablets */
        @media (max-width: 768px) {
            .marquee-container {
                max-width: 500px;
                /* narrower */
            }

            .marquee-content span {
                font-size: 16px;
                margin-right: 18px;
            }
        }

        /* Mobile */
        @media (max-width: 480px) {
            .marquee-container {
                max-width: 163px;
                /* even smaller */
            }

            .marquee-content span {
                font-size: 14px;
                margin-right: 12px;
            }

            .marquee-track {
                animation-duration: 22s;
                /* slower on small screens */
            }
        }

        .pc-header .pc-head-link:before {
            background: none!important;
        }

        .pc-header .user-avtar {
    width: 35px;
    height: 35px;
    border-radius: 50%;
}

.wid-70
 {
    width: 70px;
    height: 70px;
}
    </style>
</head>

<body oncontextmenu="return false;" data-pc-preset="preset-7" data-pc-sidebar-caption="true" data-pc-direction="ltr"
    data-pc-theme_contrast="" <?php if (!isset($_COOKIE["sitetheme"])) { ?> data-pc-theme="light" <?php } elseif ($_COOKIE["sitetheme"] == 'true') { ?> data-pc-theme="light" <?php } else { ?> data-pc-theme="dark" <?php } ?> >
	@php
		$user = auth()->user();
	@endphp
    <div id="app" data-v-app="">
        <div>
            <h1></h1>
            <nav class="pc-sidebar">
                <div class="navbar-wrapper">
                    <div class="m-header">
                        <a href="/dashboard" class="b-brand text-primary">
                            @if (!isset($_COOKIE['sitetheme']))
                                <img src="/{{ $settings['admin_sidebar_logo'] }}" class="img-fluid logo-lg 1"
                                    alt="logo" />
                            @elseif ($_COOKIE['sitetheme'] == 'true')
                                <img src="/{{ $settings['admin_sidebar_logo'] }}" class="img-fluid logo-lg 2"
                                    alt="logo" style="width: 100px;" />
                            @else
                                <img src="/{{ $settings['admin_sidebar_logo_dark'] }}" class="img-fluid logo-lg 3"
                                    alt="logo" style="width: 100px;" />
                            @endif
                            <!--<span class="badge bg-light-primary rounded-pill ms-2 theme-version">v2.1</span>-->
                        </a>
                    </div>
                    <div class="navbar-content">
                        <div class="card pc-user-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center w-75">
                                    <div class="flex-shrink-0"> 
										<img class="user-avtar wid-70 rounded-circle"
											 src="{{ !empty($user->profile_image) 
													? asset('storage/uploads/profile/'.$user->profile_image) 
													: asset('assets/images/user.png') }}"
											 alt="User image" />
                                    </div>
                                    <div class="flex-grow-1 ms-3 me-2 w-75">
                                        @auth
                                            <h6 class="mb-0 w-75">{{ ucfirst($user->fullname) }}</h6>
                                            <small class="ellipsis w-75"
                                                tooltip="{{ $user->email }}">{{ $user->email }}</small>
                                        @endauth
                                    </div>
                                    <a class="btn btn-icon btn-link-secondary avtar" style="position: absolute; right: 0;" data-bs-toggle="collapse"
                                        href="/dashboard#pc_sidebar_userlink">
                                        <svg class="pc-icon">
                                            <use xlink:href="#custom-sort-outline"></use>
                                        </svg>
                                    </a>
                                </div>
                                <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                                    <div class="pt-3">
                                        <a href="/user-profile" class=""><i class="ti ti-user"></i><span>My Account</span></a>
                                        <a href="/logout" id="logout-link"><i class="ti ti-power"></i><span>Logout</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="pc-navbar">
                            <li class="pc-item pc-caption"><label>Navigation</label></li>
                            <li class="pc-item">
                                <a href="{{ url('/dashboard') }}" class="pc-link" aria-current="page">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-status-up"></use>
                                        </svg> --}}
                                        <i class="ti ti-layout-dashboard"  style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">Dashboard</span>
                                </a>
                            </li>
                            @if (session('user')->kyc_verify == 0)
                                <li class="pc-item">
                                    <a href="{{ url('/user-profile#kyc') }}" class="pc-link">
                                        <span class="pc-micon">
                                            {{-- <svg class="pc-icon">
                                                <use xlink:href="#custom-document-text"></use>
                                            </svg> --}}
                                            <i class="ti ti-lock-check" style="font-size:20px"></i>
                                        </span>
                                        <span class="pc-mtext">KYC Verification</span>
                                        <span class="pc-badge"><i class="ti ti-user"></i></span>
                                    </a>
                                </li>
                            @endif

                            @if (auth()->user() && (auth()->user()->wallet_enabled == 1 || auth()->user()->wallet_enabled != null))
                                <li class="pc-item">
                                    <a href="{{ url('/wallet') }}" data-bs-toggle="collapse"
                                        data-bs-target="#wallet-collapse" class="pc-link">
                                        <span class="pc-micon">
                                            {{-- <svg class="pc-icon">
                                                <use xlink:href="#custom-security-safe"></use>
                                            </svg> --}}
                                            <i class="ti ti-wallet"
                                                            style="font-size:20px"></i>
                                        </span>
                                        <span class="pc-mtext">Finance</span>
                                    </a>
                                    <div class="collapse" id="wallet-collapse">
                                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-4">
											
											<li><a href="{{ url('/wallet_deposit') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="ti ti-database-import"
                                                            style="font-size:20px"></i></span>
                                                    <span class="pc-mtext">Wallet Deposit</span>
                                                </a></li>
                                            <li><a href="{{ url('/wallet_withdrawal') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="ti ti-wallet-off"
                                                            style="font-size:20px"></i></span>
                                                    <span class="pc-mtext">Wallet Withdrawal</span>
                                                </a></li>
											 <li><a href="{{ url('/trade-deposit') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i
                                                            class="feather icon-credit-card"></i></span>
                                                    <span class="pc-mtext">Trade Deposit</span>
                                                </a></li>
                                            <li><a href="{{ url('/trade-withdrawal') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i
                                                            class="feather icon-dollar-sign"></i></span>
                                                    <span class="pc-mtext">Trade Withdrawal</span>
                                                </a></li>								
                                            <li><a href="{{ url('/internal-transfer') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="feather icon-repeat"></i></span>
                                                    <span class="pc-mtext">Internal Transfer</span>
                                                </a></li>
                                            <li><a href="{{ url('/wallet-transcation') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="ti ti-file-invoice"></i></span>
                                                    <span class="pc-mtext">Transactions</span>
                                                </a></li>
                                        </ul>
                                    </div>
                                </li>
                                
                            @else
                                <li class="mb-1 pc-item">
                                    <a data-bs-toggle="collapse" data-bs-target="#dashboard-collapse"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            {{-- <svg class="pc-icon">
                                                <use xlink:href="#custom-box-1"></use>
                                            </svg> --}}
                                            <i class="ti ti-device-ipad-horizontal-dollar"
                                                            style="font-size:20px"></i>
                                        </span>
                                        <span class="pc-mtext">Payment</span>
                                    </a>
                                    <div class="collapse" id="dashboard-collapse">
                                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-4">
                                            <li><a href="{{ url('/trade-deposit') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i
                                                            class="feather icon-credit-card"></i></span>
                                                    <span class="pc-mtext">Deposit</span>
                                                </a></li>
                                            <li><a href="{{ url('/trade-withdrawal') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i
                                                            class="feather icon-dollar-sign"></i></span>
                                                    <span class="pc-mtext">Withdrawal To Wallet</span>
                                                </a></li>
                                            @php
                                                $user_groups = \App\Models\UserGroup::find(session('user')['group_id']);
                                            @endphp
                                            @if (!empty($user_groups['agent_account']) && $user_groups['agent_status'] == 1)
                                                <li><a href="{{ url('/trade-withdrawal/agent_withdraw') }}"
                                                        class="pc-link link-dark">
                                                        <span class="pc-micon"><i
                                                                class="feather icon-dollar-sign"></i></span>
                                                        <span class="pc-mtext">Withdrawal To Agent</span>
                                                    </a></li>
                                            @endif
                                            <li><a href="{{ url('/internal-transfer') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="feather icon-repeat"></i></span>
                                                    <span class="pc-mtext">Internal Transfer</span>
                                                </a></li>
                                            <li><a href="{{ url('/transactions') }}" class="pc-link link-dark">
                                                    <span class="pc-micon"><i class="feather icon-menu"></i></span>
                                                    <span class="pc-mtext">Transactions</span>
                                                </a></li>
                                        </ul>
                                    </div>
                                </li>
                            @endif

                            <li class="mb-1 pc-item">
                                <a data-bs-toggle="collapse" data-bs-target="#tradingAccounts" class="pc-link">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-shield"></use>
                                        </svg> --}}
                                         <i class="ti ti-user-check"
                                                            style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">Trading Accounts</span>
                                </a>
                                <div class="collapse" id="tradingAccounts">
                                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-4">
                                        <li class="pc-item"><a href="{{ url('/liveAccounts') }}" class="pc-link">
                                                <span class="pc-micon"><i class="feather icon-shield"></i></span>
                                                <span class="pc-mtext">Live MT5 Accounts</span>
                                            </a></li>
                                        <li class="pc-item"><a href="{{ url('/demoAccounts') }}" class="pc-link">
                                                <span class="pc-micon"><i class="feather icon-octagon"></i></span>
                                                <span class="pc-mtext">Demo MT5 Accounts</span>
                                            </a></li>
                                    </ul>
                                </div>
                            </li>

                            <li class="pc-item">
                                <a href="{{ url('/offers') }}" class="pc-link" aria-current="page">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-dollar-square"></use>
                                        </svg> --}}
                                        <i class="ti ti-award"
                                                            style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">Bonuses</span>
                                </a>
                            </li>
							
							<li class="pc-item">
                                <a href="{{ url('/promotions') }}" class="pc-link" aria-current="page">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-dollar-square"></use>
                                        </svg> --}}
                                        <i class="ti ti-tag" style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">Promotions</span>
                                </a>
                            </li>

                            <li class="pc-item">
                                <a href="{{ url('/ib') }}" class="pc-link" aria-current="page">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-status-up"></use>
                                        </svg> --}}
                                        <i class="ti ti-users"
                                                            style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">IB Dashboard</span>
                                </a>
                            </li>

                            <li class="mb-1 pc-item">
                                <a data-bs-toggle="collapse" data-bs-target="#P2P-collapse" class="pc-link">
									<span class="pc-micon">
										<svg class="pc-icon">
											<use xlink:href="#custom-box-1"></use>
										</svg>
									</span>
									<span class="pc-mtext">P2P Exchange</span>
                                </a>
                                <div class="collapse" id="P2P-collapse">
									<ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small ps-4">
										<li><a href="{{ url('/p2p-marketplace') }}" class="pc-link link-dark">
											<span class="pc-micon"><i
												class="feather icon-credit-card"></i></span>
											<span class="pc-mtext">P2P Marketplace</span>
										</a></li>
										
										<li><a href="{{ url('/p2p-myorders') }}" class="pc-link link-dark">
											<span class="pc-micon"><i
												class="feather icon-credit-card"></i></span>
											<span class="pc-mtext">My Orders</span>
										</a></li>
										
										<li><a href="{{ url('/p2p-myadslist') }}" class="pc-link link-dark">
											<span class="pc-micon"><i class="feather icon-menu"></i></span>
											<span class="pc-mtext">P2P Merchant</span>
										</a></li>
										@php
											$showReceiveOrdersMenu = \App\Models\P2PMerchant::where(
												'email',
												auth()->user()->email
											)->exists();
										@endphp

										@if($showReceiveOrdersMenu)
										<li><a href="{{ url('/p2p-receiveorders') }}" class="pc-link link-dark">
											<span class="pc-micon"><i class="feather icon-menu"></i></span>
											<span class="pc-mtext">Receive Orders</span>
										</a></li>
										@endif
									</ul>
                                </div>
                            </li>
							
							<li class="pc-item">
                                <a href="/pamm/investments" class="pc-link"><span class="pc-micon">
                                    {{-- <svg class="pc-icon">
                                        <use xlink:href="#custom-dollar-square"></use>
                                    </svg> --}}
                                    <i class="ti ti-user-dollar"
                                                            style="font-size:20px"></i>
                                </span><span class="pc-mtext">PAMM</span></a>
                            </li>

                            <li class="pc-item">
                                <a href="{{ url('/support') }}" class="pc-link">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-document"></use>
                                        </svg> --}}
                                        <i class="ti ti-help"
                                                            style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">My Tickets</span>
                                </a>
                            </li>

                            <li class="pc-item">
                                <a href="#" class="pc-link" data-bs-toggle="modal"
                                    data-bs-target="#staticBackdrop">
                                    <span class="pc-micon">
                                        {{-- <svg class="pc-icon">
                                            <use xlink:href="#custom-setting-2"></use>
                                        </svg> --}}
                                        <i class="ti ti-device-desktop-down"
                                                            style="font-size:20px"></i>
                                    </span>
                                    <span class="pc-mtext">Downloads</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="modal fade" id="staticBackdrop" tabindex="-1"
                aria-labelledby="staticBackdropLabel"aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Platform Downloads</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-4">
                                    <a target="_blank" href="{{ $settings['mt5_android_platform'] }}"
                                        class="card text-center platform-download">
                                        <img class="w-100 ps-4 pe-4 pt-3" src="/assets/platform/playstore.png"
                                            alt="Android">
                                        <span class="pb-3 pt-2">Android</span>
                                    </a>
                                </div>
                                <div class="col-lg-4">
                                    <a target="_blank" href="{{ $settings['mt5_ios_platform'] }}"
                                        class="card text-center platform-download">
                                        <img class="w-100 ps-4 pe-4 pt-3" src="/assets/platform/appstore.png"
                                            alt="Apple iOS">
                                        <span class="pb-3 pt-2">Apple iOS</span>
                                    </a>
                                </div>
                                <div class="col-lg-4">
                                    <a target="_blank" href="{{ $settings['mt5_windows_platform'] }}"
                                        class="card text-center platform-download">
                                        <img class="w-100 ps-4 pe-4 pt-3" src="/assets/platform/windowslogo.png"
                                            alt="Windows">
                                        <span class="pb-3 pt-2">Windows</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <header class="pc-header">
                <div class="header-wrapper">
                    <div class="me-auto pc-mob-drp">
                        <ul class="list-unstyled">
                            <li class="pc-h-item pc-sidebar-collapse">
                                <a href="/dashboard" class="pc-head-link ms-0" id="sidebar-hide">
                                    <i class="ti ti-menu-2"></i>
                                </a>
                            </li>
                            <li class="pc-h-item pc-sidebar-popup">
                                <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                                    <i class="ti ti-menu-2"></i>
                                </a>
                            </li>
                            <li class="dropdown pc-h-item">
                                <a class="pc-head-link dropdown-toggle arrow-none m-0 trig-drp-search"
                                    data-bs-toggle="dropdown" href="/dashboard" role="button" aria-haspopup="false"
                                    aria-expanded="false">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-search-normal-1"></use>
                                    </svg>
                                </a>
                                <div class="dropdown-menu pc-h-dropdown drp-search">
                                    <form class="px-3 py-2">
                                        <input type="search" class="form-control border-0 shadow-none"
                                            placeholder="Search here. . .">
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="marquee-container">
                            <div class="marquee-track">
                                <div class="marquee-content">
                                    @foreach ($promotions as $promo)
									<a href="{{ $promo->promo_url ?? '#' }}" target="_blank">
                                        <span class="d-flex align-items-center">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
												viewBox="0 0 24 24" fill="none" stroke="currentColor"
												stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
												class="icon icon-tabler icons-tabler-outline icon-tabler-bell-dollar">
												<path stroke="none" d="M0 0h24v24H0z" fill="none" />
												<path
													d="M13 17h-9a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6a2 2 0 1 1 4 0a7 7 0 0 1 3.911 5.17" />
												<path d="M9 17v1a3 3 0 0 0 4.02 2.822" />
												<path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
												<path d="M19 21v1m0 -8v1" />
											</svg>
											{{ $promo->promo_name }}
										</span>
                                    </a>
									@endforeach
                                </div> 
								
								<div class="marquee-content">
                                    @foreach ($promotions as $promo)
									<a href="{{ $promo->promo_url ?? '#' }}" target="_blank">
                                        <span class="d-flex align-items-center">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
												viewBox="0 0 24 24" fill="none" stroke="currentColor"
												stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
												class="icon icon-tabler icons-tabler-outline icon-tabler-bell-dollar">
												<path stroke="none" d="M0 0h24v24H0z" fill="none" />
												<path
													d="M13 17h-9a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6a2 2 0 1 1 4 0a7 7 0 0 1 3.911 5.17" />
												<path d="M9 17v1a3 3 0 0 0 4.02 2.822" />
												<path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
												<path d="M19 21v1m0 -8v1" />
											</svg>
											{{ $promo->promo_name }}
										</span>
                                    </a>
									@endforeach
                                </div>         
                            </div>
                        </div>
                    </div>

                    <div class="ms-auto">
                        <ul class="list-unstyled">
                            <li class="dropdown pc-h-item">
                                <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="/dashboard" role="button" aria-haspopup="false" aria-expanded="false">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-setting-2"></use>
                                    </svg>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                                    <a href="/user-profile" class="dropdown-item"><i class="ti ti-user"></i><span>My
                                            Account</span></a>
                                    <a href="/support" class="dropdown-item"><i
                                            class="ti ti-headset"></i><span>Support</span></a>
                                    <a href="/logout" class="dropdown-item" id="logout-link-2"><i
                                            class="ti ti-power"></i><span>Logout</span></a>
                                </div>
                            </li>
                            <li class="pc-h-item">
                                <a href="/dashboard" class="pc-head-link me-0" data-bs-toggle="offcanvas"
                                    data-bs-target="#announcement" aria-controls="announcement">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-flash"></use>
                                    </svg>
                                </a>
                            </li>
                            <li class="dropdown pc-h-item d-none">
                                <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="/dashboard" role="button" aria-haspopup="false" aria-expanded="false">
                                    <svg class="pc-icon">
                                        <use xlink:href="#custom-notification"></use>
                                    </svg>
                                    <span class="badge bg-success pc-h-badge">3</span>
                                </a>
                                <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                                    <div class="dropdown-body text-wrap header-notification-scroll position-relative"
                                        style="max-height: calc(-215px + 100vh);">
                                        <p class="text-span">Today</p>
                                        <div class="card mb-2">
                                            <div class="card-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0">
                                                        <svg class="pc-icon text-primary">
                                                            <use xlink:href="#custom-layer"></use>
                                                        </svg>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <span class="float-end text-sm text-muted">19 April.
                                                            Friday</span>
                                                        <h5 class="text-body mb-2">We've Upgraded Our Client Portal!
                                                        </h5>
                                                        <p class="mb-0">We're excited to announce that our client
                                                            portal
                                                            has
                                                            been upgraded! We've introduced several enhancements to
                                                            improve
                                                            functionality and provide you with a more intuitive and
                                                            streamlined
                                                            experience.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center py-2"><a href="/dashboard" class="link-danger">Clear
                                            all
                                            Notifications</a></div>
                                </div>
                            </li>
                            <li class="dropdown pc-h-item header-user/profile">
                                <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="/dashboard" role="button" aria-haspopup="false"
                                    data-bs-auto-close="outside" aria-expanded="false">
                                    <img class="user-avtar wid-35 rounded-circle"
									 src="{{ !empty($user->profile_image) 
											? asset('storage/uploads/profile/'.$user->profile_image) 
											: asset('assets/images/user.png') }}"
									 alt="User image" />
                                </a>
                                <div class="dropdown-menu dropdown-user/profile dropdown-menu-end pc-h-dropdown">
                                    <div class="dropdown-header d-flex align-items-center justify-content-between">
                                        <h5 class="m-0">Profile</h5>
                                    </div>
                                    <div class="dropdown-body">
                                        <div class="profile-notification-scroll position-relative"
                                            style="max-height: calc(-225px + 100vh);">
                                            <div class="d-flex mb-1">
                                                <div class="flex-shrink-0">
													<img class="user-avtar wid-35 rounded-circle"
														 src="{{ !empty($user->profile_image) 
																? asset('storage/uploads/profile/'.$user->profile_image) 
																: asset('assets/images/user.png') }}"
														 alt="User image" />
                                                          
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1">{{ ucfirst($user->fullname) }} 🖖
                                                    </h6>
                                                    <span>{{ $user->email }}</span>
                                                </div>
                                            </div>
                                            <hr class="border-secondary border-opacity-50">
                                            <div class="card">
                                                <div class="card-body py-3">
                                                    <a href="/user-profile" class="">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <h6 class="mb-0 d-inline-flex align-items-center">
                                                                <svg class="pc-icon text-muted me-2">
                                                                    <use xlink:href="#custom-user"></use>
                                                                </svg>My Profile
                                                            </h6>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <hr class="border-secondary border-opacity-50">
                                            <div class="d-grid mb-3">
                                                <a href="/logout" class="btn btn-primary">
                                                    <svg class="pc-icon me-2">
                                                        <use xlink:href="#custom-logout-1-outline"></use>
                                                    </svg>Logout
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
                aria-labelledby="announcementLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <p class="text-span">Today</p>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                                <div class="badge bg-light-success f-12">Big News</div>
                                <p class="mb-0 text-muted">2 min ago</p>
                                <span class="badge dot bg-warning"></span>
                            </div>
                            <!-- Blade syntax to output the dynamic $title -->
                            <h5 class="mb-3">{{ $settings['admin_title'] }} is Redesigned</h5>
                            <p class="text-muted">
                                Please note that we are still in the process of renewing aspects of the user
                                experience. You might encounter some areas under development, but rest assured,
                                these improvements are being made to better serve your needs.
                            </p>
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-grid">
                                        <!-- Use Laravel's route helper for better maintainability -->
                                        <a href="{{ url('/dashboard') }}"
                                            class="router-link-active router-link-exact-active btn btn-outline-secondary"
                                            aria-current="page">
                                            Explore More
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
                    aria-labelledby="announcementLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="announcementLabel">What's new announcement?</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <p class="text-span">Today</p>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
                                    <div class="badge bg-light-success f-12">Big News</div>
                                    <p class="mb-0 text-muted">2 min ago</p>
                                    <span class="badge dot bg-warning"></span>
                                </div>
                                <!-- Laravel Blade Syntax to dynamically insert the title -->
                                <h5 class="mb-3">{{ $settings['admin_title'] }} is Redesigned</h5>
                                <p class="text-muted">
                                    Please note that we are still in the process of renewing aspects of the user
                                    experience.
                                    You might encounter some areas under development, but rest assured, these
                                    improvements
                                    are being made to better serve your needs.
                                </p>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-grid">
                                            <!-- Use the route() helper function if you are generating URLs from named routes -->
                                            <a href="{{ url('/dashboard') }}"
                                                class="router-link-active router-link-exact-active btn btn-outline-secondary"
                                                aria-current="page">Explore More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
