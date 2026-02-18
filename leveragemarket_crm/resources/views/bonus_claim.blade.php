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
            @if ($user->kyc_verify > 0)
                <div class="page-header mb-0 pb-0">
                    <div class="page-block">
                        <div class="row align-items-center">
                            <div class="col-md-12">
                                <div class="page-header-title h2">
                                    <h4 class="mb-0">{{ $bonusname }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        
                        <div class="tab-content">
                            <div>
                                <?php if (isset($error)) {?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <strong>
                                        <?php echo $error; ?>
                                    </strong>
                                </div>
                                <script>
                                    $(".alert").alert();
                                </script>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-12">
										<div class="card">
										
											<div class="card-body">
												<div class="divider my-4">
													<span>Eligible Accounts</span>
												</div>

												<div class="row g-1">
												<input type="hidden" id="bonus_id" value="{{ md5($bonuscode->bonus_id) }}" />
													@forelse ($liveaccount_details as $liveaccount)
														<div class="col-md-3 col-lg-4 col-xl-4">
															<div class="address-check border rounded">
																<div class="form-check paycard">
																	<input
																		id="liveaccount{{ $liveaccount->trade_id }}"
																		type="radio"
																		name="live-account"
																		class="select-liveaccount form-check-input input-primary"
																		data-mindep="{{ $liveaccount->mindep }}"
																		value="{{ $liveaccount->trade_id }}">

																	<label class="form-check-label d-block">
																		<div class="p-1 my-1">
																			<span class="row">
																				<span class="col-6 mt-1">
																					<span class="h5 mb-0 d-block f-w-500 f-14">
																						<img src="/assets/images/mt5.png"
																							 class="wid-25 me-1 ms-1">
																						{{ $liveaccount->trade_id }}
																					</span>
																				</span>

																				<span class="col-6 text-end pe-3">
																					<span class="h5 mb-0 d-block f-w-500">
																						${{ ($liveaccount->Balance ?? 0) + ($liveaccount->credit ?? 0) }}
																					</span>
																					<span class="text-muted f-10">
																						Current Balance
																					</span>
																				</span>
																			</span>
																		</div>
																	</label>
																</div>
															</div>
														</div>

													@empty
														<!-- ✅ No accounts found -->
														<div class="col-12 text-center py-5">
															<img src="/assets/images/no-data.jpg"
																 class="mb-3"
																 style="width:150px;opacity:.7;">
															<h6 class="text-muted mb-1">
																No eligible accounts found
															</h6>
															<small class="text-muted">
																Please make a deposit to become eligible for the Apply Bonus.
															</small>
														</div>
													@endforelse

												</div>
											</div>
											
											<div class="card-footer">
												<div class="d-flex justify-content-end gap-2">
													<button type="button"
															class="btn btn-outline-secondary"
															id="cancelApply">
														Cancel
													</button>

													<button type="button"
															class="btn btn-primary"
															id="applyBonus">
														Apply Now
													</button>
												</div>
											</div>
										</div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
                    <div class="row p-3">
                        <div class="card-body text-center">
                            <div class="text-center me-4"><a href="/transactions/deposit#"><img
                                        src="/assets/images/doc_upload.png" class="w-25" alt="img"></a></div>
                            <h6 class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">KYC Not Yet Verified !
                            </h6>
                            <a href="/user-profile#kyc" id="verify-user-kyc-disabled" class="mt-3"><button
                                    class="btn btn-outline-primary"><span class="text-truncate">Verify Now To
                                        Proceed</span></button></a>
                        </div>
                    </div>
                </div>
            @endif
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
	$(document).ready(function () {

		$('#applyBonus').on('click', function () {

			let selected = $('input[name="live-account"]:checked');

			if (!selected.length) {
				Swal.fire('Warning', 'Please select a live account', 'warning');
				return;
			}

			let tradeId = selected.val();
			let bonusId = $('#bonus_id').val();

			// STEP 1: CHECK ELIGIBILITY + CALCULATE BONUS
			$.ajax({
				url: "{{ route('bonuspreview') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}",
					trade_id: tradeId,
					bonus_id: bonusId
				},
				beforeSend: function () {
					Swal.fire({
						title: 'Checking eligibility...',
						allowOutsideClick: false,
						didOpen: () => Swal.showLoading()
					});
				},
				success: function (res) {

					if (!res.status) {
						Swal.fire('Error', res.message, 'error');
						return;
					}

					// STEP 2: SHOW BONUS PREVIEW
					Swal.fire({
						title: 'Confirm Bonus Application',
						html: `
							<div class="text-start">
								<p><b>Account:</b> ${res.trade_id}</p>
								<p><b>Deposit:</b> $${res.deposit}</p>
								<p><b>Bonus %:</b> ${res.bonus_percent}%</p>
								<p><b>Bonus Amount:</b> $${res.bonus_amount}</p>
							</div>
						`,
						icon: 'question',
						showCancelButton: true,
						confirmButtonText: 'Apply Bonus'
					}).then((result) => {
						if (result.isConfirmed) {
							applyBonus(tradeId, bonusId);
						}
					});
				}
			});
		});

		$('#cancelApply').on('click', function () {
			$('input[name="live-account"]').prop('checked', false);
		});

		// STEP 3: APPLY BONUS (FINAL)
		function applyBonus(tradeId, bonusId) {
			$.ajax({
				url: "{{ route('applybonus') }}",
				type: "POST",
				data: {
					_token: "{{ csrf_token() }}",
					trade_id: tradeId,
					bonus_id: bonusId
				},
				beforeSend: function () {
					Swal.fire({
						title: 'Applying bonus...',
						allowOutsideClick: false,
						didOpen: () => Swal.showLoading()
					});
				},
				success: function (res) {
					if (res.status) {
						Swal.fire('Success', res.message, 'success')
							.then(() => location.reload());
					} else {
						Swal.fire('Error', res.message, 'error');
					}
				}
			});
		}

	});
	</script>
@endsection
