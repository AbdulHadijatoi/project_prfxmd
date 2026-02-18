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
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex mx-1 ">
                                <input type="text" class="form-control" name="dtStartDate" id="dtStartDate"
                                    placeholder="Start Date" >
                                <input type="text" class="ms-2 form-control" name="dtEndDate" id="dtEndDate"
                                    placeholder="End Date">
                                <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableWalletDeposit" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Account Name</th>
                                            <th>Bank Name</th>
                                            <th>Account Number</th>
                                            <th>IFSC</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#tableWalletDeposit').DataTable({
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: function(d) {
                        d.action = 'getBankDetails';
                        d.startdate = $('#dtStartDate').val();
                        d.enddate = $('#dtEndDate').val();
                    }
                },
                columns: [{
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'account_name',
                        name: 'account_name'
                    },
                    {
                        data: 'bank_name',
                        name: 'bank_name'
                    },
                    {
                        data: 'account_no',
                        name: 'account_no'
                    },
                    {
                        data: 'ifsc',
                        name: 'ifsc'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
        });
        $(document).on("click", ".dtDateFilter", function() {
            $('#tableWalletDeposit').DataTable().ajax.reload();
        });
    </script>
@endsection
