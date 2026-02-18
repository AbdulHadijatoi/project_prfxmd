@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Configuration</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">PAMM</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Configuration</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableConfiguration" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#tableConfiguration').on("draw.dt", dTSelection).DataTable({
            order: [
                [0, "desc"]
            ],
            "ajax": {
                "url": "/admin/pamm/get_manager_configuration",
                "type": "GET",
                data: {
                },
            },
            columns: [{
                    data: 'id',
                    title: '#'
                },
                {
                    data: 'name',
                    title: 'Name',
                },
                {
                    data: 'managerCount',
                    title: 'Manager Count'
                },
            ]
        });

        function dTSelection() {

        }
    </script>
@endsection
