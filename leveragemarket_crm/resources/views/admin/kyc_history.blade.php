@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">KYC History</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">KYC History</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex mx-1 ">
                                <select class="form-select form-control me-1" name="dtOption" id="dtOption">
                                    <option value="registered_date_js">Date Added</option>
                                    <option value="Admin_Remark_Date">Date Approved</option>
                                </select>
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
                    [0, "desc"]
                ],
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: function(d) {
                        d.action = 'getKYCHistory';
                        d.startdate = $('#dtStartDate').val();
                        d.enddate = $('#dtEndDate').val();
                        d.option = $('#dtOption').val();
                    }
                },
                columns: [{
                        data: 'id',
                        title: '#'
                    },
                    {
                        data: 'fullname',
                        title: 'Client',
                        render: function(data, row, row_data) {
                            var return_data = "<a href='/admin/client_details?id=" + row_data
                                .enc_id +
                                "'><div class='d-flex align-items-center'><div class='me-2'><svg xmlns='http://www.w3.org/2000/svg' width='28' height='28' viewBox='0 0 24 24' fill='none' stroke='#000000' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' size='28' color='#000000' class='tabler-icon tabler-icon-user-square-rounded'><path d='M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z'></path><path d='M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z'></path><path d='M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05'></path></svg></div><div><div class='lh-1'><span>" +
                                row_data.fullname +
                                "</span></div><div class='lh-1'><span class='fs-11 text-muted'>" +
                                row_data.email + "</span></div></div></div></a>";
                            return return_data;
                        }
                    },
                    {
                        data: 'summary',
                        title: 'KYC Summary',
                        render: function(data) {
                            var dv = "";
                            var r_data = data.split("#");
                            r_data.forEach(function(item) {
                                name = item.split("=")[0];
                                status = item.split("=")[1];
                                if (status == "0") sta = "info";
                                if (status == "1") sta = "success";
                                if (status == "2") sta = "danger";
                                dv +=
                                    "<button class='mt-1 ibToggle badge btn-sm btn btn-outline-" +
                                    sta + " d-block'>" + name + "</button>";
                            });
                            return dv;

                        }
                    },
                    {
                        data: 'date',
                        title: 'Latest Sub. Date',
                        render: function(data, type, row) {
                            var dateTime = row.date.split(' ');
                            var date = dateTime[0];
                            var time = dateTime[1];
                            var return_data = "<div class='d-grid'><div class='date'>" + date +
                                "</div><div class='time text-muted'>" + time + "</div></div>";
                            return return_data;
                        }
                    },
                    {
                        data: 'approved_date',
                        title: 'Approved Date',
                        render: function(data, type, row) {
                            date = '';
                            time = '';;
                            if (row.approved_date != null) {
                                var dateTime = row.approved_date.split(' ');
                                var date = dateTime[0];
                                var time = dateTime[1];
                            }
                            var return_data = "<div class='d-grid'><div class='date'>" + date +
                                "</div><div class='time text-muted'>" + time + "</div></div>";
                            return return_data;
                        }
                    },
                    {
                        data: 'approved_email',
                        title: 'Approved By',
                        render: function(data, row, row_data) {
                            var return_data = "";
                            if (row_data.approved_email != null) {
                                var return_data =
                                    "<div class='d-flex align-items-center'><div><div class='lh-1'><span>" +
                                    row_data.approved_name +
                                    "</span></div></div></div>";
                            }
                            return return_data;
                        }
                    },
                    {
                        data: 'enc_id',
                        title: 'Action',
                        render: function(data) {
                            var rend_data =
                                '<a class="btn btn-sm btn-primary" href="/admin/kyc_details?id=' +
                                data + '">View</a>';
                            return rend_data;
                        }
                    },
                ]
            });
        });
        $(document).on("click", ".dtDateFilter", function() {
            $('#tableWalletDeposit').DataTable().ajax.reload();
        });
    </script>
@endsection
