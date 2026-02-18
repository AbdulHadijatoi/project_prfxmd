@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Wallet Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Wallet Details</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-8 mx-auto">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                Wallet Details
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                    WALLET NAME
                                    <span class="badge bg-primary rounded-pill"><?= $details->wallet_name ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                    WALLET CURRENCY
                                    <span class="badge bg-secondary rounded-pill"><?= $details->wallet_currency ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                    WALLET NETWORK
                                    <span class="badge bg-danger rounded-pill"><?= $details->wallet_network ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                    WALLET ADDRESS
                                    <span class="badge bg-dark rounded-pill"><?= $details->wallet_address ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
