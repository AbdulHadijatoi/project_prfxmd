@extends('layouts.admin.admin')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" />
@endsection
@section('content')
    @php
        $current_permissions = session('current_permissions');
    @endphp
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- Start:: row-1 -->
            <div class="row mt-5" id="user-profile">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">CLIENT DETAILS</div>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Client Details</li>
                            </ol>
                        </div>
                        <div class="card-body">
                            <div class="wideget-user">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12">
                                        <div class="wideget-user-desc d-flex flex-column flex-lg-row">
                                            <div
                                                class="wideget-user-img d-flex align-items-center justify-content-center mb-3 mb-lg-0">
                                                <img src="/admin_assets/assets/images/users/client.jpeg" alt="img"
                                                    style="width:100px">
                                            </div>
                                            <div class="user-wrap">
                                                <div class="row mb-3">
                                                    <div data-enc="<?= md5($user->email) ?>"
                                                        class="col-lg-12 d-flex ">
                                                        <h4 class="fw-normal text-uppercase">{{ $user->fullname }}</h4>
                                                    </div>
                                                </div>
                                                <h6 class="mb-3 fw-normal">
                                                    <span class="mt-2 px-2 d-lg-inline d-block"><span
                                                            class="fi fis fi-{{ strtolower($country_code->country_alpha) }} me-2"></span>{{ $user->country }}</span>

                                                    <span
                                                        class="mt-2 border-start border-2  px-2 d-lg-inline d-block "
                                                        data-status="{{ $user->status }}"
                                                        data-kyc_verify="{{ $user->kyc_verify }}"
                                                        data-email_confirmed="{{ $user->email_confirmed }}"
                                                        data-enc="<?= md5($user->email) ?>"
                                                        data-email="<?= $user->email ?>"
                                                        data-fullname="<?= $user->fullname ?>">{!! $user->kyc_verify == 0
                                                            ? '<span class="badge bg-outline-danger">Pending KYC</span>'
                                                            : ($user->kyc_verify == 1
                                                                ? '<span class="badge bg-outline-success">KYC Verified</span>'
                                                                : '') !!}</span>

                                                    <span
                                                        class="mt-2 border-start border-2  px-2 d-lg-inline d-block"><strong>DOJ:</strong>{{ date('d M Y h:i A', strtotime($user->created_at)) }}</span>

                                                    <span
                                                        class=" mt-2 border-start border-2  px-2 d-lg-inline d-block "
                                                        data-status="{{ $user->status }}"
                                                        data-kyc_verify="{{ $user->kyc_verify }}"
                                                        data-email_confirmed="{{ $user->email_confirmed }}"
                                                        data-enc="<?= md5($user->email) ?>"
                                                        data-email="<?= $user->email ?>"
                                                        data-fullname="<?= $user->fullname ?>">{!! $user->status == 0
                                                            ? '<span class="badge bg-outline-danger">Inactive</span>'
                                                            : ($user->status == 1
                                                                ? '<span class="badge bg-outline-success">Active</span>'
                                                                : '') !!}</span>
                                                    <span
                                                        class="mt-2 border-start border-2  px-2 d-lg-inline d-block"><span
                                                            class="badge bg-outline-info">{{ $user->user_group_name }}</span></span>
                                                </h6>
                                                <div class="row">
                                                    <div class="col-12 col-lg-6">
                                                        <div class="d-flex align-items-center">
                                                            <button
                                                                class="btn btn-icon  bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                <i class="ri-mail-line"></i>
                                                            </button>
                                                            <div>
                                                                <div class="text-muted fs-11 mb-0">Email:</div>
                                                                <div class="fs-12 mb-1">{{ $user->email }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6">
                                                        <div class="d-flex align-items-center">
                                                            <button
                                                                class="btn btn-icon  bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                <i class="ri-phone-line"></i>
                                                            </button>
                                                            <div>
                                                                <div class="text-muted fs-11 mb-0">Phone:</div>
                                                                <div class="fs-12 mb-1">{{ $user->number }}</div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="row mt-1 border-top border-2 border-default py-1">
                                                    <div class="col-12 col-lg-6 cursor-pointer"
                                                        data-rm="<?= $rm_details->rm_id ?? '' ?>"
                                                        data-enc="<?= md5($user->email) ?>"
                                                        data-email="<?= $user->email ?>"
                                                        data-fullname="<?= $user->fullname ?>">
                                                        <div class="d-flex align-items-center">
                                                            <button
                                                                class="btn btn-icon  bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                <i class="ri-user-2-fill"></i>
                                                            </button>
                                                            <div>
                                                                <div class="text-muted fs-11 mb-0">Relationship Manager:
                                                                </div>
                                                                <div class="fs-12 mb-1">{{ $rm_details->username ?? '' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-6 cursor-pointer"
                                                        data-enc="<?= md5($user->email) ?>"
                                                        data-email="<?= $user->email ?>"
                                                        data-fullname="<?= $user->fullname ?>">
                                                        <div class="d-flex align-items-center">
                                                            <button
                                                                class="btn btn-icon  bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                <i class="ri-user-line"></i>
                                                            </button>
                                                            <div>
                                                                <div class="text-muted fs-11 mb-0">Parent IB:</div>
                                                                <div class="fs-12 mb-1">{{ $user->ib1 }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                

                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="">
                        <div class="">
                            <div class="border-0">
                                <div class="tab-content clienttabs">
                                    <div class="tab-pane p-0 active" id="tab-info">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card custom-card">
                                                    <div class="card-header">
                                                        <div class="card-title"> IB Request Details</div>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-group">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                How long have you worked as an IB and with which broker(s)?
                                                                <span>{{ $ibDetails->ibExp }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Choose your partnership model
                                                                <span>{{ $ibDetails->partnershipModel }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Which countries or regions do you target?
                                                                <span>{{ $ibDetails->regions }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                What type of clients do you usually work with?
                                                                <span>{{ $ibDetails->clientType }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                How many new clients can you bring each month?
                                                                <span>{{ $ibDetails->clientsbring }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                What is your expected monthly trading turnover?
                                                                <span>{{ $ibDetails->turnover }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                What is the expected monthly deposits from your clients?
                                                                <span>{{ $ibDetails->deposits }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
																Do you have a website or landing page for promotion?
                                                                <span>{{ $ibDetails->websitepromo }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Which marketing channels do you use?
                                                                <span>{{ $ibDetails->channels }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                What is your monthly marketing budget?
                                                                <span>{{ $ibDetails->budget }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                What is your preferred language for support?
                                                                <span>{{ $ibDetails->languagePref }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Where did you hear about us?
                                                                <span>{{ $ibDetails->referral }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Investment
                                                                <span>{{ $ibDetails->investment }}</span>
                                                            </li>
															<li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                Accept Terms and Conditions?
                                                                <span>{{ $ibDetails->termsaccept }}</span>
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
                </div><!-- COL-END -->
            </div>
            <!-- End:: row-1 -->
        </div>
    </div>
@endsection
