@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="modal fade" id="addTournamentModal" tabindex="-1" aria-labelledby="addTournamentModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="addTournamentModalLabel">Add Tournament</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.tournaments') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control " name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control " name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Entry Fee</label>
                                            <input type="number" class="form-control " name="entry_fee" required
                                                step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea type="text" class="form-control editor " name="desc" required rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Email Description</label>
                                            <textarea type="text" class="form-control editor" name="email_description" required rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Registration Starts At</label>
                                            <input type="datetime-local" class="form-control " name="starts_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Registration Ends At</label>
                                            <input type="datetime-local" class="form-control " name="ends_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">MT5 Group</label>
                                            <select name="account_type" id="account_type" class="form-control"
                                                required="required">
                                                <option value="">Select</option>
                                                @foreach ($acc_priority as $acc)
                                                    <option value="{{ $acc->ac_index }}">{{ $acc->ac_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Leverage</label>
                                            <input type="number" class="form-control " name="leverage" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Visible To</label>
                                            <select name="shows_on" id="shows_on" class="form-control shows_on"
                                                required="required">
                                                <option value="all">All</option>
                                                <option value="groups">Groups</option>
                                                <option value="users">Users</option>
                                                <option value="user_groups">User Groups</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Users / Groups</label>
                                            <select name="shows_list[]" id="shows_list"
                                                class="form-control select2-groups shows_list" multiple required="required">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Image</label>
                                            <input class="form-control" type="file" id="image" name="image"
                                                required="required" accept="image/jpeg, image/png" />
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-4">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="status" id="status" value="1">
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-4">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="send_notification" value="0">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="send_notification" id="send_notification" value="1">
                                            <label class="form-check-label" for="status">Send Notification</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                @if (session('userData')['role_id'] == 1)
                                    <input type="submit" class="btn btn-primary" name="action" value="Add">
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="updateTournamentModal" tabindex="-1"
                aria-labelledby="updateTournamentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="updateTournamentModalLabel">Update Tournament</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form id="updateTournamentForm" method="POST" action="{{ route('admin.updateTournament') }}">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" id="enc_id" name="enc_id">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" class="form-control " name="name" id="name"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Date</label>
                                            <input type="date" class="form-control " name="date" id="date"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Entry Fee</label>
                                            <input type="number" class="form-control " name="entry_fee" id="entry_fee"
                                                required step="0.01" min="0" placeholder="0.00">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea type="text" class="form-control editor " name="desc" id="description" required rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="mb-3">
                                            <label class="form-label">Email Description</label>
                                            <textarea type="text" class="form-control editor" name="email_description" id="email_description" required
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Registration Starts At</label>
                                            <input type="datetime-local" class="form-control " name="starts_at"
                                                id="starts_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Registration Ends At</label>
                                            <input type="datetime-local" class="form-control " name="ends_at"
                                                id="ends_at">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">MT5 Group</label>
                                            <select name="account_type" id="account_type" class="form-control"
                                                required="required">
                                                <option value="">Select</option>
                                                @foreach ($acc_priority as $acc)
                                                    <option value="{{ $acc->ac_index }}">{{ $acc->ac_group }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Leverage</label>
                                            <input type="number" class="form-control " name="leverage" id="leverage"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Visible To</label>
                                            <select name="shows_on" id="shows_on" class="form-control shows_on"
                                                required="required">
                                                <option value="all">All</option>
                                                <option value="groups">Groups</option>
                                                <option value="users">Users</option>
                                                <option value="user_groups">User Groups</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">Users / Groups</label>
                                            <select name="shows_list[]" id="shows_list"
                                                class="form-control select2-groups shows_list" multiple
                                                required="required">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="status" value="0"
                                                class="status_checkbox status_hidden" id="status" >
                                            <input class="form-check-input status_checkbox" type="checkbox"
                                                role="switch" name="status"  value="1">
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input type="hidden" name="send_notification" id="send_notification"
                                                value="0"
                                                class="send_notification_checkbox send_notification_hidden">
                                            <input class="form-check-input send_notification_checkbox" type="checkbox"
                                                role="switch" name="send_notification" value="1">
                                            <label class="form-check-label" for="status">Send Email Notification</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                @if (session('userData')['role_id'] == 1)
                                    <input type="submit" class="btn btn-primary" name="action" value="Update">
                                @endif
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="page-header">
                <h1 class="page-title">Tournaments</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tournaments</li>
                </ol>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#addTournamentModal">
                    Add New Tournament
                </button>
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
        // $(document).ready(function() {
        //     CKEDITOR.replace('desc');
        //     CKEDITOR.replace('email_description');
        // });
        $('#tableTournaments').on("draw.dt", dTSelection).DataTable({
            order: [
                [0, "desc"]
            ],
            "ajax": {
                "url": "/admin/ajax",
                "type": "GET",
                data: {
                    action: 'getTournaments',
                },
            },
            columns: [{
                    data: 'id',
                    title: '#'
                },
                {
                    data: 'name',
                    title: 'Name'
                },
                {
                    data: 'date',
                    title: 'Date'
                },
                {
                    data: 'entry_fee',
                    title: 'Entry Fee'
                },
                {
                    data: 'starts_at',
                    title: 'Starts At'
                },
                {
                    data: 'ends_at',
                    title: 'Ends At'
                },
                {
                    data: 'shows_on',
                    title: 'Shows To'
                },
                {
                    data: 'status',
                    title: 'Status'
                },
                {
                    data: 'created_at',
                    title: 'Created At'
                },
                {
                    data: 'action',
                    title: 'Action',
                    render: function(data, row, row_data) {
                        var return_data = '';
                        var admin_role_id = @json(session('userData')['role_id']);
                        if (admin_role_id == 1 || true) {
                            return_data += '<a data-id="' + row_data.enc_id +
                                '" class="update-tournament" data-bs-toggle="modal" data-bs-target="#updateTournamentModal" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit text-secondary"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path><path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path><path d="M16 5l3 3"></path></svg></a>';
                        }
                        return return_data;
                    },
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('.send_notification_checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                $('.send_notification_hidden').val(1);
            } else {
                $('.send_notification_hidden').val(0);
            }
        });
        $('.status_checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                $('.status_hidden').val(1);
            } else {
                $('.status_hidden').val(0);
            }
        });

        function dTSelection() {
            $(document).off("click", ".update-tournament");
            $(document).on("click", ".update-tournament", function() {
                let id = $(this).data("id");
                $.ajax({
                    url: "/admin/ajax",
                    type: "GET",
                    data: {
                        action: 'getTournamentDetails',
                        id: id
                    },
                    success: function(response) {
                        response = JSON.parse(response.trim());
                        $.each(response, function(key, value) {
                            if (key == 'shows_on') {
                                $('#updateTournamentForm [name="' + key + '"]').val(value);
                                tournamentOnChange(value, "update");
                            } else if (key == 'shows_list') {
                                var interval = setTimeout(function() {
                                    var valuesArray = String(value).split(',');
                                    $('#updateTournamentForm #' + key).val(valuesArray)
                                        .trigger('change');
                                }, 1000);
                                // } else if (key == 'description') {
                                //     CKEDITOR.instances['desc'].setData(value);
                                // } else if ( key == 'email_description') {
                                //     CKEDITOR.instances['email_description'].setData(value);
                            } else {
                                $('#updateTournamentForm #' + key).val(value);
                            }
                        });
                        $('#updateTournamentForm input[name="status"]').prop('checked', response.status == 1);
                        $('#updateTournamentForm  input[name="send_notification"]').prop('checked',
                            response
                            .send_notification == 1);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });

            });
        }
        $("#addTournamentModal .shows_on").change(function() {
            let val = $(this).val();
            tournamentOnChange(val, 'add');
        });
        $("#updateTournamentModal .shows_on").change(function() {
            let val = $(this).val();
            tournamentOnChange(val, 'update');
        });
        $('.shows_on').trigger('change');

        function tournamentOnChange(type, modal) {
            $("#" + modal + "TournamentModal .shows_list").select2();
            $("#" + modal + "TournamentModal .shows_list").empty();
            $("#" + modal + "TournamentModal .shows_list").val(null).trigger("change");
            $("#" + modal + "TournamentModal .shows_list").attr("required", "true");
            if (type == "groups") {
                var show_type = "getListOfGroups";
            } else if (type == "users") {
                var show_type = "getListOfUsers";
            } else if (type == "user_groups") {
                var show_type = "getListOfUserGroups";
            } else {
                $("#" + modal + "TournamentModal .shows_list").attr("disabled");
                $("#" + modal + "TournamentModal .shows_list").removeAttr("required");
                return;
            }
            $.ajax({
                url: '/admin/ajax?action=' + show_type,
                type: 'GET',
                success: function(data) {
                    data = JSON.parse(data);
                    data.forEach(function(item) {
                        $("#" + modal + "TournamentModal .shows_list").append($('<option>', {
                            value: item.id,
                            text: item.text
                        }));
                        $("#" + modal + "TournamentModal .shows_list").select2({
                            dropdownParent: $('#' + modal + 'TournamentModal')
                        });
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error occurred:", textStatus, errorThrown);
                }
            });
        }
        $('#addTournamentModal').on('show.bs.modal', function(e) {
            $("#addTournamentModal .shows_list").select2({
                dropdownParent: $('#addTournamentModal')
            });
        });
    </script>
@endsection
