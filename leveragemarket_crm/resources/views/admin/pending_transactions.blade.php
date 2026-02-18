@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Pending Transaction List</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Transaction List</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3 border-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link {{ $id == 'all_transactions' ? 'active' : '' }}"
                                        data-type="all_transactions" data-bs-toggle="tab" role="tab" href="#all_transactions"
                                        aria-selected="true">All Transactions</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$id == 'wallet_deposit'? 'active':''}}" data-type="wallet_deposit" data-bs-toggle="tab" role="tab"
                                        href="#walletdeposit" aria-selected="true">Wallet Deposit</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$id == 'wallet_withdrawal'? 'active':''}}" data-bs-toggle="tab" data-type="wallet_withdrawal" role="tab"
                                        href="#walletwithdrawal" aria-selected="false">Wallet
                                        Withdrawal</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$id == 'trading_deposit'? 'active':''}}" data-bs-toggle="tab" data-type="trading_deposit" role="tab"
                                        href="#tradingdeposit" aria-selected="false">Trading
                                        Deposit</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$id == 'trading_withdrawal'? 'active':''}}" data-bs-toggle="tab" data-type="trading_withdrawal" role="tab"
                                        href="#tradingwithdrawal" aria-selected="false">Trading
                                        Withdrawal</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane text-muted {{ $id == 'all_transactions' ? 'active show' : '' }}"
                                    id="all_transactions" role="tabpanel">
                                    <div class="d-flex justify-content-end mt-1 mb-4">
                                        <div class="d-flex mx-1 w-75">
                                            {{-- <select class="form-select form-control me-1 atOption" name="dtOption" id="dtOption">
                                                <option value="deposted_date">Transaction Date</option>
                                                <option value="Js_Admin_Remark_Date">Date Approved</option>
                                            </select> --}}
                                            <input type="text" class="form-control atStartDate" name="dtStartDate" id="dtStartDate"
                                                placeholder="Start Date">
                                            <input type="text" class="ms-2 form-control atEndDate" name="dtEndDate" id="dtEndDate"
                                                placeholder="End Date">
                                            <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tableAllTransactions"
                                            class="ajaxDataTable table table-bordered text-nowrap w-100">

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane text-muted {{ $id == 'wallet_deposit' ? 'active show' : '' }}" id="walletdeposit" role="tabpanel">
                                    <div class="d-flex justify-content-end mt-1 mb-4">
                                        <div class="d-flex mx-1 w-75">
                                            <input type="text" class="form-control wdStartDate" name="dtStartDate" id="dtStartDate"
                                                placeholder="Start Date" >
                                            <input type="text" class="ms-2 form-control wdEndDate" name="dtEndDate" id="dtEndDate"
                                                placeholder="End Date">
                                            <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tableWalletDeposit"
                                            class="ajaxDataTable table table-bordered text-nowrap w-100">

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane text-muted {{ $id == 'wallet_withdrawal' ? 'active show' : '' }}" id="walletwithdrawal" role="tabpanel">
                                    <div class="d-flex justify-content-end">
                                        <div class="d-flex mx-1 ">
                                            <input type="text" class="form-control wwStartDate" name="dtStartDate" id="dtStartDate"
                                                placeholder="Start Date" >
                                            <input type="text" class="ms-2 form-control wwEndDate" name="dtEndDate" id="dtEndDate"
                                                placeholder="End Date">
                                            <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tableWalletWithdrawal"
                                            class="ajaxDataTable table table-bordered text-nowrap w-100">

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane text-muted {{ $id == 'trading_deposit' ? 'active show' : '' }}" id="tradingdeposit" role="tabpanel">
                                    <div class="d-flex justify-content-end">
                                        <div class="d-flex mx-1 ">
                                            <input type="text" class="form-control tdStartDate" name="dtStartDate" id="dtStartDate"
                                                placeholder="Start Date" >
                                            <input type="text" class="ms-2 form-control tdEndDate" name="dtEndDate" id="dtEndDate"
                                                placeholder="End Date">
                                            <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tableTradingDeposit"
                                            class="ajaxDataTable table table-bordered text-nowrap w-100">

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane text-muted {{ $id == 'trading_withdrawal' ? 'active show' : '' }}" id="tradingwithdrawal" role="tabpanel">
                                    <div class="d-flex justify-content-end">
                                        <div class="d-flex mx-1 ">
                                            <input type="text" class="form-control twStartDate" name="dtStartDate" id="dtStartDate"
                                                placeholder="Start Date" >
                                            <input type="text" class="ms-2 form-control twEndDate" name="dtEndDate" id="dtEndDate"
                                                placeholder="End Date">
                                            <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="tableTradingWithdrawal"
                                            class="ajaxDataTable table table-bordered text-nowrap w-100">

                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane text-muted" id="transaction5" role="tabpanel">
                                    <table id="tableInternalTransfer"
                                        class="ajaxDataTable table table-bordered text-nowrap w-100">

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.shared.script_pending');
@endsection
