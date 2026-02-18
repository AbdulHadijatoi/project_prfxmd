@extends('layouts.admin.admin')
@section('content')
    @php
        $current_permissions = session('current_permissions');
    @endphp
    <div class="modal fade" id="updateBonusModal" tabindex="-1" aria-labelledby="updateBonusModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="updateBonusModalLabel1">Update Bonus Details</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="update_bonus_form" action="{{ route('admin.bonusUpdate') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="bonus_id" id="bonus_id">
                    <div class="modal-body">
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
                                    <input type="datetime-local" class="form-control " name="bonus_starts_at" >
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
                                    <input type="number" class="form-control" min="1" value="1" required
                                        step="1" name="bonus_limit" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="mb-3">
                                    <label class="form-label">Accessible For</label>
                                    <select name="bonus_accessable[]" id="bonus_accessable" class="form-control select2"
                                        multiple required="required">
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
                                    <select name="bonus_shows_on" id="bonus_shows_on" class="form-control"
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
								<label class="form-label">Bonus Image</label>

								<div class="mb-2">
									<img id="updateBonusImagePreview"
										 src="{{ asset('images/placeholder.png') }}"
										 style="width:120px;height:120px;object-fit:cover;"
										 class="rounded border">
								</div>

								<input type="file" name="bonus_images" class="form-control"
									   onchange="updateBonusPreview(event)">
							</div>
                              <div class="col-lg-3">
                                <div class="mb-3">
                                    <label class="form-label">Expiry date</label>
                                    <input type="date" class="form-control" name="expiry_date" >
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-lg-2 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="status"
                                        id="status">
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        @if (in_array('/admin/bonusUpdate', $current_permissions))
                            <input type="submit" class="btn btn-primary" name="action" value="Update">
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Bonus</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bonus</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            @if (in_array('/admin/bonusAdd', $current_permissions))
                                <a href="{{ route('admin.bonusAdd') }}">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addBonusModal">
                                        Add New Bonus
                                    </button>
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableBonus" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
											<th>Actions</th>
											<th>Status</th>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <!--<th>Description</th>-->
                                            <th>Starts At</th>
                                            <th>Ends At</th>
                                            <th>Accessable for</th>
                                            <th>Shows to</th>
                                            <th>Updated At</th>
                                        </tr>
                                    </thead>
									
									<tbody>
										@forelse($bounsdata as $bouns)
											<tr>
												<td>{{ $bouns->bonus_id }}</td>
												<td>
													<a href="javascript:void(0);" class="btn update-bonus" data-id="{{ $bouns->bonus_id }}"><i class="fa fa-edit"></i></a>
												</td>
												<td>
													@if($bouns->status == 1)
														<span class="badge bg-success">Active</span>
													@else
														<span class="badge bg-danger">Inactive</span>
													@endif
												</td>
												<td>
													@php
														$image = $bouns->bonus_images;
														$imagePath = $image && Storage::disk('public')->exists('bonus/' . $image)
															? asset('storage/bonus/' . $image)
															: asset('images/placeholder.png');
													@endphp

													<img src="{{ $imagePath }}" 
														 alt="Bonus Image" 
														 width="60" 
														 height="60" 
														 class="rounded border">
												</td>
												<td>{{ $bouns->bonus_name }}</td>
												<td>{{ $bouns->bonus_code }}</td>
												<!--<td >{{ $bouns->bonus_desc }}</td>-->
												<td>{{ \Carbon\Carbon::parse($bouns->bonus_starts_at)->format('d M Y H:i') }}</td>
												<td>{{ \Carbon\Carbon::parse($bouns->bonus_ends_at)->format('d M Y H:i') }}</td>
												<td>{{ $bouns->bonus_accessable }}</td>
												<td>{{ $bouns->bonus_shows_on }}</td>
												<td>{{ $bouns->updated_at?->format('d M Y H:i') }}</td>
											</tr>
										@empty
											<tr>
												<td colspan="12" class="text-center text-danger fw-bold">
													No Bonuses Found
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
            $('#tableBonus').DataTable({
				autoWidth: false,
				columnDefs: [
					{ width: "200px", targets: [4, 5] }
				],
                order: [
                    [0, "desc"]
                ]                
            });
        });
        // $("#status").change(function() {
        //     if ($(this).is(':checked')) {
        //         $(this).val("1");
        //     } else {
        //         $(this).val("0");
        //     }
        // });
        $(".select2").select2();
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
                    action: 'getBonusDetails',
                    id: id
                },
                success: function(response) {
                    response = JSON.parse(response);
                    $('#updateBonusModal .role_id').trigger("change");
					
					// Update image preview
					if (response.bonus_images && response.bonus_images !== "") {
                    $("#updateBonusImagePreview").attr("src", "/storage/bonus/" + response.bonus_images);
                } else {
                    $("#updateBonusImagePreview").attr("src", "/images/placeholder.png");
                }
					
					
                   $.each(response, function(key, value) {
    if (key === 'expiry_date' && value) {
        let dateOnly = value.split('T')[0];
        $('#update_bonus_form [name="expiry_date"]').val(dateOnly);

    } else if (key == 'bonus_accessable') {
        var valuesArray = String(value).split(',');
        $('#' + key).val(valuesArray).trigger('change');
        $("#bonus_accessable").select2({
            dropdownParent: $('#updateBonusModal')
        });

    } else if (key === 'status') {
        let statusCheckbox = $('#update_bonus_form #status');
        statusCheckbox.prop('checked', value == 1);
        statusCheckbox.val(value == 1 ? 1 : 0);

    } else if (key == 'bonus_shows_on') {
        $('#update_bonus_form [name="' + key + '"]').val(value);
        bonusOnChange(value);

    } else if (key == 'bonus_show_list') {
        setTimeout(function() {
            var valuesArray = String(value).split(',');
            $('#' + key).val(valuesArray).trigger('change');
        }, 1000);

    } else if (key === 'bonus_images') {
        // DO NOT set value for file input
        // Already handled via preview
    } else {
        $('#update_bonus_form [name="' + key + '"]').val(value);
    }
});
                    $('#update_bonus_form #status').prop('checked', response.status == 1);
                    $('#updateBonusModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                }
            });
        });

        $("#status").change(function() {
    $(this).val($(this).is(':checked') ? 1 : 0);
});
        $("#bonus_shows_on").change(function() {
            let val = $(this).val();
            bonusOnChange(val);
        });
		
		function updateBonusPreview(event) {
			const reader = new FileReader();
			reader.onload = function() {
				$("#updateBonusImagePreview").attr("src", reader.result);
			};
			reader.readAsDataURL(event.target.files[0]);
		}

        function bonusOnChange(type) {
            $("#bonus_show_list").select2();
            $("#bonus_show_list").empty();
            $("#bonus_show_list").val(null).trigger("change");
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
            $.ajax({
                url: '/admin/ajax?action=' + show_type,
                type: 'GET',
                success: function(data) {
                    data = JSON.parse(data);
                    data.forEach(function(item) {
                        $('#bonus_show_list').append($('<option>', {
                            value: item.id,
                            text: item.text
                        }));
                        $("#bonus_show_list").select2({
                            dropdownParent: $('#updateBonusModal')
                        });
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error occurred:", textStatus, errorThrown);
                }
            });
        }
    </script>
@endsection
