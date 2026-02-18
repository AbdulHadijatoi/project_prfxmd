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
@section('content')
<div class="pc-container">
  <div class="pc-content">
    <div class="page-header mb-0 pb-0">
      <div class="page-block">
        <div class="row align-items-center">
          <div class="col-md-12">
            <div class="page-header-title h2">
              <h4 class="mb-0">Bonuses</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        
            <div class="row g-2">
				<!-- Offer Card 1 -->
				@forelse($bonuses as $bonus)
				<div class="col-lg-12 col-xl-6 col-xxl-4">
					<div class="card banking-card pull-up">
						<div class="card-body">
							<div class="row m-b-30">
								<div class="col-md-5 col-xxl-12">
									<div class="new-arrival-product mb-4 mb-xxl-4 mb-md-0">
										<div class="new-arrivals-img-contnent">
											@php
												$image = $bonus->bonus_images;
												$imagePath = $image && Storage::disk('public')->exists('bonus/' . $image)
													? asset('storage/bonus/' . $image)
													: asset('images/placeholder.png');
											@endphp

											<img src="{{ $imagePath }}" 
												 alt="bonus Image"
												 class="img-fluid" style="height:196px; width: 100%;" />
										</div>
									</div>
								</div>
								<div class="col-md-7 col-xxl-12" style="height: 150px">
									<div class="new-arrival-content position-relative">
										<h4>{{ $bonus->bonus_name }}</h4>
										<div class="bootstrap-badge mb-3">
											<span class="badge light badge-danger">Code: {{ $bonus->bonus_code }}</span>
											@if($bonus->status == 1)
												<span class="badge light badge-info mt-3">Active</span>
											@else
												<span class="badge light badge-danger mt-3">Inactive</span>
											@endif
										</div>
										<div class="row mt-1">
											<p>{{ Str::limit($bonus->bonus_desc, 200) }}</p>
										</div>										
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<!--<button class="btn btn-success btn-sm light text-uppercase btn-block me-3 w-100 text-black claimbouns" data-url="{{ route('bouns.enroll', ['bonusid' => md5($bonus->bonus_id)]) }}" >Claim Now</button>-->
							
							<button class="btn btn-success btn-sm light text-uppercase btn-block me-3 w-100 text-black claimbouns" data-url="{{ route('trade-deposit', ['bonusid' => md5($bonus->bonus_id)]) }}" >Claim Now</button>
						</div>
					</div>
				</div>
				@empty
					<div class="col-12 text-center">
						<p class="text-muted">No bonuses are available at the moment.</p>
					</div>
				@endforelse
			</div>
      </div>
    </div>
  </div>
</div>
<script>
	$(document).on("click", ".claimbouns", function (e) {
		e.preventDefault();

		let redirectUrl = $(this).data('url'); // Your deposit page URL

		Swal.fire({
			title: "Are you sure?",
			text: "You need to make a deposit to claim this bonus.",
			icon: "warning",
			showCancelButton: true,
			confirmButtonText: "Yes, proceed",
			cancelButtonText: "Cancel",
		}).then((result) => {
			if (result.isConfirmed) {
				window.location.href = redirectUrl;   // 🔥 Redirect to deposit page
			}
		});
	});
</script>
@endsection

