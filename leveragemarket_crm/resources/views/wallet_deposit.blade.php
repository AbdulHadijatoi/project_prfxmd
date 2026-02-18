@extends('layouts.crm.crm')
@section('content')
    <div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <iframe id="checkoutIframe" style="width: 100vw; height: 100vh;border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Wallet</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <ul class="nav nav-tabs checkout-tabs mb-0" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation"><a class="nav-link active" id="ecomtab-tab-1"
                                        href="/wallet_deposit" role="tab" aria-controls="ecomtab-1" aria-selected="true"
                                        tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-credit-card"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Deposit</h6>
                                            </div>
                                        </div>
                                    </a></li>
                                <li class="nav-item" role="presentation"><a class="nav-link" href="/wallet_withdrawal"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-dollar-sign"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Withdraw</h6>
                                            </div>
                                        </div>
                                    </a></li>
								<!--<li class="nav-item" role="presentation"><a class="nav-link" href="/wallet-transfer"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-shuffle"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Client to Client Transfer</h6>
                                            </div>
                                        </div>
                                    </a></li>-->
								<li class="nav-item" role="presentation"><a class="nav-link" href="/wallet-transcation"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="ti ti-file-invoice"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Transcation</h6>
                                            </div>
                                        </div>
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content">
                        <div>
                            <div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-xl-8">
                                                
                                                   
                                                    <div class="card">
                                                        <div class="card-body border-bottom">
                                                            <h6>CREATE DEPOSIT TICKET</h6>
                                                        </div>
                                                        <div class="card-body">

                                                            <div class="divider my-4">
                                                                <span>SELECT PAYMENT METHOD</span>
                                                            </div>
                                                            <div class="row g-1">
                                                                <div class="col-6 col-lg-6 col-xl-6" >
																	<div class="address-check trade-deposit-type border rounded">
																		<div class="form-check">
																			<input type="radio" name="deposit_type" class="form-check-input input-primary wallet-payment" id="option_paytiko" value="Paytiko" data-type="Paytiko" />
																			
																			<label class="form-check-label d-block" for="option_paytiko">
																				<span class="card-body p-2 d-block">
																					<span class="d-flex flex-wrap align-items-center justify-content-between">
																						<span>Card Payment</span>
																						<span>
																							<span class="h6 f-w-500 mb-1 d-block">
																								<img src="/assets/images/paytiko.png" alt="Paytiko" style="height: 30px;" />
																							</span>
																						</span>
																					</span>
																				</span>
																			</label>
																		</div>
																	</div>
																</div>                                                               
																@if (!empty($user_groups['now_payment_api']) && $user_groups['now_payment_status'] == 1)
																	<div class="col-6 col-lg-6 col-xl-6">
																		<div
																			class="address-check trade-deposit-type border rounded">
																			<div class="form-check">
																				<input type="radio" name="deposit_type" 
																					class="form-check-input input-primary wallet-payment"
																					id="option_nowpayment" value="Now Payment"
																					data-type="Now-Payment">
																				<label class="form-check-label d-block"
																					for="option_nowpayment">
																					<span class="card-body p-2 d-block">
																						<span
																							class="d-flex flex-wrap align-items-center justify-content-between">
																							<span>Crypto Payment</span>
																							<span>
																								<span
																									class="h6 f-w-500 mb-1 d-block">
																									<img src="/assets/images/nowpayments-white.png"
																										alt="Now Payment"
																										style="height: 30px;">
																								</span>
																							</span>
																						</span>
																					</span>
																				</label>
																			</div>
																		</div>
																	</div>
																@endif
                                                                <div class="col-6 col-lg-6 col-xl-6">
                                                                    <div class="address-check border rounded">
                                                                        <div class="form-check"><input type="radio"
                                                                                name="deposit_type"
                                                                                class="form-check-input input-primary wallet-payment"
                                                                                id="payopn-check-3" value="Other Payments"
                                                                                data-type="Other-Payments"><label
                                                                                class="form-check-label d-block"
                                                                                for="payopn-check-3"><span
                                                                                    class="card-body p-2 d-block"><span
                                                                                        class="d-flex align-items-center"><span><span
                                                                                                class="h6 f-w-500 mb-1 d-block">OTHERS</span><span
                                                                                                class="f-10 text-muted">Other
                                                                                                Payment
                                                                                                Options</span></span></span></span></label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="divider my-4"><span>DEPOSIT DETAILS</span></div>
                                                            
                                                            <div class="Now-Payment wallet-deposit-details"
                                                                style="display:none">
                                                                <form method="post">
                                                                    @csrf
                                                                    <input type="hidden" name="email"
                                                                        value="{{ session('clogin') }}" min="10"
                                                                        required class="form-control fill">
                                                                    <input class="user_trade_id" type="hidden"
                                                                        name="user[trade_id]" value=""
                                                                        class="form-control fill" readonly required>
                                                                    <div class="row">
                                                                        <div class="col-12 mt-2">
                                                                            <input type="hidden" name="deposit_type"
                                                                                class="tradedeposittype"
                                                                                value="Now Payment">
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-lg-4 col-form-label">DEPOSIT
                                                                                    CURRENCY:
                                                                                    <small
                                                                                        class="text-muted d-block">Please
                                                                                        select the currency you wish to use
                                                                                        for the payment</small>
                                                                                </label>
                                                                                <div class="col-lg-8">
                                                                                    <select class="form-select"
                                                                                        id="exampleFormControlSelect1"
                                                                                        required>
                                                                                        <option value="USD">USD</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group row">
                                                                                <label
                                                                                    class="col-lg-4 col-form-label">ENTER
                                                                                    AMOUNT:
                                                                                    <small
                                                                                        class="text-muted d-block">Please
                                                                                        enter the amount to be deposited in
                                                                                        selected currency</small>
                                                                                </label>
                                                                                <div class="col-lg-8">
                                                                                    <div class="input-group mb-3">
                                                                                        <span
                                                                                            class="input-group-text">USD</span>
                                                                                        <input placeholder="Minimum $10"
                                                                                            name="deposit"
                                                                                            id="deposit_amount_now"
                                                                                            type="number" min="10"
                                                                                            title="Minimum $10"
                                                                                            class="form-control fill nowdeposit_amount"
                                                                                            aria-label="Amount" required>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="row">
                                                                                    <div class="col-lg-4"></div>
                                                                                    <div class="col-lg-8">
                                                                                        <div class="row g-1">
                                                                                            <input type="submit" name="add_wallet"
																								class="btn btn-primary col-12 depositaccount"
																								value="Deposit To Wallet" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
															
                                                            <div class="row Bank-Deposit Other-Payments wallet-deposit-details" style="display:none" >
                                                                <form method="post" style="padding:10px;"
                                                                    class="md-float-material form-material" id="bank-deposit">
                                                                    <div class="col-12 mt-2">
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">DEPOSIT
                                                                                CURRENCY
                                                                                :<small class="text-muted d-block"> Please
                                                                                    select the currency you wish to use for
                                                                                    the payment </small></label>
                                                                            <input type="hidden" name="currency"
                                                                                value="USD">
                                                                            <input class="deposit_type" type="hidden"
                                                                                name="deposit_type" value="Bank-Deposit">
                                                                            <div class="col-lg-8"><select
                                                                                    class="form-select" id="currencyType"
                                                                                    disabled name="currencyType">
                                                                                    <option value="USD" selected>USD
                                                                                    </option>
                                                                                </select></div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">ENTER
                                                                                AMOUNT :<small class="text-muted d-block">
                                                                                    Please enter the amount to be deposited
                                                                                    in selected
                                                                                    currency</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text currency-type">USD</span><input
                                                                                        type="number"
                                                                                        class="form-control wallet-amount"
                                                                                        aria-label="Amount"
                                                                                        name="wallet_amount" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">AMOUNT IN
                                                                                USD :<small class="text-muted d-block">
                                                                                    Deposit amount in USD </small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text">USD</span><input
                                                                                        type="text"
                                                                                        class="form-control wallet-amount-usd"
                                                                                        aria-label="Amount"
                                                                                        disabled=""><!----></div>
                                                                            </div>
                                                                        </div>
																		<div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">DEPOSIT
                                                                                PROOF
                                                                                :<small class="text-muted d-block"> Upload
                                                                                    proof of your transaction
                                                                                </small></label>
                                                                            <div class="col-lg-8"><input type="file"
                                                                                    accept="application/pdf,image/png,image/jpeg"
                                                                                    class="form-control"
                                                                                    name="deposit_proof" required></div>
                                                                        </div>
                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"></div>
                                                                                <div class="col-lg-8">
                                                                                    <div class="row g-1">
                                                                                        <input type="submit" name="add_wallet"
																								class="btn btn-primary col-12 depositaccount"
																								value="Deposit To Wallet" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
															
															<div class="Paytiko wallet-deposit-details" style="display:none">
																<form method="post">
																	@csrf
																	<input type="hidden" name="user[email]"
																		value="{{ session('clogin') }}" min="10" required
																		class="form-control fill">
																	<input class="user_trade_id" type="hidden"
																		name="user[trade_id]" value=""
																		class="form-control fill" readonly required>
																	<div class="row">
																		<div class="col-12 mt-2">
																			<input type="hidden" name="deposit_type"
																				class="tradedeposittype" value="Paytiko">
																			<div class="form-group row">
																				<label class="col-lg-4 col-form-label">DEPOSIT
																					CURRENCY:
																					<small class="text-muted d-block">Please
																						select the currency you wish to use
																						for the payment</small>
																				</label>
																				<div class="col-lg-8">
																					<select class="form-select"
																						id="exampleFormControlSelect1" required>
																						<option value="USD">USD</option>
																					</select>
																				</div>
																			</div>
																			<div class="form-group row">
																				<label class="col-lg-4 col-form-label">ENTER
																					AMOUNT:
																					<small class="text-muted d-block">Please
																						enter the amount to be deposited in
																						selected currency</small>
																				</label>
																				<div class="col-lg-8">
																					<div class="input-group mb-3">
																						<span class="input-group-text">USD</span>
																						<input placeholder="Minimum $10"
																							name="deposit" id="deposit_amount_now"
																							type="number" min="10"
																							title="Minimum $10"
																							class="form-control fill nowdeposit_amount"
																							aria-label="Amount" required />
																					</div>
																				</div>
																			</div>
																			<div class="">
																				<div class="row">
																					<div class="col-lg-4"></div>
																					<div class="col-lg-8">
																						<div class="row g-1">
																							<input type="submit" name="add_wallet"
																								class="btn btn-primary col-12 depositaccount"
																								value="Deposit To Wallet" />
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																	</div>
																</form>
															</div>

                                                        </div>
                                                    </div>
                                                
                                            </div>


                                            <div class="col-xl-4">
                                                <div class="card coupon-card bg-primary">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div
                                                                class="col-8 d-flex flex-column align-items-start justify-content-center">
                                                                <h3 class="text-white f-w-500">Fuel Your Trading Journey
                                                                </h3>
                                                                <span class="f-16 py-2 text-white">
                                                                    Deposit now and unlock the gateway to global markets.
                                                                </span>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <img src="/assets/images/fund_now.png" alt="img"
                                                                    class="img-fluid wid-110">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5>MT5 ACCOUNTS SUMMARY</h5>
                                                    </div>
                                                    <div class="card-body p-0">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach ($liveaccount_details as $liveaccount)
                                                                <li class="list-group-item">
                                                                    <div class="media align-items-start">
                                                                        <span class="h4 mb-0 d-block f-w-500 pb-0">
                                                                            <img src="/assets/images/mt5.png"
                                                                                alt="user-image" class="wid-25 me-1 ms-1">
                                                                        </span>
                                                                        <div class="media-body mx-2">
                                                                            <h5 class="mb-1">
                                                                                <span
                                                                                    class="h4 mb-0 d-block f-w-500 pb-0">{{ $liveaccount->trade_id }}</span>
                                                                            </h5>
                                                                            <p class="text-sm mb-2">
                                                                                <span class="text-muted">ACCOUNT CATEGORY
                                                                                    :</span> ECN
                                                                            </p>
                                                                            <div class="border-top border-dashed">
                                                                                <p class="mb-1 mt-2">
                                                                                    <span class="text-muted">LEVERAGE
                                                                                        :</span>
                                                                                    {{ $liveaccount->leverage }}
                                                                                    <span class="text-muted">| CREDIT
                                                                                        :</span> $0.0000
                                                                                    <span class="text-muted">| EQUITY
                                                                                        :</span>
                                                                                    ${{ $liveaccount->equity }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex-shrink-0">
                                                                            <h4 class="f-w-500">
                                                                                ${{ $liveaccount->Balance }}</h4>
                                                                            <p class="text-muted text-sm mb-2 text-end">
                                                                                Balance</p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">$0.0000</h4>
                                                                </div>
                                                                <span class="text-muted">TOTAL CREDIT</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">$ {{ $totals->equity }}
                                                                    </h4>
                                                                </div>
                                                                <span class="text-muted">TOTAL EQUITY</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="card">
                                                    <div class="card-body py-2">
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item px-0">
                                                                <div class="float-end">
                                                                    <h3 class="mb-0 fw-medium">$ {{ $totals->balance }}
                                                                    </h3>
                                                                </div>
                                                                <h5 class="mb-0 d-inline-block">TOTAL BALANCE</h5>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script>
        var clipboard = new ClipboardJS('#qrCodeUsdt');
        clipboard.on('success', function(e) {
            Swal.fire({
                icon: "success",
                title: "Wallet ID Copied"
            })
        });
        $(document).ready(function() {
            let isSwalOpen = false;
            var intervalId = setInterval(function() {
                console.log("checking payment status...");
                if ($('#checkoutModal').hasClass('show')) {
                    var payment_id = $('#checkoutIframe').data('paymentid');
                    $.ajax({
                        url: '/check-payment-status',
                        type: 'GET',
                        data: {
                            payment_id: payment_id
                        },
                        success: function(response) {
                            if (response.payment_status === 'done') {
                                clearInterval(intervalId);
                                $('#checkoutModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Payment completed successfully!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else if (response.payment_status === 'declined') {
                                clearInterval(intervalId);
                                $('#checkoutModal').modal('hide');
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Payment ERROR',
                                    text: 'Payment Declined!!!!!',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            } else if (response.payment_status == 'pending' && !isSwalOpen) {
                                isSwalOpen = true;
                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Please wait while we process your request.',
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error checking payment status:', xhr);
                        }
                    });
                }
            }, 5000);



            $('#match2payForm').on('submit', function(event) {
                event.preventDefault();
                var actionUrl = $(this).attr('action');
                var formData = $(this).serialize();
                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#checkoutIframe').attr('src', response.checkoutUrl);
                            $('#checkoutIframe').attr('data-paymentid', response.paymentId);
                            var myModal = new bootstrap.Modal(document.getElementById(
                                'checkoutModal'), {
                                backdrop: 'static',
                                keyboard: false
                            });
                            myModal.show();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#responseMessage').html(
                            '<p style="color:red;">An error occurred. Please try again later.</p>'
                        );
                        console.error('Error:', xhr);
                    }
                });
            });
        });
        window.addEventListener('beforeunload', function(e) {
            if ($('#checkoutModal').hasClass('show')) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
		
		$('#bank-deposit').on('submit', function(e) {
			e.preventDefault(); // stop default submit

			const $form = $(this);
			const $btn = $form.find('input[type="submit"]');

			// prevent double click
			if ($btn.prop('disabled')) return;

			$btn.html(
				'<span class="spinner-border spinner-border-sm me-2"></span>Please wait...'
			);
			$btn.prop('disabled', true);

			// SweetAlert loader
			Swal.fire({
				title: "Processing...",
				text: "Your wallet deposit request has been sent for admin approval. Once approved, you will be notified via email.",
				icon: "info",
				allowOutsideClick: false,
				allowEscapeKey: false,
				showConfirmButton: false,
				didOpen: () => {
					Swal.showLoading();
				}
			});

			// submit only once
			setTimeout(() => {
				$form[0].submit(); // native submit (no jquery re-trigger)
			}, 500);
		});

    </script>

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
    @include('pgi_cryptoChill')
@endsection
