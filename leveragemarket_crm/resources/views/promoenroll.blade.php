@php
    use Carbon\Carbon;
@endphp
@extends('layouts.crm.crm')
@section('content')
<style>
    #wallet_transactions .td-wrap {
        max-width: 75px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .wallet-plus td {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
    }

    .wallet-minus td {
        --bs-text-opacity: 1;
        color: rgba(var(--bs-danger-rgb), var(--bs-text-opacity)) !important;
    }
</style>
<div class="pc-container">
  <div class="pc-content">
    <div class="page-header mb-0 pb-0">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title h2">
              <h4 class="mb-0">Enroll Now</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
		<div class="col-lg-8 col-md-8 col-sm-12">
			<div class="new-arrival-product mb-4 mb-xxl-4 mb-md-0">
				<div class="new-arrivals-img-contnent">
					@php
						$image = $promo->promo_image;
						$imagePath = $image && Storage::disk('public')->exists('promo/' . $image)
							? asset('storage/promo/' . $image)
							: asset('images/placeholder.png');
					@endphp

					<img src="{{ $imagePath }}" 
						 alt="Promo Image"
						 class="img-fluid promo-img" style="height:196px; width: 100%;" />
				</div>
			</div>  
			<div class="new-arrival-content position-relative">
				<h4>{{ $promo->promo_name }}</h4>
				<div class="bootstrap-badge mb-3">											
					<span class="badge light badge-success">Start: {{ $promo->promo_starts_at ? Carbon::parse($promo->promo_starts_at)->format('d M Y H:i') : '' }}</span>
					<span class="badge light badge-secondary">End: {{ $promo->promo_ends_at ? Carbon::parse($promo->promo_ends_at)->format('d M Y H:i') : '' }}</span>
					@if($promo->status == 1)
						<span class="badge light badge-info mt-3">Active</span>
					@else
						<span class="badge light badge-danger mt-3">Inactive</span>
					@endif
				</div>
				<div class="row mt-1">
					<p>{!! Str::limit($promo->promo_desc, 250) !!}</p>
				</div>										
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12">
			<form method="post" enctype="multipart/form-data" action="{{ route($linkaccount) }}">
				@csrf
				<input type="hidden" name="promoid" value="{{ $promo->promo_id }}"
				<div class="form-group mb-0">
					<div class="row">
						<div class="col-12 mb-3">
							<label class="form-label">Choose Account Type <span class="text-danger">*</span></label>
							<select class="form-select acc-types" name="options" id="options" required>
								@foreach($promoselgroup as $progroup)
									<option value="{{ $progroup->ac_index }}" data-inquiry="{{ $progroup->inquiry_status ?? 0 }}" data-group="{{ $progroup->ac_group }}">
										{{ $progroup->ac_name }} - {{ $progroup->ac_group }}
									</option>
								@endforeach
							</select>
						</div>
						
						@if($promoselapplyacc->isNotEmpty())
							<div class="col-12 mb-3">
								<label class="form-label">Choose Existing Account</label>
								<select class="form-select" name="trade_id" id="trade_id">
									@foreach($promoselapplyacc as $proliveacc)
										<option value="{{ $proliveacc->trade_id }}">
											{{ $proliveacc->trade_id }} - {{ $proliveacc->accountType->ac_group }}
										</option>
									@endforeach
								</select>
							</div>
						@endif
						
						@if($promoselapplyacc->isEmpty())
						<div class="col-12 mb-3">
							<label class="form-label">Leverage <span class="text-danger">*</span></label>
							<select class="form-select" name="leverage" id="leverage" required >
								@foreach($leveragegroup as $leverage)
									<option value="{{ $leverage->account_leverage }}">
										{{ $leverage->account_leverage }}
									</option>
								@endforeach
							</select>
						</div> 
						@endif
					</div>	
					<div class="row">
						<div class="col-12">
							<div class="d-grid gap-2 mt-2">
								<button class="btn btn-md btn-primary create-account-btn" value="Live Account Creation" name="a[register]" type="submit" ><i class="ti ti-plus me-2"></i>{{ $btntext }}</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
    </div>
  </div>
</div>

@if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}'
            }).then(() => {
                window.location.href = '{{ route('promotions') }}';
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: "Something Went Wrong !!!!",
                text: '{{ session('error') }}',
            });
        </script>
    @endif

<script>
	$(".acc-types").change(function() {

		var selected = $(this).find(":selected");

		var inquiry_status = selected.data("inquiry");
		var inquiry = selected.data("group");
		var selectedValue = selected.val();

		if (inquiry_status == 0) {

			$(".is_account").removeClass("d-none");
			$(".is_inquiry").addClass("d-none");

			$("#leverage").html("<option>Loading...</option>");

			$.ajax({
				url: "{{ route('get-leverage') }}?id=" + selectedValue,
				success: function(data) {
					$("#leverage").html("");
					$.each(data, function(key, value) {
						$("#leverage").append(
							"<option value='" + value.account_leverage + "'>" + value.account_leverage + "</option>"
						);
					});
				}
			});

		} else {

			$(".is_account").addClass("d-none");
			$(".is_inquiry").removeClass("d-none");

			var href = "/support?reg=" + inquiry;
			$(".contactus-btn").attr("href", href);
		}
	});

	// Trigger on page load
	$(".acc-types").trigger("change");
</script>

<script>
$(document).ready(function () {

	$(".create-account-btn").on("click", function (e) {
		e.preventDefault(); // stop immediate form submit

		let form = $(this).closest("form"); // get parent form

		// Disable button
		$(this).prop("disabled", true)
			   .html('<span class="spinner-border spinner-border-sm"></span> Creating...');

		// SweetAlert loading popup
		Swal.fire({
			title: "Please wait...",
			html: "Your Promo live account is being created shortly.",
			icon: "info",
			allowOutsideClick: false,
			allowEscapeKey: false,
			showConfirmButton: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		// Submit form after showing loading
		form.submit();
	});

});
</script>

@endsection
