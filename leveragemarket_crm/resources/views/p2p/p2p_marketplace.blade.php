@extends('layouts.crm.crm')
<style>
    .p2p-card { background:#1A1D21; border:1px solid #2C2F33; border-radius:8px; }
    .filter-btn.active { background:#FCD535; color:#000; }
</style>
@section('content')

<div class="pc-container">
    <div class="pc-content pt-0">
        <div class="page-header mb-0 pb-0">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title h2">
                            <h4 class="mb-0">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="row">
			<div class="col-12">
				@if(!empty($providerlist))
				
				<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
					<!-- Buy / Sell -->
					<div class="d-flex gap-2">
					<select class="form-select w-auto">
						<option>Buy</option>
						<option>Sell</option>
					</select>

					<!-- Currency -->
					<select class="form-select w-auto" name="cryptoval">
					@foreach($cryptolist as $cp)
						<option value="{{ $cp->symbol }}">{{ $cp->symbol }}</option>
					@endforeach
					</select>
					</div>
					
					<div class="d-flex gap-2">
					<!-- Payment Currency -->
					<select class="form-select w-auto" name="currencyval">
					@foreach($currency as $cur)
						<option value="{{ $cur->currency_code }}">{{ $cur->currency_symbol }} - {{ $cur->currency_code }}</option>
					@endforeach
					</select>

					<!-- Payment Methods -->
					<select class="form-select w-auto " name="paymenttype">
						<option value="all">All payment methods</option>
						<option value="upi">UPI</option>
						<option value="banktransfer">Bank Transfer</option>
					</select>

					<!-- Sort By -->
					<select class="form-select w-auto " name="orderby">
						<option>Sort by Price</option>
						<option value="lowprice">Lowest First</option>
						<option value="highprice">Highest First</option>
					</select>
					</div>

				</div>
				
				<div class="row text-muted px-2 mb-2" name="paymenttype">
					<div class="col-3">Advertisers</div>
					<div class="col-2">Price</div>
					<div class="col-3">Available / Order Limit</div>
					<div class="col-2">Payment</div>
					<div class="col-2 text-end">Trade</div>
				</div>
				
				
				@foreach($providerlist as $prolist)
				<div class="card p2p-card mb-3 pull-up">
					<div class="card-body d-flex align-items-center">

						<!-- Advertiser -->
						<div class="col-3 d-flex align-items-center">
							<img src="https://ui-avatars.com/api/?name=LM&background=444&color=fff" 
								 class="rounded-circle me-2" width="35" height="35">
							<div>
								<h6 class="mb-0 fw-bold"> {{ $prolist->merchantcompany }} <span class="badge bg-warning text-dark">Pro</span></h6>
								<small class="text-muted">0 orders | 100% completion</small><br>
								<small class="text-success">✔ 99.96% | ⏱ {{ $prolist->time_limit }} min</small>
							</div>
						</div>

						<!-- Price -->
						<div class="col-2">
							<h5 class="fw-bold text-warning">₹ {{ $prolist->quoteprice }}</h5>
						</div>

						<!-- Available / Limit -->
						<div class="col-3">
							<div class="fw-semibold">{{ $prolist->total_amount }} {{ $prolist->cryptoval }}</div>
							<small class="text-muted">{{ $prolist->min_limit }} – {{ $prolist->max_limit }} INR</small>
						</div>

						<!-- Payment -->
						<div class="col-2">
							@foreach (json_decode($prolist->payment_method, true) as $method)
								<span class="badge bg-secondary me-1">
									{{ ucfirst($method) }}
								</span>
							@endforeach 
						</div>

						<!-- Button -->
						<div class="col-2 text-end">
							<button class="btn {{ strtolower($prolist->wanttype) === 'buy' ? 'btn-primary' : 'btn-danger text-black' }} fw-bold px-4 buyp2p" data-marketid="{{ md5($prolist->id) }}"  data-url="{{ route('p2pbuy', md5($prolist->id)) }}" >{{ $prolist->wanttype }} {{ $prolist->cryptoval }}</button>
						</div>
					</div>
				</div>
				@endforeach
				@else
				<div class="card p2p-card mb-3 pull-up">
					<div class="card-body d-flex align-items-center">
						<p>No Records</p>
					</div>
				</div>
				
				@endif
				
			</div>			
		</div>
    </div>
</div>
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
	<script>
	$(document).on('click', '.buyp2p', function () {

		let redirectUrl = $(this).data('url');

		Swal.fire({
			title: 'Please wait...',
			text: 'Redirecting to buy/sell page',
			allowOutsideClick: false,
			allowEscapeKey: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		// Small delay for better UX
		setTimeout(function () {
			window.location.href = redirectUrl;
		}, 1000);
	});
	</script>
@endsection
