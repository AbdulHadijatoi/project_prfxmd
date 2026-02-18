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

	/* Lightbox background */
.lightbox {
    display: none;
    position: fixed;
    z-index: 9999;
    padding-top: 40px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background: rgba(0, 0, 0, 0.85);
}

/* Lightbox image */
.lightbox-img {
    margin: auto;
    display: block;
    max-width: 90%;
    max-height: 90vh;
    border-radius: 10px;
}

/* Close button */
.lightbox-close {
    position: absolute;
    top: 15px;
    right: 25px;
    color: #fff;
    font-size: 40px;
    font-weight: 700;
    cursor: pointer;
    transition: 0.3s;
}

.lightbox-close:hover {
    color: rgb(183, 183, 183);
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
              <h4 class="mb-0">Promotions</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-12">
        
            <div class="row g-2">
				<!-- Offer Card 1 -->
				@forelse($promotionsdata as $promo)
				<div class="col-lg-12 col-xl-6 col-xxl-4">
					<div class="card banking-card pull-up">
						<div class="card-body">
							<div class="row m-b-30">
								<div class="col-md-5 col-xxl-12">
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
								</div>
								<div class="col-md-7 col-xxl-12" style="height: 150px">
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
							</div>
						</div>
						<div class="card-footer">
							<button class="btn btn-success btn-sm light text-uppercase btn-block me-3 w-100 text-black enrollpromo" data-url="{{ route('promo.enroll', ['promoid' => md5($promo->promo_id)]) }}" >Enroll Now</button>
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

<!-- LIGHTBOX POPUP -->
<div id="lightbox" class="lightbox">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-img" id="lightbox-img">
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const lightbox = document.getElementById("lightbox");
    const lightboxImg = document.getElementById("lightbox-img");
    const closeBtn = document.querySelector(".lightbox-close");

    // Click image -> open lightbox
    document.querySelectorAll(".promo-img").forEach(img => {
        img.addEventListener("click", function () {
            lightbox.style.display = "block";
            lightboxImg.src = this.src;
        });
    });

    // Close on click X
    closeBtn.addEventListener("click", function () {
        lightbox.style.display = "none";
    });

    // Close on clicking outside image
    lightbox.addEventListener("click", function (e) {
        if (e.target === lightbox) {
            lightbox.style.display = "none";
        }
    });
});
</script>

<script>
	$(document).on("click", ".enrollpromo", function (e) {
		e.preventDefault();

		let redirectUrl = $(this).data('url'); // Your deposit page URL

		Swal.fire({
			title: "Are you sure?",
			text: "You are enroll this promotions?",
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
