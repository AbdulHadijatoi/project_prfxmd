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
            @include('mt5_accounts_tab') <!-- Adjust the path according to your structure -->
            <div class="col-md-12 col-lg-9">
                <div class="card">
                    <div class="card-body border-bottom pb-0 mb-4">
                        <div class="d-flex align-items-center justify-content-between flex-wrap">
							<h3 class="mb-0">My Trading Demo Accounts</h3>

							<div class="d-flex gap-2 mt-2 mt-md-0">								
								<a href="/createDemoAccount" class="btn btn-outline-primary">
									Open Demo Account
								</a>
							</div>
						</div>
                    </div>
                    <div class="tab-content" id="myTabContent">
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
                                        @foreach ($results as $acc)
                                            <tr>
                                                <td>
                                                    <div class="row align-items-center">
                                                        <div class="col-auto pe-0">
                                                            <img src="/assets/images/mt5.png" alt="user-image" class="wid-50 hei-50 rounded">
                                                        </div>
                                                        <div class="col">
                                                            <h4 class="mb-0 ms-2"><span class="text-truncate w-100">{{ $acc->trade_id }}</span></h4>
                                                            <div class="d-flex justify-content-between ms-2" >
                                                                <div>
                                                                    <div class="fw-bold text-muted">
                                                                        {{ $acc->accountType->ac_name }}</div>
                                                                    {{-- <div class="mb-2 fw-normal">
                                                                        {{ $acc->accountType->ac_group }}</div> --}}
                                                                </div>
                                                            </div>
                                                            {{-- <p class="text-muted ms-2 f-12 mb-0"><span class="text-truncate w-100">{{ $acc->account_type }}</span></p> --}}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="f-w-400 f-16">1:{{ $acc->leverage }}</td>
                                                <td class="text-end f-w-400 f-16">$ {{ number_format($acc->Balance, 2) }}</td>
                                                <td class="text-end f-w-400 f-16">$ {{ number_format($acc->equity, 2) }}</td>
                                                <td class="text-end f-w-200">
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ url('/view_account_details?type=demo&id=' . $acc->trade_id) }}"
                                                           class="btn btn-sm btn-outline-secondary d-grid me-2">
                                                           <span>View <svg class="pc-icon">
                                                               <use xlink:href="#custom-login"></use>
                                                           </svg></span>
                                                        </a>
														<a href="{{ route('tradeonline', ['login' => $acc->trade_id, 'password' => $acc->trader_pwd]) }}" target="_blank" class="btn btn-sm btn-outline-secondary d-grid">
															<span class="">Trade <i class="ti ti-chart-line"></i></span>
														</a>
														<input type="hidden" id="acc_login" value="{{ $acc->trade_id }}" />
														<input type="hidden" id="acc_password" value="{{ $acc->trader_pwd }}" />
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
                <a href="/createDemoAccount">
                    <div class="card bg-primary available-balance-card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-0 text-white">Create Account</h4>
                                    <p class="mb-0 text-white text-opacity-75">Open Demo Account</p>
                                </div>
                                <div class="avtar"><i class="ti ti-folder-plus f-20"></i></div>
                            </div>
                        </div>
                    </div>
                </a>
                <a href="/liveAccounts#">
                    <div class="card">
                        <div class="card-body p-3">
                            <a href="user-profile" class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="mb-0 text-black">My Profile</h4>
                                </div>
                                <div class="avtar bg-success-subtle"><i class="ti ti-user f-18"></i></div>
                            </a>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>


@endsection
