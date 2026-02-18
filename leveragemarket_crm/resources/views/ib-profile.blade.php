@extends('layouts.crm.crm')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0 mt-0 pt-0">
                <div class="page-block mb-0 pb-0 mt-0 pt-0">
                    <div class="row align-items-center mb-0 pb-0 mt-0 pt-0">
                        <div class="col-md-12 mb-0 pb-0 mt-0 pt-0">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">MY IB PROFILE</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-0 pb-0 mt-0 pt-0">
                <div class="col-12 mb-0 pb-0 mt-0 pt-0">
                    <div class="card mb-0 pb-0 mt-0 pt-0">
                        <div class="card-body mb-0 pb-0 mt-0 pt-0">
                            <div class="row mb-0 pb-0 mt-0 pt-0">
                                <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation"><a class="nav-link active" id="profile-tab-1"
                                            data-bs-toggle="tab" href="#ib-home" role="tab" aria-selected="true"><i
                                                class="ti ti-smart-home me-2"></i>IB Home </a></li>
                                    <li class="nav-item" role="presentation"><a class="nav-link" id="profile-tab-2"
                                            data-bs-toggle="tab" href="#ib-connect" role="tab" aria-selected="false"
                                            tabindex="-1"><i class="ti ti-affiliate me-2"></i>My Connections </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content mb-0 pb-0 mt-0 pt-0">
                <div class="tab-pane pt-3 active show mb-0 pb-0 mt-0 pt-0" id="ib-home" role="tabpanel"
                    aria-labelledby="profile-tab-1">
                    <div class="row mb-0 pb-0 mt-0 pt-0">
                        <div class="col-lg-9 mb-0 pb-0 mt-0 pt-0">
                            <div class="card mb-0 pb-0 mt-0 pt-0">
                                <div class="card-body mb-0 pb-3 mt-0 pt-3">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-xxl-4">
                                            <div class="card mb-0">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <h3 class="mb-0 f-w-500"><?= $ib_clients_total ?></h3>
                                                        </div>
                                                        <div class="avtar avtar-s bg-light-primary"><i
                                                                class="ti ti-mood-kid f-18"></i></div>
                                                    </div>
                                                    <p class="mb-0 text-muted d-flex align-items-center gap-2 f-12 mt-3">
                                                        Total Clients </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xxl-4">
                                            <div class="card mb-0">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <h5 class="mb-0 f-w-500">$
                                                                <?= isset($ib_wallet_raw->wallet) ? $ib_wallet_raw->wallet : '0.00' ?>
                                                            </h5>
                                                        </div>
                                                        <div class="avtar avtar-s bg-light-primary"><i
                                                                class="ti ti-report-money f-18"></i></div>
                                                    </div>
                                                    <p class="mb-0 text-muted d-flex align-items-center gap-2 f-12 mt-3">
                                                        Generated Commission
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xxl-4">
                                            <div class="card mb-0">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <h5 class="mb-0 f-w-500">$
                                                                <?= isset($ib_wallet_raw->withdraw) ? $ib_wallet_raw->withdraw : '0.00' ?>
                                                            </h5>
                                                        </div>
                                                        <div class="avtar avtar-s bg-light-primary"><i
                                                                class="ti ti-shield-check f-18"></i></div>
                                                    </div>
                                                    <p class="mb-0 text-muted d-flex align-items-center gap-2 f-12 mt-3">
                                                        Commission Transferred
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row p-1">
                                        <div class="col-12">
                                            <div class="bg-body p-2 rounded">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0"><span
                                                                class="p-1 d-block bg-primary rounded-circle"><span
                                                                    class="visually-hidden">New alerts</span></span></div>
                                                        <div class="flex-grow-1 ms-2">
                                                            <p class="mb-0">Deposits</p>
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-0 f-w-500">$ 0.00</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row p-1">
                                        <div class="col-12">
                                            <div class="bg-body p-2 rounded">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0"><span
                                                                class="p-1 d-block bg-primary rounded-circle"><span
                                                                    class="visually-hidden">New alerts</span></span></div>
                                                        <div class="flex-grow-1 ms-2">
                                                            <p class="mb-0">Withdraw</p>
                                                        </div>
                                                    </div>
                                                    <h5 class="mb-0 f-w-500">$ 0.00</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-0 pb-0 mt-0 pt-0">
                        <div class="col-xl-6 col-md-6 mb-0 pb-0 mt-0 pt-0">
                           
                                <div class="card mb-0 pb-0 mt-0 pt-0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h5 class="mb-0 f-w-500">TRANSFER MY COMMISSION</h5>
                                            <div class="bg-body p-1 mt-1 rounded">
                                                <div class="mt-1 row align-items-center">
                                                    <div class="col-12 text-end">
                                                        <h3 class="mb-1 me-2 ms-2 f-w-500">
                                                            $<?= number_format($ib_wallet, 2) ?></h3>
                                                        <p class="text-warning mb-0 me-2 ms-2"> Transferrable Balance</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr style="opacity: 0.1;">
										
										<div class="divider my-4"><span>SELECT WITHDRAW METHOD</span></div>
										<div class="row mb-3">
											<div class="col-md-6 col-lg-6 col-xl-6 col-sm-12" >
												<div class="address-check trade-withdraw-type border rounded">
													<div class="form-check"><input type="radio"
															name="withdraw_type"
															class="form-check-input wallet-withdraw input-primary tradefund-deposit"
															id="bank_withdraw"
															value="Other Withdrawal"
															data-type="Bank-Withdrawal"><label
															class="form-check-label d-block"
															for="bank_withdraw"><span
																class="card-body p-2 d-block"><span
																	class="h6 f-w-500 mb-1 d-block">BANK
																	WITHDRAWAL</span><span
																	class="d-flex align-items-center"><span
																		class="f-10 badge bg-light-success me-3">WITHDRAW
																		TO BANK ACCOUNT</span><img
																		src="/assets/images/bank.png"
																		alt="img"
																		class="img-fluid ms-1 wid-25"></span></span></label>
													</div>
												</div>
											</div>
											
											<div class="col-md-6 col-lg-6 col-xl-6 col-sm-12" >
												<div class="address-check trade-withdraw-type border rounded">
													<div class="form-check"><input type="radio"
															name="withdraw_type"
															class="form-check-input wallet-withdraw input-primary tradefund-deposit"
															id="mt5_withdraw"
															value="MT5 Withdrawal"
															data-type="MT5-Withdrawal"><label
															class="form-check-label d-block"
															for="mt5_withdraw"><span
																class="card-body p-2 d-block"><span
																	class="h6 f-w-500 mb-1 d-block">MT5 Account</span><span
																	class="d-flex align-items-center"><span
																		class="f-10 badge bg-light-success me-3">WITHDRAW
																		TO MT5 Account</span><img src="/assets/images/mt5.png" class="img-fluid ms-1 wid-25" /></span></span></label>
													</div>
												</div>
											</div>
										</div>
										<?php if($withdrawallow != 0) { ?>
										<div class="col-xl-12 col-sm-12">
											<div class="px-5 table-responsive">
												<div class="auth-main">
													<div class="card-body">
														<div class="text-center me-4">
															<svg version="1.1" id="Layer_1"
																xmlns="http://www.w3.org/2000/svg"
																xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
																y="0px" width="250px" height="200px"
																viewBox="0 0 250 200"
																style="enable-background:new 0 0 250 200;"
																xml:space="preserve">
																<style type="text/css">
																	.st0 {
																		fill: #D0EFF7;
																	}

																	.st1 {
																		fill: none;
																		stroke: #0069AA;
																		stroke-width: 4.8368;
																		stroke-linecap: round;
																		stroke-linejoin: round;
																	}

																	.st2 {
																		fill: #0F3D47;
																	}

																	.st3 {
																		fill: #0069AA;
																	}

																	.st4 {
																		fill: #2B78B1;
																	}

																	.st5 {
																		fill: none;
																		stroke: #0F3D47;
																		stroke-width: 4.8368;
																		stroke-linecap: round;
																		stroke-linejoin: round;
																	}

																	.st6 {
																		fill: #0F3D47;
																		stroke: #0F3D47;
																		stroke-miterlimit: 10;
																	}

																	.st7 {
																		fill:
																			<?=$settings['sidebar_color'] ?>;
																		stroke: #5A6771;
																		stroke-width: 0.7045;
																		stroke-miterlimit: 10;
																	}

																	.st8 {
																		fill: #EEEEEE;
																	}

																	.st9 {
																		fill: none;
																		stroke: #333333;
																		stroke-width: 3.1704;
																		stroke-linecap: round;
																		stroke-linejoin: round;
																		stroke-miterlimit: 10;
																	}

																	.st10 {
																		fill: #FFFFFF;
																	}

																	.st11 {
																		fill: none;
																		stroke: #0F3D47;
																		stroke-width: 3.1704;
																		stroke-linecap: round;
																		stroke-linejoin: round;
																		stroke-miterlimit: 10;
																	}
																</style>
																<g>
																	<circle class="st0" cx="125" cy="100"
																		r="85.5" />
																	<g>
																		<line class="st1" x1="122.3"
																			y1="43.7" x2="133.8"
																			y2="43.7" />
																		<line class="st1" x1="128.1"
																			y1="38" x2="128.1"
																			y2="49.5" />
																	</g>
																	<path class="st2"
																		d="M146.2,35c0-1.6-1.3-3-3-3c-1.6,0-3,1.3-3,3c0,1.6,1.3,3,3,3S146.2,36.7,146.2,35z" />
																	<g>
																		<path class="st2" d="M198.1,158.8c-0.5,2.2,1,4.4,3.2,4.9s4.4-0.9,4.9-3.2c0.5-2.2-1-4.4-3.2-4.9
							  C200.8,155.2,198.6,156.6,198.1,158.8z" />
																		<path class="st3" d="M205.2,147.4c-0.3,1.2,0.5,2.4,1.7,2.6c1.2,0.3,2.4-0.5,2.6-1.7c0.3-1.2-0.5-2.4-1.7-2.6
							  C206.7,145.4,205.5,146.2,205.2,147.4z" />
																	</g>
																	<g>
																		<g>
																			<g>
																				<path class="st4" d="M183.6,146.6H65c-4.1,0-7.4-3.3-7.4-7.4V75.2c0-4.1,3.3-7.4,7.4-7.4h118.6c4.1,0,7.4,3.3,7.4,7.4v63.9
								  C191.1,143.2,187.7,146.6,183.6,146.6z" />
																				<path class="st5" d="M183.6,146.6H65c-4.1,0-7.4-3.3-7.4-7.4V75.2c0-4.1,3.3-7.4,7.4-7.4h118.6c4.1,0,7.4,3.3,7.4,7.4v63.9
								  C191.1,143.2,187.7,146.6,183.6,146.6z" />
																				<rect x="57.6" y="84.8" class="st6"
																					width="133.5" height="20.3" />
																				<polygon class="st5"
																					points="191.1,105.1 124.3,105.1 57.6,105.1 57.6,95 57.6,84.8 124.3,84.8 191.1,84.8 191.1,95 				" />
																				<g>
																					<line class="st5" x1="69.8"
																						y1="119.1" x2="145.8"
																						y2="119.1" />
																					<line class="st5" x1="92.9"
																						y1="132.5" x2="128.4"
																						y2="132.5" />
																					<line class="st5" x1="69.8"
																						y1="132.5" x2="83.2"
																						y2="132.5" />
																				</g>
																			</g>
																		</g>
																		<g>
																			<g>
																				<path class="st7" d="M198.7,103.8h-41.4c-4.3,0-7.7-3.5-7.7-7.7V61.8c0-4.3,3.5-7.7,7.7-7.7h41.4c4.3,0,7.7,3.5,7.7,7.7v34.3
								  C206.4,100.4,202.9,103.8,198.7,103.8z" />
																				<g>
																					<path class="st8" d="M183.7,74c0-3.2-2.6-5.8-5.9-5.7c-3,0.1-5.5,2.6-5.5,5.7c0,2.2,1.2,4.1,3.1,5.1v9.6c0,0.6,0.5,1.1,1.1,1.1
									h3.1c0.6,0,1.1-0.5,1.1-1.1V79C182.4,78.1,183.7,76.2,183.7,74z" />
																					<path class="st9" d="M183.7,74c0-3.2-2.6-5.8-5.9-5.7c-3,0.1-5.5,2.6-5.5,5.7c0,2.2,1.2,4.1,3.1,5.1v9.6c0,0.6,0.5,1.1,1.1,1.1
									h3.1c0.6,0,1.1-0.5,1.1-1.1V79C182.4,78.1,183.7,76.2,183.7,74z" />
																				</g>
																				<path class="st10" d="M198,47.3c0-11.1-9-20-20-20l0,0c-11.1,0-20,9-20,20v6.8h7.1v-6.8c0-7.1,5.8-13,13-13c7.1,0,13,5.8,13,13
								  v6.8h7.1V47.3z" />
																				<path class="st11" d="M198,47.3c0-11.1-9-20-20-20l0,0c-11.1,0-20,9-20,20v6.8h7.1v-6.8c0-7.1,5.8-13,13-13c7.1,0,13,5.8,13,13
								  v6.8h7.1V47.3z" />
																				<path class="st11" d="M198.7,103.8h-41.4c-4.3,0-7.7-3.5-7.7-7.7V61.8c0-4.3,3.5-7.7,7.7-7.7h41.4c4.3,0,7.7,3.5,7.7,7.7v34.3
								  C206.4,100.4,202.9,103.8,198.7,103.8z" />
																			</g>
																		</g>
																	</g>
																</g>
															</svg>
														</div>
														<h6 class="text-center text-secondary f-w-400 mb-0 f-16 mt-2" style="line-height:30px">A
															IB withdrawal request is
															already pending. Please wait for confirmation or reach out
															to support before creating
															another request.</h6>
													</div>
												</div>
											</div>
										</div>
										<?PHP } else { ?>
										<div id="bankwithdrawal" class="wallet-withdrawal Bank-Withdrawal">
											
											<form method="post" enctype="multipart/form-data" style="padding:10px;" class="md-float-material form-material tradeWithdrawalForm">
												@csrf
												<input id="adjustment_inr" type="hidden" name="adjustment_inr" value="" class="form-control fill" readonly="" >
												<input type="hidden" name="withdraw_type" value="Bank Withdrawal" />
												
												<div class="row">
													<div class="col-12 mt-2">
														<div class="form-group row">
															<label class="col-lg-4 col-form-label">SELECT BANK ACCOUNT:<small class="text-muted d-block"> Please select the bank account to which you wish to transfer your funds </small></label>
															<div class="col-lg-8">
																<select name="withdraw_to" required class="form-control fill" >
																	<?php foreach ($bank_details as $details): ?>
																	<option
																		value="<?= $details->accountNumber ?>">
																		<?= $details->accountNumber ?>
																	</option>
																	<?php endforeach; ?>
																</select>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-4 col-form-label">WITHDRAWAL CURRENCY:<small class="text-muted d-block"> Please select the currency you wish to use for the withdrawal </small></label>
															<div class="col-lg-8">
																<select class="form-select" id="withdrawal_currency" name="withdrawal_currency" required >
																	<option value="INR">INR</option>
																	<option value="USD">USD</option>
																</select>
															</div>
														</div>
														<div class="form-group row">
															<label class="col-lg-4 col-form-label">ENTER AMOUNT IN USD :<small class="text-muted d-block"> Please enter the amount that you need totransfer</small></label>
															<div class="col-lg-8">
																<div class="input-group mb-3"><span
																		class="input-group-text">USD</span><input
																		type="number"
																		class="form-control"
																		step="1"
																		name="withdraw_amount"
																		min="1"
																		id="withdraw_amount_usd"
																		aria-label="Amount (to the nearest dollar)"
																		required><span
																		class="input-group-text">.00</span>
																</div>
															</div>
														</div>

														<div class="form-group row"><label
																class="col-lg-4 col-form-label">AMOUNT IN
																<span
																	class="convertCurrencyText">INR</span>
																:<small class="text-muted d-block">
																	Withdrawal
																	Amount in Selected
																	Currency</small></label>
															<div class="col-lg-8">
																<div class="input-group mb-3"><span
																		class="input-group-text convertCurrencyText">INR</span><input
																		name="amount_in_other_currency"
																		id="amount_in_other_currency"
																		type="text"
																		class="form-control fill"
																		aria-label="Amount"
																		readonly="true" required>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- <div class="row">
													<div class="col-12 mt-2">
														<div class="form-group row">
															<label
																class="col-lg-4 col-form-label">Verification
																OTP :
															</label>
															<div class="col-lg-8">
																<div class="input-group mb-3">
																	<span
																		class="input-group-text otp-req" data-type="Bank Withdrawal">Send
																		OTP</span>
																	<input type="number"
																		class="form-control"
																		name="otp" disabled required data-type="Bank Withdrawal">
																	<span class="input-group-text">
																		<i class="feather icon-info mb-auto mt-auto"
																			data-bs-toggle="tooltip"
																			data-bs-placement="top"
																			title="You will receive OTP on your registered email address"></i>
																	</span>
																</div>
															</div>
														</div>
													</div>
												</div> -->
												<div class="">
													<div class="row">
														<div class="col-lg-4"></div>
														<div class="col-lg-8">
															<div class="row g-1"><input type="submit"
																	name="fund_add"
																	class="btn btn-primary col-12"
																	value="Send to Request">
															</div>
														</div>
													</div>
												</div>
											</form>
											
										</div>
										
                                        <div class="wallet-withdrawal MT5-Withdrawal form" id="mt5accountwithdrawal" style="display:none">
										<form method="post" enctype="multipart/form-data">
											@csrf
											<label class="form-label mt-3" for="exampleFormControlSelect1">Select Your Account</label>
                                            <div class="row mb-3">
                                                <?php if (count($live_accs)) {
													foreach ($live_accs as $acc) {
												?>
                                                <div class="col-lg-6">
                                                    <div class="border card p-2">
                                                        <div class="form-check mb-0">
															<input type="radio" name="tradeId" class="form-check-input input-primary" id="<?= $acc->trade_id ?>"value="<?= $acc->trade_id ?>" />
															<label class="form-check-label d-block mb-0" for="<?= $acc->trade_id ?>">
																<span>
																	<span class="h5 d-block">
																		<span class="float-end badge bg-light-primary f-14 fw-medium">$ <?= $acc->Balance ?></span>
																		<span><img src="/assets/images/mt5.png" class="hei-30" /><?= $acc->trade_id ?></span>
																	</span>
																	<span class="text-muted mt-2 mb-0">
																		<span class="float-end text-muted mt-2 f-12"> Current Balance</span>
																	</span>
																</span>
															</label>
														</div>
                                                    </div>
                                                </div>
                                                <?php }
												} else { ?>
                                                <div class="col-lg-12">
                                                    <a href="/trade-deposit" class="d-grid btn-primary"> 
														<span class="text-truncate w-100">Create new Live Account</span>
													</a>
                                                </div>
                                                <?php } ?>
                                            </div>
											
											<?php if (count($externalliveaccs) != 0) { ?>
											<label class="form-label mt-3" for="exampleFormControlSelect1">Select External Account</label>
											<div class="row ">
                                                <?php 
													foreach ($externalliveaccs as $acc) {
												?>
                                                <div class="col-lg-6">
                                                    <div class="border card p-2">
                                                        <div class="form-check mb-0">
															<input type="radio" name="tradeId" class="form-check-input input-primary" id="<?= $acc->trade_id ?>"value="<?= $acc->trade_id ?>" />
															<label class="form-check-label d-block mb-0" for="<?= $acc->trade_id ?>">
																<span>
																	<span class="h5 d-block">
																		<span class="float-end badge bg-light-primary f-14 fw-medium">$ <?= $acc->Balance ?></span>
																		<span><img src="/assets/images/mt5.png" class="hei-30" /> &nbsp;<?= $acc->trade_id ?></span>
																	</span>
																	<span class="text-muted mt-2 mb-0">
																		<span class="text-muted mt-2 f-12"> <?= $acc->email ?></span>
																		<span class="float-end text-muted mt-2 f-12"> Current Balance</span>
																	</span>
																</span>
															</label>
														</div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
											<?PHP } ?> 
											
											<label class="form-label" for="exampleFormControlSelect1">Enter Amount</label>
                                            <div class="input-group mb-3"><span class="input-group-text">$</span><input
                                                    type="number" name="amount" max="<?= $ib_wallet ?>"
                                                    class="form-control" required
                                                    aria-label="Amount (to the nearest dollar)"><span
                                                    class="input-group-text">.00</span>
                                                <!---->
                                            </div>
                                            <div class="d-grid mb-5 mt-4"><button class="btn btn-outline-secondary"
                                                    name="transfer" type="submit"><i
                                                        class="ti ti-shield-check me-2"></i>
                                                    <!----> Process Transfer</button></div>
                                        </form>
										</div>
										<?PHP } ?>
                                    </div>
                                </div>
                            
                        </div>
                        <div class="col-xl-6 col-md-6 mt-0 pt-0">
                            <div class="card mt-0 pt-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="mb-0 f-w-500">MY REFERRAL LINK</h5>
                                        <div class="avtar avtar-s bg-light-primary"><i class="ti ti-list f-18"></i></div>
                                    </div>
                                    <?php

                                    ?>
                                    <hr style="opacity:.1;"><label class="col-form-label col-12 text-lg-start">Your
                                        exclusive referral
                                        link is ready! Share this link to invite potential clients to register under your
                                        supervision and
                                        start their trading journey.</label>
                                    <div class="col-12 mb-4">
                                        {{ session('email') }}
                                        <div class="input-group mb-2"><input type="text" class="form-control"
                                                id="pc-clipboard-1" placeholder="Type some value to copy"
                                                value="{{ url('/ib-ref?refercode=' . base64_encode(session('clogin'))) }}"
                                                readonly=""><button class="btn btn-lg btn-primary cb"
                                                data-clipboard-target="#pc-clipboard-1"><i
                                                    class="feather icon-copy"></i></button></div>
                                        <!---->
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body pb-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0">Transfer History</h4>
                                        <div class="avtar avtar-s bg-light-primary"><i class="ti ti-list f-18"></i></div>
                                    </div>
                                    <hr style="opacity:.1;">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>TRANSFERRED TO</th>
                                                    <th>PROCESSED ON</th>
                                                    <th>AMOUNT</th>
                                                    <th>STATUS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
												@forelse($histories as $transfer)
													<tr>
														<td>{{ $transfer->account }} <br>
															<small class="text-muted">{{ $transfer->transfer_type }}</small>
														</td>
														<td>{{ \Carbon\Carbon::parse($transfer->transdate)->format('d M Y, h:i A') }}</td>
														<td>${{ number_format($transfer->amount, 2) }}</td>
														<td>
															@if($transfer->Status == '1' || $transfer->Status == 'Completed')
																<span class="badge bg-success">Completed</span>
															@elseif($transfer->Status == '0')
																<span class="badge bg-warning">Pending</span>
															@else
																<span class="badge bg-danger">{{ $transfer->Status }}</span>
															@endif
														</td>
													</tr>
												@empty
													<tr>
														<td colspan="4" class="text-center text-muted">No transfer records found.</td>
													</tr>
												@endforelse
											</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2 pb-0">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body pb-0">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0">IB Commission Histories</h4>
                                        <div class="avtar avtar-s bg-light-primary">
                                            <i class="ti ti-list f-18"></i>
                                        </div>
                                    </div>
                                    <hr style="opacity:.1;">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="commissionTbl">

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane pt-3" id="ib-connect" role="tabpanel" aria-labelledby="profile-tab-2">
                    <div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body p-3">
                                        <ul class="nav nav-pills nav-tabs nav-justified" role="tablist">
                                            <li class="nav-item" data-target-form="#LEVEL1" role="presentation"><a
                                                    href="/ib-profile#LEVEL1" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL1" data-toggle="tab" class="nav-link active"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL1</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL2" role="presentation"><a
                                                    href="/ib-profile#LEVEL2" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL2" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL2</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL3" role="presentation"><a
                                                    href="/ib-profile#LEVEL3" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL3" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL3</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL4" role="presentation"><a
                                                    href="/ib-profile#LEVEL4" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL4" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL4</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL5" role="presentation"><a
                                                    href="/ib-profile#LEVEL5" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL5" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL5</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL6" role="presentation"><a
                                                    href="/ib-profile#LEVEL6" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL6" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL6</span></a></li>
                                            <li class="nav-item" data-target-form="#LEVEL7" role="presentation"><a
                                                    href="/ib-profile#LEVEL7" data-bs-toggle="tab"
                                                    data-bs-target="#LEVEL7" data-toggle="tab" class="nav-link"
                                                    aria-selected="false" role="tab" tabindex="-1"><i
                                                        class="ti ti-chart-bar me-2"></i><span
                                                        class="d-none d-sm-inline">LEVEL7</span></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="tab-content connectionTab" id="nav-tabContent">
                                            <?php for ($i = 1; $i <= 7; $i++) { ?>
                                            <div class="tab-pane fade<?= $i == 1 ? ' show active' : '' ?>"
                                                id="LEVEL<?= $i ?>" role="tabpanel">
                                                <div class="datatable-container">
                                                    <table class="table table-hover datatable-table" id="pc-dt-simple">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 30%;">CLIENT</th>
                                                                <th class="text-end" style="width: 10%;">TOTAL ACCOUNTS
                                                                </th>
                                                                <th class="text-end" style="width: 10%;">TOTAL DEPOSIT
                                                                </th>
                                                                <th class="text-end" style="width: 15%;">PROFILE STATUS
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $clients = [];
                                                            if(isset($ib_clients[$i])){
                                                                $clients = $ib_clients[$i];
                                                            }
                                foreach ($clients as $client) {
                                ?>
                                                            <tr data-index="0">
                                                                <td>
                                                                    <div class="row align-items-center">
                                                                        <div class="col-auto pe-0"><img
                                                                                src="/assets/images/ib_avatar.png"
                                                                                alt="user-image"
                                                                                class="wid-55 hei-55 rounded"></div>
                                                                        <div class="col">
                                                                            <h6 class="mb-2"><span
                                                                                    class="text-truncate w-100"><?= $client->fullname ?></span>
                                                                            </h6>
                                                                            <p class="text-muted f-12 mb-0"><span
                                                                                    class="text-truncate w-100"><?= $client->email ?></span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-end f-w-400"><?= $client->liveaccounts ?>
                                                                </td>
                                                                <td class="f-w-400 text-end">$<?= $client->total_deposit ?>
                                                                </td>
                                                                <td class="text-end">
                                                                    <?php if ($client->email_confirmed == 1) { ?>
                                                                    <span class="badge btn bg-success">Active</span>
                                                                    <?php } else { ?>
                                                                    <span class="badge btn bg-info">Not Verified</span>
                                                                    <?php } ?>
                                                                </td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <?php } ?>
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
@endsection
@section('scripts')
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
            }).then(() => {
                location.reload();
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Something went wrong',
                text: '{{ session('error') }}',
                showConfirmButton: true
            });
        </script>
    @endif
    
	<script>
        var withdraw_type = @json($type ?? '');
        if (withdraw_type != '') {
            setTimeout(function() {
                $("#" + withdraw_type).click();
            }, 2000);
        }
        $("#withdrawal_currency,#withdraw_amount_usd").on("keyup change", function(e) {
            var fromCurrency = $("#withdrawal_currency").val();
            $(".convertCurrencyText").html(fromCurrency.toLocaleUpperCase());
            var toCurrency = "usd";
            var currency_amt = $("#withdraw_amount_usd").val();
            if (currency_amt != '') {
                var usd_amt = convertCurrency(toCurrency, fromCurrency, currency_amt)
            }
        });

        function convertCurrency(fromCurrency, toCurrency, amount) {
            if (fromCurrency && toCurrency && amount) {
                const apiKey = '0f061f933a8359addf8aac5c';
                const url = `https://v6.exchangerate-api.com/v6/${apiKey}/pair/${fromCurrency}/${toCurrency}/${amount}`;
                $(".convertCurrency").html(toCurrency.toLocaleUpperCase());
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        if (data.result === "success") {
                            if (toCurrency == 'INR') {
                                var inr_adj_amount = parseFloat(<?= $settings['inr_adj_amount_withdrawal'] ?>);
                                var rate = data.conversion_rate;
                                var rate_inr = rate - parseFloat(inr_adj_amount);
                                var result = parseFloat(amount * rate_inr).toFixed(2);
                                var adjustment = (data.conversion_result - result);
                                $("#adjustment_inr").val(adjustment);
                                console.log(fromCurrency, toCurrency, amount, data.conversion_rate, data
                                    .conversion_result, result, adjustment);
                            } else {
                                var result = data.conversion_result;
                            }
                            $("#amount_in_other_currency").val(result);
                        } else {
                            $("#amount_in_other_currency").val("0.00");
                            swal.fire({
                                icon: "info",
                                title: "Something went wrong on Currency convertion.",
                                text: "Please try again after sometimes or Contact support."
                            });
                        }
                    },
                    error: function() {
                        // $("#result").text('Error: Something went wrong.');
                        swal.fire({
                            icon: "info",
                            title: "Something went wrong on Currency convertion.",
                            text: "Please try again after sometimes or Contact support."
                        });
                    }
                });
            }
        }

        $(".tradeWithdrawalForm").submit(function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            let formData = new FormData(this);
            $.ajax({
                type: "POST",
                url: "{{ route('ib_bank_withdraw') }}",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
					Swal.fire({
						icon: response.status === 'success' ? 'success' : 'error',
						title: response.status === 'success' ? 'Success' : 'Error',
						html: response.message
					}).then(() => {
						if (response.status === 'success') {
							window.location.reload(true);
						}
					});
				},
				error: function(xhr) {
					let msg = xhr.responseJSON?.message || 'Something went wrong.';
					Swal.fire({
						icon: 'error',
						title: 'Error',
						html: msg
					});
				}
            });
        });
        window.addEventListener('beforeunload', function(e) {
            if ($('#checkoutModal').hasClass('show')) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
        $('#match2payForm').on('submit', function(event) {
            event.preventDefault();
            var actionUrl = $(this).attr('action');
            var formData = $(this).serialize();
            console.log(formData);
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
        $('.select-liveaccount').change(function() {
            var maxAmount = $(this).data('balance');
            $('input[name="withdraw_amount"]').attr('max', maxAmount);
        });
        $('.select-liveaccount').trigger('change');
        $(".otp-req").click(function(e) {
            e.preventDefault();
            var type=$(this).attr("data-type");
            if($(this).attr("disabled")){
                return true;
            }
            $.ajax({
                url: "/getOtp",
                type: "POST",
                data: {
                    "action": "getPOotp",
                    "type":type
                },
                beforeSend: function() {
                    $(".otp-req[data-type='" + type + "']").attr("disabled", "true");
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Please wait while we process your request.',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    if (data.success == true) {
                        $("[name='otp'][data-type='" + type + "']").removeAttr("disabled");
                        Swal.fire({
                            icon: 'success',
                            title: "Please check the Email and Put the OTP here",
                            text: "Kindly check in SPAM/JUNK folder for second case. Otherwise try again after sometime."
                        }).then((val) => {
                            $("[name='otp'][data-type='" + type + "']")[0].focus();
                        });
                    } else {
                        $(".otp-req[data-type='" + type + "']").attr("disabled", "true");
                        Swal.fire({
                            icon: 'warning',
                            title: data.message,
                        })
                    }
                }
            });
        });
    </script>
	
	
	
	
	<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script>
        $("[data-bs-target]").click(function() {
            var target = $(this).attr("data-bs-target");
            var targetTab = ".connectionTab .tab-pane" + target;
            console.log(targetTab);
            $(".connectionTab .tab-pane").removeClass("show");
            $(".connectionTab .tab-pane").removeClass("active");
            $(targetTab).addClass("show active");
        });
        var clipboard = new ClipboardJS('.cb');
        clipboard.on('success', function(e) {
            swal.fire({
                icon: "success",
                title: "IB Referral Link Copied"
            });
        });

        // $("#commissionTbl").dataTable();
        $(document).ready(function() {
            $('#commissionTbl').DataTable({
				order: [[0, 'desc']],
                dom: '<"row" <"col"B><"col text-center"l><"col"f>><"row"<"col"Q>><"row"<"col"t>><"row"<"col"i><"col"p>>',
                buttons: [{
                    extend: 'excel',
                    text: 'Excel',
                    title: `{{ ucfirst(session('user')->fullname) }} -IB Commission Histories`,
                    exportOptions: {
                        columns: ':not(:eq(4))',
                        modifier: {
                            visible: true
                        },
                    },
                    className: 'bg-primary text-white'
                }],
                ajax: {
                    "url": "/ib-commission-histories",
                    "type": "GET"
                },

                columns: [{
                        data: 'created_at',
                        name: 'created_at',
                        title: 'DATETIME',
                        render: function(data, type, row) {
                            var dateTime = row.created_at.split('T');
                            var date = dateTime[0];
                            var time = dateTime[1];
                            var return_data = "<div class='d-grid'><div class='date'>" + date +
                                "</div><div class='time text-muted'>" + time + "</div></div>";
                            return return_data;
                        }
                    }, {
                        data: 'order_id',
                        title: 'ORDER #',
                    }, {
                        data: 'trade_id',
                        title: 'Trade ID',
                        visible: false
                    },
                    {
                        data: 'remark',
                        title: 'Remark',
                        visible: false
                    }, {
                        data: null,
                        title: 'ACCOUNT',
                        render: function(data, type, row) {
                            return `
            <div class="row align-items-center">
                <div class="col-auto pe-0">
                    <img src="/assets/images/mt5.png" alt="user-image" class="wid-50 hei-50 rounded">
                </div>
                <div class="col">
                    <h4 class="mb-2 ms-2">
                        <span class="text-truncate w-100">${row.trade_id??''}</span>
                    </h4>
                    <p class="text-muted ms-2 f-12 mb-0">
                        <span class="text-truncate w-100">${row.remark}</span>
                    </p>
                </div>
            </div>
        `;
                        }
                    },
                    {
                        title: 'Name',
                        data: "user_name",
                    },
                    {
                        title: 'TYPE',
                        data: "ib_wallet",
                        render: function(data, type, row) {
                            return row.ib_wallet?(row.order_type==0?"Buy":(row.order_type==1?"Sell":"")):"Transfer";
                        }
                    },
                      
                      {
                        title: 'COMMISSION PER LOT',
                        data: "comission_per_lot",
                        render: function(data, type, row) {
                            return data ? data : '0.00';
                        }
                    },

                     {
                        data: 'ib_level',
                        title: 'LEVEL',
                        render: function(data, type, row) {
                            return data ? data : '';
                        }

                    }, {
                        data: 'volume',
                        title: 'VOLUME',
                        render: function(data, type, row) {
                            return data ? data : '0.000';
                        }
                    }, {
                        data: 'trade_id',
                        title: 'AMOUNT',
                        render: function(data, type, row) {
                            return row.ib_wallet ? row.ib_wallet : row.ib_withdraw;
                        }
                    }
                ]
            });
        });
    </script>
@endsection
