@extends('layouts.admin.admin')
@section('content')
    @php
        $current_permissions = session('current_permissions');
    @endphp
    <div class="modal fade" id="updateBonusModal" tabindex="-1" aria-labelledby="updateBonusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="updateBonusModalLabel1">Update Promotion Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="update_bonus_form" action="{{ route('admin.promotionsUpdate') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="promo_id" id="promo_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
								<div class="mb-3">
									<label class="form-label">Title</label>
									<input type="text" class="form-control " name="promo_name" required />
								</div>
							</div>

							<div class="col-lg-3">
								<div class="mb-3">
									<label class="form-label">Starts At</label>
									<input type="date" class="form-control" id="promo_starts_at" name="promo_starts_at" required />
								</div>
							</div>
							<div class="col-lg-3">
								<div class="mb-3">
									<label class="form-label">Ends At</label>
									<input type="date" class="form-control" id="promo_ends_at" name="promo_ends_at" required />
								</div>
							</div>
							<div class="col-lg-6">
								<div class="mb-3">
									<label class="form-label">URL</label>
									<input type="text" class="form-control " id="promo_url" name="promo_url" required />
								</div>
							</div>
                             <div class="col-lg-6">
                                <div class="mb-3">
                                    <label class="form-label">Expiry date</label>
                                    <input type="date" class="form-control " name="expiry_date">
                                </div>
                            </div>
							<div class="col-lg-6">
								<div class="mb-3">
									<label class="form-label">Description</label>
									<input type="text" class="form-control" id="promo_desc" name="promo_desc" required />
								</div>
							</div>
                            
                            <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="status"
                                        id="status">
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
							<div class="col-lg-6">
								<label class="form-label">Bonus Image</label>

								<div class="mb-2">
									<img id="updateBonusImagePreview"
										 src="{{ asset('images/placeholder.png') }}"
										 style="width:120px;height:120px;object-fit:cover;"
										 class="rounded border">
								</div>

								<input type="file" name="promo_image" class="form-control"
									   onchange="updateBonusPreview(event)">
							</div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <input type="submit" class="btn btn-primary" name="action" value="Update">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Promotions</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Promotions</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                                <a href="{{ route('admin.promotionsAdd') }}">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBonusModal">
                                        Add New
                                    </button>
                                </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablePromotion" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>											
                                            <th>Actions</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Starts At</th>
                                            <th>Ends At</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
									<tbody>
										@forelse($promodata as $promo)
											<tr>
												<td>{{ $promo->promo_id }}</td>
												<td>
													<a href="javascript:void(0);" class="btn update-bonus" data-id="{{ $promo->promo_id }}"><i class="fa fa-edit"></i></a>
												</td>
												<td>
													@php
														$image = $promo->promo_image;
														$imagePath = $image && Storage::disk('public')->exists('promo/' . $image)
															? asset('storage/promo/' . $image)
															: asset('images/placeholder.png');
													@endphp

													<img src="{{ $imagePath }}" 
														 alt="Promo Image" 
														 width="60" 
														 height="60" 
														 class="rounded border">
												</td>
												<td>{{ $promo->promo_name }}</td>
												<td>{!! $promo->promo_desc !!}</td>
												<td>{{ \Carbon\Carbon::parse($promo->promo_starts_at)->format('d M Y H:i') }}</td>
												<td>{{ \Carbon\Carbon::parse($promo->promo_ends_at)->format('d M Y H:i') }}</td>
												<td>
													@if($promo->status == 1)
														<span class="badge bg-success">Active</span>
													@else
														<span class="badge bg-danger">Inactive</span>
													@endif
												</td>
												
											</tr>
										@empty
											<tr>
												<td colspan="12" class="text-center text-danger fw-bold">
													No Promotions Found
												</td>
											</tr>
										@endforelse
									</tbody>
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
            $('#tablePromotion').DataTable();
        });
        // $("#status").change(function() {
        //     if ($(this).is(':checked')) {
        //         $(this).val(1);
        //     } else {
        //         $(this).val(0);
        //     }
        // });
		
        $('#updateBonusModal').on('shown.bs.modal', function() {
            $("#bonus_accessable").find(".select2").select2({
                dropdownParent: $('#updateBonusModal')
            });
        });
    $(document).on("click", ".update-bonus", function() {
    $('#updateBonusModal').modal('show');
    let id = $(this).data("id");

    $.ajax({
        url: "/admin/ajax",
        type: "GET",
        data: {
            action: 'getPromoDetails',
            id: id
        },
        success: function(response) {
            response = JSON.parse(response);
            console.log(response);

            $('#updateBonusModal .role_id').trigger("change");

            // Update image preview (file input value not set programmatically)
            if (response.promo_image && response.promo_image !== "") {
                $("#updateBonusImagePreview").attr("src", "/storage/promo/" + response.promo_image);
            } else {
                $("#updateBonusImagePreview").attr("src", "/images/placeholder.png");
            }

            // Populate form fields
            $.each(response, function(key, value) {
                if (key === 'expiry_date' && value) {
                    // Convert ISO datetime to YYYY-MM-DD for <input type="date">
                    let dateOnly = value.split('T')[0];
                    $('#update_bonus_form [name="expiry_date"]').val(dateOnly);

                } else if (key === 'promo_starts_at' || key === 'promo_ends_at') {
                    // Format datetime for input (use your formatDateToInput function)
                    let formatted = formatDateToInput(value);
                    $('#update_bonus_form [name="'+ key +'"]').val(formatted);

                } else if (key === 'status') {
                    // Set checkbox checked and value
                    let statusCheckbox = $('#update_bonus_form #status');
                    statusCheckbox.prop('checked', value == 1);
                    statusCheckbox.val(value == 1 ? 1 : 0);

                } else if (key === 'promo_image') {
                    // Do NOT set value for file input
                    // Already handled in preview
                } else {
                    // Default for other inputs
                    $('#update_bonus_form [name="'+ key +'"]').val(value);
                }
            });

            $('#updateBonusModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });
});

// Keep this handler for checkbox changes
$("#status").change(function() {
    $(this).val($(this).is(':checked') ? 1 : 0);
});
	
		function formatDateToInput(dateString) {
			if (!dateString) return "";

			const d = new Date(dateString);
			if (isNaN(d.getTime())) return "";

			let month = ('0' + (d.getMonth() + 1)).slice(-2);
			let day = ('0' + d.getDate()).slice(-2);
			let year = d.getFullYear();

			return `${year}-${month}-${day}`;
		}
		
		function updateBonusPreview(event) {
			const reader = new FileReader();
			reader.onload = function() {
				$("#updateBonusImagePreview").attr("src", reader.result);
			};
			reader.readAsDataURL(event.target.files[0]);
		}
		
    </script>
@endsection
