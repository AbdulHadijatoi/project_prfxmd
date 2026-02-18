@extends('layouts.admin.admin')
@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css" />
    <style>
        .dropbox-card>* {
            position: relative;
            z-index: 5;
        }

        .bg-gray-800 {
            background: #1d2630;
        }

        .dropbox-card:after {
            content: "";
            background-image: url('https://dashboard.leveragemarkets.com/assets/dropbox.png');
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            opacity: .5;
            background-position: bottom right;
            background-size: 100%;
            background-repeat: no-repeat;
        }

        .avtar.avtar-s {
            width: 40px;
            height: 40px;
            font-size: 14px;
            border-radius: 12px;
        }

        .dropbox-card .avtar {
            background: #ffffff4d;
            color: #fff;
        }

        .avtar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            width: 48px;
            height: 48px;
        }

        .pc-icon:not([class*=hei-]) {
            height: 22px;
        }

        .pc-icon:not([class*=wid-]) {
            width: 22px;
        }
    </style>
@endsection
@section('content')
	@php
    use Carbon\Carbon;
@endphp
    @include('admin.client_update_common')
    @php
        $current_permissions = session('current_permissions');
    @endphp
    <div class="modal fade" id="editUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="editUserLabel">
        <div class="modal-dialog  modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('admin.updateUser') }}" id="editUserForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Update Client Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0">
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="input-label" class="form-label">Email:</label>
                                <input type="text" class="form-control" name="email" required readonly>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="input-label" class="form-label">Full Name:</label>
                                <input type="text" class="form-control" name="fullname" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="input-label" class="form-label">Phone:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend w-25">
                                        <select class="form-select me-2 w-25 edit-countrycode" name="country_code" required>
                                            <option value="">Country Code</option>
                                            <?php foreach ($countries as $country) { ?>
                                            <option value="+<?= $country['country_code'] ?>"
                                                data-flag="<?= strtolower($country['country_alpha']) ?>">
                                                +<?= $country['country_code'] ?>
                                                (<?= $country['country_name'] ?>)</option>
                                            <?php } ?>
                                        </select>


                                    </div>
                                    <input type="text" class="form-control" id="phone_number" name="telephone"
                                        placeholder="Enter phone number">
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="input-label" class="form-label">Country:</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <?php foreach ($countries as $country) { ?>
                                    <option value="<?= $country['country_name'] ?>">
                                        <?= $country['country_name'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="">Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text showPassword h-100">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <div class="form-group">
                                    <label for="">Confirm Password:</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="input" name="confirm_password"
                                            required>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text showPassword h-100">
                                                <i class="fa fa-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 form-group mb-3">
                                <label for="input-label" class="form-label">Group:</label>
                                <select class="form-select" id="group_id" name="group_id" required>
                                    <option value="">Select Group</option>
                                    @foreach ($user_groups as $group)
                                        <option value="{{ $group->user_group_id }}">
                                            {{ $group->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="email_notification">
                                    <label class="form-check-label">Send Notification Email</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="updateUser" value="update" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addTicketModalLabel1">Add New Ticket</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{ route('admin.addTicket') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-12 col-lg-6">
                                <label for="input-label" class="form-label">Name</label>
                                <input type="text" class="form-control" name="subject_name" required>
                            </div>
                            <div class="col-12 col-lg-6">
                                <label for="input-label" class="form-label">Type</label>
                                <select class="form-control" name="ticket_type_id" required>
                                    <option value="">Select Type</option>
                                    <?php foreach ($ticket_types as $type) { ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo $type['ticket_type']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="input-label" class="form-label">Description</label>
                                <textarea class="form-control" name="discription" required rows="3"></textarea>
                            </div>

                            <div class="col-12 col-lg-6">
                                <label for="input-label" class="form-label">Status</label>
                                <select class="form-control" name="ticket_status_id" required>
                                    <?php foreach ($ticket_status as $status) { ?>
                                    <option {{ $status['ticket_status'] == 'Open' ? 'selected' : '' }}
                                        value="<?php echo $status['id']; ?>">
                                        <?php echo $status['ticket_status']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="email" value="<?php echo $user->email; ?>" />
                        <input type="submit" class="btn btn-primary" name="add_ticket" value="Add">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="kycModal" tabindex="-1" aria-labelledby="kycModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="kycModalLabel">KYC Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed id="kycFile" src="" type="" width="100%">
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="accountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form action="#" id="createMT5Form" method="post">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id" value="{{ md5($user->email) }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="accountModalLabel">Create New MT5 Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0">
                        <div class="row">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Account Type</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-select acc-types" required name="acc-types">
                                    <option value="" selected>Choose Account Type</option>
                                    <?php foreach ($acc_types as $gp) { ?>
                                    <option value="{{ $gp->ac_index }}">{{ $gp->ac_name }} [{{ $gp->ac_group }}]
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Leverage</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-select" name="leverage" id="leverage" required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_account" value="update" class="btn btn-primary">Create
                            Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mapAccountModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="mapAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form action="#" id="mapMT5Form" method="post">
                    <input type="hidden" name="client_id" id="client_id" value="<?= md5($user->email) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mapAccountModalLabel">MAP MT5 Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0">
                        <div class="row">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Trade ID</label>
                            </div>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <input class="form-control map-trade-id" name="trade_id" id="trade_id" required>
                                    <div class="input-group-prepend">
                                        <span style="cursor: pointer"
                                            class="bg-primary text-white cursor-pointer input-group-text fetch-account-details h-100">
                                            <i class="fa fa-download"></i>
                                        </span>
                                    </div>
                                </div>
                                {{-- <input class="form-control map-trade-id" name="trade_id" id="trade_id" required> --}}
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Account Type</label>
                            </div>
                            <div class="col-lg-8">
                                <select id="map_acc_type" class="form-select acc-types " required name="acc_type">
                                    <option value="" selected>Choose Account Type</option>
                                    <?php foreach ($create_new_acc_types as $gp) { ?>
                                    <option data-group="<?= $gp->ac_group ?>" value="<?= $gp->ac_index ?>"
                                        <?= $gp->status == 1 ? '' : 'disabled' ?>>
                                        <?= $gp->ac_name . '  (' . $gp->ac_group . ')' ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Leverage</label>
                            </div>
                            <div class="col-lg-8">
                                <select id="map_leverage" class="form-select leverage" name="leverage" id="leverage"
                                    required>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="map_account" class="btn btn-primary">Map Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ibModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="ibModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form action="#" id="ibRequestForm" method="post">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id" value="{{ md5($user->email) }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ibModalLabel">IB Request Management</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0">
                        <div class="d-flex align-items-center card-header w-100">
                            <div class="me-2">
                                <span class="avatar avatar-rounded">
                                    <img src="/admin_assets/assets/images/users/user.png" alt="img">
                                </span>
                            </div>
                            <div class="">
                                <div class="fs-15 fw-medium text-capitalize" id="clientName"></div>
                                <p class="mb-0 text-muted fs-11" id="clientEmail"></p>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <div class="col-lg-4 m-auto">
                                    <label class="form-label">IB Request Status</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select" required name="ib_status"
                                        aria-label="Default select example">
                                        <option value="" selected>--Status--</option>
                                        <option value="1">Approve</option>
                                        <option value="0">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 m-auto">
                                    <label class="form-label">Account Group</label>
                                </div>
                                <div class="col-lg-8">
                                    <select class="form-select" required name="ib_group"
                                        aria-label="Default select example">
                                        <option value="" selected>--Plans--</option>
                                        <?php foreach ($acc_groups as $gp) { ?>
                                        <option value="<?= $gp->ib_plan_id ?>"><?= $gp->ib_cat_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="ibRequest" value="update" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
                                    <div class="col-lg-9 col-xl-9">
                                        <div class="wideget-user-desc d-flex flex-column flex-lg-row">
                                            <div
                                                class="wideget-user-img d-flex align-items-center justify-content-center mb-3 mb-lg-0">
                                                <img src="/admin_assets/assets/images/users/client.jpeg" alt="img"
                                                    style="width:100px">
                                            </div>
                                            <div class="user-wrap">
                                                <div class="row mb-3">
                                                    <div data-enc="<?= md5($user->email) ?>"
                                                        class="col-lg-12 d-flex {{ in_array('update_client_details', $current_permissions) || in_array('/admin/updateUser', $current_permissions) ? 'edit-pencil-after editClient' : '' }}">
                                                        <h4 class="fw-normal text-uppercase">{{ $user->fullname }}</h4>
                                                    </div>
                                                </div>
                                                <h6 class="mb-3 fw-normal">
                                                    <span class="mt-2 px-2 d-lg-inline d-block"><span
                                                            class="fi fis fi-{{ strtolower($country_code->country_alpha) }} me-2"></span>{{ $user->country }}</span>

                                                    <span
                                                        class="mt-2 border-start border-2  px-2 d-lg-inline d-block {{ in_array('update_client_details', $current_permissions) ? 'statusToggle edit-pencil-after' : '' }}"
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
                                                        class=" mt-2 border-start border-2  px-2 d-lg-inline d-block {{ in_array('update_client_details', $current_permissions) ? 'statusToggle edit-pencil-after' : '' }}"
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
                                                    <div class="col-12 col-lg-6 {{ in_array('/admin/updateRM', $current_permissions) ? 'rmToggle edit-pencil-after d-flex align-items-center' : '' }} cursor-pointer"
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
                                                    <div class="col-12 col-lg-6 {{ in_array('/admin/updateIB', $current_permissions) ? 'updateIb edit-pencil-after d-flex align-items-center' : '' }} cursor-pointer"
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
                                                @if (in_array('updateAccLimit', $current_permissions))
                                                    <form method="post" action="updateAccLimit">
                                                        @csrf
                                                        <div class="row mt-1 border-top border-2 border-default py-1">
                                                            <div class="col-12">
                                                                <input type="hidden" name="email"
                                                                    value="{{ md5($user->email) }}" />
                                                                <div class="d-flex align-items-center">
                                                                    <img class="me-3"
                                                                        src="/admin_assets/assets/images/mt5.png"
                                                                        alt="card img" style="width:30px;">
                                                                    <div>
                                                                        <div class="text-muted fs-11 mb-0">Account Limit:
                                                                        </div>
                                                                        <div class="d-flex mt-1">
                                                                            <input type="number"
                                                                                class="form-control me-3"
                                                                                placeholder="Account Limit"
                                                                                name="acc_limit"
                                                                                value="{{ $user->acc_limit }}">
                                                                            <input type="submit" class="btn btn-primary"
                                                                                value="Update">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
									<?php if ($user->wallet_enabled): ?>
                                    <div class="col-lg-3 col-xl-3">
                                        <div class="card bg-gray-800 dropbox-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <h5 class="text-white">Wallet </h5>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-between mb-2 mt-2">
                                                    <div>
                                                        <div class="avtar avtar-s">
                                                            <span class="pc-icon">

                                                                <svg version="1.1" id="Capa_1"
                                                                    xmlns="http://www.w3.org/2000/svg"
                                                                    xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                                                                    y="0px" viewBox="0 0 229.5 229.5"
                                                                    style="enable-background:new 0 0 229.5 229.5;"
                                                                    xml:space="preserve" fill="#fff">
                                                                    <path
                                                                        d="M214.419,32.12c-0.412-2.959-2.541-5.393-5.419-6.193L116.76,0.275c-1.315-0.366-2.704-0.366-4.02,0L20.5,25.927
                                                                                c-2.878,0.8-5.007,3.233-5.419,6.193c-0.535,3.847-12.74,94.743,18.565,139.961c31.268,45.164,77.395,56.738,79.343,57.209
                                                                                c0.579,0.14,1.169,0.209,1.761,0.209s1.182-0.07,1.761-0.209c1.949-0.471,48.076-12.045,79.343-57.209
                                                                                C227.159,126.864,214.954,35.968,214.419,32.12z M174.233,85.186l-62.917,62.917c-1.464,1.464-3.384,2.197-5.303,2.197
                                                                                s-3.839-0.732-5.303-2.197l-38.901-38.901c-1.407-1.406-2.197-3.314-2.197-5.303s0.791-3.897,2.197-5.303l7.724-7.724
                                                                                c2.929-2.928,7.678-2.929,10.606,0l25.874,25.874l49.89-49.891c1.406-1.407,3.314-2.197,5.303-2.197s3.897,0.79,5.303,2.197
                                                                                l7.724,7.724C177.162,77.508,177.162,82.257,174.233,85.186z" />
                                                                </svg>

                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h2 class="text-center text-white">${{ $walletBalance }}</h2>
                                                    </div>
                                                </div><a href="#"><small class="text-white">Fund Now</small></a>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-3 align-items-center justify-content-center mt-1">
                                        
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#depositModal" data-email="<?= $user->email ?>">
                                                Wallet Deposit
                                            </button>
                                            <button class="btn btn-primary-light" data-bs-toggle="modal"
                                                data-bs-target="#walletModal" data-email="<?= $user->email ?>">
                                                Wallet Withdraw
                                            </button>
                                        </div>
                                    </div>
									<?php endif; ?>
                                    {{-- <div class="col-lg-3 col-xl-12" style="display:none">
                                        <button type="button" class="btn btn-outline-light shadow-sm"><i
                                                class="ri-mail-line text-primary me-1"></i>Send Mail</button>
                                        <button type="button" class="btn btn-outline-light shadow-sm"><i
                                                class="ri-share-line"></i></button>
                                        <button type="button" class="btn btn-outline-light shadow-sm"><i
                                                class="ri-flag-line"></i></button>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="border-top">
                            <div class="wideget-user-tab">
                                <div class="tab-menu-heading border-0">
                                    <div class="tabs-menu1">
                                        <ul class="nav clienttabs">
                                            <li class=""><a href="#tab-overview" class="active show"
                                                    data-bs-toggle="tab">OVERVIEW</a></li>
                                            <li><a href="#tab-transactions" data-bs-toggle="tab"
                                                    class="">TRANSACTIONS</a></li>
                                            <?php if (!empty($ib_details)): ?>
                                            <li><a href="#tab-ib" data-bs-toggle="tab" class="">IB PROFILE</a></li>
                                            <?php endif; ?>
                                            <li><a href="#tab-info" data-bs-toggle="tab" class="">ADDITIONAL
                                                    INFO</a></li>
                                            <li><a href="#tab-profile" data-bs-toggle="tab" class="">PROFILE
                                                    SETTINGS</a></li>
                                            @if (session('userData.client_index') == 1)
                                                <li><a href="#tab-tickets" data-bs-toggle="tab"
                                                        class="">TICKETS<span
                                                            class="badge bg-success ms-1 rounded-5">{{ count($tickets) ?? 0 }}</span></a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="">
                            <div class="border-0">
                                <div class="tab-content clienttabs">
                                    <div class="tab-pane p-0 active show" id="tab-overview">
                                        <div class="row">
                                            <div class="col-12 col-xl-9">
                                                <div class="card custom-card">
                                                    <div class="card-header justify-content-between">
                                                        <div class="card-title">SUMMARY</div>
                                                        @if (count($live_accounts) < $user->acc_limit)
                                                            <div class="d-flex">
                                                                <button type="button" class="btn btn-primary btn-sm "
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#mapAccountModal">
                                                                    <i class="ri-arrow-left-right-fill"></i>
                                                                    MAP MT5 ACCOUNTS
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row pb-3 border-bottom">
                                                            <div class="col-xl-3">
                                                                <h4 class="text-muted mb-3 fw-normal">TOTAL DEPOSIT</h4>
                                                                <h4 class="fw-normal">
                                                                    ${{ htmlentities(number_format((float) $total_wd, 2)) }}
                                                                </h4>
                                                            </div>
                                                            <div class="col-xl-3">
                                                                <h4 class="text-muted mb-3 fw-normal">TOTAL WITHDRAW</h4>
                                                                <h4 class="fw-normal">
                                                                    ${{ htmlentities(number_format((float) $total_ww, 2)) }}
                                                                </h4>
                                                            </div>
                                                            <div class="col-xl-3">
                                                                <h4 class="text-muted mb-3 fw-normal">TRANSFER</h4>
                                                                <h4 class="fw-normal">
                                                                    ${{ htmlentities(number_format($a2a_transfer, 2)) }}
                                                                </h4>
                                                            </div>

                                                        </div>
                                                        <div class="row mt-3">
                                                            <div class="d-flex justify-content-between">
                                                                <h4>LIVE MT5 ACCOUNTS</h4>
                                                                @if (count($live_accounts) < $user->acc_limit)
                                                                    <button type="button"
                                                                        class="btn btn-outline-dark btn-sm bg-light"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#accountModal">
                                                                        <i class="ri-add-box-fill"></i>
                                                                        CREATE NEW MT5 ACCOUNTS
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="row px-2">
                                                            <?php if (empty($live_accounts)) { ?>
                                                            <div class="text-muted my-4">No Live Accounts Found.</div>
                                                            <?php } ?>
                                                            <?php foreach ($live_accounts as $acc): ?>
                                                            <div
                                                                class="col-xl-4 col-lg-6 my-2 border border-3 border-dashed">
                                                                <div>
                                                                    <div
                                                                        class="row mb-2 mt-2 border-2 border-bottom pb-2 border-bottom-dashed">
                                                                        <div class="d-flex w-50 flex-column">
                                                                            <img src="/admin_assets/assets/images/mt5.png"
                                                                                alt="card img" style="width:50px;">
                                                                            <div class="fs-18 mt-1 text-black-50 fw-bold">
                                                                                {{ $acc->trade_id }}</div>
                                                                        </div>
                                                                        <div class="d-flex justify-content-end w-50">
                                                                            <span
                                                                                class="h4 mt-2 fw-normal">${{ $acc->Balance }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <?php
                                                                    $index = array_search($acc->account_type, array_column((array) $acc_types, 'ac_index'));
                                                                    ?>
                                                                    <div class="d-flex justify-content-between">
                                                                        <div>
                                                                            <div class="fw-bold fs-12">
                                                                                {{ $acc->ac_name }}</div>
                                                                            <div class="mb-2 fw-normal fs-10">
                                                                                {{ $acc->ac_group }}</div>
                                                                        </div>
                                                                        <div class="mt-auto mb-auto">
                                                                            <a
                                                                                href="/admin/view_account_details?id={{ md5($acc->trade_id) }}">
                                                                                <i class="fa fa-edit fw-bold"
                                                                                    style="font-size: 1rem;color: var(--primary-color);"></i>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        @if (count($live_accounts) >= $user->acc_limit)
                                                            <div class="row mt-4">
                                                                <div class="col-12">
                                                                    <div class="alert alert-danger">
                                                                        User has reached the maximum account limit of
                                                                        {{ $user->acc_limit }}. If you need to create new
                                                                        account, please update the account limit.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-xl-3">
                                                <div>
                                                    <button type="button"
                                                        class="my-2 py-3 btn btn-outline-dark btn-sm w-100"
                                                        data-bs-toggle="modal" data-bs-target="#addTicketModal">
                                                        CREATE TICKET
                                                    </button>
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <div class="d-flex justify-content-between">
                                                                <div class="card-title">INTRODUCING BROKER</div>
                                                                <div>
                                                                    <?php if ($user->ib_status == 0): ?>
                                                                    <span
                                                                        class="badge bg-outline-warning text-end">Pending</span>
                                                                    <?php elseif ($user->ib_status == 1): ?>
                                                                    <span class="badge bg-outline-success text-end">Active
                                                                        IB</span>
                                                                    <?php else: ?>
                                                                    <span class="badge bg-outline-info text-end">Not
                                                                        Requested</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <p class="card-text">A request on behalf of client for creating
                                                                IB profile for this client.
                                                            </p>
                                                            <?php if ($user->ib_status != 1): ?>
                                                            <?php if ($user->ib_status == '0'): ?>
                                                            <button type="button"
                                                                class=" ibToggle ib-enroll my-2 py-3 btn btn-outline-dark btn-sm w-100 text-uppercase"
                                                                data-bs-toggle="modal" data-bs-target="#ibModal"
                                                                data-fullname="<?= $user->fullname ?>"
                                                                data-email="<?= $user->email ?>"
                                                                data-enc="<?= md5($user->email) ?>"
                                                                data-ib_status="<?= $user->ib_status ?>">
                                                                Approve Request
                                                            </button>
                                                            <?php else: ?>
                                                            <button type="button"
                                                                class="ibToggle ib-enroll my-2 py-3 btn btn-outline-dark btn-sm w-100 text-uppercase"
                                                                data-bs-toggle="modal" data-bs-target="#ibModal"
                                                                data-fullname="<?= $user->fullname ?>"
                                                                data-email="<?= $user->email ?>"
                                                                data-enc="<?= md5($user->email) ?>"
                                                                data-ib_status="<?= $user->ib_status ?>">
                                                                Request To become ib
                                                            </button>
                                                            <?php endif; ?>
                                                            <?php else: ?>
                                                            <hr style="opacity:.1;">
                                                            <label class="col-form-label col-12 text-lg-start">
                                                                Copy this IB referral link to share with potential clients!
                                                            </label>
                                                            <div class="col-12 mb-4">
                                                                <div class="input-group mb-2"><input type="text"
                                                                        class="form-control" id="pc-clipboard-1"
                                                                        value="https://{{ $_SERVER['HTTP_HOST'] }}/ib-ref?refercode={{ base64_encode($user->email) }}"
                                                                        readonly=""><button
                                                                        class="btn btn-lg btn-primary cb" id="ibClient"
                                                                        data-clipboard-target="#pc-clipboard-1"><i
                                                                            class="fa fa-copy"></i></button>
                                                                </div>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									
                                    <div class="tab-pane p-0" id="tab-transactions">
                                        <div class="row">
										<div class="col-12">
											<div class="card custom-card">
											<div class="card-header justify-content-between">
												<div class="card-title">ALL TRANSACTIONS</div>
																								
											</div>
											<div class="card-body">
												
												<div class=" row pb-3 border-bottom">
													<div class="col-xl-3">
														<h4 class="text-muted mb-3 fw-normal">TOTAL DEPOSIT</h4>
														<h4 class="fw-normal">
															${{ htmlentities(number_format((float) $total_wd, 2)) }}
														</h4>
													</div>
													<div class="col-xl-3">
														<h4 class="text-muted mb-3 fw-normal">TOTAL WITHDRAW</h4>
														<h4 class="fw-normal">
															${{ htmlentities(number_format((float) $total_ww, 2)) }}
														</h4>
													</div>
													<div class="col-xl-3">
														<h4 class="text-muted mb-3 fw-normal">TRANSFER</h4>
														<h4 class="fw-normal">
															${{ htmlentities(number_format($a2a_transfer, 2)) }}
														</h4>
													</div>
												</div>							
										
												<div class="d-flex align-items-center justify-content-between mt-3">
												<div class="table-responsive" style="width: 100%; overflow-x: auto;">
												<table id="ledgerTable" class="ledgerTable table table-bordered text-nowrap w-100">
													<thead>
														<tr>
															<th>#ID</th>
															<th>Date</th>
															<th>Type</th>
															<th>Amount</th>
															<th>Currency</th>
															<th>Method</th>
															<th>Status</th>
															{{-- <th>Action</th> --}}
														</tr>
													</thead>
													<tbody>
														@foreach($ledger as $row)
														<tr>
															<td>TXN0{{ $loop->iteration }}</td>									
															<td>
																<h6 class="f-w-500">{{Carbon::parse($row->created_at)->format('Y-m-d') }}</h6>
																<p class="text-muted mb-0">
																  <small>{{ Carbon::parse($row->created_at)->format('H:i A') }}</small>
																</p>
															</td>
															<td>{{ $row->transtype }}</td>
															<td><h6 class="f-w-500 f-16">{{ $row->valamount }}</h6></td>
															<td><h6 class="f-w-500 f-16">USD</h6></td>
															<td><h6 class="f-w-500 f-16">{{ $row->particulars }}</h6></td>
															<td class="{{ $row->Status == 0 ? 'text-warning' : ($row->Status == 1 ? 'text-success' : 'text-danger') }}">
																<p>{{ $row->Status == 0 ? 'Pending' : ($row->Status == 1 ? 'Success' : 'Rejected') }}</p>
															</td>
															{{-- <td></td> --}}
														</tr>
														@endforeach
													</tbody>
												</table>
												</div>
												</div>
												<?PHP /*<div class="d-flex justify-content-between align-items-center mt-3">
													<div>
														Showing {{ $ledger->firstItem() }} to {{ $ledger->lastItem() }}
														of {{ $ledger->total() }} entries
													</div>
													<div>
														{{ $ledger->links() }}
													</div>
												</div> */ ?>
												
												<div class="d-flex justify-content-between align-items-center mt-3">
													<div>
														Notes:
													</div>
												</div>
												<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
													<div><b>W2A</b> - Wallet to Account</div>
													<div><b>A2W</b> - Account to Wallet</div>
													<div><b>C2C</b> - Client to Client</div>
													<div><b>A2A</b> - Account to Account</div>
													<div><b>W2D</b> - Wallet to Deposit</div>
													<div><b>P2P</b> - Peer to Peer</div>
												</div>
											</div>
											</div>
                                        </div>
                                        </div>
                                    </div>
									
									
                                    <?php if (!empty($ib_details)): ?>
                                    <div class="tab-pane p-0" id="tab-ib">
                                        <div class="row">
                                            <div class="col-sm-12 col-xl-3 col-lg-3 col-xl-3">
                                                <div class="card custom-card">
                                                    <div class="card-body">
                                                        <div class="card-order">
                                                            <div class="row {{ in_array('update_ib_plan', $current_permissions) ? 'ibToggle edit-pencil-absolute' : '' }}"
                                                                data-bs-toggle="modal" data-bs-target="#ibModal"
                                                                data-fullname="<?= $user->fullname ?>"
                                                                data-email="<?= $user->email ?>"
                                                                data-enc="<?= md5($user->email) ?>"
                                                                data-ib_status="<?= $user->ib_status ?>"
                                                                data-ib_group="<?= $ib_details->acc_type ?>">
                                                                <div class="col-4">
                                                                    <button
                                                                        class="w-100 h-75 btn btn-icon  btn-lg bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                        <i class="fa fa-list-alt" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="col-8">
                                                                    <p class="h5 text-muted">IB </br> PLAN</p>
                                                                    <h4>{{ getPlanNameByPlanId($acc_groups, $ib_details->acc_type) }}
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-xl-3 col-lg-3 col-xl-3">
                                                <div class="card custom-card">
                                                    <div class="card-body">
                                                        <div class="card-order">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <button
                                                                        class="w-100 h-75 btn btn-icon  btn-lg bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                        <i class="fa fa-credit-card"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="col-8">
                                                                    <p class="h5 text-muted">IB </br>WALLET</p>
                                                                    <h4><?= "$" . number_format($ib_details->deposit -
                                                                    $ib_details->withdraw, 2) ?></h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-xl-3 col-lg-3 col-xl-3">
                                                <div class="card custom-card">
                                                    <div class="card-body">
                                                        <div class="card-order">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <button
                                                                        class=" w-100 h-75 btn btn-icon  btn-lg bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                        <i class="fa fa-credit-card-alt"
                                                                            aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="col-8">
                                                                    <p class="h5 text-muted">TOTAL </br>COMMISSION</p>
                                                                    <h4>{{ $ib_details->deposit ? "$" . $ib_details->deposit : "$0.00" }}
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-xl-3 col-lg-3 col-xl-3">
                                                <div class="card custom-card">
                                                    <div class="card-body">
                                                        <div class="card-order">
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <button
                                                                        class="w-100 h-75  btn btn-icon  btn-lg bg-light border-light rounded-pill disabled me-3 text-secondary">
                                                                        <i class="fa fa-usd" aria-hidden="true"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="col-8">
                                                                    <p class="h5 text-muted">TOTAL </br>WITHDRAWAL</p>
                                                                    <h4>{{ $ib_details->withdraw ? "$" . $ib_details->withdraw : "$0.00" }}
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="card">
                                                    <div class="card-body p-3">
                                                        <ul class="nav nav-pills nav-tabs nav-justified" role="tablist">
                                                            <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                            <li class="nav-item"
                                                                data-target-form="#LEVEL{{ $i }}"
                                                                role="presentation"><a href="#LEVEL{{ $i }}"
                                                                    data-bs-toggle="tab"
                                                                    data-bs-target="#LEVEL{{ $i }}"
                                                                    data-toggle="tab"
                                                                    class="nav-link {{ $i == 1 ? 'active' : '' }}"
                                                                    aria-selected="false" role="tab"
                                                                    tabindex="-1"><i
                                                                        class="ti ti-chart-bar me-2"></i><span
                                                                        class="d-none d-sm-inline">LEVEL{{ $i }}</span></a>
                                                            </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="tab-content connectionTab" id="nav-tabContent">
                                                            <?php for ($i = 1; $i <= 15; $i++) { ?>
                                                            <div class="tab-pane fade{{ $i == 1 ? ' show active' : '' }}"
                                                                id="LEVEL{{ $i }}" role="tabpanel">
                                                                <div class="datatable-container">
                                                                    <table class="table table-hover datatable-table"
                                                                        id="pc-dt-simple">
                                                                        <thead>
                                                                            <tr>
                                                                                <th style="width: 30%;">CLIENT</th>
                                                                                <th style="width: 30%;">TYPE</th>
                                                                                <th class="text-end" style="width: 10%;">
                                                                                    TOTAL ACCOUNTS</th>
                                                                                <th class="text-end" style="width: 10%;">
                                                                                    TOTAL DEPOSIT</th>
                                                                                <th class="text-end" style="width: 15%;">
                                                                                    PROFILE STATUS</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php foreach ($clients[$i] as $client) { ?>
                                                                            <tr data-index="0">
                                                                                <td>
                                                                                    <div class="row align-items-center">
                                                                                        <div class="col-auto pe-0"><img
                                                                                                src="/assets/images/ib_avatar.png"
                                                                                                alt="user-image"
                                                                                                class="wid-55 hei-55 rounded"
                                                                                                style="height:50px">
                                                                                        </div>
                                                                                        <div class="col">
                                                                                            <h6 class="mb-2"><span
                                                                                                    class="text-truncate w-100">{{ $client->fullname }}</span>
                                                                                            </h6>
                                                                                            <p
                                                                                                class="text-muted f-12 mb-0">
                                                                                                <span
                                                                                                    class="text-truncate w-100">{{ $client->email }}</span>
                                                                                            </p>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text-start f-w-400">
                                                                                    {{ $client->ib_exists ? 'IB' : 'Client' }}
                                                                                </td>
                                                                                <td class="text-end f-w-400">
                                                                                    {{ $client->liveaccounts }}</td>
                                                                                <td class="f-w-400 text-end">
                                                                                    ${{ $client->total_deposit }}</td>
                                                                                <td class="text-end">
                                                                                    <?php if ($client->email_confirmed == 1) { ?>
                                                                                    <span
                                                                                        class="badge btn bg-success">Active</span>
                                                                                    <?php } else { ?>
                                                                                    <span class="badge btn bg-info">Not
                                                                                        Verified</span>
                                                                                    <?php } ?>
                                                                                </td>
                                                                            </tr>
                                                                            <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="tab-pane p-0" id="tab-info">
                                        <div class="row">
                                            <div class="col-12 col-lg-6">
                                                <div class="card custom-card">
                                                    <div class="card-header">
                                                        <div class="card-title">Bank Details</div>
                                                    </div>
                                                    <div class="card-body">
                                                        <ul class="list-group">
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                ACCOUNT HOLDER NAME
                                                                <span>{{ $bank_details->ClientName ?? '' }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                BANK NAME
                                                                <span>{{ $bank_details->bankName ?? '' }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                ACCOUNT NUMBER
                                                                <span>{{ $bank_details->accountNumber ?? '' }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                IFSC CODE
                                                                <span>{{ $bank_details->code ?? '' }}</span>
                                                            </li>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                SWIFT CODE
                                                                <span>{{ $bank_details->swift_code ?? '' }}</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <div class="card custom-card">
                                                    <div class="card-header">
                                                        <div class="card-title">Client Documents</div>
                                                    </div>
                                                    <?php
                                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                                                    $pdfExtensions = ['pdf'];
                                                    $mimeTypes = [
                                                        'jpeg' => 'image/jpeg',
                                                        'jpg' => 'image/jpeg',
                                                        'png' => 'image/png',
                                                        'pdf' => 'application/pdf',
                                                        'gif' => 'image/gif',
                                                    ];
                                                    ?>

                                                    <div class="card-body">
                                                        <?php foreach ($kyc_details as $kyc): ?>
                                                        <?php
                                                        $files = [
                                                            'front_image' => strtolower(pathinfo($kyc->front_image, PATHINFO_EXTENSION)),
                                                            'kyc_frontside' => strtolower(pathinfo($kyc->kyc_frontside, PATHINFO_EXTENSION)),
                                                            'kyc_backside' => strtolower(pathinfo($kyc->kyc_backside, PATHINFO_EXTENSION)),
                                                        ];
                                                        $statusText = $kyc->Status == '1' ? 'Approved' : ($kyc->Status == '2' ? 'Rejected' : 'Pending');
                                                        [$badgeClass, $icon] = getBadgeProperties($kyc->Status);
                                                        ?>

                                                        <?php if ($kyc->kyc_type == 'Address Proof' || $kyc->kyc_type == 'ID Proof'): ?>
                                                        <div
                                                            class="media card-body media-xs overflow-visible d-sm-flex d-block m-0 justify-content-between">
                                                            <div class="d-flex mb-2 mb-sm-0">
                                                                <div class="media-body valign-middle my-auto"
                                                                    style="max-width: 100px; display: flex; flex-direction: column;">
                                                                    <?php foreach (['front_image' => $files['front_image'], 'kyc_frontside' => $files['kyc_frontside'], 'kyc_backside' => $files['kyc_backside']] as $key => $extension): ?>
                                                                    <?php if (in_array($extension, $imageExtensions) || in_array($extension, $pdfExtensions)): ?>
                                                                    <button
                                                                        class="btn btn-lg btn-icon btn-light text-info me-2 mt-1"
                                                                        data-bs-toggle="modal" data-bs-target="#kycModal"
                                                                        data-bs-kyc="{{ asset($kyc->$key) }}"
                                                                        data-bs-type="{{ $mimeTypes[$extension] }}">
                                                                        <i
                                                                            class="ri-{{ in_array($extension, $pdfExtensions) ? 'file-pdf-2-line' : 'image-2-fill' }}"></i>
                                                                    </button>
                                                                    <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                                <div class="media-body valign-middle my-auto">
                                                                    <a href=""
                                                                        class="fw-semibold text-dark">{{ $kyc->kyc_type }}</a>
                                                                    <p class="text-muted m-0">
                                                                        {{ $kyc->registered_date_js }}</p>
                                                                </div>
                                                            </div>
                                                            <div
                                                                class="media-body valign-middle text-sm-end overflow-visible my-auto">
                                                                <span
                                                                    class="badge {{ $badgeClass }}">{!! $icon !!}
                                                                    <?= $statusText ?>
                                                                </span>
                                                            </div>
                                                            <div
                                                                class="media-body valign-middle text-sm-end overflow-visible my-auto">
                                                                <?php if ($kyc->Status == 2 || $kyc->Status == 0) { ?>
                                                                <button class="btn btn-lg btn-icon btn-light text-success"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="Approve"
                                                                    onclick="takeAction('{{ $kyc->id }}','{{ $kyc->email }}',1)">
                                                                    <i class="ri-check-line"></i>
                                                                </button>
                                                                <?php }
                                    if ($kyc->Status == 1 || $kyc->Status == 0) { ?>
                                                                <button class="btn btn-lg btn-icon btn-light text-danger"
                                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                                    title="Reject"
                                                                    onclick="takeAction('{{ $kyc->id }}','{{ $kyc->email }}',2)">
                                                                    <i class="ri-close-circle-line"></i>
                                                                </button>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>

                                                </div>
                                                <?php if (!isset($kyc)) { ?>
                                                <form method="post" enctype="multipart/form-data">
                                                    <div class="card custom-card">
                                                        <div class="card-header">
                                                            <div class="card-title">Upload Documents</div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label for="formFile" class="form-label">ID Proof Front
                                                                    Side</label>
                                                                <input class="form-control" id="formFile" name="image"
                                                                    type="file" accept="image/png,image/jpeg">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="formFile" class="form-label">ID Proof Back
                                                                    Side</label>
                                                                <input class="form-control" id="formFile" name="image1"
                                                                    type="file" accept="image/png,image/jpeg">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="formFile" class="form-label">Address Proof
                                                                    Front Side</label>
                                                                <input class="form-control" id="formFile" name="image2"
                                                                    type="file" accept="image/png,image/jpeg">
                                                            </div>
                                                        </div>
                                                        <div class="card-footer">
                                                            <input type="hidden" name="email"
                                                                value="{{ $user->email }}">
                                                            <input type="submit" href="javascript:void(0);"
                                                                class="btn btn-primary d-grid" value="Upload Document"
                                                                name="upload_kyc">
                                                        </div>
                                                    </div>
                                                </form>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="card custom-card" id="wallet-details-card" data-client-id="{{ request('id') }}">
                                                    <div class="card-header">
                                                        <div class="card-title">Wallet Details</div>
                                                    </div>
                                                    <div class="card-body" id="wallet-details-card-body">
                                                        <div class="table-responsive">
                                                            <table id="walletDetailsDataTable" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                                                <thead>
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>Wallet Name</th>
                                                                        <th>Currency</th>
                                                                        <th>Network</th>
                                                                        <th>Address</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody></tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="tab-pane" id="wallets" role="tabpanel" aria-labelledby="profile-tab-5">
                                    <div>
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h5>Wallet Details</h5>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <a href="user-profile#"
                                                            class="btn btn-primary d-inline-flex align-item-center"
                                                            data-bs-toggle="modal" data-bs-target="#addWalletModal">
                                                            <i class="ti ti-plus f-18"></i> Add Wallet Information
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="card-body table-card">
                                                @if (count($wallet_accounts) > 0)
                                                    <div class="card-body">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Wallet Name</th>
                                                                    <th>Currency</th>
                                                                    <th>Network</th>
                                                                    <th>Address</th>
                                                                    <th>Status / Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($wallet_accounts as $acc)
                                                                    <tr>
                                                                        <td>CWA{{ sprintf('%04u', $acc->client_wallet_id) }}
                                                                        </td>
                                                                        <td>{{ $acc->wallet_name }}</td>
                                                                        <td>{{ $acc->wallet_currency }}</td>
                                                                        <td>{{ $acc->wallet_network }}</td>
                                                                        <td>{{ $acc->wallet_address }}</td>
                                                                        <td
                                                                            class="text-start {{ $acc->status == 0 ? 'text-warning' : ($acc->status == 1 ? 'text-success' : ($acc->status == 2 ? 'text-danger' : '')) }}">
                                                                            @if ($acc->status == 0)
                                                                                <a class="wallet-action"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="Inactive Wallet Address"
                                                                                    data-status="Activate" data-toggle="{{ md5($acc->client_wallet_id) }}">
                                                                                    <i class="f-24 ti ti-toggle-left"></i>
                                                                                </a>
                                                                            @else
                                                                                <a class="wallet-action"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="Active Wallet Address"
                                                                                    data-status="Inactivate" data-toggle="{{ md5($acc->client_wallet_id) }}">
                                                                                    <i class="f-24 ti ti-toggle-right"></i>
                                                                                </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="auth-main">
                                                        <div class="card-body">
                                                            <div class="text-center me-4">
                                                                <a href="user-profile#"><img
                                                                        src="{{ asset('assets/images/nowallet.png') }}"
                                                                        class="w-25" alt="img"></a>
                                                            </div>
                                                            <h6 class="text-center text-secondary f-w-400 mb-0 f-16">Please
                                                                add
                                                                your Wallet Details</h6>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>
                                    <div class="tab-pane p-0" id="tab-profile">
                                        <div class="row">
                                            <div class="col-lg-5 col-xl-4 col-xl-12 col-sm-12">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="text-center">
                                                            <div class="userprofile">
                                                                <div class="avatar userpic avatar-rounded">
                                                                    <img src="/admin_assets/assets/images/users/client.jpeg"
                                                                        alt="img" style="width:100px">
                                                                </div>
                                                                <h3 class="username mb-2">{{ $user->fullname }}</h3>
                                                                <p class="mb-1 text-muted">{{ $user->email }}</p>
                                                                @if (!empty($user->password))
                                                                    <span>
                                                                        <span class="mb-1 text-muted"><input
                                                                                type="password"
                                                                                value="{{ $user->password }}"
                                                                                id="clientpassword"
                                                                                style="border:0px"></span>
                                                                        <span class="togglePassword"
                                                                            style="cursor:pointer"><i class="fa fa-eye"
                                                                                aria-hidden="true"></i></span>
                                                                    </span>
                                                                @endif
                                                                <div class="row justify-content-center mt-3">
                                                                    <div class="col-auto">
                                                                        <form method="post"
                                                                            action="{{ route('admin.sendPasswordResetLink') }}">
                                                                            @csrf
                                                                            <input type="hidden" name="txtemail"
                                                                                value="{{ $user->email }}">
                                                                            <button class="btn btn-primary" type="submit"
                                                                                name="btn-submit" value="reset">Send
                                                                                Reset
                                                                                Password Link</button>
                                                                        </form>
                                                                    </div>
                                                                    @if (in_array('clientProfile', $current_permissions))
                                                                        <div class="col-auto">
                                                                            <button class="client-profile btn btn-success"
                                                                                type="submit" name="btn-submit"
                                                                                value="reset">Client
                                                                                Profile</button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-7 col-xl-8 col-xl-12 col-sm-12">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane p-0" id="tab-tickets">
                                        <div class="row mb-5">
                                            @forelse ($tickets as $ticket)
                                                <div class="col-12 mt-3">
                                                    <div class="card custom-card shadow-none mb-0 ribbon-card">
                                                        <div class="card-body p-4">
                                                            <div
                                                                class="ribbon ribbon-{{ $ticket->ticket_label }} ribbon-top-left">
                                                                <span>{{ $ticket->ticket_status }}</span>
                                                            </div>
                                                            <div class="card-subtitle fw-semibold mb-2">
                                                                <div class="row">
                                                                    <div class="col-7">
                                                                        <div class="ms-5">
                                                                            <h3>#TICKET-{{ sprintf('%02d', $ticket->ticket_id + 10000) }}
                                                                            </h3>
                                                                            <div class="text-muted">
                                                                                <span><i
                                                                                        class="ri-calendar-2-line text-secondary"></i>
                                                                                    {{ $ticket->created_at }}</span>
                                                                                <span class="ms-3"><i
                                                                                        class="ri-user-line text-secondary"></i>
                                                                                    {{ $ticket->created_user }}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-5 text-end">
                                                                        <div class="d-flex w-100 justify-content-end">
                                                                            <div
                                                                                class="d-flex align-items-center text-start">
                                                                                <div class="ms-5 text-start">
                                                                                    <p class="text-muted mb-0">
                                                                                        {{ $ticket->fullname }}</p>
                                                                                    <p class="fw-medium fs-16 mb-0">
                                                                                        {{ $ticket->email_id }}</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="ms-2">
                                                                                <span
                                                                                    class="avatar avatar-lg avatar-rounded">
                                                                                    <img src="{{ asset('admin_assets/assets/images/users/client.png') }}"
                                                                                        alt="img">
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-4">
                                                                <div class="col-8">
                                                                    <div class="d-flex w-100 ms-5">
                                                                        <div
                                                                            class="d-flex align-items-center justify-content-between w-100 flex-wrap align-items-center">
                                                                            <div class="me-3">
                                                                                <p class="text-muted mb-0">
                                                                                    {{ $ticket->subject_name }}</p>
                                                                                <p class="fs-16 mb-0">
                                                                                    {{ $ticket->discription }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer justify-content-between d-flex">
                                                            <div>Ticket Type: <span
                                                                    class="fw-semibold badge bg-outline-primary">{{ $ticket->ticket_type }}</span>
                                                            </div>
                                                            <div>Last Follow-Up Date: <span
                                                                    class="fw-semibold badge bg-outline-info">{{ $ticket->last_followup }}</span>
                                                            </div>
                                                            <div>Last Follow-Up By:
                                                                <span class="fw-semibold badge bg-outline-success">
                                                                    {{ $ticket->followup_type == 'admin' ? $ticket->followup_admin ?? 'N/A' : $ticket->followup_user ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                            <a href="{{ route('admin.ticket_details', ['id' => md5($ticket->ticket_id)]) }}"
                                                                class="btn btn-info">
                                                                View <i
                                                                    class="ri-arrow-right-line ms-2 d-inline-block align-middle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="col-12 mt-3">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <h5 class="card-title">No Tickets Created</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforelse
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
    <div class="modal fade" id="depositModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-overlay d-none"
                    style="
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1000;
">
                </div>
                <form id="depositForm" method="post">
                    @csrf
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="type" value="wallet_deposit">

                    <div class="modal-header">
                        <h5 class="modal-title">Deposit To Wallet Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                        <div class="card-body">
                            <div class="trade-deposit-details">

                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD:</label>
                                            <div class="col-lg-8">
                                                <input name="amount" id="amount_deposit" type="number"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-3">
                                            <label class="col-lg-4 col-form-label">ADMIN REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="description" name="description" rows="3" class="form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" id="depositBtn" class="btn btn-primary">
                                                    Wallet Deposit
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>OTP has been sent to your email. Please enter the 6-digit OTP below:</p>
                    <input type="text" id="otpInput" class="form-control text-center mb-2" maxlength="6"
                        placeholder="Enter OTP">
                    <p id="otpTimer" class="text-danger text-center">OTP expires in 60s</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" id="resendOtpBtn" class="btn btn-primary aligin-center">Resend
                            OTP</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmOtpBtn" class="btn btn-primary">Verify & Deposit</button>
                </div>
            </div>
        </div>
    </div>
 <div class="modal fade" id="walletModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="walletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-overlay d-none"
                    style="
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1000;
">
                </div>
                <form id="walletForm" method="post">
                    @csrf
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="type" value="wallet_Withdraw">

                    <div class="modal-header">
                        <h5 class="modal-title">Withdraw To wallet Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                        <div class="card-body">
                            <div class="trade-deposit-details">

                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD:</label>
                                            <div class="col-lg-8">
                                                <input name="amountwithdraw" id="amount_withdraw" type="number"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-3">
                                            <label class="col-lg-4 col-form-label">ADMIN REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="descriptionwithdraw" name="descriptionwithdraw" rows="3" class="form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" id="walletBtn" class="btn btn-primary">
                                                    Wallet Withdraw
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

   
    <div class="modal fade" id="otpModal2" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>OTP has been sent to your email. Please enter the 6-digit OTP below:</p>
                    <input type="text" id="otpInput2" class="form-control text-center mb-2" maxlength="6"
                        placeholder="Enter OTP">
                    <p id="otpTimer" class="text-danger text-center">OTP expires in 60s</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" id="resendOtpBtn2" class="btn btn-primary aligin-center">Resend
                            OTP</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmOtpBtn2" class="btn btn-primary">Verify & Withdraw</button>
                </div>
            </div>
        </div>
    </div>
    {{-- 
    <div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="depositModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="depositModalLabel">Deposit To Wallet Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <form id="depositForm">
            <input type="hidden" name="email" value="{{ $user->email }}">
          @csrf
          

          <div class="mb-3">
            <label for="amount_deposit" class="form-label">Amount (USD)</label>
            <input type="text" id="amount_deposit" name="amount" class="form-control" required>
          </div>

          <div class="form-group row mb-3"><label class="col-lg-4 col-form-label">ADMIN
                                                REMARK:</label>
                                            <div class="col-lg-8"><input id="description" name="description"
                                                    rows="3" class="mt-2 form-control"
                                                    placeholder="Add a remark" required>
                                            </div>
                                        </div>

          <button type="button" id="depositBtn" class="btn btn-primary">
            Deposit
          </button>
        </form>
      </div>

    </div>
  </div>
</div> --}}


    {{-- <div class="modal fade" id="walletModal" tabindex="-1" aria-labelledby="walletModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="walletModalLabel">Withdraw To Wallet Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="walletForm">
                        @csrf


                        <div class="mb-3">
                            <label for="amount_deposit" class="form-label">Amount (USD)</label>
                            <input type="number" id="amount_deposit" name="amount" class="form-control" required>
                        </div>

                        <div class="form-group row mb-3"><label class="col-lg-4 col-form-label">ADMIN
                                REMARK:</label>
                            <div class="col-lg-8"><input id="description" name="description" rows="3"
                                    class="mt-2 form-control" placeholder="Add a remark" required>
                            </div>
                        </div>

                        <button type="button" id="walletBtn" class="btn btn-primary">
                            Withdraw
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div> --}}
	<script>
$(document).ready(function () {

    $('#ledgerTable').DataTable({
        paging: true,        // Laravel pagination already
        info: true,
        ordering: true,
        searching: true,
        responsive: true,
        pageLength: 10,

        columnDefs: [
            { orderable: false, targets: [7] } // action column
        ],

        language: {
            search: "Search:",
            zeroRecords: "No transactions found",
            emptyTable: "No data available"
        }
    });

});
</script>

    <script>
      $('#otpModal2').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).ready(function () {

    const walletModal = $('#walletModal');
    const otpModal = $('#otpModal2');
    let otpTimerInterval;

    function startOtpTimer(seconds = 60) {
        clearInterval(otpTimerInterval);
        let remaining = seconds;

        $('#otpTimer').text(`OTP expires in ${remaining}s`);
        $('#resendOtpBtn2').addClass('d-none');
        $('#confirmOtpBtn2').prop('disabled', false);

        otpTimerInterval = setInterval(() => {
            remaining--;
            $('#otpTimer').text(`OTP expires in ${remaining}s`);

            if (remaining <= 0) {
                clearInterval(otpTimerInterval);
                $('#otpTimer').text('OTP expired');
                $('#confirmOtpBtn2').prop('disabled', true);
                $('#resendOtpBtn2')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .text('Resend OTP');
            }
        }, 1000);
    }

    // STEP 1 — SEND OTP
    $('#walletBtn').on('click', function (e) {
        e.preventDefault();

        let amount = $('#amount_withdraw').val().trim();
        let description = $('#descriptionwithdraw').val().trim();

        if (!amount) {
            Swal.fire('Error', 'Please enter Amount in USD', 'error');
            return;
        }

        if (!description) {
            Swal.fire('Error', 'Please enter Admin Remark', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        walletModal.modal('hide');

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "wallet_withdraw", // corrected
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    otpModal.modal('show');
                    $('#otpInput2').val('').focus();
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                    btn.prop('disabled', false).text('Wallet Withdraw');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to send OTP', 'error');
                btn.prop('disabled', false).text('Wallet Withdraw');
            }
        });
    });

    // STEP 2 — VERIFY OTP
    $('#confirmOtpBtn2').on('click', function () {

        let otp = $('#otpInput2').val().trim();

        if (otp.length !== 6) {
            Swal.fire('Error', 'Enter valid 6-digit OTP', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...'
        );

        let formData = $('#walletForm').serialize() + '&otp=' + otp;

        $.ajax({
            url: "{{ route('admin.updatewalletwithdrawal')}}", // set correct route
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                 clearInterval(otpTimerInterval);
                        otpModal.modal('hide');
                        Swal.fire('Success', res.message, 'success').then(() => location
                        .reload());
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Invalid OTP',
                    'error'
                );

                btn.prop('disabled', false).text('Verify & Withdraw');
            }
        });
    });

    // STEP 3 — RESEND OTP
    $('#resendOtpBtn2').on('click', function () {

        let btn = $(this);

        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        $('#otpInput2').val('').focus();
        $('#confirmOtpBtn2').prop('disabled', false);

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "wallet_withdraw",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire('Success', 'New OTP sent to your email', 'success');
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to resend OTP', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Resend OTP');
            }
        });
    });

});

        $('#otpModal').on('hidden.bs.modal', function() {
            location.reload();
        });

        $(document).ready(function() {

            const depositModal = $('#depositModal');
            const otpModal = $('#otpModal');
            let otpTimerInterval;
            let resendTimerInterval;

            function startOtpTimer(seconds = 60) {
                clearInterval(otpTimerInterval);
                let remaining = seconds;
                $('#otpTimer').text(`OTP expires in ${remaining}s`);
                $('#resendOtpBtn').addClass('d-none'); // hide resend initially

                otpTimerInterval = setInterval(() => {
                    remaining--;
                    $('#otpTimer').text(`OTP expires in ${remaining}s`);
                    if (remaining <= 0) {
                        clearInterval(otpTimerInterval);
                        $('#otpTimer').text('OTP expired');
                        $('#confirmOtpBtn').prop('disabled', true);
                        // Show resend button immediately
                        $('#resendOtpBtn').removeClass('d-none').prop('disabled', false).text('Resend OTP');
                    }
                }, 1000);
            }
            // Step 1: Send OTP
            $('#depositBtn').on('click', function(e) {
                e.preventDefault();

                let amount = $('#amount_deposit').val().trim();
                let description = $('#description').val().trim();

                if (!amount) {
                    Swal.fire('Error', 'Please enter Amount in USD', 'error');
                    return;
                }
                if (!description) {
                    Swal.fire('Error', 'Please enter Admin Remark', 'error');
                    return;
                }

                let btn = $(this);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...');

                depositModal.modal('hide');

                $.ajax({
                    url: "{{ route('admin.admingetOtp') }}",
                    type: "POST",
                    data: {
                        action: "getPOotp",
                        type: 'wallet_deposit',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            otpModal.modal('show');
                            $('#otpInput').val('').focus();
                            $('#confirmOtpBtn').prop('disabled', false);
                            startOtpTimer(60);
                        } else {
                            Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                            btn.prop('disabled', false).text('Wallet Deposit');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to send OTP', 'error');
                        btn.prop('disabled', false).text('Wallet Deposit');
                    }
                });
            });

            // Step 2: Verify OTP
            $('#confirmOtpBtn').on('click', function() {
                let otp = $('#otpInput').val().trim();
                if (otp.length !== 6) {
                    Swal.fire('Error', 'Enter valid 6-digit OTP', 'error');
                    return;
                }

                let btn = $(this);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...');

                let formData = $('#depositForm').serialize() + '&otp=' + otp;

                $.ajax({
                    url: "{{ route('admin.adminwalletupdate') }}",
                    type: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        clearInterval(otpTimerInterval);
                        otpModal.modal('hide');
                        Swal.fire('Success', res.message, 'success').then(() => location
                        .reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Invalid OTP', 'error');
                        btn.prop('disabled', false).text('Verify & Deposit');
                    }
                });
            });

            // Step 3: Resend OTP with spinner
            $('#resendOtpBtn').on('click', function() {
                let btn = $(this);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...');

                $('#otpInput').val('').focus();
                $('#confirmOtpBtn').prop('disabled', false);

                $.ajax({
                    url: "{{ route('admin.admingetOtp') }}",
                    type: "POST",
                    data: {
                        action: "getPOotp",
                        type: 'wallet_deposit',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire('Success', 'New OTP sent to your email', 'success');
                            startOtpTimer(60);
                        } else {
                            Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to resend OTP', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).text('Resend OTP');
                    }
                });
            });

        });



        $(document).ready(function() {
            $(".edit-countrycode").select2({
                placeholder: "Country Code",
                selectionCssClass: "country-code-select",
                dropdownParent: $('#editUserModal')
            });
            $(document).on('click', '.ibToggle', function() {
                var data = $(this).data();
                $("#clientName,#clientEmail").html("");
                $("#clientName").html(data.fullname)
                $("#clientEmail").html(data.email)
                $("#client_id").val(data.enc)
                $("[name='ib_status']").val(data.ib_status).trigger("change");
                $("[name='ib_group']").val(data.ib_group).trigger("change");
                // myModal.show();
            });
            $('#tableDeposit').DataTable({
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: {
                        action: 'getLatestDeposit',
                        id: '{{ $user->email }}'
                    },
                },
                columns: [{
                        data: 'created_on',
                        name: 'date'
                    },
                    {
                        data: 'from_to',
                        name: 'from_to'
                    },
                    {
                        data: 'payment_method',
                        name: 'method'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
            $('#tableWithdrawal').DataTable({
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: {
                        action: 'getLatestWithdrawal',
                        id: '{{ $user->email }}'
                    },
                },
                columns: [{
                        data: 'created_on',
                        name: 'date'
                    },
                    {
                        data: 'from_to',
                        name: 'from_to'
                    },
                    {
                        data: 'payment_method',
                        name: 'method'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
            $('#tableInternalTransfer').DataTable({
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: {
                        action: 'getLatestTransfer',
                        id: '{{ $user->email }}'
                    },
                },
                columns: [{
                        data: 'created_on',
                        name: 'date'
                    },
                    {
                        data: 'from',
                        name: 'from'
                    },
                    {
                        data: 'to',
                        name: 'to'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
        });
        // $("#ibRequestForm").submit(function(e) {
        //     e.preventDefault();
        //     $.ajax({
        //         url: "/admin/api/ajax",
        //         type: "POST",
        //         data: $("#ibRequestForm").serialize(),
        //         success: function(data) {
        //             if (data == "true") {
        //                 swal.fire({
        //                     icon: "success",
        //                     title: "IB Request Successfully Updated",
        //                 }).then((val) => {
        //                     location.reload();
        //                 });
        //             } else {
        //                 swal.fire({
        //                     icon: "error",
        //                     title: "Something went wrong.",
        //                     text: "Please try again or contact support."
        //                 }).then((val) => {
        //                     location.reload();
        //                 });
        //             }
        //         }
        //     });
        // });
        $("#createMT5Form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/api/accounts",
                type: "POST",
                data: $("#createMT5Form").serialize(),
                success: function(data) {
                    if (data.trim() == "true") {
                        swal.fire({
                            icon: "success",
                            title: "MT5 Account Successfully Created",
                        }).then((val) => {
                            location.reload();
                        });
                    } else {
                        swal.fire({
                            icon: "error",
                            title: "Something went wrong.",
                            text: "Please try again or contact support."
                        }).then((val) => {
                            location.reload();
                        });
                    }
                }
            });
        });

        $(".acc-types").change(function() {
            var selectedValue = $(this).val();
            $("#leverage").html("<option value='' checked>Loading...</option>");
            $.ajax({
                url: "/admin/api/ajax?type=leverage&id=" + selectedValue,
                success: function(data) {
                    $("#leverage").html("");
                    $.each(data, function(key, value) {
                        $("#leverage").append("<option value='" + value.account_leverage +
                            "'>" +
                            value.account_leverage + "</option>");
                    });
                }
            })
        });
        $(".acc-types").trigger("change");

        $('#kycModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var fileSrc = button.data('bs-kyc');
            var fileType = button.data('bs-type');
            var modal = $(this);
            modal.find('#kycFile').attr('src', fileSrc);
            modal.find('#kycFile').attr('type', fileType);
        });

        function takeAction(id, email, status) {
            Swal.fire({
                title: `Are you sure you want to ${status === 1 ? "approve" : "reject"} this Document?`,
                html: `
            <form id="updateKYCForm" method="post" action="{{ route('admin.updateKyc') }}">
                @csrf
             <input type="hidden" name="id" value="${id}">
              <input type="hidden" name="email" value="${email}">
              <input type="hidden" name="status" value="${status}">
              <input type="hidden" name="action" value="update_kyc">
              <div class="col-12 mt-2 text-start">
                  <textarea id="description" name="description" rows="3" class="mt-2 form-control" placeholder="Add a description"></textarea>
              </div>
              </form>
          `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: () => {
                    const description = document.querySelector('#updateKYCForm textarea').value;
                    if (!description) {
                        Swal.showValidationMessage('Please add a comment');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#updateKYCForm').submit();
                }
            });
        }
        $('#ibList .nav-link').on('click', function(e) {
            e.preventDefault();
            let tier = $(this).data('tier');
            $.ajax({
                url: '/admin/ajax',
                type: 'GET',
                data: {
                    action: 'getIbTierData',
                    id: '{{ $user->email }}',
                    tier: tier
                },
                success: function(response) {
                    $('#content').html(response);
                },
                error: function(xhr) {
                    $('#content').html('<p>Sorry, something went wrong.</p>');
                }
            });
        });
        $(document).ready(function() {
            $('.togglePassword').on('click', function() {
                var passwordField = $('#clientpassword');
                var passwordFieldType = passwordField.attr('type');
                if (passwordFieldType === 'password') {
                    passwordField.attr('type', 'text');
                    $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordField.attr('type', 'password');
                    $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
        });
        $(".acc-types").change(function() {
            var selectedValue = $(this).val();
            $(".leverage").html("<option value='' checked>Loading...</option>");
            $.ajax({
                url: "/admin/api/ajax?type=leverage&id=" + selectedValue,
                success: function(data) {
                    $(".leverage").html("");
                    $.each(data, function(key, value) {
                        $(".leverage").append("<option value='" + value.account_leverage +
                            "'>" +
                            value.account_leverage + "</option>");
                    });
                }
            })
        });
        $(".acc-types").trigger("change");
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script>
        var clipboard = new ClipboardJS('#ibClient');
        clipboard.on('success', function(e) {
            Swal.fire({
                icon: "success",
                title: "IB Link Copied"
            })
        });
        $(document).ready(function() {
            $('[role="tab"]').click(function() {
                if ($(this).attr("href")) {
                    location.hash = $(this).attr("href");
                } else if ($(this).data("bs-target")) {
                    location.hash = $(this).data("bs-target");
                }
            });
            if (location.hash) {
                var tab = location.hash;
                if ($('a[href="' + tab + '"]').length) {
                    var triggerEl = document.querySelector('a[href="' + tab + '"]')
                    bootstrap.Tab.getInstance(triggerEl).show() // Select tab by name
                }
            }
        })
        $(document).on('click', '.editClient', function() {
            var data = $(this).data();
            $.ajax({
                url: "/admin/ajax",
                type: "GET",
                cache: false,
                data: {
                    "action": "getClientDetails",
                    "id": data.enc
                },
                success: function(response) {
                    let resp = JSON.parse(response);
                    $.each(resp, function(key, value) {
                        $('#editUserForm [name="' + key + '"]').val(value);
                    });
                    $('#editUserForm [name="country_code"]').trigger('change');
                }
            });
            $('#editUserModal').modal('show');
        });
        $(document).on('click', '.client-profile', function() {
            var copyright_site_name_text = @json(settings()['copyright_site_name_text'] ?? '');
            var clientProfileWindow = window.open(copyright_site_name_text +
                "/client-profile/{{ md5($user->email) }}",
                "clientProfileWindow", `width=${screen.width},height=${screen.height}`);
            clientProfileWindow.onload = function() {
                const content = clientProfileWindow.document.body.innerHTML.trim();
                if (!content) {
                    clientProfileWindow.close();
                    swal.fire({
                        icon: "error",
                        title: "Client details are invalid or email is not verified.",
                    }).then((val) => {
                        location.reload();
                    });
                }
            };
        });
        $("#mapMT5Form").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/mapMt5Account",
                type: "POST",
                data: $("#mapMT5Form").serialize(),
                success: function(data) {
                    swal.fire({
                        icon: data.status,
                        title: data.message,
                    }).then((val) => {
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        }
                    });
                }
            });
        });
        $('#mapAccountModal').on('shown.bs.modal', function() {
            $(this).find("form")[0].reset();
            $("#map_acc_type").css("pointer-events", "all");
            $("#map_leverage").css("pointer-events", "all");
        });
        $(document).on("click", ".fetch-account-details ", function() {
            var trade_id = $('.map-trade-id').val();
            $("#map_acc_type").val('').change();
            $("#map_leverage").val('').change();
            $("#map_acc_type").css("pointer-events", "all");
            $("#map_leverage").css("pointer-events", "all");
            if (trade_id != '') {
                $.ajax({
                    url: "/admin/userAccountGet",
                    type: "POST",
                    data: {
                        trade_id: trade_id
                    },
                    success: function(resp) {
                        if (resp.data) {
                            var response = resp.data;
                            $("#map_acc_type option").filter(function() {
                                return $(this).attr("data-group") === response.Group;
                            }).prop("selected", true).change();
                            if (response.Group != '') {
                                $("#map_acc_type").css("pointer-events", "none");
                            }
                            setTimeout(function() {
                                $("#map_leverage").val(response.Leverage).change();
                            }, 2000);
                            if (response.Leverage != '') {
                                $("#map_leverage").css("pointer-events", "none");
                            }
                        }
                    }
                });
            }
        });
        // Wallet Details DataTable (same component pattern as client_list)
        (function() {
            var tableEl = document.getElementById('walletDetailsDataTable');
            if (!tableEl) return;
            var card = document.getElementById('wallet-details-card');
            var clientId = card && card.getAttribute('data-client-id') ? card.getAttribute('data-client-id') : (new URLSearchParams(window.location.search).get('id') || '');
            if ($.fn.DataTable.isDataTable('#walletDetailsDataTable')) return;
            $('#walletDetailsDataTable').DataTable({
                dom: '<"row" <"col"B><"col text-center"l><"col"f>><"row"<"col"t>><"row"<"col"i><"col"p>>',
                buttons: ['excel'],
                order: [[0, 'asc']],
                ajax: {
                    url: '/admin/ajax',
                    type: 'GET',
                    data: function(d) {
                        d.action = 'getClientWalletList';
                        d.id = clientId;
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'wallet_name', name: 'wallet_name' },
                    { data: 'wallet_currency', name: 'wallet_currency' },
                    { data: 'wallet_network', name: 'wallet_network' },
                    { data: 'wallet_address', name: 'wallet_address' }
                ]
            });
        })();
    </script>
@endsection
