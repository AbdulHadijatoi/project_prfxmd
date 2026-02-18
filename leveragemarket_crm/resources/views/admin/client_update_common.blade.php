<div class="modal fade" id="updateIbModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="updateIbModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.updateIB') }}" id="ibUpdateForm" method="post">
                @csrf
                <input type="hidden" class="client_id" name="client_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateIbModalLabel">Update IB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-card card mb-0" style="max-height:500px;overflow-y: auto;">
                    <div class="d-flex align-items-center card-header w-100">
                        <div class="me-2">
                            <span class="avatar avatar-rounded">
                                <img src="/admin_assets/assets/images/users/user.png" alt="img">
                            </span>
                        </div>
                        <div class="">
                            <div class="fs-15 fw-medium text-capitalize clientName"></div>
                            <p class="mb-0 text-muted fs-11 clientEmail"></p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php for ($i = 1; $i <= 15; $i++) { ?>
                            <div class="col-lg-4 m-auto mb-3 update-ib-dropdown-<?= $i ?>">
                                <label class="form-label">IB<?= $i ?></label>
                                <select id="ib-select<?= $i ?>" data-id="<?= $i ?>" class="form-select ib-select"
                                    name="ib<?= $i ?>" disabled>
                                    <option value="" selected>--Select--</option>
                                    <?php foreach ($ibdetails as $ib) { ?>
                                    <option value="<?php echo $ib->email; ?>"><?php echo $ib->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="ibUpdate" value="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
<div class="modal fade" id="rmModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="rmModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.updateRM') }}" id="rmRequestForm" method="post">
                @csrf
                <input type="hidden" name="user_id" id="customer_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="rmModalLabel">Assign/Reassign RM</h5>
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
                            <div class="fs-15 fw-medium text-capitalize" id="customerName"></div>
                            <p class="mb-0 text-muted fs-11" id="customerEmail"></p>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="mb-3 row">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Relationship Manager</label>
                            </div>
                            <div class="col-lg-8">
                                <select class="form-select" required name="rm_id" id="group_rm_list">

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="rmUpdate" value="update" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="statusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <form id="statusUpdateForm" >
                @csrf
                <input type="hidden" name="action" value="updateClientStatus">
                <input type="hidden" name="client_id" id="user_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Status</h5>
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
                            <div class="fs-15 fw-medium text-capitalize" id="userName"></div>
                            <p class="mb-0 text-muted fs-11" id="userEmail"></p>
                        </div>

                    </div>
                    <div class="card-body">
                        <div class="mb-3 row">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">User Status</label>
                            </div>
                            <div class="col-lg-8">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="status"
                                        id="user_status" checked>
                                    <label class="form-check-label" for="user_status"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">Email Confirmed</label>
                            </div>
                            <div class="col-lg-8">

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                        name="email_confirmed" id="email_status">
                                    <label class="form-check-label" for="email_status"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 m-auto">
                                <label class="form-label">KYC Verification</label>
                            </div>
                            <div class="col-lg-8">

                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="kyc_verify"
                                        id="kyc_verify">
                                    <label class="form-check-label" for="kyc_verify"></label>
                                </div>
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
<script>
    $(document).ready(function() {
        window.rmModal = new bootstrap.Modal(document.getElementById('rmModal'));
        window.updateIbModal = new bootstrap.Modal(document.getElementById('updateIbModal'));
        window.statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        $(".ib-select").each(function() {
            let id = $(this).data('id');
            $(this).select2({
                dropdownParent: $('.update-ib-dropdown-' + id)
            });
        });

        function updateIbSelects(id) {
            let val = $('#ib-select' + id).val();
            let nextId = id + 1;
            if (val != '') {
                $('#ib-select' + nextId).prop('disabled', false).trigger('change.select2');
            } else {
                for (let i = nextId; i <= 15; i++) {
                    $('#ib-select' + i).val("");
                    $('#ib-select' + i).prop('disabled', true).trigger('change.select2');
                }
            }
        }
        $(document).on('change', ".ib-select", function() {
            let val = $(this).val();
            $('.ib-select').find('option').prop('disabled', false);
            $('.ib-select').each(function() {
                let val = $(this).val();
                if (val) {
                    $('.ib-select').not(this).find('option[value="' + val + '"]').prop(
                        'disabled', true);
                }
            });
            let id = $(this).data("id");
            updateIbSelects(id);
        });
        $(document).on('click', '.rmToggle', function() {
            var data = $(this).data();
            let rm_id = data.rm;
            console.log(rm_id);
            $("#customerName,#customerEmail").html("");
            $("#customerName").html(data.fullname);
            $("#customerEmail").html(data.email);
            $("#customer_id").val(data.email);
            $.ajax({
                url: "/admin/ajax",
                type: "GET",
                data: {
                    action: 'getRMbyGroup',
                    "id": data.enc
                },
                success: function(response) {
                    var userGroupIds = JSON.parse(response);
                    var defaultOption = $('<option></option>').val('').text('--Select--')
                        .attr('selected', 'selected');
                    $('#group_rm_list').html(defaultOption);
                    $.each(userGroupIds, function(index, option) {
                        var $option = $('<option></option>').val(option.email).text(
                            option.username);
                        if (option.email === rm_id) {
                            $option.prop('selected', true);
                        }
                        $('#group_rm_list').append($option);
                    });
                }
            });
            rmModal.show();
        });
        $(document).on('click','.statusToggle', function() {
            var data = $(this).data();
            $("#userName,#userEmail").html("");
            $("#userName").html(data.fullname);
            $("#userEmail").html(data.email);
            $("#user_id").val(data.enc);
            $("#user_status").prop("checked", data.status == 1);
            $("#email_status").prop("checked", data.email_confirmed == 1);
            $("#kyc_verify").prop("checked", (data.kyc_verify == 1));
            statusModal.show();
        });
        $(document).on('click', '.updateIb', function() {
            var data = $(this).data();
            $(".clientName,.clientEmail,.client_id").html("");
            $(".clientName").html(data.fullname);
            $(".clientEmail").html(data.email);
            $(".client_id").val(data.enc);
            $('#ibUpdateForm select').each(function() {
                this.selectedIndex = 0;
            });
            $.ajax({
                url: "/admin/ajax",
                type: "GET",
                cache: false,
                data: {
                    "action": "getIbList",
                    "id": data.enc
                },
                success: function(response) {
                    var ibValues = JSON.parse(response);
                    $('.ib-select').val(null).trigger('change');
                    $.each(ibValues, function(key, value) {
                        if ((value != "noIB" && value != "" && value != null) ||
                            key == 'ib1') {
                            if (value == 'noIB') {
                                value = '';
                            }
                            $('#ibUpdateForm select[name="' + key + '"]').prop(
                                'disabled', false);
                            $('#ibUpdateForm select[name="' + key + '"]').val(value)
                                .trigger('change');
                        }
                    })
                }
            });
            updateIbModal.show();
        });
    });
    $("#statusUpdateForm").submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/ajax",
                type: "POST",
                cache: false,
                data: $("#statusUpdateForm").serialize(),
                success: function(response) {
                    let resp = JSON.parse(response);
                    if (resp.success == true) {
                        swal.fire({
                            icon: "success",
                            title: "Status Successfully Updated",
                        }).then((val) => {
                            location.reload();
                        });
                    } else {
                        swal.fire({
                            icon: "error",
                            title: "Something went wrong.",
                            text: "Please try again or contact support."
                        }).then((val) => {
                            location.reload();
                        });
                    }
                }
            });
        });
</script>
