@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Tournament Live Accounts</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tournaments</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableTournaments" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#tableTournaments').on("draw.dt", dTSelection).DataTable({
            order: [
                [0, "desc"]
            ],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: {
                    action: 'getTournamentLiveAccounts',
                },
            },
            columns: [{
                    data: 'id',
                    title: '#'
                },
                {
                    data: 'name',
                    title: 'Tournament'
                },
                {
                    data: 'email',
                    title: 'Client',
                    render: function(data, type, row) {
                        var return_data = `<tr> <td>
                                                <a href='/admin/client_details?id=` + row.enc_id + `'>
                                                    <div class='d-flex align-items-center'>
                                                        <div class='me-2'><svg xmlns='http://www.w3.org/2000/svg'
                                                                width='28' height='28' viewBox='0 0 24 24'
                                                                fill='none' stroke='#000000' stroke-width='1.5'
                                                                stroke-linecap='round' stroke-linejoin='round'
                                                                size='28' color='#000000'
                                                                class='tabler-icon tabler-icon-user-square-rounded'>
                                                                <path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path>
                                                                <path
                                                                    d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'>
                                                                </path>
                                                                <path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'>
                                                                </path>
                                                            </svg></div>
                                                        <div>
                                                            <div class='lh-1'><span>` + row.fullname + `</span>
                                                            </div>
                                                            <div class='lh-1'><span
                                                                    class='fs-11 text-muted'>` + row.email + `</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>`;
                        return return_data;
                    }
                },
                {
                    data: 'trade_id',
                    title: 'Trade ID',
                    render: function(data, type, row) {
                        var return_data = `<a href="/admin/tournament_account_details?id=`+row.enc_acc+`">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto pe-0"><img src="/assets/images/mt5.png"
                                                                alt="user-image" class="wid-50 hei-50 rounded"></div>
                                                        <div class="col ps-2">
                                                            <h6 class="mb-0"><span
                                                                    class="text-truncate w-100">`+row.trade_id+`</span>
                                                            </h6>
                                                            <p class="text-muted f-12 mb-0"><span
                                                                    class="text-truncate w-100">`+row.ac_group+`</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>`;
                        return return_data;
                    }
                },
                {
                    data: 'leverage',
                    title: 'Leverage'
                },
                {
                    data: 'balance',
                    title: 'Balance'
                },
                {
                    data: 'balance',
                    title: 'Action',
                    render: function(data, type, row) {
                        let html = '';
                        html += `<a href="/admin/tournament_account_details?id=`+row.enc_acc+`"><span class="badge text-danger" data-bs-toggle="tooltip" title="View Client"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-eye"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg></span></a>`;
                            return html;
                    }
                }
            ]
        });

        function dTSelection() {}
    </script>
@endsection
