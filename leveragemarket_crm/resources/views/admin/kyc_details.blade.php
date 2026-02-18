@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">KYC Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">KYC Details</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <div class="userprofile">
                                    <div class="avatar userpic avatar-rounded">
                                        <img src="/admin_assets/assets/images/users/client.jpeg" alt="img"
                                            style="width:100px">
                                    </div>
                                    <h3 class="username mb-2"><?= $user->fullname ?? '' ?></h3>
                                    <p class="mb-1 text-muted"><?= $user->email ?? '' ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php foreach ($details_all as $details) { ?>
                <div class="col-xl-6">
                    <div class="card custom-card">
                        <div class="card-header justify-content-between">
                            <div class="card-title w-100 d-flex justify-content-between">
                                <span>
                                    <?= $details->kyc_type ?>
                                    <?= $details->Status == 1 ? '<div class="badge ms-2 bg-outline-success">Approved</div>' : ($details->Status == 2 ? '<span class="badge ms-2 bg-outline-danger">Not Approved</span>' : '<span class="badge ms-2 bg-outline-primary">Waiting for Approval</span>') ?>

                                </span>
                                <span class="badge text-bg-light">
                                    <?= $details->registered_date_js ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body w-100 d-flex justify-content-between">
                            <?php if ($details->kyc_frontside != NULL) { ?>
                            <a target="_blank" class="w-100" style="max-width:48%;margin-right: 2%"
                                href="<?= $details->kyc_frontside ?>">
                                <img src="<?= $details->kyc_frontside ?>" class="w-100">
                            </a>
                            <?php } ?>
                            <?php if ($details->kyc_backside != NULL) { ?>
                            <a target="_blank" class="w-100" href="<?= $details->kyc_backside ?>" style="max-width:48%">
                                <img src="<?= $details->kyc_backside ?>" class="w-100">
                            </a>
                            <?php } ?>
                            <?php if ($details->front_image != NULL) { ?>
                            <a target="_blank" class="w-100" href="<?= $details->front_image ?>" style="max-width:48%">
                                <img src="<?= $details->front_image ?>" class="w-100">
                            </a>
                            <?php } ?>
                            <?php if ($details->back_image != NULL) { ?>
                            <a target="_blank" class="w-100" href="<?= $details->back_image ?>" style="max-width:48%">
                                <img src="<?= $details->back_image ?>" class="w-100">
                            </a>
                            <?php } ?>

                        </div>
                        <div class="card-footer">
                            <?php if ($details->Status == 0) { ?>
                            <div class="card-footer ">
                                <div class="btn-list ms-auto text-end my-auto">
                                    <button onclick="takeAction('<?= $details->id ?>','<?= $details->email ?>',1)"
                                        type="button" class="btn btn-success btn-space m-1">Approve</button>
                                    <button onclick="takeAction('<?= $details->id ?>','<?= $details->email ?>',2)"
                                        type="submit" class="btn btn-danger btn-space m-1">Reject</button>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
        function takeAction(id, email, status) {
            Swal.fire({
                title: `Are you sure you want to ${status === 1 ? "approve" : "reject"} this Document?`,
                html: `
            <form id="updateKYCForm">
             @csrf
             <input type="hidden" name="id" value="${id}">
              <input type="hidden" name="email" value="${email}">
              <input type="hidden" name="status" value="${status}">
              <input type="hidden" name="action" value="updateKYC">
              <div class="col-12 mt-2 text-start">
                  <textarea id="description" name="description" rows="3" class="mt-2 form-control" placeholder="Add a description"></textarea>
              </div>
              </form>
          `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: () => {
                    const description = document.querySelector('#updateKYCForm textarea').value;
                    if (!description) {
                        Swal.showValidationMessage('Please add a comment');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateKYC();
                }
            });
        }

        function updateKYC() {
            var formData = $("#updateKYCForm").serializeArray();
            $.ajax({
                url: "/admin/ajax",
                type: "POST",
                data: formData,
                responseType: 'json',
                success: function(data) {
                    data = JSON.parse(data.trim());
                    swal.fire({
                        icon: data.status,
                        title: data.message,
                    }).then((val) => {
                        location.reload();
                    });
                }
            });
        }
    </script>
@endsection
