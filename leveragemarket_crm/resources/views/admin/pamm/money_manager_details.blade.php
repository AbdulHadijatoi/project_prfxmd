@extends('layouts.admin.admin')
@section('content')
    <div class="modal fade" id="reqModal" tabindex="-1" aria-labelledby="reqModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="reqUpdateForm" class="ajaxForm">
                    <input type="hidden" name="action" value="requestactions_investments" />
                    <input type="hidden" name="requestIds" id="requestIds" value="0" />
                    <input type="hidden" name="managerId" id="managerId" value="{{ request()->get('id') }}" />
                    <div class="modal-header">
                        <h5 class="modal-title" id="mmUpdateModalLabel">Pending Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0 p-4">
                        <div class="row mb-3">
                            <div class="text-muted"><span id="requestName"></span> # <span id="requestId"></span></div>
                            <div class="fw-bold fs-14 text-uppercase"><span id="requestType"></span> : <span
                                    id="requestAmount"></span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Status</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-control" name="request_action" id="requestAction">
                                    <option value="Confirm">Confirm</option>
                                    <option value="Cancel">Cancel</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Comment</label>
                            </div>
                            <div class="col-lg-8">
                                <textarea class="form-control" name="comment" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" name="" value="Update" class="btn btn-primary">
                        <input type="button" class="btn btn-dark" data-bs-dismiss="modal" value="Close">
                    </div>
            </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateOfferModal" tabindex="-1" aria-labelledby="updateOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="updateOfferForm" class="ajaxForm" action="update_manager_offer">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-12">
                                <div><span id="mgr_offer_name" class="h2">New Offer</span><span
                                        class="text-muted fs-20 ms-1"><span id="mgr_offer_id"></span></span>
                                </div>
                            </div>
                            <input type="hidden" name="action" value="update_manager_offer" />
                            <input type="hidden" name="id" id="id" value="0" />
                            <input type="hidden" name="managerId" id="managerId" value="{{ request()->get('id') }}" />
                            <div class="row mt-2">
                                <div class="col-lg-12 m-auto">
                                    <label class="form-label">Name</label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="text" class="form-control" placeholder="Name" name="name"
                                        id="name" required>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-12 m-auto">
                                    <label class="form-label"></label>
                                </div>
                                <div class="col-lg-12">
                                    <input class="form-check-input" type="checkbox" value="" id="isActive"
                                        name="isActive">
                                    <label class="form-check-label" for="isActive">
                                        Public
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-lg-12 m-auto">
                                    <label class="form-label">Description</label>
                                </div>
                                <div class="col-lg-12">
                                    <input type="text" id="description" class="form-control"
                                        placeholder="Description" name="description">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="accordion" id="accordionOffer">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_offer_config"
                                                aria-expanded="true" aria-controls="collapse_offer_config">
                                                Configuration
                                            </button>
                                        </h2>
                                        <div id="collapse_offer_config" class="accordion-collapse collapse"
                                            aria-labelledby="headingOne" data-bs-parent="#accordionOffer">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label for="username" class="form-label">Trading Interval</label>
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <input type="text" class="form-control"
                                                                    name="tradingInterval_count"
                                                                    id="tradingInterval_count" value="1" required>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <select class="form-control" name="tradingInterval_type"
                                                                    id="tradingInterval_type" required>
                                                                    <option value="Days">Days</option>
                                                                    <option value="Weeks">Weeks</option>
                                                                    <option value="Months">Months</option>
                                                                    <option value="CalendarMonths" selected>Calendar
                                                                        Month(s) </option>
                                                                    <option value="OnRollover"> On rollover </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="row">
                                                            <div class="col-lg-4">
                                                                <label for="minDeposit" class="form-label">Min.
                                                                    Deposit</label>
                                                                <input type="text" class="form-control"
                                                                    name="minDeposit" id="minDeposit" value="0"
                                                                    required>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label for="minWithdrawal" class="form-label">Min.
                                                                    Withdrawal</label>
                                                                <input type="text" class="form-control"
                                                                    name="minWithdrawal" id="minWithdrawal"
                                                                    value="0" required>
                                                            </div>
                                                            <div class="col-lg-4">
                                                                <label for="minInitialInvestment" class="form-label">Min.
                                                                    Initial Investment</label>
                                                                <input type="text" class="form-control"
                                                                    name="minInitialInvestment" id="minInitialInvestment"
                                                                    value="0" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_offer_performance"
                                                aria-expanded="false" aria-controls="collapse_offer_performance">
                                                Performance Fees
                                            </button>
                                        </h2>
                                        <div id="collapse_offer_performance" class="accordion-collapse collapse"
                                            aria-labelledby="headingTwo" data-bs-parent="#accordionOffer">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label for="username" class="form-label">Performance Fee
                                                            Mode</label>
                                                        <select class="form-control" name="performanceFees_mode"
                                                            id="performanceFees_mode" disabled>
                                                            <option value="Equity">Equity</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-12 mt-2">
                                                        <div class="row mt-2 dynamic-table-container">
                                                            <div class="row mt-2">
                                                                <div class="col-lg-5">
                                                                    <label for="username" class="form-label">Equity
                                                                        From</label>
                                                                </div>
                                                                <div class="col-lg-5">
                                                                    <label for="username" class="form-label">Performance
                                                                        Fee</label>
                                                                </div>
                                                                <div class="col-lg-2 d-flex justify-content-end">
                                                                    <button type="button"
                                                                        class="btn btn-primary dynamic-table-add">
                                                                        <i class="fa fa-plus"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="dynamic-table-content mt-2"
                                                                id="offerPerformanceFeeContainer">
                                                                <div class="row dynamic-table-clone mb-2">
                                                                    <div class="col-lg-5">
                                                                        <input type="text" class="form-control"
                                                                            name="performanceFees_level[]" placeholder="">
                                                                    </div>
                                                                    <div class="col-lg-5">
                                                                        <input type="text" class="form-control"
                                                                            name="performanceFees_value[]" placeholder="">
                                                                    </div>
                                                                    <div class="col-lg-2">
                                                                        <button type="button"
                                                                            class=" btn btn-light border-0 bg-transparent dynamic-table-delete text-danger"
                                                                            disabled>
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_offer_agentchain"
                                                aria-expanded="false" aria-controls="collapse_offer_agentchain">
                                                Agent Chain
                                            </button>
                                        </h2>
                                        <div id="collapse_offer_agentchain" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree" data-bs-parent="#accordionOffer">
                                            <div class="accordion-body dynamic-table-container">
                                                <div class="row mt-2">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="offerExtendedMode" name="offerExtendedMode">
                                                        <label class="form-check-label" for="">Extended
                                                            Mode</label>
                                                    </div>
                                                </div>
                                                <div class="row mt-2 agentChainInput">
                                                    <div class="col-lg-12 m-auto">
                                                        <label class="form-label">Login</label>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <input type="number" class="form-control" placeholder=""
                                                            name="agentChain" id="agentChain">
                                                    </div>
                                                </div>
                                                <div class="row mt-2 agentChainList">
                                                    <div class="row mt-2">
                                                        <div class="col-lg-5">
                                                            <label for="username" class="form-label">Login</label>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <label for="username" class="form-label">Server</label>
                                                        </div>
                                                        <div class="col-lg-2 d-flex justify-content-end">
                                                            <button type="button"
                                                                class="btn btn-primary dynamic-table-add">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="dynamic-table-content mt-2" id="agentChainContent">
                                                        <div class="row dynamic-table-clone mb-2">
                                                            <div class="col-lg-5">
                                                                <input type="text" class="form-control"
                                                                    name="agentChainLogin[]" placeholder="">
                                                            </div>
                                                            <div class="col-lg-5">
                                                                <select class="form-control" name="agentChainServer[]"
                                                                    id="">
                                                                    <option value="1" selected> MT5 (#1)</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <button type="button"
                                                                    class=" btn btn-light border-0 bg-transparent dynamic-table-delete text-danger"
                                                                    disabled>
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse_offer_joinlinks"
                                                aria-expanded="false" aria-controls="collapse_offer_joinlinks">
                                                Join Links
                                            </button>
                                        </h2>
                                        <div id="collapse_offer_joinlinks" class="accordion-collapse collapse"
                                            aria-labelledby="headingThree" data-bs-parent="#accordionOffer">
                                            <div class="accordion-body dynamic-table-container">
                                                <div class="row mt-2">
                                                    <div class="col-lg-3">
                                                        <label for="username" class="form-label">Link</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="username" class="form-label">Expiration</label>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label for="username" class="form-label">Agents</label>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <label for="username" class="form-label">One Time</label>
                                                    </div>
                                                    <div class="col-lg-1 d-flex justify-content-end">
                                                        <button type="button" class="btn btn-primary dynamic-table-add">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="dynamic-table-content mt-2" id="joinLinkContent">
                                                    <div class="row dynamic-table-clone mb-2">
                                                        <div class="col-lg-3">
                                                            <input type="text" class="form-control"
                                                                name="joinLinkKey[]" placeholder="">
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="text"
                                                                class="form-control join-link-expiration"
                                                                name="joinLinkExpiration[]" placeholder="">
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <input type="text" class="form-control"
                                                                name="joinLinkAgent[]" placeholder="">
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox"
                                                                    role="switch" name="joinLinkOneTime[]">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1">
                                                            <button type="button"
                                                                class=" btn btn-light border-0 bg-transparent dynamic-table-delete text-danger"
                                                                disabled>
                                                                <i class="fa fa-trash"></i>
                                                            </button>
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
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="add_user" value="Update">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addUserModalLabel1">Add Investment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="createInvestorForm">
                    <div class="modal-body">
                        <div class="row gy-4">
                            <input type="hidden" name="manager_id" value="{{ request()->get('id') }}" />
                            <div class="col-12">
                                <label for="input-label" class="form-label">Offer</label>
                                <select id="offer_id" name="offer_id" class="form-control offer-select" required>
                                    <option value="">Select Account</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="col-12">
                                    <label for="input-label" class="form-label">Userame</label>
                                    <select class="form-control live-acc-select" name="owner_id" required>
                                        <option value="">Select Account</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="input-label" class="form-label">Initial Investment</label>
                                    <input type="number" min="0" class="form-control" name="amount"
                                        autocomplete="off" id="amount" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="add_user" value="Create">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mmUpdateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="mmUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form id="managerUpdateForm">
                    <input type="hidden" name="id" value="{{ request()->get('id') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mmUpdateModalLabel">Edit Manager</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0 p-4">
                        <div class="row mb-3">
                            <div class="text-muted">Manager # <?= base64_decode($_GET['id']) ?></div>
                            <div class="fw-bold fs-16"><?= $manager_details['name'] ?? '' ?></div>
                        </div>
                        <div class="row pb-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Description</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" placeholder="Description" name="description"
                                    value="<?= $manager_details['description'] ?>">
                            </div>
                        </div>

                        <div class="row mt-2 border-top pt-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label"></label>
                            </div>
                            <div class="col-lg-8">
                                <input class="form-check-input" type="checkbox" value="" id="isPublic"
                                    name="visibility" <?= $manager_details['isPublic'] == true ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isPublic">
                                    Public
                                </label>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Name</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" placeholder="Name (Custom)" name="name"
                                    value="<?= $manager_details['name'] ?>">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Configuration</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-select" required name="configuration">
                                    <option value="" selected>Select Configuration</option>
                                    <?php foreach ($managerConfig as $conf) { ?>
                                    <option value="<?= $conf['id'] ?>"
                                        <?= $conf['id'] == $manager_details['configurationId'] ? 'selected' : '' ?>>
                                        <?= $conf['name'] ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="updateManagerConfiguration" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Money Manager Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Money Manager Details</li>
                </ol>
            </div>
            <div class="row money-manager-details">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="wideget-user mb-5">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="wideget-user-desc d-flex">
                                                <div class="wideget-user-img" style="max-width:150px">
                                                    <img class="w-100" id="pamm_avatar"
                                                        src="{{ asset('admin_assets/assets/images/users/client.jpeg') }}"
                                                        alt="img">
                                                </div>
                                                <div class="user-wrap ms-2">
                                                    <h6 class="text-muted fw-normal fs-11">
                                                        PAMM ACCOUNT #{{ base64_decode($_GET['id']) }}
                                                        <span id="pamm_visibility">
                                                            @if ($manager_details['isPublic'] == true)
                                                                <span class="text-success"><i class="fa fa-users p-1"
                                                                        aria-hidden="true"></i>Public</span>
                                                            @else
                                                                <span class="text-muted"><i class="fa fa-lock p-1"
                                                                        aria-hidden="true"></i>Private</span>
                                                            @endif
                                                        </span>
                                                    </h6>
                                                    <h4 class="fw-normal mb-3" id="pamm_name">
                                                        {{ $manager_details['name'] ?? '' }}</h4>
                                                    <a href="javascript:void(0);" class="btn btn-secondary mt-1 mb-1"
                                                        data-bs-toggle="modal" data-bs-target="#addUserModal"><i
                                                            class="fa fa-user-plus"></i>
                                                        Add Investment</a>
                                                    <span class="dropdown mm-actions">
                                                        <button class="btn btn-dark dropdown-toggle" type="button"
                                                            id="dropdownBtn" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownBtn">
                                                            <li data-bs-toggle="modal" data-bs-target="#mmUpdateModal"><a
                                                                    class="dropdown-item" href="javascript:void(0);"><i
                                                                        class="fa fa-pencil-square-o"
                                                                        aria-hidden="true"></i>
                                                                    Edit</a></li>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="nav nav-tabs mb-3 nav-style-1 d-sm-flex d-block" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" role="tab" href="#tab-info"
                                            aria-selected="false">
                                            <i class="fa fa-user me-1" aria-hidden="true"></i>Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" role="tab" href="#tab-investments"
                                            aria-selected="false"><i class="fa fa-users me-1"
                                                aria-hidden="true"></i>Investments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id="mm_offers" class="nav-link" data-bs-toggle="tab" role="tab"
                                            href="#tab-offers" aria-selected="true">
                                            <i class="fa fa-dollar me-1" aria-hidden="true"></i>Offers</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id="mm_history" class="nav-link" data-bs-toggle="tab" role="tab"
                                            href="#tab-history" aria-selected="false"><i
                                                class="fa fa-solid fa-history me-1"
                                                aria-hidden="true"></i>Transactions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a id="mm_history" class="nav-link" data-bs-toggle="tab" role="tab"
                                            href="#tab-requests" aria-selected="false"><i class="fa fa-credit-card me-1"
                                                aria-hidden="true"></i>Requests</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane text-muted active show" id="tab-info" role="tabpanel">
                                        <div class="row mm-info">
                                            <div class="row mm-info">
                                                <div class="col-xl-6">
                                                    <div class="card custom-card">
                                                        <div class="card-header justify-content-between">
                                                            <div class="card-title">
                                                                Summary
                                                                <?php if ($manager_details['updateDt']) { ?>
                                                                <div class="text-muted fs-11">Last Update
                                                                    <span>{{ date('M d,Y, h:i:s A', strtotime($manager_details['updateDt'])) }}</span>
                                                                </div>
                                                                <?php } ?>
                                                            </div>

                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    {{ $manager_details['name'] }}
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Trading Account
                                                                    <span class=""><span class="text-muted">MT5
                                                                        </span>#{{ $manager_details['accountId'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Created Date
                                                                    <span
                                                                        class="">{{ date('M d,Y', strtotime($manager_details['createdDt'])) }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Configuration
                                                                    <span
                                                                        class="">{{ $manager_details['configurationName'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="mt-3 list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Open Trade Profit
                                                                    <span
                                                                        class="{{ $manager_details['summary']['profitOpen'] > 0 ? 'text-success' : ($manager_details['summary']['profitOpen'] < 0 ? 'text-danger' : '') }}">${{ $manager_details['summary']['profitOpen'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Closed Trade Profit
                                                                    <span
                                                                        class="{{ $manager_details['summary']['profitClosed'] > 0 ? 'text-success' : ($manager_details['summary']['profitClosed'] < 0 ? 'text-danger' : '') }}">${{ $manager_details['summary']['profitClosed'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Trade Results
                                                                    <span
                                                                        class="{{ $manager_details['profitTotal'] > 0 ? 'text-success' : ($manager_details['profitTotal'] < 0 ? 'text-danger' : '') }}">${{ $manager_details['profitTotal'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="mt-3 list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Investor Funds
                                                                    <span
                                                                        class="">{{ $manager_details['fundsInvestor'] }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    <span class="fs-16 fw-bold">Total Funds</span>
                                                                    <span
                                                                        class="fs-20"><b>${{ $manager_details['funds'] }}</b></span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card custom-card">
                                                        <div class="card-header justify-content-between">
                                                            <div class="card-title">
                                                                Deposit & Withdrawals
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Investor deposits
                                                                    <span
                                                                        class="">${{ $manager_details['summary']['investorDeposits'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Investor withdrawals
                                                                    <span
                                                                        class="">${{ $manager_details['summary']['investorWithdrawals'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Own deposits
                                                                    <span
                                                                        class="">${{ $manager_details['ownInvestmentSummary']['deposits'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Own withdrawals
                                                                    <span
                                                                        class="">${{ $manager_details['ownInvestmentSummary']['withdrawals'] ?? '' }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="card custom-card">
                                                        <div class="card-header justify-content-between">
                                                            <div class="card-title">
                                                                Fees
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Performance Fee<span
                                                                        class="">${{ $manager_details['ownInvestmentSummary']['performanceFees'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Agent Commissions
                                                                    <span
                                                                        class="">${{ $manager_details['ownInvestmentSummary']['agentCommissions'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    <span class="fs-16 fw-bold">Total Fees</span>
                                                                    <span
                                                                        class="fs-20 fw-bold">${{ $manager_details['ownInvestmentSummary']['feesTotal'] ?? '' }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card custom-card">
                                                        <div class="card-header justify-content-between">
                                                            <div class="card-title">
                                                                Trading Account
                                                                <div class="text-muted fs-11">
                                                                    <?= $manager_details['name'] ?>,
                                                                    #<?=
                                                                        $manager_details['accountId'].
                                                                        $manager_details['serverName'].
                                                                        $manager_details['tradingAccount']['group']
                                                                        ?>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Balance
                                                                    <span
                                                                        class="">${{ $manager_details['tradingAccount']['balance'] ?? '' }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Floating Profit
                                                                    <span
                                                                        class="<?= $manager_details['tradingAccount']['profitFloating'] > 0 ? 'text-success' : ($manager_details['profitTotal'] < 0 ? 'text-danger' : '') ?>">$<?= $manager_details['tradingAccount']['profitFloating'] ?? '' ?></span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-bold">
                                                                    Equity<span
                                                                        class="">${{ $manager_details['tradingAccount']['equity'] ?? '' }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card custom-card">
                                                        <div class="card-header justify-content-between">
                                                            <div class="card-title">
                                                                Owner Account
                                                                <div class="text-muted fs-11">
                                                                    <?= $manager_details['ownerAccount']['name'] ?>,
                                                                    #<?= $manager_details['ownerAccount']['id'] ?><?= $manager_details['ownerAccount']['group'] ?>
                                                                    </li>
                                                                  </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <ul class="list-group">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                                                    Balance
                                                                    <span
                                                                        class="">${{ $manager_details['ownerAccount']['wallets'][0]['balance'] ?? '' }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane text-muted" id="tab-investments" role="tabpanel">
                                        <div class="card custom-card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table
                                                        class="ajaxDataTable tableInvestments table table-bordered text-nowrap w-100">
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane text-muted" id="tab-offers" role="tabpanel">
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-primary create-offer"
                                                data-bs-toggle="modal" data-bs-target="#updateOfferModal">
                                                Create Offer <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        <div id="offer-container" class="mt-2">
                                            <div class="d-flex justify-content-center">
                                                <strong>Loading...</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane text-muted" id="tab-history" role="tabpanel">
                                        <div class="card custom-card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table
                                                        class="ajaxDataTable tableHistory table table-bordered text-nowrap w-100">
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane text-muted" id="tab-requests" role="tabpanel">
                                        <div class="card custom-card">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table
                                                        class="ajaxDataTable tableRequests table table-bordered text-nowrap w-100">
                                                    </table>
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
    </div>
    <script>
        $(document).on('change', '#offerExtendedMode', function(e) {
            if ($(this).is(':checked')) {
                $('.agentChainList').show();
                $('.agentChainInput').hide();
            } else {
                $('.agentChainInput').show();
                $('.agentChainList').hide();
            }
        });

        $(document).on('click', '.edit-offer', function(e) {
            var id = $(this).data('id');
            $.ajax({
                url: '/admin/pamm/offers_money_manager',
                method: 'POST',
                data: {
                    id: '<?= $_GET['id'] ?>'
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Fetching ....',
                        text: 'Please wait while we process your request.',
                        icon: 'info',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(resp) {
                    Swal.close();
                    // var resp = JSON.parse(response);
                    const filteredData = resp.data.filter(item => item.id === id);


                    $(`#updateOfferForm #mgr_offer_id`).html("#" + filteredData[0].id);
                    $(`#updateOfferForm #mgr_offer_name`).html(filteredData[0].name);
                    $(`#updateOfferForm #id`).val(filteredData[0].id);
                    $(`#updateOfferForm #name`).val(filteredData[0].name);
                    $(`#updateOfferForm #isActive`).prop("checked", Boolean(filteredData[0].isActive));
                    $(`#updateOfferForm #description`).val(filteredData[0].description);


                    $(`#updateOfferForm #tradingInterval_type`).val(filteredData[0].settings
                        .tradingInterval.type).trigger("change");
                    $(`#updateOfferForm #tradingInterval_count`).val(filteredData[0].settings
                        .tradingInterval.count);


                    $(`#updateOfferForm #minDeposit`).val(filteredData[0].settings.minDeposit);
                    $(`#updateOfferForm #minWithdrawal`).val(filteredData[0].settings.minWithdrawal);
                    $(`#updateOfferForm #minInitialInvestment`).val(filteredData[0].settings
                        .minInitialInvestment);


                    $('#offerExtendedMode').prop('checked', false).trigger('change');
                    if (filteredData[0].settings.agentChain != null) {
                        if (/^\d+$/.test(filteredData[0].settings.agentChain)) {
                            $(`#updateOfferForm #agentChain`).val(filteredData[0].settings.agentChain);
                            $('#offerExtendedMode').prop('checked', false).trigger('change');
                        } else {
                            const agentChainArray = filteredData[0].settings.agentChain.split(',');
                            if (agentChainArray.length > 0) {
                                let $container = $('#agentChainContent');
                                let $templateClone = $container.find('.dynamic-table-clone').clone();
                                $container.empty();
                                agentChainArray.forEach((item, index) => {
                                    const [login, server] = item.split(':');
                                    let $clone = $templateClone.clone();
                                    if (index === 0) {
                                        $clone.addClass('dynamic-table-clone');
                                    } else {
                                        $clone.removeClass('dynamic-table-clone');
                                    }
                                    $clone.show();
                                    $clone.find('input[name="agentChainLogin[]"]').val(login ||
                                        '');
                                    $clone.find('select[name="agentChainServer[]"]').val(
                                        server || '1');
                                    $clone.find('.dynamic-table-delete').prop('disabled',
                                        index === 0);
                                    $container.append($clone);
                                });

                                $('#offerExtendedMode').prop('checked', true).trigger('change');
                            }

                        }
                    }


                    if (filteredData[0].settings.performanceFees.levels.length > 0) {
                        let $container = $('#offerPerformanceFeeContainer');
                        let $templateClone = $container.find('.dynamic-table-clone').clone();
                        $container.empty();
                        filteredData[0].settings.performanceFees.levels.forEach((item, index) => {
                            let $clone = $templateClone.clone();
                            if (index === 0) {
                                $clone.addClass('dynamic-table-clone');
                            } else {
                                $clone.removeClass('dynamic-table-clone');
                            }
                            $clone.show();
                            $clone.find('input[name="performanceFees_level[]"]').val(item
                                .level || '');
                            $clone.find('input[name="performanceFees_value[]"]').val(item
                                .value || '');
                            $clone.find('.dynamic-table-delete').prop('disabled', index === 0);
                            $container.append($clone);
                        });
                    }
                    if (filteredData[0].joinLinks.length > 0) {
                        let $container = $('#joinLinkContent');
                        let $templateClone = $container.find('.dynamic-table-clone').clone();
                        $container.empty();
                        filteredData[0].joinLinks.forEach((item, index) => {
                            let $clone = $templateClone.clone();
                            if (index === 0) {
                                $clone.addClass('dynamic-table-clone');
                            } else {
                                $clone.removeClass('dynamic-table-clone');
                            }
                            $clone.show();
                            $clone.find('input[name="joinLinkKey[]"]').val(item.key || '');
                            $clone.find('input[name="joinLinkExpiration[]"]').val(item
                                .expiration || '');
                            $clone.find('input[name="joinLinkAgent[]"]').val(item.agentChain ||
                                '');
                            $clone.find('input[name="joinLinkOneTime[]"]').prop('checked', item
                                .oneTime || false);
                            $clone.find('.dynamic-table-delete').prop('disabled', index === 0);
                            $container.append($clone);
                        });
                    }
                    $('.join-link-expiration').datepicker('destroy');
                    $('.join-link-expiration').datepicker({
                        format: 'yyyy-mm-ddT00:00:00Z',
                        autoclose: true,
                        todayHighlight: true
                    });

                    $("#updateOfferModal").modal('show');
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        title: 'Error: Could Not Find Offer. Please try again.',
                        icon: 'danger',
                    }).then(() => {
                        // location.reload();
                    });
                }
            });
        });

        $(document).ready(function() {
            select2init();
            $('.join-link-expiration').datepicker({
                format: 'yyyy-mm-ddT00:00:00Z',
                autoclose: true,
                todayHighlight: true
            });


            $(document).on("click", '.dynamic-table-add', function(e) {
                let $container = $(this).closest('.dynamic-table-container');
                let $clone = $container.find('.dynamic-table-clone').clone();
                $clone.removeClass('dynamic-table-clone').show();
                $clone.find('input').val('');
                $clone.find('.dynamic-table-delete').prop('disabled', false);
                $container.find('.dynamic-table-content').append($clone);
            });
            $(document).on('click', '.dynamic-table-delete', function() {
                $(this).closest('.row').remove();
            });



            window.dTSelection = function() {

                $('.ajaxDataTable').on('click', '.reqToggle', function() {
                    var type = $(this).data('type');
                    var data = tableRequsests.row($(this).closest("tr")).data();
                    $("#reqModal #requestName").html(data.investorName);
                    $("#reqModal #requestId").html(data.id);
                    $("#reqModal #requestIds").val(data.id);
                    $("#reqModal #requestType").html(data.requestType);
                    $("#reqModal #requestAmount").html(`${"$" + data.amount}`);
                    $("#reqModal #requestAction").val(type);
                    $("#reqModal ").modal("show");

                    // $.ajax({
                    //   url: "/admin/ajax.php",
                    //   type: "GET",
                    //   data: {
                    //     action: 'getRMbyGroup',
                    //     "id": data.enc
                    //   },
                    //   success: function (response) {
                    //     var userGroupIds = JSON.parse(response);
                    //     var defaultOption = $('<option></option>').val('').text('--Select--').attr('selected', 'selected');
                    //     $('#group_rm_list').html(defaultOption);
                    //     $.each(userGroupIds, function (index, option) {
                    //       var $option = $('<option></option>').val(option.email).text(option.username);
                    //       $('#group_rm_list').append($option);
                    //     });
                    //   }
                    // });


                });

            }

            window.formatCurrency = (data, isMuted = false) => {
                const formatted = `$${parseFloat(data).toFixed(2)}`;
                return isMuted ? `<span class="text-muted">${formatted}</span>` : formatted;
            };

            window.formatProfit = (data) => {
                const formatted = `$${parseFloat(data).toFixed(2)}`;
                if (data == 0) return `<span class="text-muted">${formatted}</span>`;
                return `<span class="${data > 0 ? 'text-success' : 'text-danger'}">${formatted}</span>`;
            };

            $.ajax({
                url: '/admin/pamm/get_manager_offer',
                type: "POST",
                data: {
                    'manager_id': '<?= $_GET['id'] ?>'
                },
                success: function(response) {
                    const resp = JSON.parse(response);
                    $('#offer_id').empty();
                    $('#offer_id').append('<option value="">Select an Offer</option>');
                    resp.data.forEach(function(item) {
                        $('#offer_id').append(
                            $('<option>', {
                                value: item.id,
                                text: item.name,
                                'data-mindeposit': item.settings.minInitialInvestment
                            })
                        );
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching manager data:', error);
                }
            });
        });

        $('#createInvestorForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            $.ajax({
                url: '/admin/pamm/create_investments',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // response = JSON.parse(response);
                    Swal.fire({
                        title: response.message,
                        icon: response.status,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error: Could not create Investment. Please try again.',
                        icon: 'danger',
                    }).then(() => {
                        // location.reload();
                    });
                }
            });
        });

        $('#managerUpdateForm').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var formData = new FormData(form);
            // formData.append('action', 'update_money_manager');
            var isPublicChecked = $('#isPublic').is(':checked');
            formData.append('isPublic', isPublicChecked ? true : false);
            $.ajax({
                url: '/admin/pamm/update_money_manager',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(resp) {
                    // var resp = JSON.parse(response);
                    if ('error' in resp) {
                        Swal.fire({
                            title: 'Error!',
                            text: response.errorCode,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Manager details updated successfully!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an issue updating the manager details. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


        $(document).on('change', '#offer_id', function() {
            var selectedOption = $(this).find(':selected');
            var minimumValue = selectedOption.data('mindeposit');
            $('#amount').attr('min', minimumValue);
            $('#amount').val('');
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const activeTabId = $(e.target).attr('href');
            if (activeTabId === '#tab-investments') {
                if ($.fn.DataTable.isDataTable('.tableInvestments')) {
                    $('.tableInvestments').DataTable().destroy();
                }
                const table = $('.tableInvestments').on("draw.dt", dTSelection).DataTable({
                    order: [
                        [0, "desc"]
                    ],
                    ajax: {
                        url: "/admin/pamm/investments_money_manager",
                        type: "POST",
                        data: {
                            id: '<?= $_GET['id'] ?>'
                        },
                    },
                    columns: [{
                            data: 'id',
                            title: '#'
                        },
                        {
                            data: 'name',
                            title: 'Investor'
                        },
                        {
                            data: 'ownerId',
                            title: 'Owner',
                            render: (data, options, row) =>
                                `<span class="text-muted">#${row.ownerId} (${row.ownerServerName})</span>`
                        },
                        {
                            data: 'fundsTotal',
                            title: 'Funds',
                            render: (data) => formatCurrency(data, data == 0)
                        },
                        {
                            data: 'profitNet',
                            title: 'Net Profit',
                            render: formatProfit
                        },
                        {
                            data: 'profitTotal',
                            title: 'Trade Results',
                            render: formatProfit
                        },
                        {
                            data: 'profitIntervalTotal',
                            title: 'Trade Results',
                            render: formatProfit
                        },
                        {
                            data: 'currency',
                            title: 'Trading Interval',
                            render: (data, options, row) => {
                                const start = moment(row.tradingIntervalStart).format(
                                    "MMM D, YYYY");
                                const end = moment(row.tradingIntervalEnd).format("MMM D, YYYY");
                                return moment(row.tradingIntervalEnd).format('YYYY') != '0001' ?
                                    `${start} - ${end}` : "";
                            }
                        },
                        {
                            data: 'offerName',
                            title: 'Offer'
                        },
                        {
                            data: 'createdDt',
                            title: 'Created',
                            render: (data) => moment(data).format("YYYY-MM-DD HH:mm:ss")
                        }
                    ]
                });



            } else if (activeTabId === '#tab-offers') {
                $.ajax({
                    url: '/admin/pamm/offers_money_manager',
                    method: 'POST',
                    data: {
                        id: '<?= $_GET['id'] ?>'
                    },
                    success: function(response) {
                        var html = '';
                        $.each(response.data, function(index, value) {
                            html += `
    <div class="row">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <h2 class="card-title fw-medium fs-20">${value.name}${value.isActive == 0 ? `<span><i class="ms-2 fa fa-lock text-muted"></i></span>` : ``}</h2>
                        ${value.activeInvestmentCount > 0 ? `<span class="text-muted">${value.activeInvestmentCount} Investments</span>` : ''}
                        <div class="my-2">${value.description || ''}</div>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-end">
                        <span class="edit-offer" data-id="${value.id}">
                            <i class="fa fa-edit"></i>
                        </span>
                    </div>
                </div>`;

                            if (value.settings) {
                                html += `
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                            Trading Interval
                            <span class="">${value.settings.tradingInterval.count + ' ' + value.settings.tradingInterval.type}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                            Min Deposit
                            <span class="">$${value.settings.minDeposit}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                            Min Withdrawal
                            <span class="">$${value.settings.minWithdrawal}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                            Min Initial Investment
                            <span class="">$${value.settings.minInitialInvestment}</span>
                        </li>

                    </ul>`;
                            }
                            html += `</div>
            </div>
        </div>`;
                        });
                        $('#offer-container').html(html);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error: Could Not Find Money Manager. Please try again.',
                            icon: 'danger',
                        }).then(() => {
                            // location.reload();
                        });
                    }
                });

            } else if (activeTabId === '#tab-history') {
                if ($.fn.DataTable.isDataTable('.tableHistory')) {
                    $('.tableHistory').DataTable().destroy();
                }

                const tableHistory = $('.tableHistory').on("draw.dt", dTSelection).DataTable({
                    order: [
                        [0, "desc"]
                    ],
                    ajax: {
                        url: "/admin/pamm/transactions_money_manager",
                        type: "POST",
                        data: {
                            id: '<?= $_GET['id'] ?>'
                        },
                        dataSrc: function(response) {
                            return response.data.items;
                        }
                    },
                    columns: [{
                            data: 'id',
                            title: '#'
                        },
                        {
                            data: 'reason',
                            title: 'Reason'
                        },
                        {
                            data: 'investorName',
                            title: 'Investment'
                        },
                        {
                            data: 'amount',
                            title: 'Amount',
                            render: function(data) {
                                return "$" + data
                            }
                        },
                        {
                            data: 'time',
                            title: 'Time',
                            render: (data) => moment(data).format("YYYY-MM-DD HH:mm:ss")
                        },
                        {
                            data: 'comment',
                            title: 'Comment'
                        },
                        {
                            data: 'requestId',
                            title: 'Request',
                            render: (data) => ''
                        },
                        {
                            data: 'rolloverId',
                            title: 'Rollover ID'
                        },
                        {
                            data: 'platformPositionId',
                            title: 'Position ID'
                        },
                        {
                            data: 'counterpartyLogin',
                            title: 'Counterparty Account',
                            render: (data, type, row) => data ?
                                `#${data} (${row.counterpartyServerName})` : ''
                        },

                    ]
                });


            } else if (activeTabId === '#tab-requests') {
                if ($.fn.DataTable.isDataTable('.tableRequests')) {
                    $('.tableRequests').DataTable().destroy();
                }

                window.tableRequsests = $('.tableRequests').on("draw.dt", dTSelection).DataTable({
                    order: [
                        [0, "desc"]
                    ],
                    ajax: {
                        url: "/admin/pamm/requests_money_manager",
                        type: "POST",
                        data: {
                            id: '<?= $_GET['id'] ?>'
                        },
                        dataSrc: function(response) {
                            return response.data.items;
                        }
                    },
                    columns: [{
                            data: 'id',
                            title: '#'
                        },
                        {
                            data: 'status',
                            title: 'Action',
                            render: function(data, row, row_data) {
                                return '<span class="statusToggle" data-status="' + row_data
                                    .status + '">' + (row_data.status == "Pending" ?
                                        '<button data-type="Confirm" class="reqToggle btn btn-sm btn-primary"><i class="fa fa-check"></i></button><button data-type="Cancel" class="reqToggle ms-1  btn-sm btn btn-danger"><i class="fa fa-times"></i></button>' :
                                        '') + '</span>';
                            },
                        },
                        {
                            data: 'requestType',
                            title: 'Type'
                        },
                        {
                            data: 'investorName',
                            title: 'Investment'
                        },
                        {
                            data: 'amount',
                            title: 'Amount',
                            render: function(data) {
                                return "$" + data
                            }
                        },
                        {
                            data: 'status',
                            title: 'Status',
                            render: function(data, row, row_data) {
                                return '<span class="statusToggle" data-status="' + row_data
                                    .status + '">' + (row_data.status == "Pending" ?
                                        '<button class=" badge btn-sm btn btn-outline-warning">Pending</button>' :
                                        (row_data.status == 'Cancelled' ?
                                            '<button class=" badge btn-sm btn btn-outline-danger">Cancelled</button>' :
                                            '<button class="badge btn-sm btn btn-outline-primary">Confirmed</button>'
                                        )) + '</span>';
                            },
                        },
                        {
                            data: 'platformOperationId',
                            title: 'External ID'
                        },
                        {
                            data: 'comment',
                            title: 'Comment'
                        },
                        {
                            data: 'requestTime',
                            title: 'Time',
                            render: (data) => moment(data).format("YYYY-MM-DD HH:mm:ss")
                        }
                    ]
                });

            }
        });

        function select2init() {
            $('.live-acc-select').select2({
                dropdownParent: $('#addUserModal'),
                ajax: {
                    url: '/admin/ajax',
                    type: "GET",
                    data: function(params) {
                        var searchValue = params.term;
                        return {
                            term: searchValue,
                            action: 'getLiveAccounts'
                        };
                    },
                    processResults: function(data) {
                        data = JSON.parse(data);
                        return {
                            results: $.map(data, function(item) {
                                console.log(item.trade_id + " [" + item.name + " - " + item.email +
                                    "]");
                                return {
                                    text: item.trade_id + " [" + item.name + " - " + item.email + "]",
                                    id: item.trade_id
                                }
                            })
                        };
                    }
                }
            });
        }

        $(document).ready(function() {
            if (window.location.hash) {
                var hash = window.location.hash;
                $('.nav-link').each(function() {
                    if ($(this).attr('href') === hash) {
                        $(this).addClass('active');
                        $(hash).addClass('active show');
                        $(this).trigger('shown.bs.tab');
                    } else {
                        $(this).removeClass('active');
                        $($(this).attr('href')).removeClass('active show');
                    }
                });
            }
            $('.nav-link').on('click', function() {
                var newHash = $(this).attr('href');
                window.location.hash = newHash;
            });
        });
    </script>
    @include('admin.pamm.pamm_scripts');
@endsection
