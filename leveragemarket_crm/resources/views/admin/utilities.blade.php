@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">Single Form Transactions</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Single Form Transactions</li>
                </ol>
            </div>
            <!-- PAGE-HEADER END -->
            <!-- ROW-1 OPEN -->
            <div class="row">
                <div class="col-lg-4">
                    <form method="post" action="{{ route('admin.singleFormTransaction') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control email" name="email" value=""
                                        placeholder="Email" id="email">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Type</label>
                                    <select class="form-select" name="type" required id="transaction_type">
                                        <option value="">Select</option>
                                        <option value="withdrawal">Withdrawal</option>
                                        <option value="deposit">Deposit</option>
                                        <option value="internal_transfer">Internal Transfer</option>
                                        <option value="bonus_in">Bonus In</option>
                                        <option value="bonus_out">Bonus Out</option>
                                        <option value="ib_withdrawal">IB Withdrawal</option>
                                    </select>
                                </div>
                                <div class="mb-3 trade-id-wrapper">
                                    <label class="form-label">Trade ID</label>
                                    <select id="from_account" class="form-select trade-id liveAccountsDD" name="trade_id"
                                        required>
                                        <option value="">Select</option>
                                        {{-- @foreach ($live_accounts as $account)
                                            <option value="{{ $account->trade_id }}" data-email="{{ $account->email }}">
                                                {{ $account->trade_id }} ({{ $account->name }}-{{ $account->email }})
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="mb-3 it-to-account d-none">
                                    <label class="form-label">To Account</label>
                                    <select id="to_account" class="form-select liveAccountsDD to-account" name="to_account">
                                        <option value="">Select</option>
                                        {{-- @foreach ($live_accounts as $account)
                                            <option value="{{ $account->trade_id }}" data-email="{{ $account->email }}">
                                                {{ $account->trade_id }} ({{ $account->name }}-{{ $account->email }})
                                            </option>
                                        @endforeach --}}
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Amount</label>
                                    <input type="number" class="form-control" name="amount" value=""
                                        placeholder="Amount" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3" placeholder="Description" required></textarea>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <input type="submit" class="btn btn-primary" value="Create Transaction" name="update">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-8 ">
                    <div class="card custom-card ">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mt-1 mb-4">
                                <div class="d-flex mx-1 w-75">
                                    <input type="text" class="form-control tdStartDate" name="dtStartDate"
                                        id="dtStartDate" placeholder="Start Date">
                                    <input type="text" class="ms-2 form-control tdEndDate" name="dtEndDate"
                                        id="dtEndDate" placeholder="End Date">
                                    <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="tableUtilities" class="ajaxDataTable table table-bordered text-nowrap w-100">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endSection
@section('scripts')
    <script>
        // $(document).ready(function() {

        // var email = $('#email').val();

        function select2init() {
            $('.liveAccountsDD').select2({
                ajax: {
                    url: '{{ route('admin.getUtilityAccounts') }}',
                    dataType: 'json',
                    data: function(params) {
                        var searchValue = params.term;
                        console.log('Search Input Value:', searchValue);
                        return {
                            term: searchValue,
                            email: $('#email').val()
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
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
        select2init();
        $('#email').on("change", function() {
            $(".liveAccountsDD").select2("destroy");
            select2init();
        });

        $(document).on("change", "#transaction_type", function() {
            select2init();
            var type = $(this).val();
            if (type == 'internal_transfer') {
                $(".it-to-account").removeClass('d-none');
                $('.to-account').attr('required', true);
            } else {
                $(".it-to-account").addClass('d-none');
                $('.to-account').attr('required', false);
            }
            if (type == 'ib_withdrawal') {
                $(".trade-id-wrapper").addClass('d-none');
                $('.trade-id').attr('required', false);
                $('.email').attr('required', true);
            } else {
                $(".trade-id-wrapper").removeClass('d-none');
                $('.trade-id').attr('required', true);
                $('.email').attr('required', false);
            }
        });
        $('#tableUtilities').DataTable({
            order: [
                [0, "desc"]
            ],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: function(d) {
                    d.action = 'getSingleFormTransactions';
                    d.startdate = $('#dtStartDate').val();
                    d.enddate = $('#dtEndDate').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id',
                    title: "ID"
                },
                {
                    data: 'email',
                    name: 'email',
                    title: "Email",
                    render: function(data, row, row_data) {
                        var return_data = "<a href='/admin/client_details?id=" +
                            row_data
                            .enc_id +
                            "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                            row_data.fullname +
                            "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                            row_data.email + "</span></div></div></div></a>";
                        return return_data;
                    }
                },
                {
                    data: 'transaction_id',
                    name: 'transaction_id',
                    title: "Transaction ID",
                    render: function(data, row, row_data) {
                        var transactionId = row_data.enc_transaction_id;
                        var typeToLink = {
                            deposit: '/admin/trading_deposit_details?id=',
                            withdrawal: '/admin/trading_withdrawal_details?id=',
                            internal_transfer: '/admin/internal_transfer_details?id=',
                            ib_withdrawal: '/admin/ib_withdrawal_details?id=',
                        };
                        var link = typeToLink[row_data.type];
                        if (link) {
                            var return_data = "<a class='text-decoration-underline' href='" + link +
                                transactionId + "'>" + row_data
                                .transaction_id + "</a>";
                        } else {
                            var return_data = row_data.transaction_id;
                        }
                        return return_data;
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    title: "Amount",
                    render: function(data, type, row) {
                        if (row.amount != 0 && row.amount != '') {
                            return "$" + row.amount;
                        } else {
                            return "";
                        }
                    }
                },
                {
                    data: 'type',
                    name: 'type',
                    title: "Type",
                    render: function(data, type, row) {
                        if (row.type) {
                            let updatedString = row.type
                                .replace('_', ' ')
                                .replace(/_/g, ' ')
                                .toLowerCase()
                                .replace(/\b\w/g, char => char
                                    .toUpperCase());
                            return updatedString;
                        }
                        return '';
                    }

                },
                {
                    data: 'trade_id',
                    name: 'trade_id',
                    title: "Trade ID",
                    render: function(data, type, row) {
                        var html = '';
                        if (row.type == 'internal_transfer') {
                            html = `<div class="text-muted fs-10">From Account:</div>`;
                            html += `<a href="/admin/view_account_details?id=${row.enc_trade_id}"> <div class="row align-items-center">
                                                    <div class="col-lg-3"><img src="/assets/images/mt5.png"
                                                            alt="user-image" style="max-width:25px" class="rounded"></div>
                                                    <div class="col-lg-8">
                                                        <span class="mb-0 ps-1"><span
                                                                class="text-truncate">${row.trade_id}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>`;
                            if (row.to_account != null) {
                                html += `<div class="text-muted fs-10 mt-2">To Account:</div>`;
                                html += `<a href="/admin/view_account_details?id=${row.enc_to_account}"> <div class="row align-items-center">
                                                        <div class="col-lg-3"><img src="/assets/images/mt5.png"
                                                                alt="user-image" style="max-width:25px" class="rounded"></div>
                                                        <div class="col-lg-8">
                                                            <span class="mb-0 ps-1"><span
                                                                    class="text-truncate">${row.to_account}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </a>`;
                            }
                        } else {
                            if (row.trade_id != null) {
                                var html = `<a href="/admin/view_account_details?id=${row.enc_trade_id}"> <div class="row align-items-center">
                                                    <div class="col-lg-3"><img src="/assets/images/mt5.png"
                                                            alt="user-image" style="max-width:25px" class="rounded"></div>
                                                    <div class="col-lg-8">
                                                        <span class="mb-0 ps-1"><span
                                                                class="text-truncate">${row.trade_id}</span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>`;
                            }
                        }
                        return html;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    title: "Created At"
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                    title: 'Created By'
                }
            ],
            initComplete: function() {
                var needs = [4];
                this.api().columns().every(function(index) {
                    if (needs.indexOf(index) == -1) {
                        return false;
                    }
                    var column = this;
                    var select = $(
                            '<select class="d-block form-control form-control-sm mt-2 text-capitalize w-auto"><option value="">All</option></select>'
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
        $(document).on("click", ".dtDateFilter", function() {
            $('#tableUtilities').DataTable().ajax.reload();
        });

        $('[name="to_account"],#from_account').change(function() {
            var from_account = $("#from_account").val();
            var to_account = $("#to_account").val();
            if (to_account !='' && to_account == from_account) {
                Swal.fire({
                    icon: 'warning',
                    title: "From and To Account Cant Be Same",
                }).then(() => {
                    $('[name="to_account"]').val("").trigger("change");
                    $('#from_account').val("").trigger("change");
                });
            }
        });
    </script>
@endsection
