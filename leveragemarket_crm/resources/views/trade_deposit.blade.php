@extends('layouts.crm.crm')
<style>
    .selected-card {
        border-color: #0d6efd;
        /* example: blue border */
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
    }
</style>
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
						@if($liveaccount_details && $liveaccount_details->isNotEmpty())
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
                                                                            <input
                                                                                id="liveaccount{{ $liveaccount->trade_id }}"
                                                                                type="radio" name="live-account"
                                                                                class="select-liveaccount form-check-input input-primary"
                                                                                data-mindep="{{ $liveaccount->accountType->ac_min_deposit }}"
                                                                                value="{{ $liveaccount->trade_id }}"
                                                                                required>

                                                                            <label class="form-check-label d-block"
                                                                                for="liveaccount{{ $liveaccount->trade_id }}">
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
                                                                                                ${{ ($liveaccount->Balance ?? 0.0) + ($liveaccount->credit ?? 0.0) }}
                                                                                            </span>
                                                                                            <span
                                                                                                class="text-muted mb-0 f-10">Current
                                                                                                Balance</span>
                                                                                        </span>
																						<span class="text-muted mb-0 f-12 f-w-100"><b>{{ $liveaccount->accountType->ac_name }}</b></span>
                                                                                    </span>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                    {{-- <div class="address-check border rounded">
                                                                        <div class="form-check paycard">
                                                                            <input
                                                                                id="liveaccount{{ $liveaccount->trade_id }}"
                                                                                type="radio" name="live-account"
                                                                                class="select-liveaccount form-check-input input-primary"
                                                                                data-mindep="{{ $liveaccount->accountType->ac_min_deposit }}"
                                                                                value="{{ $liveaccount->trade_id }}">
                                                                            <label class="form-check-label d-block"
                                                                                required>
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
                                                                                                ${{ ($liveaccount->Balance ?? 0.0) + ($liveaccount->credit ?? 0.0) }}
                                                                                            </span>
                                                                                            <span
                                                                                                class="text-muted mb-0 f-10">Current
                                                                                                Balance</span>
                                                                                        </span>
                                                                                    </span>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </div> --}}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="divider my-4"><span>SELECT PAYMENT METHOD</span>
                                                        </div>
                                                        <div class="row g-1">
                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="payopn-check-wallet" value="Wallet Payments"
                                                                            data-type="Wallet-Payments">
                                                                        <label class="form-check-label d-block"
                                                                            for="payopn-check-wallet">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span
                                                                                    class="d-flex align-items-center justify-content-between">
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">Wallet
                                                                                            Amount</span>
                                                                                        <span class="f-10 text-muted">Wallet
                                                                                            Amount Options</span>
                                                                                    </span>
                                                                                    <span class="text-block h6 mb-0">
                                                                                        $ <?php echo number_format($walletBalance, 2); ?>
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
                                                                                class="form-check-input input-primary tradefund-deposit"
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
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="option_paytiko" value="Paytiko"
                                                                            data-type="Paytiko" />
                                                                        <label class="form-check-label d-block"
                                                                            for="option_paytiko">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span
                                                                                    class="d-flex flex-wrap align-items-center justify-content-between">
                                                                                    <span>Card Payment</span>
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">
                                                                                            <img src="/assets/images/paytiko.png"
                                                                                                alt="Paytiko"
                                                                                                style="height: 30px;" />
                                                                                        </span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-6 col-lg-6 col-xl-6">
                                                                <div
                                                                    class="address-check trade-deposit-type border rounded">
                                                                    <div class="form-check">
                                                                        <input type="radio" name="deposit_type"
                                                                            class="form-check-input input-primary tradefund-deposit"
                                                                            id="payopn-check-others"
                                                                            value="Other Payments"
                                                                            data-type="Other-Payments">
                                                                        <label class="form-check-label d-block"
                                                                            for="payopn-check-others">
                                                                            <span class="card-body p-2 d-block">
                                                                                <span class="d-flex align-items-center">
                                                                                    <span>
                                                                                        <span
                                                                                            class="h6 f-w-500 mb-1 d-block">OTHERS</span>
                                                                                        <span class="f-10 text-muted">Other
                                                                                            Payment Options</span>
                                                                                    </span>
                                                                                </span>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="divider my-4"><span>DEPOSIT DETAILS</span></div>


                                                        <div class="Other-Payments trade-deposit-details"
                                                            style="display:none">
                                                            <form method="post" id="other_payment_form"
                                                                enctype="multipart/form-data">
                                                                <input hidden name="user[email]"
                                                                    value="{{ session('clogin') }}" min="10"
                                                                    type="email" required=""
                                                                    class="form-control fill">
                                                                <input class="user_trade_id" type="hidden"
                                                                    name="user[trade_id]" value=""
                                                                    class="form-control fill" readonly="" required>
                                                                <input id="adjustment_inr" type="hidden"
                                                                    name="user[adjustment_inr]" value=""
                                                                    class="form-control fill" readonly="" required>
                                                                <div class="row">
                                                                    <div class="col-12 mt-2">
                                                                        <input type="hidden" name="user[deposit_type]"
                                                                            class="tradedeposittype"
                                                                            value="OTHER PAYMENT">
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">DEPOSIT
                                                                                CURRENCY
                                                                                :<small class="text-muted d-block"> Please
                                                                                    select the currency you wish to use for
                                                                                    the
                                                                                    payment </small></label>
                                                                            <div class="col-lg-8">
                                                                              
                                                                              <select class="form-select deposit_currency" name="user[deposit_currency]" id="deposit_currency_op" required>
                                                                        <option value="INR" selected>INR</option>
                                                                        <option value="USD">USD</option>
                                                                    </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">ENTER
                                                                                AMOUNT
                                                                                :<small class="text-muted d-block"> Please
                                                                                    enter
                                                                                    the amount to be deposited in selected
                                                                                    currency</small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
																					<span class="input-group-text convertCurrencyText">INR</span>
																					<input name="user[deposit]"
																						   id="deposit_amount_op"
																						   type="number"
																						   class="form-control fill"
																						   placeholder=""
																						   required>
																					<input type="hidden"
																						   name="deposit_currency_amount"
																						   id="deposit_currency_amount">
																				</div>

                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row"><label
                                                                                class="col-lg-4 col-form-label">AMOUNT IN
                                                                                USD
                                                                                :<small class="text-muted d-block"> Deposit
                                                                                    amount in USD </small></label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3"><span
                                                                                        class="input-group-text">USD</span><input
                                                                                        name="user[deposit_currency_in_usd]"
                                                                                        id="amount_deposit_usd"
                                                                                        type="text"
                                                                                        class="form-control fill tradedeposit_amount"
                                                                                        aria-label="Amount" readonly
                                                                                        required>
                                                                                </div>
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
                                                                        @if ($bonuscode)
                                                                            <div class="form-group row">
                                                                                <label class="col-lg-4 col-form-label">
                                                                                    Apply Bonus Code:
                                                                                </label>
                                                                                <div class="col-lg-8">
                                                                                    <div class="input-group mb-3">
                                                                                        <select class="form-select" name="user[bonus_id]" id="bonus_id" >
																							<option value="">-- Select
																								Bonus --</option>

																							@forelse($bonuslist as $bonus)
																								<option
																									value="{{ $bonus->bonus_id }}">
																									{{ $bonus->bonus_name }}
																									({{ $bonus->percentage }}%) - {{ $bonus->bonus_code }}
																								</option>
																							@empty
																								<option value=""
																									disabled>
																									No bonuses available
																								</option>
																							@endforelse
																						</select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"></div>
                                                                                <div class="col-lg-8">
                                                                                    <input type="hidden"
                                                                                        name="a[register]" value="true">
                                                                                    <div class="row g-1">
                                                                                        <button type="submit"
                                                                                            class="btn btn-primary other_payment col-12">Deposit
                                                                                            To Trade Account</button>

                                                                                        <!--<input type="submit"
                                                                                                class="btn btn-primary col-12 other_payment op_btn"
                                                                                                value="Deposit To Trade Account"> -->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                        <div class="Wallet-Payments trade-deposit-details"
                                                            style="display:none;">
                                                            <form method="post" id="wallet_payment_form"
                                                                enctype="multipart/form-data">

                                                                <!--USER EMAIL-->
                                                                <input hidden name="user[email]"
                                                                    value="{{ session('clogin') }}" type="email"
                                                                    required class="form-control fill">

                                                                <input type="hidden" name="user[currentamt]"
                                                                    value="<?php echo $walletBalance; ?>">


                                                                <input type="hidden" name="user[trade_id]"
                                                                    value="" class="form-control fill user_trade_id"
                                                                    readonly required>

                                                                <!--WALLET BALANCE FOR VALIDATION-->
                                                                <input type="hidden" id="wallet_balance"
                                                                    value="<?php echo $walletBalance; ?>">

                                                                <input id="adjustment_inr" type="hidden"
                                                                    name="user[adjustment_inr]" value=""
                                                                    class="form-control fill" readonly required>

                                                                <div class="row">
                                                                    <div class="col-12 mt-2">

                                                                        <input type="hidden" name="user[deposit_type]"
                                                                            class="tradedeposittype"
                                                                            value="WALLET PAYMENT">

                                                                        <!-- Enter Amount -->
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label">
                                                                                ENTER AMOUNT:
                                                                                <small class="text-muted d-block">Please
                                                                                    enter the amount you want to
                                                                                    deposit</small>
                                                                            </label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
                                                                                    <span
                                                                                        class="input-group-text ">USD</span>
                                                                                    <input name="user[deposit]"
                                                                                        id="deposit_amount_wp"
                                                                                        type="number"
                                                                                        class="form-control fill"
                                                                                        placeholder="" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label">
                                                                                Apply Bonus Code:
                                                                            </label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
                                                                                    <select class="form-select"
                                                                                        name="user[bonus_id]" id="bonus_id">
                                                                                        <option value="">-- Select
                                                                                            Bonus --</option>

                                                                                        @forelse($bonuslist as $bonus)
                                                                                            <option
                                                                                                value="{{ $bonus->bonus_id }}">
                                                                                                {{ $bonus->bonus_name }}
                                                                                                ({{ $bonus->percentage }}%) - {{ $bonus->bonus_code }}
                                                                                            </option>
                                                                                        @empty
                                                                                            <option value=""
                                                                                                disabled>
                                                                                                No bonuses available
                                                                                            </option>
                                                                                        @endforelse
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>


                                                                        <!-- Submit -->
                                                                        <div class="row mt-3">
                                                                            <div class="col-lg-4"></div>
                                                                            <div class="col-lg-8">
                                                                                <button type="submit"
                                                                                    class="btn btn-primary wallet_payment col-12">
                                                                                    Deposit To Trade Account
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>

                                                            </form>
                                                        </div>

                                                        <div class="Now-Payment trade-deposit-details"
                                                            style="display:none">
                                                            <form method="post">
                                                                @csrf
                                                                <input type="hidden" name="user[email]"
                                                                    value="{{ session('clogin') }}" min="10"
                                                                    required class="form-control fill">
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
                                                                                    id="exampleFormControlSelect1"
                                                                                    required>
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
                                                                                    <span
                                                                                        class="input-group-text">USD</span>
                                                                                    <input name="user[deposit]"
                                                                                        id="deposit_amount_now"
                                                                                        type="number"
                                                                                        class="form-control fill"
                                                                                        placeholder="" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label">
                                                                                Apply Bonus Code:
                                                                            </label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
                                                                                    <select class="form-select"
                                                                                        name="user[bonus_id]" id="bonus_id">
                                                                                        <option value="">-- Select
                                                                                            Bonus --</option>

                                                                                        @forelse($bonuslist as $bonus)
                                                                                            <option
                                                                                                value="{{ $bonus->bonus_id }}">
                                                                                                {{ $bonus->bonus_name }}
                                                                                                ({{ $bonus->percentage }}%)  - {{ $bonus->bonus_code }}
                                                                                            </option>
                                                                                        @empty
                                                                                            <option value=""
                                                                                                disabled>
                                                                                                No bonuses available
                                                                                            </option>
                                                                                        @endforelse
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"></div>
                                                                                <div class="col-lg-8">
                                                                                    <div class="row g-1">
                                                                                        <input type="submit"
                                                                                            name="register"
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
                                                        <div class="Paytiko trade-deposit-details" style="display:none">
                                                            <form method="post">
                                                                @csrf
                                                                <input type="hidden" name="user[email]"
                                                                    value="{{ session('clogin') }}" min="10"
                                                                    required class="form-control fill">
                                                                <input class="user_trade_id" type="hidden"
                                                                    name="user[trade_id]" value=""
                                                                    class="form-control fill" readonly required>
                                                                <div class="row">
                                                                    <div class="col-12 mt-2">
                                                                        <input type="hidden" name="user[deposit_type]"
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
                                                                                    id="exampleFormControlSelect1"
                                                                                    required>
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
                                                                                    <span
                                                                                        class="input-group-text">USD</span>
                                                                                    <input name="user[deposit]"
                                                                                        id="deposit_amount_pay"
                                                                                        type="number"
                                                                                        class="form-control fill"
                                                                                        placeholder="" required>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group row">
                                                                            <label class="col-lg-4 col-form-label">
                                                                                Apply Bonus Code:
                                                                            </label>
                                                                            <div class="col-lg-8">
                                                                                <div class="input-group mb-3">
                                                                                    <select class="form-select"
                                                                                        name="user[bonus_id]" id="bonus_id">
                                                                                        <option value="">-- Select
                                                                                            Bonus --</option>

                                                                                        @forelse($bonuslist as $bonus)
                                                                                            <option
                                                                                                value="{{ $bonus->bonus_id }}">
                                                                                                {{ $bonus->bonus_name }}
                                                                                                ({{ $bonus->percentage }}%) - {{ $bonus->bonus_code }}
                                                                                            </option>
                                                                                        @empty
                                                                                            <option value=""
                                                                                                disabled>
                                                                                                No bonuses available
                                                                                            </option>
                                                                                        @endforelse
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-lg-4"></div>
                                                                                <div class="col-lg-8">
                                                                                    <div class="row g-1">
                                                                                        <input type="submit"
                                                                                            name="register"
                                                                                            class="btn btn-primary col-12 depositaccount"
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
                                                                <h3 class="text-white f-w-500">Fuel Your Trading Journey
                                                                </h3>
                                                                <span class="f-16 py-2 text-white">Deposit now and unlock
                                                                    the
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
                                                                                    class="text-muted">ACCOUNT
                                                                                    CATEGORY:</span>
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
                                                                            <h4 class="f-w-500">
                                                                                ${{ $liveaccount->Balance }}
                                                                            </h4>
                                                                            <p class="text-muted text-sm mb-2 text-end">
                                                                                Balance
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">
                                                                        ${{ !empty($totals->credit) ? number_format($totals->credit, 2) : '0.00' }}
                                                                    </h4>
                                                                </div>
                                                                <span class="text-muted">TOTAL CREDIT</span>
                                                            </li>
                                                            <li class="list-group-item">
                                                                <div class="float-end">
                                                                    <h4 class="mb-0 fw-medium">
                                                                        ${{ !empty($totals->equity) ? number_format($totals->equity, 2) : '0.00' }}
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
                                                                    <h3 class="mb-0 fw-medium">
                                                                        ${{ !empty($totals->balance) ? number_format($totals->balance, 2) : '0.00' }}
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
						@else
						<div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
							<div class="row p-3">
								<div class="card-body justify-content-center">
									<div class="text-center me-4"><a href="{{ route('create-live-account') }}"><img
												src="/assets/images/noaccfound.png" class="w-25" alt="img"></a></div>
									<h6 class="text-center text-secondary mb-3 mt-2 f-w-400 mb-0 f-16">No live account founded!
									</h6>
									<a href="{{ route('create-live-account') }}" class="d-grid w-25 m-auto">
										<button class="btn btn-primary">
											<span class="text-truncate w-100">Create new Live Account</span>
										</button>
									</a>
								</div>	
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
        $('.select-liveaccount').on('change', function() {
            $('.address-check').removeClass('selected-card'); // remove from all
            $(this).closest('.address-check').addClass('selected-card'); // highlight selected
        });
        $(document).ready(function() {


            const currencyLabel = $('.convertCurrencyText');
                const currencySelect = $('#deposit_currency_op');
                console.log(currencyLabel);
                console.log(currencySelect);
                currencySelect.on('change', function() {
                    const selectedCurrency = $('#deposit_amount_wp'); // "INR" or "USD"
                    currencyLabel.text(selectedCurrency);
                    
                
                });
            $('.select-liveaccount').on('change', function() {
                const minDeposit = $(this).data('mindep'); // get minimum deposit

                MIN_USD = Number($(this).data('mindep')) || 0;
                const depositInput1 = $('#deposit_amount_wp');
                const depositInput2 = $('#deposit_amount_now');
                const depositInput3 = $('#deposit_amount_pay');
                const depositInput4 = $('#deposit_amount_op');
                // Update both inputs
                depositInput1.attr('min', minDeposit);
                depositInput1.attr('placeholder', 'Minimum $' + minDeposit);


                // depositInput1.val(minDeposit); // optional

                depositInput2.attr('min', minDeposit);
                depositInput2.attr('placeholder', 'Minimum $' + minDeposit);
                // depositInput2.val(minDeposit); // optional

                depositInput3.attr('min', minDeposit);
                depositInput3.attr('placeholder', 'Minimum $' + minDeposit);

                depositInput4.attr('min', minDeposit);
                depositInput4.attr('placeholder', 'Minimum $' + minDeposit);

                console.log('Min deposit set to:', MIN_USD);

                 validateAmount(minDeposit);
            });



            // Trigger change for first selected account on page load
            $('.select-liveaccount:checked').trigger('change');
            // Trigger change for the first checked radio on page load
            $('.select-liveaccount:checked').trigger('change');
            $('form').on('submit', function(e) {
                const $form = $(this);

                // Check only on wallet deposit form
                if ($form.attr('id') === 'wallet_payment_form') {

                    let balance = parseFloat($('#wallet_balance').val());
                    let amount = parseFloat($('#deposit_amount_wp').val());

                    // Balance Check
                    if (amount > balance) {
                        e.preventDefault();

                        Swal.fire({
                            icon: 'error',
                            title: 'Insufficient Wallet Balance',
                            html: '<b>Wallet Balance:</b> $' + balance +
                                '<br><b>Entered Amount:</b> $' + amount,
                            confirmButtonText: 'OK'
                        });

                        return false;
                    }

                    // Minimum Deposit Check
                    if (amount < 10) {
                        e.preventDefault();

                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimum Deposit Required',
                            text: 'Minimum deposit amount is $50.',
                            confirmButtonText: 'OK'
                        });

                        return false;
                    }
                }


                e.preventDefault();


                const $btn = $form.find('button[type="submit"]');

                // Change button text + disable
                $btn.html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Please wait...'
                );
                $btn.prop('disabled', true);

                // SweetAlert - Waiting Message
                Swal.fire({
                    title: "Processing...",
                    text: "Your amount deposit process will begin shortly.",
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



            // -----------------------
            // PAYMENT STATUS CHECK
            // -----------------------
            let isSwalOpen = false;
            var intervalId = setInterval(function() {

                // console.log("checking payment status...");

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
                                    title: 'Payment Successful',
                                    text: 'Your payment has been completed!',
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());

                            } else if (response.payment_status === 'declined') {
                                clearInterval(intervalId);
                                $('#checkoutModal').modal('hide');

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Payment Declined',
                                    text: 'Your payment was declined!',
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());

                            } else if (response.payment_status === 'pending' && !isSwalOpen) {
                                isSwalOpen = true;

                                Swal.fire({
                                    title: 'Processing...',
                                    text: 'Please wait while we process your payment.',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => Swal.showLoading()
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
const depositInput = $("#deposit_amount_op");
const currencySelect = $("#deposit_currency_op");
const usdDisplay = $("#amount_deposit_usd");

// Dynamic from backend


// Example: 0.1108054 (dynamic from API)
let inrToUsdRate = 0;

// Fetch INR -> USD rate once
$.get("https://v6.exchangerate-api.com/v6/0f061f933a8359addf8aac5c/pair/INR/USD/1")
    .done(function(res) {
        if (res.result === "success") {
            inrToUsdRate = Number(res.conversion_rate);
        }
    });

// Validation function now takes minDeposit dynamically
function validateAmount(minDeposit) {
    const amount = Number($('#deposit_amount_op').val()) || 0;
    const currency = $('#deposit_currency_op').val(); // INR or USD

    const depositInput = $('#deposit_amount_op')[0];
    const usdDisplay = $('#amount_deposit_usd');

    depositInput.setCustomValidity("");

    let usdAmount = 0;

    if (currency === "USD") {
        usdAmount = amount;
    } else if (currency === "INR") {
        if (!inrToUsdRate) return;
        usdAmount = amount * inrToUsdRate;
    }

    const usdForCheck = Number(usdAmount.toFixed(2));

    if (usdDisplay.length) {
        usdDisplay.val(usdForCheck);
    }

    if (usdForCheck < minDeposit) {
        depositInput.setCustomValidity(`Minimum deposit is USD $${minDeposit}`);
    }

    depositInput.reportValidity();
}

/* ---------------- LIVE ACCOUNT CHANGE ---------------- */
$('.select-liveaccount').on('change', function() {
    const minDeposit = Number($(this).data('mindep')) || 0;
    console.log('Min deposit set to:', minDeposit);

    // Update placeholders
    $('#deposit_amount_wp, #deposit_amount_now, #deposit_amount_pay, #deposit_amount_op').attr('placeholder', 'Minimum $' + minDeposit);

    // Validate immediately
    validateAmount(minDeposit);

    // Rebind input events for dynamic minDeposit
    $('#deposit_amount_op').off("input change blur").on("input change blur", function() {
        validateAmount(minDeposit);
    });

    $('#deposit_currency_op').off("change").on("change", function() {
        validateAmount(minDeposit);
    });
});

/* ---------------- FORM SUBMIT ---------------- */
$("form").on("submit", function(e) {
    const minDeposit = Number($('.select-liveaccount').data('mindep')) || 0;
    validateAmount(minDeposit);
    if (!$('#deposit_amount_op')[0].checkValidity()) {
        e.preventDefault();
    }
});

        // $("#deposit_currency_op,#deposit_amount_op").on("change", function(e) {
        //     var fromCurrency = $("#deposit_currency_op").val();
        //     $(".convertCurrencyText").html(fromCurrency.toLocaleUpperCase());
        //     var toCurrency = "usd";
        //     var currency_amt = $("#deposit_amount_op").val();
        //     if (currency_amt != '') {
        //         var usd_amt = convertCurrency(fromCurrency, toCurrency, currency_amt)
        //     }
        // });

        // $("#deposit_currency_bank,#deposit_amount_bank").on("change", function(e) {
        //     var fromCurrency = $("#deposit_currency_bank").val();
        //     $(".convertCurrencyTextbank").html(fromCurrency.toLocaleUpperCase());
        //     var toCurrency = "usd";
        //     var currency_amt = $("#deposit_amount_bank").val();
        //     if (currency_amt != '') {
        //         var usd_amt = convertCurrency(fromCurrency, toCurrency, currency_amt)
        //     }
        // });

        // $("#ideposit_currency_bank,#ideposit_amount_bank").on("change", function(e) {
        //     var fromCurrency = $("#ideposit_currency_bank").val();
        //     $(".convertCurrencyTextibank").html(fromCurrency.toLocaleUpperCase());
        //     var toCurrency = "usd";
        //     var currency_amt = $("#ideposit_amount_bank").val();
        //     if (currency_amt != '') {
        //         var usd_amt = convertCurrency(fromCurrency, toCurrency, currency_amt)
        //     }
        // });

        // function convertCurrency(fromCurrency, toCurrency, amount) {
        //     if (fromCurrency && toCurrency && amount) {
        //         const apiKey = '0f061f933a8359addf8aac5c'; // Replace with your API key
        //         const url = `https://v6.exchangerate-api.com/v6/${apiKey}/pair/${fromCurrency}/${toCurrency}/${amount}`;
        //         $(".convertCurrency").html(toCurrency.toLocaleUpperCase());
        //         $.ajax({
        //             url: url,
        //             method: "GET",
        //             success: function(data) {
        //                 if (data.result === "success") {
        //                     console.log(data);
        //                     if (fromCurrency == 'INR') {
        //                         var inr_adj_amount = parseFloat(<?= $settings['inr_adj_amount'] ?>);
        //                         var rate = (1 / data.conversion_rate);
        //                         var rate_usd = rate + parseFloat(inr_adj_amount);
        //                         var result = parseFloat(amount / rate_usd).toFixed(2);
        //                         var adjustment = (data.conversion_result - result);
        //                         var adjustment_inr = adjustment * (1 / data.conversion_rate);
        //                         console.log(adjustment, 1 / data.conversion_rate);
        //                         $("#adjustment_inr").val(adjustment_inr);
        //                         $("#adjustment_inrbank").val(adjustment_inr);
        //                         $("#iadjustment_inrbank").val(adjustment_inr);
        //                         console.log(data.conversion_rate, data.conversion_result, rate, rate_usd,
        //                             result, adjustment, adjustment_inr);
        //                     } else {
        //                         var result = data.conversion_result;
        //                     }
        //                     $("#amount_deposit_op").val(result);
        //                     $("#amount_deposit_bank").val(result);
        //                     $("#iamount_deposit_bank").val(result);
        //                 } else {
        //                     $("#amount_deposit_op").val("0.00");
        //                     $("#amount_deposit_bank").val("0.00");
        //                     $("#iamount_deposit_bank").val("0.00");

        //                     swal.fire({
        //                         icon: "info",
        //                         title: "Something went wrong on Currency convertion.",
        //                         text: "Please try again after sometimes or Contact support."
        //                     });
        //                 }
        //             },
        //             error: function() {
        //                 // $("#result").text('Error: Something went wrong.');
        //                 swal.fire({
        //                     icon: "info",
        //                     title: "Something went wrong on Currency convertion.",
        //                     text: "Please try again after sometimes or Contact support."
        //                 });
        //             }
        //         });
        //     }
        // }
    </script>
@endsection
