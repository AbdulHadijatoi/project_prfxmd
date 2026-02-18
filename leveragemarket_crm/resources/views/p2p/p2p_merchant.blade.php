@extends('layouts.crm.crm')
@section('content')
<style>
    .dropdown-menu {
        background: #0d1117;
        border: 1px solid #2b3139;
        border-radius: 10px;
    }
    .form-check-label {
        color: #fff;
    }
    small.text-muted {
        color: #9ba1a6 !important;
    }
    .btn-dark {
        background: #161b22;
        border: 1px solid #30363d;
        padding: 10px 15px;
    }
</style>

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
				@php
					$isEdit = isset($data);
				@endphp
				<form action="{{ $isEdit ? route('p2pmerchantupdate', $data->id) : route('p2pmerchantstore') }}" method="POST">
					@csrf
					<div class="card">
						<div class="card-header">
							<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
								<div class="d-flex gap-2">
									<h4>Set Type & Price</h4>
								</div>
								
								<div class="d-flex gap-2">
									<h4>Your Price</h4>
									<h3 class="text-success yourselprice" style="font-size:18px;">{{ $cryptolist[0]->defaultprice }}</h3>
								</div>
								
								<div class="d-flex gap-2">
									<h4>Highest Order Price</h4>
									<h3 class="text-danger highorderprice" style="font-size:18px;">106.75</h3>
								</div>
								
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="form-label fw-bold">Company Name</label> <br />
										<input type="text" id="merchantcompany" name="merchantcompany" class="form-control" value="" required />
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="form-label fw-bold">I Want to?</label> <br />

										<input type="radio" id="want_buy" name="wanttype" value="Buy" checked />
										<label for="want_buy" class="form-label">Buy</label>&nbsp;&nbsp;&nbsp;&nbsp;

										<input type="radio" id="want_sell" name="wanttype" value="Sell">
										<label for="want_sell" class="form-label">Sell</label>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="form-label">Asset</label>
										<select class="form-select" name="cryptoval" id="asset">
										@foreach($cryptolist as $cp)
											<option value="{{ $cp->symbol }}"
												data-min="{{ $cp->minprice }}"
												data-max="{{ $cp->maxprice }}"
												data-default="{{ $cp->defaultprice }}">
												{{ $cp->symbol }}
											</option>
										@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="form-label">With Fiat</label>
										<select class="form-select" name="currency_code" id="currency_code">
										@foreach($currency as $cur)
											 <option value="{{ $cur->currency_code }}">
												{{ $cur->currency_symbol }} - {{ $cur->currency_code }}
											</option>
										@endforeach
										</select>
									</div>
								</div>
								<div class="col-md-4 col-12">
									<div class="form-group">
										<label class="form-label fw-bold">Price type</label> <br />

										<input type="radio" id="price_fixed" class="pricetype" name="pricetype" value="fixed" checked />
										<label for="price_fixed" class="form-label">Fixed</label>&nbsp;&nbsp;&nbsp;&nbsp;

										<input type="radio" id="price_floating" class="pricetype" name="pricetype" value="floating">
										<label for="price_floating" class="form-label">Floating</label>
										
									</div>
								</div>
								<div class="col-md-4 col-12">
									<div class="form-group">
										<label class="form-label pricetypetext">Fixed</label>
										 <div class="d-flex align-items-center justify-content-between border rounded px-3 py-2 fixed-box">
											<button type="button" class="btn btn-sm fw-bold fixed-minus">−</button>

											<span class="fw-semibold fs-5 fixed-value">{{ $cryptolist[0]->defaultprice }}</span>

											<button type="button" class="btn btn-sm fw-bold fixed-plus">+</button>
											<input type="hidden" name="quoteprice" class="quoteprice" value="{{ $cryptolist[0]->defaultprice }}" />
											<input type="hidden" name="cryptoquoteprice" class="cryptoquoteprice" value="{{ $cryptolist[0]->defaultprice }}" />
										</div>

										<small class="text-muted d-block mt-1" id="priceHelp">
											The fixed price should be between <span id="minVal">{{ $cryptolist[0]->minprice }}</span> - <span id="maxVal">{{ $cryptolist[0]->maxprice }}</span>
										</small>
										
										<small class="text-muted d-none mt-2" id="floatingFormula"></small>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="card">
						<div class="card-header">
							<h4>Set Total Amount & Payment Method</h4>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="mb-4">
									<label class="form-label fw-semibold">Total Amount</label>

									<div class="d-flex align-items-center border rounded px-3 py-2">
										<input type="number" class="form-control border-0 shadow-none p-0 total_amount" placeholder="100" name="total_amount" value="" />
										<span class="ms-3 text-muted fw-semibold totamountCrypto">USDT</span>
									</div>

									<small class="text-muted d-block mt-1 availabletotalamount">= 0 INR</small>
								</div>

								<!-- Order Limit -->
								<div class="mb-4">
									<label class="form-label fw-semibold">Order Limit</label>
									<div class="d-flex gap-3">
										<!-- Min -->
										<div class="d-flex align-items-center flex-grow-1 border rounded px-3 py-2">
											<input type="number" class="form-control border-0 shadow-none p-0 orderminlimit" placeholder="200" name="min_limit" value="" />
											<span class="ms-3 text-muted fw-semibold orderlimitcurrency">INR</span>
										</div>
										<div class="fw-semibold fs-5 text-muted">~</div>
										<!-- Max -->
										<div class="d-flex align-items-center flex-grow-1 border rounded px-3 py-2">
											<input type="number" class="form-control border-0 shadow-none p-0 ordermaxlimit" placeholder="2,000,000" name="max_limit" value="">
											<span class="ms-3 text-muted fw-semibold orderlimitcurrency">INR</span>
										</div>
									</div>
									<div class="d-flex justify-content-between mt-1">
										<small class="text-muted orderminlimitvalue">= 0 USDT</small>
										<small class="text-muted ordermaxlimitvalue">= 0 USDT</small>
									</div>
								</div>

								<!-- Payment Method -->
								
								<div class="mb-3 col-md-6 col-12">
									<label class="form-label fw-bold">Payment Method</label>
									<!-- Dropdown -->
									<div class="dropdown w-100">
										<button class="form-control w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">Add Payment Method
											<i class="bi bi-chevron-down"></i>
										</button>

										<div class="dropdown-menu p-3 shadow-lg" style="width: 100%; max-height:400px; overflow-y:auto;">
											
											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="payment_method[]" type="checkbox" value="cryptotransfer">
												<label class="form-check-label fw-semibold">Crypto</label>
												<small class="text-white d-block">Crypto Payment</small>
											</div>
											
											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="payment_method[]" type="checkbox" value="banktransfer">
												<label class="form-check-label fw-semibold">Bank Transfer</label>
												<small class="text-white d-block">Instant Transfer</small>
											</div>
										</div>
									</div>
								</div>

								<!-- Payment Time Limit -->
								<div class="mb-3 col-md-6 col-12">
									<label class="form-label fw-semibold">Payment Time Limit</label>
									<select class="form-select border rounded mt-1" name="time_limit">
										<option value="15">15 min</option>
										<option value="30">30 min</option>
										<option value="45">45 min</option>
										<option value="60">1 hr</option>
										<option value="120">2 hr</option>
										<option value="180">3 hr</option>
										<option value="240">4 hr</option>
										<option value="300">5 hr</option>
										<option value="360">6 hr</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					
					<div class="card">
						<div class="card-header">
							<h4>Set Remarks & Automatic Response</h4>
						</div>
						<div class="card-body">
							<div class="row">
								<?PHP /* <div class="mb-3">
									<label class="form-label fw-bold">Terms Tags (Optional)</label>
									<!-- Dropdown -->
									<div class="dropdown w-100">
										<button class="form-control w-100 d-flex justify-content-between align-items-center"
												type="button" data-bs-toggle="dropdown" aria-expanded="false">
											Add tags
											<i class="bi bi-chevron-down"></i>
										</button>

										<div class="dropdown-menu p-3 shadow-lg" style="width: 100%; max-height:400px; overflow-y:auto;">
											
											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="bank_statement">
												<label class="form-check-label fw-semibold">Bank statement required</label>
												<small class="text-white d-block">Bank statement will be required for addition verification</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="extra_kyc">
												<label class="form-check-label fw-semibold">Extra KYC required</label>
												<small class="text-white d-block">Need to complete one time extra KYC verification</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="no_additional_verification">
												<label class="form-check-label fw-semibold">No Additional Verification Needed</label>
												<small class="text-white d-block">No additional verification requirements from the maker</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="no_payment_receipt">
												<label class="form-check-label fw-semibold">No Payment Receipt Needed</label>
												<small class="text-white d-block">Receipt not required for this trade</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="pan_required">
												<label class="form-check-label fw-semibold">PAN Required</label>
												<small class="text-white d-block">PAN number is required</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="payment_receipt_required">
												<label class="form-check-label fw-semibold">Payment Receipt Required</label>
												<small class="text-white d-block">You must provide transaction receipt to complete the trade</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="photo_id_required">
												<label class="form-check-label fw-semibold">Photo ID Required</label>
												<small class="text-white d-block">Valid government-issued photo ID required</small>
											</div>

											<!-- ITEM -->
											<div class="form-check mb-3">
												<input class="form-check-input" name="tags[]" type="checkbox" value="tds_applied">
												<label class="form-check-label fw-semibold">TDS applied</label>
												<small class="text-white d-block">TDS will be deducted / paid</small>
											</div>

										</div>
									</div>
								</div> */ ?>
								
								<div class="mb-3 col-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label class="form-label">Remarks (Optional)</label>
										<textarea aria-label="Please do not include any crypto-related words, such as crypto, P2P, C2C, BTC, USDT, ETH etc." class="form-control" placeholder="Please do not include any crypto-related words, such as crypto, P2P, C2C, BTC, USDT, ETH etc." name="remarks" maxlength="1000"></textarea>
									</div>
								</div>
								
								<div class="mb-3 col-12 col-md-6 col-lg-6">
									<div class="form-group">
										<label class="form-label">Auto Reply (Optional)</label>
										<textarea aria-label="Auto reply message will be sent to the counterparty once the order is created" class="form-control" placeholder="Auto reply message will be sent to the counterparty once the order is created" name="autoreply" maxlength="1000"></textarea>
									</div>
								</div>
								
								<div class="mb-3">
									<div class="form-group">
										<label class="form-label fw-bold">Status</label> <br />

										<input type="radio" id="status_online" name="transferstatus" value="Online">
										<label for="status_online" class="form-label">Online</label>&nbsp;&nbsp;&nbsp;&nbsp;

										<input type="radio" id="status_offline" name="transferstatus" value="Offline">
										<label for="status_offline" class="form-label">Offline</label>&nbsp;&nbsp;&nbsp;&nbsp;

										<input type="radio" id="status_private" name="transferstatus" value="Private">
										<label for="status_private" class="form-label">Private</label>
									</div>
								</div>
							</div>
						</div>
						<div class="card-footer">
							<div class="mt-4 d-flex justify-content-end gap-3">
								<a href="{{ url()->previous() }}" class="btn btn-danger px-4 text-black">Cancel</a>
								<button type="submit" class="btn btn-primary px-4">Submit</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div>
</div>

<script>
$(document).ready(function () {
	let marketRate = 0;
	let fixedValue = 0;
	let floatingPercent = 97;
	let fixedMultiplier = $(".fixed-value").text();
	
	//let fixedValue = parseFloat($(".quoteprice").val());

	$("#asset").change(function () {

		let opt = $(this).find(":selected");

		let min = parseFloat(opt.data("min"));
		let max = parseFloat(opt.data("max"));
		let def = parseFloat(opt.data("default"));

		$("#minVal").text(min);
		$("#maxVal").text(max);

		fixedValue = def;

		$(".fixed-value").text(def.toFixed(2));
		$(".quoteprice").val(def.toFixed(2));
		
		fetchExchangeRate();
	});

	$("#currency_code").change(function () {
		fetchExchangeRate();
	});
	
	$('input[name="pricetype"]').change(function () {
		if ($(this).val() === 'fixed') {
			$(".pricetypetext").text("Fixed");
			$("#priceHelp").removeClass("d-none");
			$("#floatingFormula").addClass("d-none");
			updateFixed();
		} else {
			$(".pricetypetext").text("Floating Price Margin");
			$("#priceHelp").addClass("d-none");
			$("#floatingFormula").removeClass("d-none");
			calculateFloating();
		}
	});
	
	$(".fixed-plus").click(function () {
		if ($('input[name="pricetype"]:checked').val() === 'fixed') {

			let fixedValue = parseFloat($('.quoteprice').val());
			let cryptofixedValue = parseFloat($('.cryptoquoteprice').val());
			if (isNaN(fixedValue)) fixedValue = 0;

			let max = parseFloat($("#maxVal").text());

			fixedValue = Math.round((fixedValue + 0.1) * 10) / 10;
			cryptofixedValue = fixedValue / marketRate;

			if (fixedValue > max) fixedValue = max;

			$('.quoteprice').val(fixedValue.toFixed(2));
			$('.fixed-value').text(fixedValue.toFixed(2));
			$(".cryptoquoteprice").val(cryptofixedValue.toFixed(4));
			
			updateAvailableTotal();
			updateOrderLimitCrypto();

		} else {

			floatingPercent++;
			if (floatingPercent > 100) floatingPercent = 100;

			calculateFloating();
			updateAvailableTotal();
			updateOrderLimitCrypto();
		}
	});
	
	$(".fixed-minus").click(function () {
		if ($('input[name="pricetype"]:checked').val() === 'fixed') {
			let fixedValue = parseFloat($('.quoteprice').val());
			if (isNaN(fixedValue)) fixedValue = 0;

			let min = parseFloat($("#minVal").text());

			fixedValue = Math.round((fixedValue - 0.1) * 10) / 10;

			if (fixedValue < min) fixedValue = min;

			$('.quoteprice').val(fixedValue.toFixed(2));
			$('.fixed-value').text(fixedValue.toFixed(2));
			
			updateAvailableTotal();
			updateOrderLimitCrypto();
		} else {
			floatingPercent--;
			if (floatingPercent < 96) floatingPercent = 96;
			calculateFloating();
			updateAvailableTotal();
			updateOrderLimitCrypto();
		}
	});
	
	fetchExchangeRate();
	
	function fetchExchangeRate() {

		let asset = $("#asset").val();
		let fiat  = $("#currency_code").val();
		$.get("/exchange-rate", { asset, fiat }, function (res) {
			marketRate = res.rate || 0;
			if ($('input[name="pricetype"]:checked').val() === 'fixed') {
				updateFixed();
			} else {
				calculateFloating();
			}
			$('.totamountCrypto').text(asset);
			$('.orderlimitcurrency').text(fiat);
		});
	}

	function updateFixed() {

		if (!marketRate || marketRate === 0) return;

		let opt = $('#asset option:selected');

		let min = parseFloat(opt.data("min"));
		let max = parseFloat(opt.data("max"));
		
		
		if (fixedMultiplier < min) fixedMultiplier = min;
		if (fixedMultiplier > max) fixedMultiplier = max;

		let finalPrice = marketRate * fixedMultiplier;

		$(".fixed-value").text(finalPrice.toFixed(2));
		$(".quoteprice").val(finalPrice.toFixed(2));
		$(".yourselprice").text(finalPrice.toFixed(2));
		$(".cryptoquoteprice").val(fixedMultiplier);

		$("#minVal").text((marketRate * min).toFixed(2));
		$("#maxVal").text((marketRate * max).toFixed(2));
	}
	
	function calculateFloating() {

		if (!marketRate || marketRate === 0) return;
		
		let fiat  = $("#currency_code").val();
		let price = marketRate * (floatingPercent / 100);

		$(".fixed-value").text(floatingPercent + "%");
		$(".quoteprice").val(price.toFixed(2));
		
		if (!marketRate || marketRate === 0) return;

		if (floatingPercent < 96) floatingPercent = 96;
		if (floatingPercent > 100) floatingPercent = 100;

		let finalPrice = marketRate * (floatingPercent / 100);

		$(".fixed-value").text(floatingPercent + "%");
		$(".quoteprice").val(finalPrice.toFixed(2));
		$(".yourselprice").text(finalPrice.toFixed(2));

		$("#floatingFormula").html(
			'Pricing formula ' +
			marketRate.toFixed(2) + ' × ' +
			floatingPercent.toFixed(2) + '% ≈ <b>' +
			finalPrice.toFixed(2) + ' ' + fiat +
			'</b>'
		);	
	}
	
	$('.total_amount').on('input', function () {
		updateAvailableTotal();
	});
	
	function updateAvailableTotal() {

		let totalAmount = parseFloat($('.total_amount').val());
		let quotePrice  = parseFloat($('.quoteprice').val());
		let fiat  = $("#currency_code").val();

		if (isNaN(totalAmount) || isNaN(quotePrice)) {
			$('.availabletotalamount').text('= 0 INR');
			return;
		}

		let totalFiat = totalAmount * quotePrice;

		$('.availabletotalamount').text(
			'= ' + totalFiat.toLocaleString(undefined, {
				minimumFractionDigits: 2,
				maximumFractionDigits: 2
			}) +' '+fiat
		);
	}
	
	$('.orderminlimit, .ordermaxlimit').on('input', function () {
		updateOrderLimitCrypto();
	});
	
	function updateOrderLimitCrypto() {

		let quotePrice = parseFloat($('.quoteprice').val());
		let crypto     = $('.totamountCrypto').text().trim(); // USDT

		if (isNaN(quotePrice) || quotePrice <= 0) {
			$('.orderminlimitvalue').text('= 0 ' + crypto);
			$('.ordermaxlimitvalue').text('= 0 ' + crypto);
			return;
		}

		// MIN LIMIT
		let minFiat = parseFloat($('.orderminlimit').val());
		if (!isNaN(minFiat)) {
			let minCrypto = minFiat / quotePrice;
			$('.orderminlimitvalue').text(
				'= ' + minCrypto.toFixed(2) + ' ' + crypto
			);
		} else {
			$('.orderminlimitvalue').text('= 0 ' + crypto);
		}

		// MAX LIMIT
		let maxFiat = parseFloat($('.ordermaxlimit').val());
		if (!isNaN(maxFiat)) {
			let maxCrypto = maxFiat / quotePrice;
			$('.ordermaxlimitvalue').text(
				'= ' + maxCrypto.toFixed(2) + ' ' + crypto
			);
		} else {
			$('.ordermaxlimitvalue').text('= 0 ' + crypto);
		}
	}
	
	$('form').on('submit', function (e) {
		const $form = $(this);
		e.preventDefault();		
		const $btn = $form.find('button[type="submit"]');

		// Change button text + disable
		$btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Please wait...');
		$btn.prop('disabled', true);

		// SweetAlert - Waiting Message
		Swal.fire({
			title: "Processing...",
			text: "P2P ads post will begin created shortly.",
			icon: "info",
			allowOutsideClick: false,
			allowEscapeKey: false,
			showConfirmButton: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

		// Submit after slight delay to show UI
		setTimeout(() => {
			$form.off('submit').submit();
		}, 800);
	});
    
});


</script>

<script>
    $(document).ready(function () {
        /*let value = parseFloat($(".fixed-value").text());
        let min = parseFloat($("#minVal").text());
        let max = parseFloat($("#maxVal").text());

        $(".fixed-minus").on("click", function () {
            if (value > min) {
                value = (value - 10).toFixed(2);
                $(".fixed-value").text(value);
            }
        });

        $(".fixed-plus").on("click", function () {
            if (value < max) {
                value = (value + 10).toFixed(2);
                $(".fixed-value").text(value);
            }
        });*/
    });
</script>

@endsection
