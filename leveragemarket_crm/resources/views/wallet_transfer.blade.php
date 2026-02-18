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
                                <li class="nav-item" role="presentation"><a class="nav-link " id="ecomtab-tab-1"
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
								<li class="nav-item" role="presentation"><a class="nav-link active" href="/wallet-transfer"
                                        aria-controls="ecomtab-2" aria-selected="false" tabindex="-1">
                                        <div class="media align-items-center">
                                            <div class="avtar avtar-s"><i class="feather icon-shuffle"></i>
                                            </div>
                                            <div class="media-body ms-2">
                                                <h6 class="mb-0">Client to Client Transfer</h6>
                                            </div>
                                        </div>
                                    </a></li>
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
                                                @if ($kyc_user->kyc_verify == 0)
                                                    <div
                                                        class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
                                                        <div class="row p-3">
                                                            <div class="card-body text-center">
                                                                <div class="text-center me-4">
                                                                    <a href="/transactions/deposit#">
                                                                        <img src="/assets/images/doc_upload.png"
                                                                            class="w-25" alt="img">
                                                                    </a>
                                                                </div>
                                                                <h6
                                                                    class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">
                                                                    KYC Not Yet Verified!
                                                                </h6>
                                                                <a href="/user/documentUpload" id="verify-user-kyc-disabled"
                                                                    class="mt-3">
                                                                    <button class="btn btn-outline-primary">
                                                                        <span class="text-truncate">Verify Now To
                                                                            Proceed</span>
                                                                    </button>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
												@if($walletBalance == 0)
													<div class="col-xl-12">
														<div class="card">
															<div class="card-body ">
																<h6 class="text-danger">Wallet balance not available. Top up your balance.</h6>
															</div>
														</div>
													</div>
												@else
                                                    <div class="card">
                                                        <div class="card-body border-bottom">
                                                            <h6>Your Wallet to External Wallet Transfer</h6>
                                                        </div>
                                                        <div class="card-body">                              
                                                            <div class="row Bank-Deposit Other-Payments wallet-deposit-details">
                                                                <form method="post" style="padding:10px;" class="md-float-material form-material" action="{{ route('wallet.transferto') }}" >
																<input type="hidden" name="walletBalance" id="walletBalance" class="form-control" value="{{ $walletBalance }}" />
                                                                    <div class="col-12 mt-2">
																		<div class="form-group row">
																			<label class="col-lg-4 col-form-label">Transfer To
                                                                                :<small class="text-muted d-block"> Please
                                                                                    Enter the transfer user email address </small></label>
                                                                            <div class="col-lg-8">
																				<input type="email" name="transfer_emailto" class="form-control" required placeholder="example@gmail.com" />
																			</div>
                                                                        </div>
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
                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"></div>
                                                                                <div class="col-lg-8">
                                                                                    <div class="row g-1">
                                                                                        <input type="submit" name="add_wallet" class="btn btn-primary col-12" value="Transfer Payment" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                        </div>
                                                    </div>
                                                @endif
												@endif
                                            </div>


                                            <div class="col-xl-4">
                                                <div class="card coupon-card bg-primary">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-8 d-flex flex-column align-items-start justify-content-center">
                                                                <h3 class="text-white f-w-500">${{ $walletBalance }}</h3>
                                                                <span class="f-16 py-2 text-white">Wallet Balance</span>
                                                            </div>
                                                            <div class="col-4 text-end">
                                                                <img src="/assets/images/fund_now.png" alt="img" class="img-fluid wid-110" />
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
		$(document).ready(function () {

			$("form").on("submit", function (e) {
				e.preventDefault(); 

				let form = this;
				let walletBalance = parseFloat($("#walletBalance").val());
				let enteredAmount = parseFloat($(".wallet-amount").val());
				let emailTo = $("input[name='transfer_emailto']").val();

				// Validate email
				if (!emailTo || !emailTo.includes("@")) {
					Swal.fire("Invalid Email", "Please enter a valid email address.", "warning");
					return false;
				}

				// Validate amount
				if (!enteredAmount || enteredAmount <= 0) {
					Swal.fire("Invalid Amount", "Please enter a valid transfer amount.", "warning");
					return false;
				}

				// Amount greater than balance
				if (enteredAmount > walletBalance) {
					Swal.fire({
						icon: "error",
						title: "Insufficient Balance",
						html: 'Your wallet balance is <b>$'+walletBalance+' </b>.<br>You cannot transfer <b>$'+enteredAmount+'</b>.'
					});
					return false;
				}

				// Confirm Transfer
				Swal.fire({
					title: "Confirm Transfer?",
					html: 'Transfer <b>$'+enteredAmount+'</b> to <b>'+emailTo+'</b>?<br>This will go for admin approval.',
					icon: "question",
					showCancelButton: true,
					confirmButtonText: "Yes, Proceed",
					cancelButtonText: "Cancel"
				}).then((result) => {
					if (result.isConfirmed) {
						// AJAX SUBMIT
						$.ajax({
							url: "{{ route('wallet.transferto') }}", 
							method: "POST",
							data: $(form).serialize(),
							headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },

							beforeSend: function () {
								Swal.fire({
									title: "Processing...",
									text: "Please wait",
									allowOutsideClick: false,
									didOpen: () => Swal.showLoading()
								});
							},

							success: function (response) {
								Swal.close();

								if (response.status === "success") {
									Swal.fire({
										icon: "success",
										title: "Transfer Submitted!",
										text: "Amount Transferred to mentioned wallet.",
									}).then(() => {
										location.reload();
									});
								}

								else {
									Swal.fire({
										icon: "error",
										title: "Error",
										text: response.message
									});
								}
							},

							error: function () {
								Swal.fire("Error", "Something went wrong.", "error");
							}
						});
					}
				});
			});
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
