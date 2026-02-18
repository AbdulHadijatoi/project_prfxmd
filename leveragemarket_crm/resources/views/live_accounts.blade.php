@extends('layouts.crm.crm')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">MT5 Accounts</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                @include('mt5_accounts_tab')
                <div class="col-md-12 col-lg-9">
                    <div class="card">
                        <div class="card-body border-bottom pb-0 mb-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
								<h3 class="mb-0">My Trading Live Accounts</h3>

								<div class="d-flex gap-2 mt-2 mt-md-0">								
									<a href="/createLiveAccount" class="btn btn-outline-primary">
										Open New Live Account
									</a>
								</div>
							</div>
                        </div>
                        <div class="tab-content" id="myTabContent">
                            <div>
                                <div class="table-responsive ps-2">
                                    <table class="table" id="">
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
                                            @foreach ($results as $acc)
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
                                                                        class="text-truncate w-100">{{ $acc->trade_id }}</span>
                                                                </h4>
                                                                <div class="d-flex justify-content-between ms-2">
                                                                    <div>
                                                                        <div class="fw-bold text-muted">
                                                                            {{ $acc->accountType->ac_name }}</div>
                                                                        {{-- <div class="mb-2 fw-normal">
                                                                            {{ $acc->accountType->ac_group }}</div> --}}
                                                                    </div>
                                                                </div>
                                                                {{-- <p class="text-muted ms-2 f-12 mb-0">
                                  <span class="text-truncate w-100">{{ $acc->ac_name }}</span>
                                </p> --}}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="f-w-400 f-16">{{ $acc->leverage }}</td>
                                                    <td class="text-end f-w-400 f-16">$ {{ $acc->Balance }}</td>
                                                    <td class="text-end f-w-400 f-16">$ {{ $acc->equity }}</td>
                                                    <td class="text-end f-w-200">
                                                        <div class="d-flex align-items-center gap-1">
															<a href="{{ url('/view_account_details') }}?type=live&id={{ $acc->trade_id }}" class="btn btn-sm btn-outline-secondary d-grid">
																<span class="">View <svg class="pc-icon">
																	<use xlink:href="#custom-login"></use>
																</svg></span>
															</a>
                                                            <a href="{{ url('/trade-deposit') }}"
                                                                class="btn btn-sm btn-outline-secondary d-grid">
                                                                <span class="">Deposit <i class="ti ti-database-import"></i></span>
                                                            </a>
															<a href="{{ route('tradeonline', ['login' => $acc->trade_id, 'password' => $acc->trader_pwd]) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-grid">
                                                                <span class="">Trade <i class="ti ti-chart-line"></i></span>
                                                            </a>
															<input type="hidden" id="acc_login" value="{{ $acc->trade_id }}" />
															<input type="hidden" id="acc_password" value="{{ $acc->trader_pwd }}" />
															
															<!--<a href="javascript:void(0);"
                                                                class="btn btn-sm btn-outline-secondary deleteaccount d-grid">
                                                                <span class=""><i class="ti ti-trash text-danger"></i></span>
                                                            </a>-->
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="{{ url('/createLiveAccount') }}">
                        <div class="card bg-primary available-balance-card">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="mb-0 text-white">Create Account</h4>
                                        <p class="mb-0 text-white text-opacity-75">Open Live Account</p>
                                    </div>
                                    <div class="avtar">
                                        <i class="ti ti-folder-plus f-20"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                    <a href="/liveAccounts#">
                        <div class="card">
                            <div class="card-body p-3">
                                <a href="{{ url('/trade-deposit') }}"
                                    class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-white text-opacity-75"></p>
                                        <h4 class="mb-0 text-black">Quick Deposit</h4>
                                    </div>
                                    <div class="avtar bg-success-subtle">
                                        <i class="ti ti-bolt f-18"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </a>
                    <a href="/liveAccounts#">
                        <div class="card">
                            <div class="card-body p-3">
                                <a href="{{ url('/trade-withdrawal') }}"
                                    class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-white text-opacity-75"></p>
                                        <h4 class="mb-0 text-black">Quick Withdraw</h4>
                                    </div>
                                    <div class="avtar bg-success-subtle">
                                        <i class="ti ti-bolt f-18"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </a>
                    <a href="/liveAccounts#">
                        <div class="card">
                            <div class="card-body p-3">
                                <a href="{{ url('/user-profile') }}"
                                    class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0 text-white text-opacity-75"></p>
                                        <h4 class="mb-0 text-black">My Profile</h4>
                                    </div>
                                    <div class="avtar bg-success-subtle">
                                        <i class="ti ti-user f-18"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
	
	<script>
	$(document).on("click", ".deleteaccount", function () {
		Swal.fire({
			title: "Connecting...",
			text: "Please wait while we connect to the MT5 WebTerminal.",
			allowOutsideClick: false,
			allowEscapeKey: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});
	});
	
	$(document).on("click", ".openwebtrade", function () {

		let login    = $("#acc_login").val();
		let password = $("#acc_password").val();

		if (!login || !password) {
			Swal.fire("Error", "Missing MT5 account details!", "error");
			return;
		}

		// SweetAlert loading
		Swal.fire({
			title: "Connecting...",
			text: "Please wait while we connect to the MT5 WebTerminal.",
			allowOutsideClick: false,
			allowEscapeKey: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		$.ajax({
			url: "/mt5/generate-token",
			type: "POST",
			data: {
				login: login,
				password: password,
				_token: "{{ csrf_token() }}"
			},
			success: function (res) {
				if (!res.token) {
					Swal.fire("Error", "Could not generate MT5 token.", "error");
					return;
				}

				// Build URL
				let url =
					res.terminal_url +
					"?mode=connect&token=" +
					encodeURIComponent(res.token) +
					"&lang=en&theme-mode=0";

				// Open in new window
				window.open(url, "_blank");

				// Close loading Swal
				Swal.close();
			},
			error: function () {
				Swal.fire("Error", "MT5 login failed!", "error");
			}
		});

	});

	
	</script>
	
@endsection
