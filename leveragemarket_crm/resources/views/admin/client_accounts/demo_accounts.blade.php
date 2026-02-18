@extends('layouts.admin.admin')
@section('content')
    <!-- Start::app-content -->
    <div class="main-content app-content">
        <div class="container-fluid">

            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">Client - Demo Accounts</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item">Client List</li>
                    <li class="breadcrumb-item active" aria-current="page">Demo Accounts</li>
                </ol>
            </div>
            <!-- PAGE-HEADER END -->


            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex mx-1">
                                <input type="text" class="form-control" name="dtStartDate" id="dtStartDate"
                                    placeholder="Start Date" value="{{$_GET['startdate']??''}}">
                                <input type="text" class="ms-2 form-control" name="dtEndDate" id="dtEndDate"
                                    placeholder="End Date" value="{{$_GET['enddate']??''}}">
                                <button type="button" class="ms-2 btn btn-dark dtDateFilter"> Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ajaxDatatable" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <td>Action</td>
                                            <td>Client</td>
                                            <td>Trade ID</td>
                                            <td>Leverage</td>
                                            <td>Balance</td>
                                            <td>Equity</td>
                                            <td>Registered_Date</td>
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
												<div class="">
                                                    <span class="badge btn btn-danger deleteaccount" data-bs-toggle="modal" 
													data-id="<?= $result->trade_id ?>"
													data-url="{{ route('admin.userdemoAccountdelete.destroy', $result->trade_id) }}"
													>
                                                        <i class="ti ti-trash" style="font-weight: bold"></i>
                                                    </span>
                                                </div>
											</td>
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
                                                <div class="row align-items-center">
                                                    <div class="col-auto pe-0"><img src="/assets/images/mt5.png"
                                                            alt="user-image" class="wid-50 hei-50 rounded"></div>
                                                    <div class="col ps-2">
                                                        <h6 class="mb-0"><span
                                                                class="text-truncate w-100"><?= $result->trade_id ?></span>
                                                        </h6>
                                                        <p class="text-muted f-12 mb-0"><span
                                                                class="text-truncate w-100"><?= $result->account_type ?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlentities($result->leverage); ?></td>
                                            <td><?php echo htmlentities($result->Balance); ?></td>
                                            <td><?php echo htmlentities($result->equity); ?></td>
                                            <td>
                                                <div class="lh-1">
                                                    <?= date('Y-m-d', strtotime($result->Registered_Date)) ?>
                                                </div>
                                                <div class="lh-2 text-muted">
                                                    <?= date('H:i:s', strtotime($result->Registered_Date)) ?></div>
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
@endsection()
@section('scripts')
	@if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: true
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        </script>
    @endif
    <!-- End::app-content -->
    <script>
        $(document).ready(function() {
            window.myModal = new bootstrap.Modal(document.getElementById('ibModal'));
        });

        // $("#ibModal").modal();
        function dTSelection() {
            // alert("Init");
            $('.ajaxDataTable tbody tr').off();
            $('.ajaxDataTable tbody tr').on('click', '.ibToggle', function() {
                var data = dTtable.row($(this).closest("tr")).data();
                // console.log(data);
                $("#clientName,#clientEmail").html("");
                $("#clientName").html(data.fullname)
                $("#clientEmail").html(data.email)
                $("#client_id").val(data.enc)
                $("[name='ib_status']").val(data.ib_status).trigger("change");
                $("[name='ib_group']").val(data.ib_group).trigger("change");
                myModal.show();
                // swal.fire({
                //   icon: "info",
                //   title: "IB Status ==> " + data.ib_status
                // });

            });
        }

        window.dTtable = $('.ajaxDataTable').on("draw.dt", dTSelection).DataTable();
        $(document).on("click", ".dtDateFilter", function() {
            let startDate = $('#dtStartDate').val();
            //console.log(startDate);
            let endDate = $('#dtEndDate').val();
            let currentUrl = window.location.href.split('?')[0];
            let newUrl = new URL(currentUrl.toString());
            if(startDate!='')newUrl.searchParams.set('startdate', startDate);
            if(endDate!='') newUrl.searchParams.set('enddate', endDate);
            window.location.href = newUrl.href;
        });
		
		
		$(document).on('click', '.deleteaccount', function() {
			
			let id = $(this).data('id');
			let url = $(this).data('url');
			
			Swal.fire({
				title: "Are you sure?",
				text: "You want to delete this demo account?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Yes, Delete!"
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: $(this).data('url'),
						type: "POST",
						data: {
							_token: "{{ csrf_token() }}",
							_method: 'DELETE'
						},
						success: function(res) {

							if (res.status === 'error') {
								Swal.fire("Failed", res.message, "error");
							} else {
								Swal.fire("Deleted!", res.message, "success")
								.then(() => {
									location.reload();
								});
							}

						},
						error: function() {
							Swal.fire("Error", "Something went wrong!", "error");
						}
					});
				}
			});
		});
		
    </script>
@endsection()
