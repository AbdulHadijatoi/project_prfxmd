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
                                <h4 class="mb-0">Fund</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body p-0">
                            @include('sub_header')
                        </div>
                    </div>
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
                                    <div class="row">
                                        <div class="col-12 col-lg-8">
                                            <div class="card">
                                                <div class="card-body border-bottom">
                                                    <h6>CREATE DEPOSIT TICKET</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="divider my-4"><span>SELECT MT5 ACCOUNT</span></div>
                                                    <div class="row g-1">
                                                        @foreach ($liveaccount_details as $liveaccount)
                                                            <div class="col-md-3 col-lg-4 col-xl-4">
                                                                <div class="address-check border rounded">
                                                                    <div class="form-check paycard">
                                                                        <input id="liveaccount{{ $liveaccount->trade_id }}"
                                                                            type="radio" name="live-account"
                                                                            class="select-liveaccount form-check-input input-primary"
                                                                            data-mindep="{{ $liveaccount->mindep }}"
                                                                            value="{{ $liveaccount->trade_id }}">
                                                                        <label class="form-check-label d-block" required>
                                                                            <div class="p-1 my-1">
                                                                                <span class="row">
                                                                                    <span class="col-6 mt-1">
                                                                                        <span
                                                                                            class="h5 mb-0 d-block f-w-500 pb-0 f-14">
                                                                                            <img src="/assets/images/mt5.png"
                                                                                                alt="user-image"
                                                                                                class="wid-25 me-1 ms-1">
                                                                                            {{ $liveaccount->trade_id }}
                                                                                        </span>
                                                                                    </span>
                                                                                    <span
                                                                                        class="col-6 text-end mb-0 pb-0 pe-3">
                                                                                        <span
                                                                                            class="h5 mb-0 d-block f-w-500">
                                                                                            ${{ $liveaccount->Balance ?? 0.0 }}
                                                                                        </span>
                                                                                        <span
                                                                                            class="text-muted mb-0 f-10">Current
                                                                                            Balance</span>
                                                                                    </span>
                                                                                </span>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="divider my-4"><span>SELECT PAYMENT METHOD</span>
                                                    </div>
                                                    <div class="row g-1">
                                                        @if ($walletenabled)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="walletpayment" value="Wallet Transfer"
                                                                            data-type="Wallet-Transfer">
                                                                        <label class="form-check-label d-block"
                                                                            for="walletpayment">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span class="d-flex align-items-center">
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">Wallets</span>
                                                                                        <span class="f-10 text-muted">Wallet
                                                                                            Transfer</span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (!empty($user_groups['bankwire']) && $user_groups['bankwire_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="bankwire" value="BankWire"
                                                                            data-type="bank-wire">
                                                                        <label class="form-check-label d-block"
                                                                            for="bankwire">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span class="d-flex align-items-center">
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">BankWire</span>
                                                                                        <span
                                                                                            class="f-10 text-muted">BankWire
                                                                                            Transfer</span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (!empty($user_groups['crypto_payment_api']) && $user_groups['crypto_payment_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type" checked
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="option_match2pay" value="match2pay"
                                                                            data-type="match2pay">
                                                                        <label class="form-check-label d-block"
                                                                            for="option_match2pay">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span
                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                    <span>Crypto Payment</span>
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">
                                                                                            <img src="/{{ $settings['favicon'] }}"
                                                                                                alt="match2pay"
                                                                                                style="height: 40px;">
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        @if (
                                                            !empty($user_groups['usdt_wallet_qr']) &&
                                                                !empty($user_groups['usdt_wallet_id']) &&
                                                                $user_groups['usdt_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type" checked
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="option_usdt_deposit" value="USDT Deposit"
                                                                            data-type="USDT-Deposit">
                                                                        <label class="form-check-label d-block"><span
                                                                                class="card-body p-2 d-block"><span
                                                                                    class="h6 f-w-500 mb-1 d-block">USDT
                                                                                    DEPOSIT</span><span
                                                                                    class="d-flex align-items-center"><span
                                                                                        class="f-10 badge bg-light-success me-3">USDT
                                                                                        DEPOSIT</span><img
                                                                                        src="/assets/images/trc.png"
                                                                                        alt="img"
                                                                                        class="img-fluid ms-1 wid-25"></span></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (!empty($user_groups['payissa_wallet']) && $user_groups['payissa_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check  trade-deposit-type border rounded">
                                                                    <div class="form-check"><input type="radio"
                                                                            name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="payopn-check-4" value="USDC Polygon"
                                                                            data-type="usdc-polygon"><label
                                                                            class="form-check-label d-block"
                                                                            for="payopn-check-4"><span
                                                                                class="card-body p-2 d-block">
                                                                                <span
                                                                                    class="d-flex justify-content-between"><span><span
                                                                                            class="h6 f-w-500 mb-1 d-block">Card
                                                                                            Payment</span><span
                                                                                            class="f-10 text-muted">
                                                                                            Debit/Credit Cards</span>
                                                                                    </span><span class="text-success"><i
                                                                                            data-feather="credit-card"></i></span></span></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
														<div class="col-6 col-lg-6 col-xl-6">
															<div class="address-check  trade-deposit-type border rounded">
																<div class="form-check">
																	<input type="radio" name="deposit_type" class="form-check-input input-primary tradefund-deposit" id="xyrapay-check-5" value="xyrapay" data-type="xyrapay">
																	<label class="form-check-label d-block" for="xyrapay-check-5">
																		<span class="card-body p-2 d-block">				
																			<span class="d-flex justify-content-between">
																			<img src="{{ asset('assets/images/xyrapaylight.png') }}" title="xyrapay" alt="xyrapay" style="width:70%;" />
																			
																			<!--<span><span class="h6 f-w-500 mb-1 d-block">Card Payment</span><span class="f-10 text-muted"> Debit/Credit Cards</span></span>-->
																			<span class="text-success"><i data-feather="credit-card"></i></span></span>
																		</span>
																	</label>
																</div>
															</div>
														</div>
														
														<div class="col-6 col-lg-6 col-xl-6">
															<div class="address-check  trade-deposit-type border rounded">
																<div class="form-check">
																	<input type="radio" name="deposit_type" class="form-check-input input-primary tradefund-deposit" id="usdtpay-check-5" value="USDT Deposit" data-type="usdtpay">
																	<label class="form-check-label d-block" for="usdtpay-check-5">
																		<span class="card-body p-2 d-block">				
																			<span class="d-flex justify-content-between">
																			<span><span class="h6 f-w-500 mb-1 d-block">USDT Payment</span>
																			<span class="f-10 text-muted">USDT / CRYPTO Payment</span>
																			</span>
																			<span class="text-success">
																			<img src="/assets/images/trc.png" alt="img" class="img-fluid ms-1 wid-25" /></span>
																		</span></span>
																	</label>
																</div>
															</div>
														</div>
														
														<!--<div class="col-6 col-lg-6 col-xl-6">
															<div class="address-check  trade-deposit-type border rounded">
																<div class="form-check">
																	<input type="radio" name="deposit_type" class="form-check-input input-primary tradefund-deposit" id="paygate-check-4" value="paygate" data-type="paygate">
																	<label class="form-check-label d-block" for="paygate-check-4">
																		<span class="card-body p-2 d-block">				
																			<span class="d-flex justify-content-between">
																			<span><span class="h6 f-w-500 mb-1 d-block">CRYPTO Payment</span>
																			<span class="f-10 text-muted">CRYPTO Direct Payment</span>
																			</span>
																			<span class="text-success">
																			<img src="/assets/images/trc.png" alt="img" class="img-fluid ms-1 wid-25" /></span>
																		</span></span>
																	</label>
																</div>
															</div>
														</div> -->
                                                        @if (!empty($user_groups['now_payment_api']) && $user_groups['now_payment_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type" checked
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="option_nowpayment" value="Now Payment"
                                                                            data-type="Now-Payment">
                                                                        <label class="form-check-label d-block"
                                                                            for="option_nowpayment">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span
                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                    <span>Crypto Payment</span>
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">
                                                                                            <img src="/assets/images/nowpayments-white.png"
                                                                                                alt="Now Payment"
                                                                                                style="height: 40px;">
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if (!empty($user_groups['bank_account_details']) && $user_groups['bank_deposit_status'] == 1)
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type" checked
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="option_bankdeposit" value="Bank Deposit"
                                                                            data-type="Bank-Deposit">
                                                                        <label class="form-check-label d-block"
                                                                            for="option_bankdeposit"><span
                                                                                class="card-body p-2 d-block"><span
                                                                                    class="d-flex align-items-center"><span><span
                                                                                            class="h6 f-w-500 mb-1 d-block">Bank Deposit</span><span
                                                                                            class="f-10 text-muted">Bank Deposit</span></span></span></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
														<div class="col-6 col-lg-6 col-xl-6">
                                                            <div class="address-check trade-deposit-type border rounded">
                                                                <div class="form-check"><input type="radio"
                                                                        name="deposit_type"
                                                                        class="form-check-input input-primary tradefund-deposit"
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
                                                    <div class="Wallet-Transfer trade-deposit-details"
                                                        style="display:none" data-type="wallettransfer">
                                                        <form method="post" id="tradeDepositForm">
                                                            @csrf
                                                            <input type="hidden" name="user[email]"
                                                                value="{{ session('clogin') }}" required
                                                                class="form-control fill">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value="" readonly required>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="BANK DEPOSIT">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">DEPOSIT
                                                                            CURRENCY:
                                                                            <small class="text-muted d-block"> Please
                                                                                select the currency you wish to use for the
                                                                                payment </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <select class="form-select" required>
                                                                                <option value="USD">USD</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">ENTER
                                                                            AMOUNT:
                                                                            <small class="text-muted d-block"> Please enter
                                                                                the amount to be deposited in selected
                                                                                currency</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input name="user[deposit]" type="number"
                                                                                    class="deposit_amount form-control fill tradedeposit_amount bonus-calculate"
                                                                                    aria-label="Amount" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none"><label
                                                                            class="col-lg-4 col-form-label">BONUS AVAILABLE
                                                                            :</label>
                                                                        <div
                                                                            class="col-lg-8 bonus-options bonus-options-wallet d-flex flex-wrap">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none">
                                                                        <label class="col-lg-4 col-form-label">BONUS
                                                                            CALCULATED:
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input id="bonus_calculated"
                                                                                    class="form-control bonus_calculated"
                                                                                    readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="">
                                                                        <div class="row">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <div class="row g-1">
                                                                                    <input type="submit"
                                                                                        name="a[register]"
                                                                                        class="btn btn-primary col-12"
                                                                                        value="Deposit To Trade Account">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="bank-wire trade-deposit-details" style="display:none"
                                                        data-type="bankwire">
                                                        <form method="post" id="tradeDepositForm"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="user[email]"
                                                                value="{{ session('clogin') }}" required
                                                                class="form-control fill">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value="" readonly required>
                                                            <div class="row">
                                                                <div class="col-4">
                                                                    <label class="col-form-label">BANKWIRE DETAILS:</label>
                                                                </div>
                                                                <div class="col-8">
                                                                    <?= $user_groups['bankwire'] ?>
                                                                    <textarea class="form-control d-none" name="user[bankwire]" readonly><?= $user_groups['bankwire'] ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="BANKWIRE">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">DEPOSIT
                                                                            CURRENCY:
                                                                            <small class="text-muted d-block"> Please
                                                                                select the currency you wish to use for the
                                                                                payment </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <select class="form-select" required>
                                                                                <option value="USD">USD</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">ENTER
                                                                            AMOUNT:
                                                                            <small class="text-muted d-block"> Please enter
                                                                                the amount to be deposited in selected
                                                                                currency</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input name="user[deposit]" type="number"
                                                                                    class="deposit_amount form-control fill tradedeposit_amount bonus-calculate"
                                                                                    aria-label="Amount" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none"><label
                                                                            class="col-lg-4 col-form-label">BONUS AVAILABLE
                                                                            :</label>
                                                                        <div
                                                                            class="col-lg-8 bonus-options bonus-options-bankwire d-flex flex-wrap">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none">
                                                                        <label class="col-lg-4 col-form-label">BONUS
                                                                            CALCULATED:
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input id="bonus_calculated"
                                                                                    class="form-control bonus_calculated"
                                                                                    readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">RECEIPT:<small
                                                                                class="text-muted d-block"> Upload receipt
                                                                                of your transaction </small></label>
                                                                        <div class="col-lg-8"><input type="file"
                                                                                accept="application/pdf,image/png,image/jpeg"
                                                                                class="form-control" name="deposit_proof"
                                                                                required></div>
                                                                    </div>
                                                                    <div class="">
                                                                        <div class="row">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <div class="row g-1">
                                                                                    <input type="submit"
                                                                                        name="a[register]"
                                                                                        class="btn btn-primary col-12"
                                                                                        value="Deposit To Trade Account">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="match2pay trade-deposit-details" style="display:none"
                                                        data-type="wallettransfer">
                                                        <form method="post" id="match2payForm" action="trade-deposit">
                                                            @csrf
                                                            <input type="hidden" name="user[email]"
                                                                value="{{ session('clogin') }}" required
                                                                class="form-control fill">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value="" readonly required>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="Match2Pay">
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">DEPOSIT
                                                                            CURRENCY:
                                                                            <small class="text-muted d-block"> Please
                                                                                select the currency you wish to use for the
                                                                                payment </small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <select class="form-select" required>
                                                                                <option value="USD">USD</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-lg-4 col-form-label">ENTER
                                                                            AMOUNT:
                                                                            <small class="text-muted d-block"> Please enter
                                                                                the amount to be deposited in selected
                                                                                currency</small>
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input name="user[deposit]" type="number"
                                                                                    class="deposit_amount form-control fill tradedeposit_amount bonus-calculate"
                                                                                    aria-label="Amount" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none"><label
                                                                            class="col-lg-4 col-form-label">BONUS AVAILABLE
                                                                            :</label>
                                                                        <div
                                                                            class="col-lg-8 bonus-options bonus-options-match2pay d-flex flex-wrap">
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row bonus-row d-none">
                                                                        <label class="col-lg-4 col-form-label">BONUS
                                                                            CALCULATED:
                                                                        </label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3">
                                                                                <span class="input-group-text">USD</span>
                                                                                <input id="bonus_calculated"
                                                                                    class="form-control bonus_calculated"
                                                                                    readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="">
                                                                        <div class="row">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <div class="row g-1">
                                                                                    <input type="submit"
                                                                                        name="a[register]"
                                                                                        class="btn btn-primary col-12"
                                                                                        value="Deposit To Trade Account">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="usdtpay trade-deposit-details wallet-deposit-details"
                                                        style="display:none" >
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material"
                                                            enctype="multipart/form-data">
                                                            <input type="hidden" name="user[deposit_type]"
                                                                class="tradedeposittype" value="USDT Deposit">
                                                            <input type="hidden" name="user[email]"
                                                                value="{{ session('clogin') }}">
                                                            <input type="hidden" name="user[usdt_wallet_id]"
                                                                value="{{ $user_groups['usdt_wallet_id'] }}">
                                                            <input type="hidden" name="user[usdt_wallet_qr]"
                                                                value="{{ $user_groups['usdt_wallet_qr'] }}">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value="" readonly required >
                                                            
															
															<div class="col-12 mt-2">
																<div class="form-group row">
																	<div class="col-lg-4 col-sm-12">
																		<label class="col-form-label">DEPOSIT ADDRESS IN USDT-TRC20 :  </label>  
																	</div>
																	<div class="col-lg-4 col-sm-12">
																		<div class="mx-4">
																		<medium class="text-muted d-block text-center"><b>Tron</b></medium>
																		<a href="#" class="d-block mb-4"><img src="{{ asset('assets/images/tron.png') }}" alt="" height="210"></a>
																		<medium class="text-muted d-block text-center"><b>TKgjQfcnQPdepFaNBa5aZQp9kJFwJNzQQT</b></medium>
																		</div>
																	</div>
																	<div class="col-lg-4 col-sm-12">
																		<div class="mx-4">
																		<medium class="text-muted d-block text-center"><b>Ethereum</b></medium>
																		<a href="#" class="d-block mb-4"><img src="{{ asset('assets/images/ethereum.png') }}" alt="" height="210"></a>
																		<medium class="text-muted d-block text-center"><b>0x67608DA49B329d316679dB6Dde49a36c2A69092b</b></medium>
																		</div>
																	</div>
																</div>
															</div>
															
                                                            <div class="col-12 mt-2">
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">DEPOSIT
                                                                        CURRENCY
                                                                        :<small class="text-muted d-block"> Please
                                                                            select the currency you wish to use for
                                                                            the payment </small></label>
                                                                    <input type="hidden" name="currency"
                                                                        value="USDT TRC20">
                                                                    <div class="col-lg-8"><select class="form-select"
                                                                            id="exampleFormControlSelect1" disabled=""
                                                                            name="currencyType">
                                                                            <option value="USDT TRC20">USDT TRC20
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
                                                                                class="input-group-text">USDT
                                                                                TRC20</span><input type="number"
                                                                                class="form-control wallet-amount"
                                                                                aria-label="Amount" name="user[deposit]"
                                                                                required>
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
                                                                                aria-label="Amount" disabled=""><!---->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">DEPOSIT
                                                                        PROOF:<small class="text-muted d-block">
                                                                            Upload proof of your transaction
                                                                        </small></label>
                                                                    <div class="col-lg-8"><input type="file"
                                                                            accept="application/pdf,image/png,image/jpeg"
                                                                            class="form-control" data-limit="2"
                                                                            name="deposit_proof" required>
                                                                        <small data-v-d6f2db71=""
                                                                            class="text-muted mt-2">Maximum File
                                                                            Size Allowed : 2MB</small>
                                                                    </div>
                                                                </div><!---->
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1">
                                                                                <input type="submit" name="add_wallet"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="PROCESS PAYMENT">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </form>
                                                    </div>
                                                    <div class="row usdc-polygon wallet-deposit-details trade-deposit-details"
                                                        style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material">
                                                            <div class="col-12 mt-2">
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">DEPOSIT CURRENCY
                                                                        :<small class="text-muted d-block"> Please select
                                                                            the currency you wish to use for
                                                                            the payment </small></label>
                                                                    <input class="user_trade_id" type="hidden"
                                                                        name="user[trade_id]" value="" readonly
                                                                        required>
                                                                    <input type="hidden" name="user[email]"
                                                                        value="{{ session('clogin') }}" required
                                                                        class="form-control fill">
                                                                    <input type="hidden" name="currency" value="USD">
                                                                    <input class="deposit_type" type="hidden"
                                                                        name="user[deposit_type]" value="usdc-polygon">
                                                                    <div class="col-lg-8"><select class="form-select"
                                                                            id="currencyType" disabled
                                                                            name="currencyType">
                                                                            <option value="USD" selected>USD</option>
                                                                        </select></div>
                                                                </div>
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">ENTER AMOUNT
                                                                        :<small class="text-muted d-block"> Please enter
                                                                            the amount to be deposited in selected
                                                                            currency</small></label>
                                                                    <div class="col-lg-8">
                                                                        <div class="input-group mb-3"><span
                                                                                class="input-group-text currency-type">USD</span><input
                                                                                type="number"
                                                                                class="form-control wallet-amount"
                                                                                aria-label="Amount" name="user[deposit]"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1">
                                                                                <input type="submit" name="add_wallet"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="PROCESS PAYMENT">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
													
													<div class="row xyrapay wallet-deposit-details trade-deposit-details"
                                                        style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material">
                                                            <div class="col-12 mt-2">
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">DEPOSIT CURRENCY
                                                                        :<small class="text-muted d-block"> Please select
                                                                            the currency you wish to use for
                                                                            the payment </small></label>
                                                                    <input class="user_trade_id" type="hidden"
                                                                        name="user[trade_id]" value="123" readonly
                                                                        required>
                                                                    <input type="hidden" name="user[email]"
                                                                        value="{{ session('clogin') }}" required
                                                                        class="form-control fill">
                                                                    <input type="hidden" name="currency" value="USD">
                                                                    <input class="deposit_type" type="hidden"
                                                                        name="user[deposit_type]" value="xyrapay">
                                                                    <div class="col-lg-8"><select class="form-select"
                                                                            id="currencyType" disabled
                                                                            name="currencyType">
                                                                            <option value="USD" selected>USD</option>
                                                                        </select></div>
                                                                </div>
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">ENTER AMOUNT
                                                                        :<small class="text-muted d-block"> Please enter
                                                                            the amount to be deposited in selected
                                                                            currency</small></label>
                                                                    <div class="col-lg-8">
                                                                        <div class="input-group mb-3"><span
                                                                                class="input-group-text currency-type">USD</span><input
                                                                                type="number"
                                                                                class="form-control wallet-amount"
                                                                                aria-label="Amount" name="user[deposit]"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1">
                                                                                <input type="submit" name="add_wallet"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="PROCESS PAYMENT">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
													
													<div class="row paygate wallet-deposit-details trade-deposit-details"
                                                        style="display:none">
                                                        <form method="post" style="padding:10px;"
                                                            class="md-float-material form-material">
                                                            <div class="col-12 mt-2">
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">DEPOSIT CURRENCY
                                                                        :<small class="text-muted d-block"> Please select
                                                                            the currency you wish to use for
                                                                            the payment </small></label>
                                                                    <input class="user_trade_id" type="hidden"
                                                                        name="user[trade_id]" value="123" readonly
                                                                        required>
                                                                    <input type="hidden" name="user[email]"
                                                                        value="{{ session('clogin') }}" required
                                                                        class="form-control fill">
                                                                    <input type="hidden" name="currency" value="USD">
                                                                    <input class="deposit_type" type="hidden"
                                                                        name="user[deposit_type]" value="paygate">
                                                                    <div class="col-lg-8"><select class="form-select"
                                                                            id="currencyType" disabled
                                                                            name="currencyType">
                                                                            <option value="USD" selected>USD</option>
                                                                        </select></div>
                                                                </div>
                                                                <div class="form-group row"><label
                                                                        class="col-lg-4 col-form-label">ENTER AMOUNT
                                                                        :<small class="text-muted d-block"> Please enter
                                                                            the amount to be deposited in selected
                                                                            currency</small></label>
                                                                    <div class="col-lg-8">
                                                                        <div class="input-group mb-3"><span
                                                                                class="input-group-text currency-type">USD</span><input
                                                                                type="number"
                                                                                class="form-control wallet-amount"
                                                                                aria-label="Amount" name="user[deposit]"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="">
                                                                    <div class="row">
                                                                        <div class="col-lg-4"></div>
                                                                        <div class="col-lg-8">
                                                                            <div class="row g-1">
                                                                                <input type="submit" name="add_wallet"
                                                                                    class="btn btn-primary col-12"
                                                                                    value="PROCESS PAYMENT">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="Other-Payments trade-deposit-details"
                                                        style="display:none">
                                                        <form method="post" id="other_payment_form"
                                                            enctype="multipart/form-data">
                                                            <input hidden name="user[email]"
                                                                value="{{ session('clogin') }}" min="10"
                                                                type="email" required="" class="form-control fill">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value=""
                                                                class="form-control fill" readonly="" required>
                                                            <input id="adjustment_inr" type="hidden"
                                                                name="user[adjustment_inr]" value=""
                                                                class="form-control fill" readonly="" required>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="OTHER PAYMENT">
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">DEPOSIT
                                                                            CURRENCY
                                                                            :<small class="text-muted d-block"> Please
                                                                                select the currency you wish to use for the
                                                                                payment </small></label>
                                                                        <div class="col-lg-8">
                                                                            <input type="hidden"
                                                                                name="deposit_currency_amount">
                                                                            <select class="form-select deposit_currency"
                                                                                name="user[deposit_currency]"
                                                                                id="deposit_currency_op" required>
                                                                                <option value="INR">INR</option>
                                                                                <option value="USD">USD</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">ENTER AMOUNT
                                                                            :<small class="text-muted d-block"> Please
                                                                                enter
                                                                                the amount to be deposited in selected
                                                                                currency</small></label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3"><span
                                                                                    class="input-group-text convertCurrencyText">INR</span><input
                                                                                    name="user[deposit]"
                                                                                    id="deposit_amount_op" type="number"
                                                                                    class="form-control fill"
                                                                                    aria-label="Amount" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">AMOUNT IN USD
                                                                            :<small class="text-muted d-block"> Deposit
                                                                                amount in USD </small></label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3"><span
                                                                                    class="input-group-text">USD</span><input
                                                                                    name="user[deposit_currency_in_usd]"
                                                                                    id="amount_deposit_op" type="text"
                                                                                    class="form-control fill tradedeposit_amount"
                                                                                    aria-label="Amount" readonly required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">DEPOSIT PROOF
                                                                            :<small class="text-muted d-block"> Upload
                                                                                proof of your transaction </small></label>
                                                                        <div class="col-lg-8"><input type="file"
                                                                                accept="application/pdf,image/png,image/jpeg"
                                                                                class="form-control" name="deposit_proof"
                                                                                required></div>
                                                                    </div>
                                                                    <div class="">
                                                                        <div class="row">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <input type="hidden" name="a[register]"
                                                                                    value="true">
                                                                                <div class="row g-1"><input type="submit"
                                                                                        class="btn btn-primary col-12 other_payment op_btn"
                                                                                        value="Deposit To Trade Account">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="Now-Payment trade-deposit-details" style="display:none">
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
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="Now Payment">
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
                                                                                    name="user[deposit]" id="deposit_amount_now"
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
                                                                                    <input type="submit" name="register"
                                                                                        class="btn btn-primary col-12"
                                                                                        value="Deposit To Trade Account">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="Bank-Deposit trade-deposit-details"
                                                        style="display:none">
                                                        <form method="post" enctype="multipart/form-data">
                                                            <input hidden name="user[email]"
                                                                value="{{ session('clogin') }}" min="10"
                                                                type="email" required="" class="form-control fill">
                                                            <input class="user_trade_id" type="hidden"
                                                                name="user[trade_id]" value=""
                                                                class="form-control fill" readonly="" required>
                                                            <div class="row">
                                                                <div class="col-12 mt-2">
                                                                    <input type="hidden" name="user[deposit_type]"
                                                                        class="tradedeposittype" value="BANK ACC DEPOSIT">
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">DEPOSIT ACCOUNT
                                                                            DETAILS
                                                                            :</label>
                                                                        <div class="col-lg-8">
                                                                            <?= $user_groups['bank_account_details'] ?>
                                                                        </div>
                                                                    </div>
                                                                    <textarea class="form-control d-none" name="deposit_account_details" readonly><?= $user_groups['bank_account_details'] ?></textarea>
																	<div class="row">
																		<div class="col-12 mt-2 m6-4">
																			<medium class="text-muted d-block text-center">
																				Kindly Attention,
																				when transferring funds to any of our accounts in the future, please do not mention any remarks or comments related to Forex, FX, Stock market, Investment, Share, Trading, MT5 , Metatrade or Demat or similar terms.

																				If Mentioned This payment we not accept and refund.
																			</medium>
																		</div>
																		<div class="col-12 mt-2 m6-4">
																			<medium class="text-muted d-block text-center">
																				कृपया ध्यान दें,
																				भविष्य में हमारे किसी भी खाते में धनराशि स्थानांतरित करते समय, कृपया फॉरेक्स, एफएक्स, स्टॉक मार्केट, निवेश, शेयर, ट्रेडिंग, MT5, मेटाट्रेड, डीमैट या इसी तरह की शर्तों से संबंधित किसी भी टिप्पणी या टिप्पणी का उल्लेख न करें।

																				यदि उल्लेख किया गया है तो यह भुगतान हम स्वीकार नहीं करेंगे और रिफंड नहीं करेंगे।
																			</medium>
																		</div>
																	</div>
																	
                                                                    <div class="form-group row mt-2"><label
                                                                            class="col-lg-4 col-form-label">DEPOSIT
                                                                            CURRENCY
                                                                            :<small class="text-muted d-block"> Please
                                                                                select the currency you wish to use for the
                                                                                payment </small></label>
                                                                        <div class="col-lg-8"><select class="form-select"
                                                                                id="exampleFormControlSelect1" required>
                                                                                <option value="USD">USD</option>
                                                                                <option value="INR">INR</option>
                                                                            </select></div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">ENTER AMOUNT
                                                                            :<small class="text-muted d-block"> Please
                                                                                enter
                                                                                the amount to be deposited in selected
                                                                                currency</small></label>
                                                                        <div class="col-lg-8">
                                                                            <div class="input-group mb-3"><span
                                                                                    class="input-group-text">USD</span><input
                                                                                    name="user[deposit]"
                                                                                    id="deposit_amount_now" type="number"
                                                                                    class="form-control fill nowdeposit_amount"
                                                                                    aria-label="Amount" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row"><label
                                                                            class="col-lg-4 col-form-label">RECEIPT:<small
                                                                                class="text-muted d-block"> Upload receipt
                                                                                of your transaction </small></label>
                                                                        <div class="col-lg-8"><input type="file"
                                                                                accept="application/pdf,image/png,image/jpeg"
                                                                                class="form-control" name="deposit_proof"
                                                                                required></div>
                                                                    </div>
                                                                    <div class="">
                                                                        <div class="row">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <div class="row g-1"><input type="submit"
                                                                                        name="a[register]"
                                                                                        class="btn btn-primary col-12"
                                                                                        value="Deposit To Trade Account">
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
                                        <div class="col-12 col-lg-4">
                                            <div class="card coupon-card bg-primary">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div
                                                            class="col-8 d-flex flex-column align-items-start justify-content-center">
                                                            <h3 class="text-white f-w-500">Fuel Your Trading Journey</h3>
                                                            <span class="f-16 py-2 text-white">Deposit now and unlock the
                                                                gateway to global markets.</span>
                                                        </div>
                                                        <div class="col-4 text-end">
                                                            <img src="{{ asset('assets/images/fund_now.png') }}"
                                                                alt="img" class="img-fluid wid-110">
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
                                                                        <img src="{{ asset('assets/images/mt5.png') }}"
                                                                            alt="user-image" class="wid-25 me-1 ms-1">
                                                                    </span>
                                                                    <div class="media-body mx-2">
                                                                        <h5 class="mb-1">
                                                                            <span
                                                                                class="h4 mb-0 d-block f-w-500 pb-0">{{ $liveaccount->trade_id }}</span>
                                                                        </h5>
                                                                        <p class="text-sm mb-2"><span
                                                                                class="text-muted">ACCOUNT CATEGORY:</span>
                                                                            ECN</p>
                                                                        <div class="border-top border-dashed">
                                                                            <p class="mb-1 mt-2 d-grid">
                                                                                <span class="text-muted">LEVERAGE:
                                                                                    {{ $liveaccount->leverage }}</span>
                                                                                <span class="text-muted">CREDIT:
                                                                                    ${{ $liveaccount->credit }}</span>
                                                                                <span class="text-muted">EQUITY:
                                                                                    ${{ $liveaccount->equity }}</span>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="flex-shrink-0">
                                                                        <h4 class="f-w-500">${{ $liveaccount->Balance }}
                                                                        </h4>
                                                                        <p class="text-muted text-sm mb-2 text-end">Balance
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                        <li class="list-group-item">
                                                            <div class="float-end">
                                                                <h4 class="mb-0 fw-medium">${{ !empty($totals->credit) ? number_format($totals->credit, 2) : '0.00' }}</h4>
                                                            </div>
                                                            <span class="text-muted">TOTAL CREDIT</span>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <div class="float-end">
                                                                <h4 class="mb-0 fw-medium">${{ !empty($totals->equity) ? number_format($totals->equity, 2) : '0.00' }}</h4>
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
                                                                <h3 class="mb-0 fw-medium">${{ !empty($totals->balance) ? number_format($totals->balance, 2) : '0.00' }}</h3>
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
		@else
			<div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
				<div class="row p-3">
					<div class="card-body text-center">
						<div class="text-center me-4"><a href="/transactions/deposit#"><img
									src="/assets/images/doc_upload.png" class="w-25" alt="img"></a></div>
						<h6 class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">KYC Not Yet Verified !
						</h6>
						<a href="/user/documentUpload" id="verify-user-kyc-disabled" class="mt-3"><button
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
        $(document).on("submit", "#tradeDepositForm", function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('trade-deposit') }}",
                data: $(this).serialize(),
                success: function(response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: response.success,
                            text: response.message
                        }).then(() => {
                            window.location.href = '{{ route('transactions') }}';
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: xhr.responseJSON.message,
                        text: xhr.responseJSON.error
                    });
                }
            });
        });
    </script>
    <script>
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
                                console.log(isSwalOpen);
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
        $("#deposit_currency_op,#deposit_amount_op").on("change", function(e) {
            var fromCurrency = $("#deposit_currency_op").val();
            $(".convertCurrencyText").html(fromCurrency.toLocaleUpperCase());
            var toCurrency = "usd";
            var currency_amt = $("#deposit_amount_op").val();
            if (currency_amt != '') {
                var usd_amt = convertCurrency(fromCurrency, toCurrency, currency_amt)
            }
        });

        function convertCurrency(fromCurrency, toCurrency, amount) {
            if (fromCurrency && toCurrency && amount) {
                const apiKey = '0f061f933a8359addf8aac5c'; // Replace with your API key
                const url = `https://v6.exchangerate-api.com/v6/${apiKey}/pair/${fromCurrency}/${toCurrency}/${amount}`;
                $(".convertCurrency").html(toCurrency.toLocaleUpperCase());
                $.ajax({
                    url: url,
                    method: "GET",
                    success: function(data) {
                        if (data.result === "success") {
                            console.log(data);
                            if (fromCurrency == 'INR') {
                                var inr_adj_amount = parseFloat(<?= $settings['inr_adj_amount'] ?>);
                                var rate = (1 / data.conversion_rate);
                                var rate_usd = rate + parseFloat(inr_adj_amount);
                                var result = parseFloat(amount / rate_usd).toFixed(2);
                                var adjustment = (data.conversion_result - result);
                                var adjustment_inr = adjustment * (1 / data.conversion_rate);
                                console.log(adjustment, 1 / data.conversion_rate);
                                $("#adjustment_inr").val(adjustment_inr);
                                console.log(data.conversion_rate, data.conversion_result, rate, rate_usd,
                                    result, adjustment, adjustment_inr);
                            } else {
                                var result = data.conversion_result;
                            }
                            $("#amount_deposit_op").val(result);
                        } else {
                            $("#amount_deposit_op").val("0.00");
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
    </script>
@endsection
