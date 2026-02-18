@extends('layouts.admin.admin')
@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">Activity Log</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Activity Log</li>
                </ol>
            </div>
            <!-- PAGE-HEADER END -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableIpActivity" class="ajaxDataTable table table-bordered text-nowrap w-100">
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

            $('#tableIpActivity').DataTable({
                order: [
                    [0, "desc"]
                ],
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: {
                        action: 'getIPLogs',
                    },
                },
                dom: '<"row" <"col"B><"col text-center"l><"col"f>><"row"<"col"Q>><"row"<"col"t>><"row"<"col"i><"col"p>>',
                buttons: [{
                    extend: 'excel',
                    text: 'Excel',
                    title: "<?=settings()['admin_title'] ?>- IP Logs",
                }],
                columns: [
                    {
                        data: "id",
                        title: "#"
                    },
                    {
                        data: "fullname",
                        title: "Name"
                    },
                    {
                        data: "email",
                        title: "Email"
                    },
                    {
                        data: "action",
                        title: "Action"
                    },
                    {
                        data: "ip",
                        title: "IP"
                    },
                    {
                        data: "created_date_js",
                        title: "Log DateTime(UTC)"
                    }
                ]
            });
        });
    </script>
@endsection
