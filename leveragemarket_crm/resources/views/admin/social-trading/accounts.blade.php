@extends('layouts.admin.admin')
@section('content')
    <style>
        tr.inactive {
            background-color: #f8f9fa;
            color: #6c757d;
            opacity: 0.6;
        }

        span.select2.select2-container.select2-container--default.select2-container--below {
            border: 1px solid #e2e6f1;
            border-radius: 0.25rem;
        }
    </style>


    <!-- Update Role Modal -->
    <div class="modal fade" id="updateRoleModal" tabindex="-1" aria-labelledby="updateRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="updateRoleModalLabel">Add Account</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="/admin/social-trading/create/user" class="ajaxSubmit">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-12">
                                <label for="input-label" class="form-label">Password</label>
                                <input type="text" class="form-control" name="st_password" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Role Modal -->
    <div class="modal fade" id="createAccountModel" tabindex="-1" aria-labelledby="createAccountModelLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="createAccountModelLabel">Create ST Account
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="/admin/social-trading/store/account" class="ajaxSubmit"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-12">
                                <label for="input-label" class="form-label">User</label>
                                <select class="form-control stusersDD" name="user_id" required></select>
                            </div>
                            <div class="col-12">
                                <label for="input-label" class="form-label">MT5 Account</label>
                                <select class="form-control liveaccsDD" name="login" required></select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Role</label>
                                <select class="form-control" name="canBeLeader">
                                    <option value="" disabled selected default>-- Role --</option>
                                    <option value="0">Follower</option>
                                    <option value="1">Leader</option>
                                </select>
                            </div>
                        </div>

                        {{-- "leaderBio": "Test", --}}
                        <div class="leader row">
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader Fee Subscription Amount</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    name="leaderFeeSubscriptionAmount">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader Following Fee Percent</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    name="leaderFollowingFeePercent">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader Following Min Free Margin</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    name="leaderFollowingMinFreeMargin">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader Performance Fee Type</label>
                                <select class="form-control" name="leaderPerformanceFeeType">
                                    <option value="1">Daily</option>
                                    <option value="2">Weekly</option>
                                    <option value="3">Monthly</option>
                                    <option value="4">Off</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader Performance Fee Percent</label>
                                <input type="number" min="0" step="0.01" class="form-control"
                                    name="leaderPerformanceFeePercent">
                            </div>
                        </div>
                        <div class="follower row">
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Leader</label>
                                <select class="form-control stleaderDD" name="leader_id"></select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Mode</label>
                                <select class="form-control" name="mode">
                                    <option value="" default disabled selected>--Select Mode--</option>
                                    <option value="1">Scaled</option>
                                    <option value="4">Fixed</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Mode Parameter</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="modeParameter">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="input-label" class="form-label">Stop Loss</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="stopLoss">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                        <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Social Trading - Accounts</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Social Trading</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Accounts</li>
                </ol>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#createAccountModel">
                    Add Account
                </button>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableAccounts" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
         function dTSelection() {
            $(document).on("click", ".edit-btn", function() {
                var data = dTtable.row($(this).closest("tr")).data();
                // console.log("Data",data.login);
                $("#updateUser").html(data.login);
                $("#updateUserId").val(data.id);
                $("#updateRoleModal").modal("show");
            });
        }
        $(document).ready(function() {
            window.dTtable = $('#tableAccounts').on("draw.dt", dTSelection).DataTable({
                // order: [[0, "desc"]],
                "ajax": {
                    "url": "/admin/social-trading/getList/accounts",
                    "type": "GET"
                },
                columns: [

                    {
                        data: "login",
                        title: "Action",
                        "render": function(data) {
                            return "<button class='btn edit-btn'><i class='fa fa-edit'></i></button>";
                        }
                    },
                    {
                        data: 'login',
                        title: '#'
                    },
                    {
                        "data": "canBeLeader",
                        "title": "canBeLeader"
                    },
                    {
                        "data": "leaderBio",
                        "title": "leaderBio",
                        "render": function(data) {
                            if (data) {
                                return data
                            } else return "-";
                        }
                    },
                    {
                        "data": "leaderFeeSubscriptionAmount",
                        "title": "FeeSubscriptionAmount",
                        "render": function(data) {
                            if (data) {
                                return data
                            } else return "-";
                        }
                    },
                    {
                        "data": "leaderFollowingFeePercent",
                        "title": "FollowingFeePercent",
                        "render": function(data) {
                            if (data) {
                                return data
                            } else return "-";
                        }
                    },
                    {
                        "data": "leaderFollowingMinFreeMargin",
                        "title": "FollowingMinFreeMargin",
                        "render": function(data) {
                            if (data) {
                                return data;
                            } else return "-";
                        }
                    },
                    {
                        "data": "leaderPerformanceFeeType",
                        "title": "PerformanceFeeType",
                        "render": function(data) {
                            if (data == 1) {
                                return "Daily";
                            } else if (data == 2) {
                                return "Weekly";
                            } else if (data == 3) {
                                return "Monthly";
                            } else if (data == 4) {
                                return "Off";
                            } else {
                                return "-";
                            }
                        }
                    },
                    {
                        "data": "leaderPerformanceFeePercent",
                        "title": "PerformanceFeePercent",
                        "render": function(data) {
                            if (data) {
                                return data
                            } else return "-";
                        }
                    },
                ],
                columnDefs: [{
                    "width": "20px",
                    "targets": 0
                }],
                order: [
                    [1, 'desc']
                ]
            });
        });

        $("#createAccountModel .follower,#createAccountModel .leader").addClass("d-none");
        $("#createAccountModel .follower .form-control,#createAccountModel .leader .form-control").removeAttr("required");

        $("#createAccountModel [name='canBeLeader']").change(function() {
            $("#createAccountModel .leader .form-control,#createAccountModel .follower .form-control").val("")
                .trigger("change");
            if ($(this).val() == 0) {
                $("#createAccountModel .follower").removeClass("d-none");
                $("#createAccountModel .follower .form-control").attr("required", "true");
                $("#createAccountModel .leader").addClass("d-none");
                $("#createAccountModel .leader .form-control").removeAttr("required");
            } else {
                $("#createAccountModel .leader").removeClass("d-none");
                $("#createAccountModel .leader .form-control").attr("required", "true");
                $("#createAccountModel .follower").addClass("d-none");
                $("#createAccountModel .follower .form-control").removeAttr("required");
            }
        });

        function select2init() {
            $('.liveaccsDD').select2({
                dropdownParent: $('#createAccountModel'),
                ajax: {
                    url: '{{ route('admin.getUtilityAccounts') }}',
                    dataType: 'json',
                    data: {
                        "email": $('[name="user_id"] option:selected').text()
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
                },
                destory: true
            });
        }
        select2init();

        $("#createAccountModel [name='user_id']").change(select2init);



        function select2leader() {
            $.ajax({
                type: 'GET',
                url: '/admin/social-trading/getList/accounts',
                dataType: 'json',
                success: function(returnedData) {
                    var res = $.map(returnedData.data, function(item) {
                        if (item.canBeLeader == true) {
                            return {
                                text: item.login + " [" + item.leaderBio + "]",
                                id: item.id
                            }
                        }
                    })
                    res.push({
                        text: "Select the leader",
                        id: ""
                    })
                    $('.stleaderDD').select2({
                        placeholder: "Select the Leader",
                        dropdownParent: $('#createAccountModel .follower'),
                        data: res,
                        destroy: true
                    });
                    $('.stleaderDD').val("").trigger('change');
                    // $('.stleaderDD').select2('open');
                }
            });
            // $('.stleaderDD').select2({
            //     dropdownParent: $('#createAccountModel'),
            //     ajax: {

            //         url: "/admin/social-trading/getList/accounts",
            //         dataType: 'json',
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data.data, function(item) {
            //                     // if (item.canBeLeader == true) {
            //                     // console.log("Leader: "+item.login);
            //                     return {
            //                         text: item.login + " [" + item.leaderBio + "]",
            //                         id: item.id
            //                     }
            //                     // }
            //                 })
            //             };
            //         }
            //     }
            // });
        }

        select2leader();

       
    </script>
@endsection
