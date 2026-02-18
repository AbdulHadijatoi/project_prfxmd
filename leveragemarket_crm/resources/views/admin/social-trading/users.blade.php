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
                    <h6 class="modal-title" id="updateRoleModalLabel"><span id="updateUser"></span> - Password Update</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="/admin/social-trading/update/user" class="ajaxSubmit">
                    @csrf
                    <input type="hidden" name="user_id" id="updateUserId">
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

    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Social Trading - Users</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Social Trading</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </div>
            
            <div class="row">
                <div class="col-xl-7 col-lg-7">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableUsers" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="modal-title" id="addUserModalLabel">Add User</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="/admin/social-trading/store/user" class="ajaxSubmit"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="row gy-4">
                                        <div class="col-12">
                                            <label for="input-label" class="form-label">User</label>
                                            <select class="form-control usersDD" name="user_id" required></select>
                                        </div>
                                        <div class="col-12">
                                            <label for="input-label" class="form-label">Password</label>
                                            <input type="text" class="form-control" name="password" required>
                                        </div>
                                        <div class="col-12">
                                            <label for="input-file" class="form-label">Description</label>
                                            <select name="role" class="form-control" required>
                                                <option value="2">Trader</option>
                                                <option value="1">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                    <button type="button" class="btn btn-secondary ms-2"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        

        $(document).ready(function() {
            window.dTtable = $('#tableUsers').on("draw.dt", dTSelection).DataTable({
                // order: [[0, "desc"]],
                "ajax": {
                    "url": "/admin/social-trading/getList/users",
                    "type": "GET"
                },
                columns: [{
                        data: "id",
                        title: "Action",
                        render: function(data) {
                            return "<button class='btn edit-btn'><i class='fa fa-edit'></i></button>";
                        }
                    },
                    {
                        data: 'id',
                        title: '#'
                    },
                    {
                        data: 'login',
                        title: 'Login'
                    },
                    {
                        data: 'role',
                        title: 'Role',
                        render: function(data) {
                            if (data == 1) {
                                return "Admin";
                            } else if (data == 2) {
                                return "Trader";
                            } else {
                                return "No Role #" + data;
                            }
                        }
                    }
                ],
                columnDefs: [{
                    "width": "20px",
                    "targets": 0
                }],
                order: [[1, 'desc']]
            });
        });


        function dTSelection() {
            $(document).on("click", ".edit-btn", function() {                
                var data = dTtable.row($(this).closest("tr")).data();
                // console.log("Data",data.login);
                $("#updateUser").html(data.login);
                $("#updateUserId").val(data.id);
                $("#updateRoleModal").modal("show");
            });
        }
    </script>
@endsection
