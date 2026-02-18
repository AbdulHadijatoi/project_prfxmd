@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Bank Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bank Details</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-8 mx-auto">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title">
                                Bank Details
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                              <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                ACCOUNT HOLDER NAME
                                <span class="badge bg-primary rounded-pill"><?= $details->ClientName ?></span>
                              </li>
                              <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                BANK NAME
                                <span class="badge bg-secondary rounded-pill"><?= $details->bankName ?></span>
                              </li>
                              <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                ACCOUNT NUMBER
                                <span class="badge bg-danger rounded-pill"><?= $details->accountNumber ?></span>
                              </li>
                              <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                IFSC CODE
                                <span class="badge bg-dark rounded-pill"><?= $details->code ?></span>
                              </li>
                              <li class="list-group-item d-flex justify-content-between align-items-center fw-medium">
                                SWIFT CODE
                                <span class="badge bg-success rounded-pill"><?= $details->swift_code ?></span>
                              </li>
                            </ul>
                          </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
