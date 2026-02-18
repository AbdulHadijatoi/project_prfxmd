@extends('layouts.admin.admin')
@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">Com. Excluded - Live Accounts</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Utilities</li>
                    <li class="breadcrumb-item active" aria-current="page">Com. Excluded - Live Accounts</li>
                </ol>
            </div>
            <!-- PAGE-HEADER END -->


            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex justify-content-between w-100 mx-1">
                                <div>
                                    <button class="btn btn-primary addMt5Excl">
                                        Add Excludes
                                    </button>
                                </div>
                                <div class="d-flex">
                                    <input type="text" class="form-control" name="dtStartDate" id="dtStartDate"
                                        placeholder="Start Date" value="{{ $_GET['startdate'] ?? '' }}">
                                    <input type="text" class="ms-2 form-control" name="dtEndDate" id="dtEndDate"
                                        placeholder="End Date" value="{{ $_GET['enddate'] ?? '' }}">
                                    <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ajaxDatatable" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <td>Client</td>
                                            <td>Trade ID</td>
                                            <td>Leverage</td>
                                            <td>Balance</td>
                                            <td>Equity</td>
                                            <td>Tot. Lots</td>
                                            <td>Excluded At</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                    foreach ($accounts as $result) {
                                        // print_r($result);
                                        // exit();
                                    ?>
                                        <tr>
                                            <td>
                                                <a href='/admin/client_details?id=<?= $result->enc_id ?>'>
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
                                                            <div class='lh-1'><span><?= ucfirst($result->name) ?></span>
                                                            </div>
                                                            <div class='lh-1'><span
                                                                    class='fs-11 text-muted'><?= $result->email ?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="/admin/view_account_details?id=<?= md5($result->trade_id) ?>">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto pe-0"><img src="/assets/images/mt5.png"
                                                                alt="user-image" class="wid-50 hei-50 rounded"></div>
                                                        <div class="col ps-2">
                                                            <h6 class="mb-0"><span
                                                                    class="text-truncate w-100"><?= $result->trade_id ?></span>
                                                            </h6>
                                                            <p class="text-muted f-12 mb-0"><span
                                                                    class="text-truncate w-100"><?= $result->ac_group ?></span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>
                                            <td><?php echo htmlentities($result->leverage); ?></td>
                                            <td><?php echo htmlentities($result->Balance); ?></td>
                                            <td><?php echo htmlentities($result->equity); ?></td>
                                            <td><?php echo htmlentities($result->total_lots ?? 0); ?></td>
                                            <td>
                                                @if ($result->excluded_at)
                                                    <div class="lh-1">
                                                        <?= date('Y-m-d', strtotime($result->excluded_at)) ?>
                                                    </div>
                                                    <div class="lh-2 text-muted">
                                                        <?= date('H:i:s', strtotime($result->excluded_at)) ?></div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="#" class="btn removeBtn"
                                                    data-id="{{ md5($result->trade_id) }}">
                                                    <i class="fa fa-trash text-primary"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php }
                                    ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="addUserLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <form action="#" id="addUserForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserLabel">Add to Live acc. Exclude</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body custom-card card mb-0">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="trade-id-wrapper">
                                    <label class="form-label">Trade ID</label>
                                    <select class="form-select trade-id liveAccountsDD" name="trade_id" required>
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="addUser" value="update" class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection()
@section('scripts')
    <!-- End::app-content -->
    <script>
        function select2init() {
            $('.liveAccountsDD').select2({
                dropdownParent: $('#addUserModal'),
                ajax: {
                    url: '{{ route('admin.getUtilityAccounts') }}',
                    dataType: 'json',
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
                }
            });
        }
        select2init();
        $(document).ready(function() {
            window.addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        });

        $(".addMt5Excl").click(function(e) {
            e.preventDefault();
            addUserModal.show();
        });

        // $("#ibModal").modal();
        function dTSelection() {
            $('.ajaxDataTable tbody tr').off();
            $('.ajaxDataTable tbody tr').on('click', '.removeBtn', function(e) {
                e.preventDefault();
                var dataid = $(this).data("id");
                $.ajax({
                    url: location.href,
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "trade_id": dataid
                    },
                    beforeSend: function() {
                        swal.fire({
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: false,
                            didOpen: function(){
                                swal.enableLoading();
                            }
                        });
                    },
                    success: function(data) {
                        if(data.status){
                            swal.fire({
                                icon: "success",
                                title: "Excluded Account Removed",
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                            }).then((val) => {
                                location.reload();
                            });
                        }
                        else {
                            swal.fire({
                                icon: "warning",
                                title: data.message,
                                allowEscapeKey: false,
                                allowOutsideClick: false,
                            }).then((val) => {
                                location.reload();
                            });
                        }
                    }
                })
                // alert(id);
                // addUserModal.show();
            });
        }
        window.dTtable = $('.ajaxDataTable').on("draw.dt", dTSelection).DataTable();
        $(document).on("click", ".dtDateFilter", function() {
            let startDate = $('#dtStartDate').val();
            //console.log(startDate);
            let endDate = $('#dtEndDate').val();
            let currentUrl = window.location.href.split('?')[0];
            let newUrl = new URL(currentUrl.toString());
            if (startDate != '') newUrl.searchParams.set('startdate', startDate);
            if (endDate != '') newUrl.searchParams.set('enddate', endDate);
            window.location.href = newUrl.href;
        });
    </script>
@endsection()
