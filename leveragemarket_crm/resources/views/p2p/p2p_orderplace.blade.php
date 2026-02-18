@extends('layouts.crm.crm')
@push('styles')
<link rel="stylesheet" 
href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')

<div class="pc-container">
    <div class="pc-content">
		<div class="row">
			<div class="col-12">
				<div class="row text-muted mb-2">
					<div class="col-md-6 col-12">
						<div class="card p-4">
							<h4>Advertise Details</h4>
							<div class="table-responsive">
								<table class="table table-borderless align-middle">
									<tbody>
										<tr>
											<td class="text-muted">Type</td>
											<td class="text-end fw-semibold text-success">{{ $merchant->wanttype }}</td>
										</tr>
										<tr>
											<td class="text-muted">Asset</td>
											<td class="text-end fw-semibold">{{ $merchant->cryptoval }}</td>
										</tr>
										<tr>
											<td class="text-muted">Currency</td>
											<td class="text-end fw-semibold">{{ $merchant->currency_code }}</td>
										</tr>
										<tr>
											<td class="text-muted">Price Type</td>
											<td class="text-end fw-semibold">{{ ucfirst($merchant->pricetype) }}</td>
										</tr>
										<tr>
											<td class="text-muted">Fixed Price</td>
											<td class="text-end fw-semibold">{{ $merchant->quoteprice}} {{ $merchant->currency_code }}</td>
										</tr>
										<tr>
											<td class="text-muted">Order Limit</td>
											<td class="text-end fw-semibold">{{ $merchant->min_limit }} INR ~ {{ $merchant->max_limit }} INR</td>
										</tr>
										<tr>
											<td class="text-muted">Total Trading Amount</td>
											<td class="text-end fw-semibold">{{ $merchant->total_amount }} {{ $merchant->cryptoval }}</td>
										</tr>
										<tr>
											<td class="text-muted">
												Estimated Fee
												<i class="fa fa-info-circle ms-1" data-bs-toggle="tooltip"
												   title="Trading fee charged by platform"></i>
											</td>
											<td class="text-end fw-semibold">0.15%</td>
										</tr>

										<tr><td colspan="2"><hr class="my-2"></td></tr>

										<tr>
											<td class="text-muted">Payment Method</td>
											<td class="text-end fw-semibold">
												@foreach (json_decode($merchant->payment_method, true) as $method)
													<span class="badge bg-secondary me-1">
														{{ ucfirst($method) }}
													</span>
												@endforeach 
											</td>
										</tr>
										<tr>
											<td class="text-muted">Payment Time Limit</td>
											<td class="text-end fw-semibold">{{ $merchant->time_limit }} min</td>
										</tr>
										<tr>
											<td class="text-muted">Available Region(s)</td>
											<td class="text-end fw-semibold">All Regions</td>
										</tr>
										<tr>
											<td class="text-muted">Status</td>
											<td class="text-end fw-semibold text-success">{{ $merchant->transferstatus }}</td>
										</tr>
									</tbody>
								</table>
							</div>

						</div>
					</div>
					
					<div class="col-md-6 col-12">
						<div class="card p-4">
						<form action="{{ route('p2porderstore') }}" id="p2porderForm" method="POST" enctype="multipart/form-data" >
						@csrf
							<input type="hidden" name="merchantaccid" value="{{ $merchant->id }}" />
							<input type="hidden" name="orderprice" value="{{ $merchant->quoteprice }}" />
							<input type="hidden" name="orderpaycurrency" value="{{ $merchant->currency_code }}" />
							<input type="hidden" name="orderreceivecurrency" value="{{ $merchant->cryptoval }}" />
							<input type="hidden" name="orderusdval" class="orderusdval" value="" />
							<h4>{{ $merchant->wanttype }} {{ $merchant->cryptoval }}</h4>
							<div class="d-flex align-items-center"> 
								<h6>Wallet Balance : <span class="text-success">$ {{ $walletBalance }}</span></h6>
							</div>
							<div class="d-flex align-items-center"> 
								<h6>{{ $merchant->currency_code }} Balance : <span class="text-success converamountval">0</span></h6>
							</div>
							<div class="mt-3">
								<label>You Pay ({{ $merchant->currency_code }})</label>
								<input type="number"
									   class="form-control orderamount"
									   name="orderamount"
									   placeholder="{{ $merchant->min_limit }} - {{ $merchant->max_limit }}"
									   required />
								<small class="text-danger d-none pay-error"></small>
							</div>

							<div class="mt-3">
								<label>You Receive ({{ $merchant->cryptoval }})</label>
								<input type="text" class="form-control orderconvertcrypto" name="orderconvertcrypto" readonly />
							</div>
							
							<div class="mt-3">
								<label>Payment Method</label>
								<select class="form-control paymentMethod" name="orderpaymentmethod" >
									<option> Choose Option</option>
									@foreach (json_decode($merchant->payment_method, true) as $method)
									<option value="{{ strtolower($method) }}">{{ ucfirst($method) }}</option>
									@endforeach
								</select>
							</div>
							@if(!empty($wallet_accounts))
							<div class="mt-3 cryptoDiv d-none">
								<label>Crypto Wallet</label>

								@foreach ($wallet_accounts as $walletacc)
								<table class="table table-borderless align-middle">
									<tbody>
										<tr>
											<td class="text-muted">Wallet Network</td>
											<td class="text-end fw-semibold text-success">
												{{ $walletacc->wallet_network }}
											</td>
										</tr>
										<tr>
											<td class="text-muted">Wallet Address</td>
											<td class="text-end fw-semibold">
												<span class="copyText">{{ $walletacc->wallet_address }}</span>
												<button type="button"
														class="btn btn-sm btn-outline-secondary ms-2 copyBtn">
													Copy
												</button>
											</td>
										</tr>
									</tbody>
								</table>
								@endforeach
							</div>
							@endif
							
							@if(!empty($bank_accounts))
							<div class="mt-3 bankDiv d-none">
								<label>Bank Details</label>

								@foreach ($bank_accounts as $bankacc)
								<table class="table table-borderless align-middle">
									<tbody>
										<tr>
											<td class="text-muted">Bank Name</td>
											<td class="text-end fw-semibold text-success">
												{{ $bankacc->bankName }}
											</td>
										</tr>
										<tr>
											<td class="text-muted">Holder Name</td>
											<td class="text-end fw-semibold">
												{{ $bankacc->ClientName }}
											</td>
										</tr>
										<tr>
											<td class="text-muted">Account Number</td>
											<td class="text-end fw-semibold">
												<span class="copyText">{{ $bankacc->accountNumber }}</span>
												<button type="button"
														class="btn btn-sm btn-outline-secondary ms-2 copyBtn">
													Copy
												</button>
											</td>
										</tr>
										<tr>
											<td class="text-muted">IFSC Code</td>
											<td class="text-end fw-semibold">
												<span class="copyText">{{ $bankacc->code }}</span>
												<button type="button"
														class="btn btn-sm btn-outline-secondary ms-2 copyBtn">
													Copy
												</button>
											</td>
										</tr>
										<tr>
											<td class="text-muted">Swift Code</td>
											<td class="text-end fw-semibold">
												<span class="copyText">{{ $bankacc->swift_code }}</span>
												<button type="button"
														class="btn btn-sm btn-outline-secondary ms-2 copyBtn">
													Copy
												</button>
											</td>
										</tr>
									</tbody>
								</table>
								@endforeach
							</div>
							@endif

							<div class="mt-3">
								<label>Upload Payment Proof</label>
								<input type="file" class="form-control orderpaymentproof" name="orderpaymentproof" accept="image/*,.pdf" required />
							</div>
							<button class="btn btn-success mt-4 text-black confirmBuy">Confirm Buy</button>
						</form>
						</div>
						
						<div class="card p-3">
							<div class="d-flex align-items-center mb-2">
								<h6 class="mb-0 text-secondary fw-semibold">Advertisers' Terms</h6>
								<span class="text-danger ms-1">*</span>
							</div>

							<div class="terms-box">
								<ul class="list-unstyled mb-0">

									<li>
										<span class="icon">📝</span>
										<strong>Payment Rules</strong>
									</li>

									<li>
										<span class="icon">⚡</span>
										Auto Release – Instant if name matches.
									</li>

									<li>
										<span class="icon">🚫</span>
										No 3rd Party Payments (Husband, Wife, Parents, Friends – all considered 3rd party.  
										If paid from them → Refund only after 24h).
									</li>

									<li>
										<span class="icon">✅</span>
										Pay only via given UPI / QR.
									</li>

									<li>
										<span class="icon">⚠️</span>
										If not auto → Slight mismatch in name.
									</li>

									<li>
										<span class="icon">🕒</span>
										Don’t worry – I’ll release manually when online.
									</li>

									<li>
										<span class="icon">📵</span>
										No calls / WhatsApp (Binance chat only).  
										Calling or messaging = Report → Account may be suspended.
									</li>

									<li class="mt-2 border-bottom pb-2">
										<span class="icon">🙏</span>
										Thanks for trading!
									</li>

									<!-- Hindi Section -->
									<li class="mt-3 fw-semibold">📝 भुगतान नियम</li>

									<li>⚡ ऑटो रिलीज – नाम सही हो तो तुरंत।</li>
									<li>🚫 थर्ड पार्टी पेमेंट मना है (पति, पत्नी, माता-पिता, दोस्त – सभी थर्ड पार्टी माने जाएंगे। अगर इनसे पेमेंट हुआ → रिफंड सिर्फ 24 घंटे बाद होगा)।</li>
									<li>✅ सिर्फ दिए गए UPI / QR से पेमेंट करें।</li>
									<li>⚠️ अगर ऑटो न हो → नाम में थोड़ा फर्क।</li>
									<li>🕒 चिंता न करें – मैं ऑनलाइन आकर मैनुअली रिलीज कर दूंगा।</li>
									<li>📵 कॉल / व्हाट्सएप नहीं (सिर्फ Binance चैट)। कॉल/मैसेज = रिपोर्ट → अकाउंट सस्पेंड हो सकता है।</li>
									<li>🙏 धन्यवाद!</li>

								</ul>
							</div>
						</div>
						
					</div>
					
				</div>
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
	@if($errors->any())
		<div class="alert alert-danger">
			{{ $errors->first() }}
		</div>
	@endif
<script>
$(document).ready(function () {
    fetchExchangeRate();

    function fetchExchangeRate() {
        let asset = 'USD';
        let fiat = "<?php echo $merchant->currency_code ?>";
        let currentwalletbal = parseFloat("<?php echo $walletBalance ?>") || 0;
        let convertBalance = 0;

        $.get("/exchange-rate", { asset: asset, fiat: fiat }, function (res) {
            let marketRate = parseFloat(res.rate) || 0;
            convertBalance = marketRate * currentwalletbal;
            $('.converamountval').text(convertBalance.toFixed(2));
        });
    }
	
	const pricePerUSDT = parseFloat("{{ $merchant->quoteprice }}");
    const minLimit = parseFloat("{{ $merchant->min_limit }}");
    const maxLimit = parseFloat("{{ $merchant->max_limit }}");
	
	$('.orderamount').on('input', function () {
        let payAmount = parseFloat($(this).val());
        let $error = $('.pay-error');

        // Reset
        $('.orderconvertcrypto').val('');
        $('.confirmBuy').prop('disabled', true);
        $error.addClass('d-none');

        if (!payAmount || payAmount <= 0) return;

        // Min / Max validation
        if (payAmount < minLimit || payAmount > maxLimit) {
            $error
                .removeClass('d-none')
                .text(`Order Limits: ₹${minLimit.toLocaleString()} - ₹${maxLimit.toLocaleString()}`);
            return;
        }

        // Convert INR → USDT
        let receiveUSDT = payAmount / pricePerUSDT;

        $('.orderconvertcrypto').val(receiveUSDT.toFixed(2));
        $('.confirmBuy').prop('disabled', false);
		
		let asset = "USD";
        let fiat = "<?php echo $merchant->currency_code ?>";

        $.get("/exchange-rate", { asset: asset, fiat: fiat }, function (res) {
            let marketRate = parseFloat(res.rate) || 0;
			let currentwalletbal = parseFloat("<?php echo $walletBalance ?>") || 0;
            let convertusdBalance = payAmount / marketRate;
			if (convertusdBalance > currentwalletbal) {
				$('.orderusdval').val('');

				Swal.fire({
					icon: 'error',
					title: 'Insufficient Balance',
					html: `
						<p><b>Required:</b> ${convertusdBalance.toFixed(2)} USD</p>
						<p><b>Available:</b> ${currentwalletbal.toFixed(2)} USD</p>
					`,
					confirmButtonText: 'OK'
				});

				// Block further action
				$('.confirmBuy').prop('disabled', true);
				return;
			} else {
				$('.confirmBuy').prop('disabled', false);
			}
            $('.orderusdval').val(convertusdBalance.toFixed(2));
        });
    });
	
	// Payment method change
    $('.paymentMethod').on('change', function () {
        let method = $(this).val();

        $('.cryptoDiv, .bankDiv').addClass('d-none');

        if (method === 'cryptotransfer') {
            $('.cryptoDiv').removeClass('d-none');
        }

        if (method === 'banktransfer') {
            $('.bankDiv').removeClass('d-none');
        }
    });

    // Copy to clipboard
    $(document).on('click', '.copyBtn', function () {
        let text = $(this).closest('td').find('.copyText').text().trim();

        navigator.clipboard.writeText(text).then(() => {
            $(this).text('Copied');
            setTimeout(() => $(this).text('Copy'), 1500);
        });
    });
	
	$('.confirmBuy').on('click', function (e) {
        e.preventDefault();

        let payAmount = $('.orderamount').val();
        let receiveAmount = $('.orderconvertcrypto').val();
        let paymentMethod = $('.paymentMethod').val();

        if (!payAmount || !receiveAmount || !paymentMethod) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Order',
                text: 'Please complete all required fields',
            });
            return;
        }

        Swal.fire({
            title: 'Confirm Order',
            html: `
                <p><b>You Pay:</b> ${payAmount}</p>
                <p><b>You Receive:</b> ${receiveAmount}</p>
                <p><b>Payment Method:</b> ${paymentMethod.toUpperCase()}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Confirm',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('p2porderForm').submit();
            }
        });
    });
	
	
	
});
</script>
@endsection