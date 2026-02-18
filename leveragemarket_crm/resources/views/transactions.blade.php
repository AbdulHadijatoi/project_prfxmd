@php
    use Carbon\Carbon;
@endphp
@extends('layouts.crm.crm')
@section('content')
    <style>
        #wallet_transactions .td-wrap {
            max-width: 75px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .wallet-plus td {
            --bs-text-opacity: 1;
            color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
        }

        .wallet-minus td {
            --bs-text-opacity: 1;
            color: rgba(var(--bs-danger-rgb), var(--bs-text-opacity)) !important;
        }



        .fxtran-filter-form {
            display: flex;
            flex-wrap: wrap;
			margin-bottom: 0!important;
            gap: 10px;
            align-items: center;
        }

        .fxtran-input {
            padding: 8px 12px;
            border: 1px solid #12a300;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            min-width: 150px;
        }

        .fxtran-input:focus {
            box-shadow: 0 0 0 2px rgba(18, 163, 0, 0.2);
        }

        .fxtran-btn-outline {
            padding: 8px 16px;
            border: 1px solid #12a300;
            color: #12a300;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .fxtran-btn-outline:hover {
            background: #12a300;
            color: #fff;
        }

        .fxtran-export-wrap {
            margin-top: 5px;
        }
    </style>
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">All Transactions</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body border-bottom pb-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between"> 
                                <div class="fxtran-filter-wrap">
                                    <form method="GET" class="fxtran-filter-form">

                                        <select name="filter" class="fxtran-input">
                                            <option value="">All</option>
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                        </select>

                                        <input type="date" name="from" class="fxtran-input">
                                        <input type="date" name="to" class="fxtran-input">

                                        <button type="submit" class="fxtran-btn-outline">
                                            Filter
                                        </button>

                                    </form>
                                </div>

                                <div class="fxtran-export-wrap mt-3 mt-lg-0">
                                    <a href="" class="fxtran-btn-outline">
                                        Export Excel
                                    </a>
                                </div>

                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-md-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="avtar avtar-s bg-light-primary">
                                                    <i class="ti ti-wallet f-24"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-center">Deposit</h4>
                                                </div>
                                            </div>
                                            <hr class="p-0 m-0" style="" />
                                            <div class="mt-2 text-center">
                                                <h4 class="mb-1 f-w-400">${{ $totalCredit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="avtar avtar-s bg-light-primary">
                                                    <i class="ti ti-wallet f-24"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-center">Withdraw</h4>
                                                </div>
                                            </div>
                                            <hr class="p-0 m-0" style="" />
                                            <div class="mt-2 text-center">
                                                <h4 class="mb-1 f-w-400">${{ $totalDebit }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="avtar avtar-s bg-light-primary">
                                                    <i class="ti ti-wallet f-24"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-center">A2A Transfer</h4>
                                                </div>
                                            </div>
                                            <hr class="p-0 m-0" style="" />
                                            <div class="mt-2 text-center">
                                                <h4 class="mb-1 f-w-400">${{ $a2aTotal }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between mt-3" style="width:100%; overflow-x:auto;">
                                <table class="table table-responsive" style="min-width:900px;">
                                    <thead>
                                        <tr>
                                            <th>Sno</th>
                                            <th>Date</th>
                                            <th>Account</th>
                                            <th>Particulars</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ledger as $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <div class="avtar avtar-s border"><img
                                                                    src="/assets/images/mt5.png" class="wid-30"
                                                                    alt="logo"></div>
                                                        </div>
                                                        <div class="ms-2">
                                                            <h6 class="mb-0">{{ $row->trade_id }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h6 class="f-w-500">
                                                        {{ Carbon::parse($row->created_at)->format('Y-m-d') }}</h6>
                                                    <p class="text-muted mb-0">
                                                        <small>{{ Carbon::parse($row->created_at)->format('H:i A') }}</small>
                                                    </p>
                                                </td>
                                                <td>{{ $row->particulars }}</td>
                                                <td>
                                                    <h6 class="f-w-500 f-16">{{ $row->debit }}</h6>
                                                </td>
                                                <td>
                                                    <h6 class="f-w-500 f-16">{{ $row->credit }}</h6>
                                                </td>
                                                <td
                                                    class="{{ $row->Status == 0 ? 'text-warning' : ($row->Status == 1 ? 'text-success' : 'text-danger') }}">
                                                    <p>{{ $row->Status == 0 ? 'Pending' : ($row->Status == 1 ? 'Success' : 'Cancelled') }}
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    Showing {{ $ledger->firstItem() }} to {{ $ledger->lastItem() }}
                                    of {{ $ledger->total() }} entries
                                </div>
                                <div>
                                    {{ $ledger->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
