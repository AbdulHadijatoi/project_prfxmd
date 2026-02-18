@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Promotions - Creation</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page">Promotions</li>
                    <li class="breadcrumb-item active" aria-current="page">Creation</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <form method="post" enctype="multipart/form-data" action="{{route('admin.promotionsStore')}}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Title</label>
                                            <input type="text" class="form-control " name="promo_name" required />
                                        </div>
                                    </div>
									<div class="col-lg-3">
                                        <div class="mb-3">
                                            <label class="form-label">Code</label>
                                            <input type="text" class="form-control " name="promo_code" required>
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
									<div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">MT5 Groups</label>
                                            <select name="promo_groups[]" id="promo_groups" class="form-control select2-groups" multiple required="required">
											@foreach($acc_types as $accty)
												<option value="{{ $accty->ac_index }}">Live - {{ $accty->ac_name }} - {{ $accty->ac_group }}</option>
											@endforeach
                                            </select>
                                        </div>
                                    </div>
									<div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Apply For</label>
                                            <select name="promo_apply_for" class="form-control" required="required">
                                                <option value="exist">Exist Account</option>
                                                <option value="new">New Account</option>
                                            </select>
                                        </div>
                                    </div>
									<div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Banner Images</label>
                                            <input type="file" class="form-control " name="promo_image" accept="image/*" required />
                                        </div>
                                    </div>
									
									<div class="col-lg-6">
                                        <div class="mb-3">
                                            <label class="form-label">URL</label>
                                            <input type="text" class="form-control " name="promo_url" required />
                                        </div>
                                    </div>
                                      <div class="col-lg-4">
                                        <div class="mb-3">
                                            <label class="form-label">Expiry date</label>
                                            <input type="date" class="form-control" name="expiry_date"  required/>
                                        </div>
                                    </div>
									<div class="col-lg-2 mb-3">
										<label class="form-label">Status</label>
                                        <div class="form-check form-switch">
											
                                            <input class="form-check-input" type="checkbox" checked value="1"
                                                role="switch" name="status" id="status">
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<div class="mb-3">
											<label class="form-label">Description</label>
											<textarea class="form-control promo_desc" name="promo_desc" required></textarea>
										</div>
									</div>
                                </div>
                                <div class="card-footer text-end pb-0">
                                    <input type="submit" class="btn btn-primary" value="Create Promotions" name="action" />
                                </div>
                            </div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
@endsection
@section('scripts')
<script>
		// Disable past dates
		let today = new Date().toISOString().split("T")[0];
		document.getElementById("promo_starts_at").setAttribute("min", today);

		// End date depends on start date
		document.getElementById("promo_starts_at").addEventListener("change", function() {
			let startDate = this.value;

			document.getElementById("promo_ends_at").setAttribute("min", startDate);

			if (document.getElementById("promo_ends_at").value < startDate) {
				document.getElementById("promo_ends_at").value = "";
			}
		});
		
		
		function updateBonusPreview(event) {
			const reader = new FileReader();
			reader.onload = function() {
				$("#updateBonusImagePreview").attr("src", reader.result);
			};
			reader.readAsDataURL(event.target.files[0]);
		}
	</script>

	<script>
		CKEDITOR.replace('promo_desc');
		$(document).ready(function() {
			$("#promo_groups").select2();
		});		
		
	</script>
@endsection
