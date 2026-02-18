@extends('layouts.admin.admin')
@section('content')
    <div class="modal fade" id="ibModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="ibModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form action="#" id="ibRequestForm" method="post">
                    @csrf
                    <input type="hidden" name="client_id" id="client_id" value="">
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
                                        <option value="2">Rejected</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 m-auto">
                                    <label class="form-label">IB Plan</label>
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
            <div class="page-header">
                <h1 class="page-title">Pending Tasks</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pending Tasks</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex mx-1 ">
                                {{-- <select class="form-select form-control me-1" name="dtOption" id="dtOption">
                                    <option value="">All</option>
                                    <option value="trade_deposit">Trade Deposit</option>
                                    <option value="trade_withdrawal">Trade Withdrawal</option>
                                    <option value="wallet_deposit">Wallet Deposit</option>
                                    <option value="wallet_withdrawal">Wallet Withdrawal</option>
                                    <option value="kyc">KYC</option>
                                    <option value="ib_request">IB Requests</option>
                                </select> --}}
                                <input type="text" class="form-control" name="dtStartDate" id="dtStartDate"
                                    placeholder="Start Date">
                                <input type="text" class="ms-2 form-control" name="dtEndDate" id="dtEndDate"
                                    placeholder="End Date">
                                <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableWalletDeposit" class="ajaxDataTable table table-bordered text-nowrap w-100">
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
                order: [
                    [3, "desc"]
                ],
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: function(d) {
                        d.action = 'getAllPendingTasks';
                        d.startdate = $('#dtStartDate').val();
                        d.enddate = $('#dtEndDate').val();
                        d.option = $('#dtOption').val();
                    }
                },
                columns: [{
                        data: 'transaction_id',
                        title: '#'
                    },
                    {
                        data: 'fullname',
                        title: 'Client',
                        render: function(data, row, row_data) {
                            var return_data = "<a href='/admin/client_details?id=" + row_data
                                .enc_email +
                                "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                                row_data.fullname +
                                "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                                row_data.email + "</span></div></div></div></a>";
                            return return_data;
                        }
                    },
                    {
                        data: 'transaction_type',
                        title: 'Transaction Type',
                        render: function(data, type, row) {
                            var typeBtn = {
                                trade_deposit: 'success',
                                wallet_deposit: 'warning',
                                trade_withdrawal: 'info',
                                wallet_withdrawal: 'dark',
                                kyc: 'secondary',
                                ib_request: 'info',
                                tickets: 'primary',
                                default: 'default',
                            };
                            var btn_class = typeBtn[row.transaction_type] || typeBtn.default;
                            return "<button class='badge btn-sm btn btn-outline-" + btn_class +
                                " text-capitalize'>" +
                                data.replace("_", " ") + "</button>"

                        }
                    },
                    {
                        data: 'raw_type',
                        title: 'Type',
                        render: function(data, type, row) {
                            var return_data = "<div class='d-grid'>" + (data === null ? '' : data) + "</div>";
                            return return_data;
                        }
                    },
                    {
                        data: 'details',
                        title: 'Details',
                        render: function(data, type, row) {
                            var details = {
                                trade_deposit: 'to_trade_id',
                                wallet_deposit: '',
                                trade_withdrawal: 'from_trade_id',
                                wallet_withdrawal: '',
                                kyc: '',
                                ib_request: '',
                                tickets: '',
                                default: ''
                            };
                            var fieldName = details[row.transaction_type] || details.default;
                            var return_data = "<div class='details'>" + (row[fieldName] ?? '') +
                                "</div>";
                            return return_data;
                        }
                    },
                    {
                        data: 'created_at',
                        title: 'Created Date',
                        render: function(data, type, row) {
                            var dateTime = row.created_at.split(' ');
                            var date = dateTime[0];
                            var time = dateTime[1];
                            var return_data = "<div class='d-grid'><div class='date'>" + date +
                                "</div><div class='time text-muted'>" + time + "</div></div>";
                            return return_data;
                        }
                    },
                    {
                        data: 'enc_id',
                        title: 'Action',
                        render: function(data, type, row) {
                            var typeToLink = {
                                trade_deposit: '/admin/trading_deposit_details?id=' + row
                                    .enc_id,
                                wallet_deposit: '/admin/wallet_deposit_details?id=' + row
                                    .enc_id,
                                trade_withdrawal: '/admin/trading_withdrawal_details?id=' + row
                                    .enc_id,
                                wallet_withdrawal: '/admin/wallet_withdrawal_details?id=' + row
                                    .enc_id,
                                kyc: '/admin/kyc_details?id=' + row.enc_email,
                                ib_request: '',
                                tickets: '/admin/ticket_details?id=' + row.enc_id,
                            };
                            var link = typeToLink[row.transaction_type];
                            if (row.transaction_type != 'ib_request') {
                                return '<a class="" href="' + link +
                                    '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg></a>';
                            } else {
                                return '<a class="ibToggle" data-bs-toggle="modal" data-bs-target="#ibModal" data-fullname="' +
                                    row.fullname + '"  data-email="' + row.email +
                                    '"   data-enc="' + row.enc_email + '" data-ib_status="' + row
                                    .ib_status +
                                    '" data-ib_group="' + row
                                    .rawtype +
                                    '"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg></a>';

                            }
                        }
                    },
                ],

                initComplete: function() {
                    var needs = [2];
                    this.api().columns().every(function(index) {
                        if (needs.indexOf(index) == -1) {
                            return false;
                        }
                        var column = this;
                        var select = $(
                                '<select class="d-block form-control mt-2 text-capitalize"><option value="">All</option></select>'
                            )
                            .appendTo($(column.header()))
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d.replace("_", " ") +
                                '">' + d.replace("_", " ") + '</option>');
                        });
                    });
                }
            });
        });
        $(document).on("click", ".dtDateFilter", function() {
            $('#tableWalletDeposit').DataTable().ajax.reload();
        });
        $(document).on('click', '.ibToggle', function() {
            var data = $(this).data();
            $("#clientName,#clientEmail").html("");
            $("#clientName").html(data.fullname);
            $("#clientEmail").html(data.email);
            $("#client_id").val(data.enc);
            $("[name='ib_status']").val(data.ib_status).trigger("change");
            $("[name='ib_group']").val(data.ib_group).trigger("change");
            $("#ibModal").show();
        });
    </script>
@endsection
