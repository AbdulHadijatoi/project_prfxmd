@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Bonus - Creation</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page">Bonus</li>
                    <li class="breadcrumb-item active" aria-current="page">Creation</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form method="post" enctype="multipart/form-data" action="{{route('admin.bonusStore')}}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Name</label>
                                            <input type="text" class="form-control " name="bonus_name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Code</label>
                                            <input type="text" class="form-control " name="bonus_code" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <input type="text" class="form-control " name="bonus_desc" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Starts At</label>
                                            <input type="datetime-local" class="form-control " name="bonus_starts_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Ends At</label>
                                            <input type="datetime-local" class="form-control " name="bonus_ends_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Usage Limit</label>
                                            <input type="number" class="form-control" min="1" value="1"
                                                required step="1" name="bonus_limit" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Accessible For</label>
                                            <select name="bonus_accessable[]" class="form-control select2" multiple
                                                required="required">
                                                <option value="first_deposit">First Deposit</option>
                                                <option value="welcome_bouns">Welcome Bonus</option>
												<option value="regular_bouns">Regular Bonus</option>
                                                <option value="referred_users">Referred Users</option>
                                                <option value="direct_users">Direct Users</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Show To</label>
                                            <select name="bonus_shows_on" id="bonus_shows_on" class="form-control select2"
                                                required="required">
                                                <option value="all">All</option>
                                                <option value="groups">Groups</option>
                                                <option value="users">Users</option>
                                                <option value="user_groups">User Groups</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-5">
                                        <div class="mb-3">
                                            <label class="form-label">Users / Groups</label>
                                            <select name="bonus_show_list[]" id="bonus_show_list"
                                                class="form-control select2-groups" multiple required="required">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Type</label>
                                            <select name="bonus_type" class="form-control" required="required">
                                                <option default disabled selected></option>
                                                <option value="percentage">Percentage</option>
                                                <option value="flat">Flat / USD</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Value (% / USD)</label>
                                            <input type="number" class="form-control" step="0.01" min="0.1"
                                                name="bonus_value" required>
                                        </div>
                                    </div>
									<div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Bonus Images</label>
                                            <input type="file" class="form-control" name="bonus_images" accept="image/*" />
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Expiry date</label>
                                            <input type="date" class="form-control" name="expiry_date"  required/>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-lg-2 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked value="1"
                                                role="switch" name="status" id="status">
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end pb-0">
                                    <input type="submit" class="btn btn-primary" value="Create Bonus" name="action" />
                                </div>
                            </div>
                    </form>
                </div>


            </div>
        </div>
    </div>
    <script>
        $(".select2").select2();
        $("#bonus_shows_on").change(function() {
            var type = $(this).val();
            $("#bonus_show_list").select2();
            $("#bonus_show_list").val("").trigger("change");
            $("#bonus_show_list").attr("required", "true");
            if (type == "groups") {
                var show_type = "getListOfGroups";
            } else if (type == "users") {
                var show_type = "getListOfUsers";
            } else if (type == "user_groups") {
                var show_type = "getListOfUserGroups";
            } else {
                $("#bonus_show_list").attr("disabled");
                $("#bonus_show_list").removeAttr("required");
                return;
            }
            $("#bonus_show_list").select2({
                ajax: {
                    url: '/admin/ajax?action=' + show_type,
                    data: function(params) {
                        var query = {
                            search: params.term,
                        }
                        return query;
                    },
                    processResults: function(data) {
                        // console.log("DATA: ",data);
                        var res = [];
                        res["results"] = JSON.parse(data);
                        console.log("RES==> ", res);
                        return res;
                    }
                },
                destory: true
            });

        })

        $("#bonus_shows_on").trigger("change");
    </script>
@endsection
