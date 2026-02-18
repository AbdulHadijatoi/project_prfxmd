<div class="modal fade" id="depositModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="depositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-overlay d-none"
                    style="
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1000;
">
                </div>
                <form id="depositForm" method="post">
                    @csrf
                      <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
             <input type="hidden" name="deposit_to_account" value="1">
                    <input type="hidden" name="type" value="deposit">

                    <div class="modal-header">
                        <h5 class="modal-title">Deposit To Trade Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                        <div class="card-body">
                            <div class="trade-deposit-details">

                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD:</label>
                                            <div class="col-lg-8">
                                                <input name="amount" id="amount_deposit" type="number"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-3">
                                            <label class="col-lg-4 col-form-label">ADMIN REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="description" name="description" rows="3" class="form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" id="depositBtn" class="btn btn-primary">
                                                    Deposit
                                                </button>
                                            </div>
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

   
    <div class="modal fade" id="otpModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>OTP has been sent to your email. Please enter the 6-digit OTP below:</p>
                    <input type="text" id="otpInput" class="form-control text-center mb-2" maxlength="6"
                        placeholder="Enter OTP">
                    <p id="otpTimer" class="text-danger text-center">OTP expires in 60s</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" id="resendOtpBtn" class="btn btn-primary aligin-center">Resend
                            OTP</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmOtpBtn" class="btn btn-primary">Verify & Deposit</button>
                </div>
            </div>
        </div>
    </div>

<div class="modal fade" id="withdrawalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="withdrawalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-overlay d-none"
                    style="
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1000;
">
                </div>
                <form id="withdrawalForm" method="post">
                    @csrf
                      <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
             <input type="hidden" name="withdraw_from_account" value="1">
                    <input type="hidden" name="type" value="withdrawal">

                    <div class="modal-header">
                        <h5 class="modal-title">Withdrawal To Trade Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                        <div class="card-body">
                            <div class="trade-deposit-details">

                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD:</label>
                                            <div class="col-lg-8">
                                                <input name="amount_withdrawal" id="amount_withdrawal" type="number"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-3">
                                            <label class="col-lg-4 col-form-label">ADMIN REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="descriptionwithdrawal" name="descriptionwithdrawal" rows="3" class="form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" id="withdrawalBtn" class="btn btn-primary">
                                                    withdrawal
                                                </button>
                                            </div>
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

   
    <div class="modal fade" id="otpModal1" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>OTP has been sent to your email. Please enter the 6-digit OTP below:</p>
                    <input type="text" id="otpInput1" class="form-control text-center mb-2" maxlength="6"
                        placeholder="Enter OTP">
                    <p id="otpTimer" class="text-danger text-center">OTP expires in 60s</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" id="resendOtpBtn1" class="btn btn-primary aligin-center">Resend
                            OTP</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmOtpBtn1" class="btn btn-primary">Verify & Withdrawal</button>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="bonusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="bonustModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-overlay d-none"
                    style="
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(255, 255, 255, 0.6);
    z-index: 1000;
">
                </div>
                <form id="bonusForm" method="post">
                    @csrf
                      <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
             <input type="hidden" name="bonus_to_account" value="1">
                    <input type="hidden" name="type" value="bonus">

                    <div class="modal-header">
                        <h5 class="modal-title">Bonus To Trade Account</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                        <div class="card-body">
                            <div class="trade-deposit-details">

                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD:</label>
                                            <div class="col-lg-8">
                                                <input name="bonus" id="amount_bonus" type="number"
                                                    class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="form-group row mt-3">
                                            <label class="col-lg-4 col-form-label">ADMIN REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="descriptionbonus" name="descriptionbonus" rows="3" class="form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-lg-4"></div>
                                            <div class="col-lg-8">
                                                <button type="button" id="bonusBtn" class="btn btn-primary">
                                                    Bonus
                                                </button>
                                            </div>
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

   
    <div class="modal fade" id="otpModal2" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>OTP has been sent to your email. Please enter the 6-digit OTP below:</p>
                    <input type="text" id="otpInput2" class="form-control text-center mb-2" maxlength="6"
                        placeholder="Enter OTP">
                    <p id="otpTimer" class="text-danger text-center">OTP expires in 60s</p>
                    <div class="d-flex align-items-center justify-content-center">
                        <button type="button" id="resendOtpBtn2" class="btn btn-primary aligin-center">Resend
                            OTP</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmOtpBtn2" class="btn btn-primary">Verify & Deposit</button>
                </div>
            </div>
        </div>
    </div>



{{-- <div class="modal fade" id="depositModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="depositModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <form  id="depositForm" method="post">
                @csrf
                <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
             <input type="hidden" name="deposit_to_account" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="depositModalLabel">Deposit To Trade Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                    <div class="card-body">
                        <div class="trade-deposit-details">
                            
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row"><label class="col-lg-4 col-form-label">AMOUNT IN USD
                                                :<small class="text-muted d-block"> Deposit
                                                    amount in USD </small></label>
                                            <div class="col-lg-8">
                                                <div class="input-group mb-3"><span
                                                        class="input-group-text">USD</span><input name="amount"
                                                        id="amount_deposit" type="text"
                                                        class="form-control fill tradedeposit_amount" required><!---->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3"><label class="col-lg-4 col-form-label">ADMIN
                                                REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="description" name="description" rows="3" class="mt-2 form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="row">
                                                <div class="col-lg-4"></div>
                                                <div class="col-lg-8">
                                                    <div class="row g-1"><input type="submit" name="deposit_to_account"
                                                            class="btn btn-primary col-12"  id="depositBtn"
                                                            value="Deposit To Trade Account"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}
{{-- <div class="modal fade" id="withdrawalModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="withdrawalModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <form action="{{route('admin.withdrawFromAccount')}}" id="withdrawalForm" method="post">
                @csrf
                <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawalModalLabel">Withdraw From Trade Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                    <div class="card-body">
                        <div class="trade-deposit-details">
                            <form method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row"><label class="col-lg-4 col-form-label">AMOUNT IN USD
                                                :<small class="text-muted d-block"> Withdrawal
                                                    amount in USD </small></label>
                                            <div class="col-lg-8">
                                                <div class="input-group mb-3"><span
                                                        class="input-group-text">USD</span><input name="amount"
                                                        id="amount_deposit" type="text"
                                                        class="form-control fill tradedeposit_amount" required><!---->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3"><label class="col-lg-4 col-form-label">ADMIN
                                                REMARK:</label>
                                            <div class="col-lg-8">
                                                <textarea id="description" name="description" rows="3" class="mt-2 form-control" placeholder="Add a remark"></textarea>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="row">
                                                <div class="col-lg-4"></div>
                                                <div class="col-lg-8">
                                                    <div class="row g-1"><input type="submit"
                                                            name="withdraw_from_account"
                                                            class="btn btn-primary col-12"
                                                            value="Withdraw From Trade Account"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}
{{-- <div class="modal fade" id="bonusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="bonusModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered">
        <div class="modal-content">
            <form  id="bonusForm" method="post">
                @csrf
                <input type="hidden" name="client_id" id="client_id" value="<?= md5($id) ?>">
                <input type="hidden" name="trade_id" id="trade_id" value="<?= $getUser->trade_id??'' ?>">
                <input type="hidden" name="email" id="email" value="<?= $getUser->email??'' ?>">
                 <input type="hidden" name="bonus_to_account" id="email" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="bonusModalLabel">Bonus To Trade Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body custom-card card mb-0" style="max-height: 400px;overflow-y: auto;">
                    <div class="card-body">
                        <div class="trade-deposit-details">
                           
                                <div class="row">
                                    <div class="col-12 mt-2">
                                        <div class="form-group row mb-3">
                                            <label class="col-lg-4 col-form-label">BONUS TYPE:</label>
                                            <div class="col-lg-8">
                                                <select name="type" id="input" class="form-control"
                                                    required="required">
                                                    <option value="in">Bonus In</option>
                                                    <option value="out">Bonus Out</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-lg-4 col-form-label">AMOUNT IN USD
                                                :<small class="text-muted d-block"> Bonus
                                                    amount in USD </small></label>
                                            <div class="col-lg-8">
                                                <div class="input-group mb-3"><span
                                                        class="input-group-text">USD</span><input name="amount"
                                                        id="amount_deposit" type="text"
                                                        class="form-control fill tradedeposit_amount" required><!---->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-3"><label class="col-lg-4 col-form-label">ADMIN
                                                REMARK:</label>
                                            <div class="col-lg-8"><input id="description" name="description"
                                                    rows="3" class="mt-2 form-control"
                                                    placeholder="Add a remark" required>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="row">
                                                <div class="col-lg-4"></div>
                                                <div class="col-lg-8">
                                                    <div class="row g-1"><input type="submit"
                                                            name="bonus_to_account" class="btn btn-primary col-12" id="bonusBtn"
                                                            value="Bonus To Trade Account"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> --}}

{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
<script>
    $('#otpModal').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).ready(function () {

    const depositModal = $('#depositModal');
    const otpModal = $('#otpModal');
    let otpTimerInterval;

    function startOtpTimer(seconds = 60) {
        clearInterval(otpTimerInterval);
        let remaining = seconds;

        $('#otpTimer').text(`OTP expires in ${remaining}s`);
        $('#resendOtpBtn').addClass('d-none');
        $('#confirmOtpBtn').prop('disabled', false);

        otpTimerInterval = setInterval(() => {
            remaining--;
            $('#otpTimer').text(`OTP expires in ${remaining}s`);

            if (remaining <= 0) {
                clearInterval(otpTimerInterval);
                $('#otpTimer').text('OTP expired');
                $('#confirmOtpBtn').prop('disabled', true);
                $('#resendOtpBtn')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .text('Resend OTP');
            }
        }, 1000);
    }

    // STEP 1 — SEND OTP
    $('#depositBtn').on('click', function (e) {
        e.preventDefault();

        let amount = $('#amount_deposit').val().trim();
        let description = $('#description').val().trim();

        if (!amount) {
            Swal.fire('Error', 'Please enter Amount in USD', 'error');
            return;
        }

        if (!description) {
            Swal.fire('Error', 'Please enter Admin Remark', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        depositModal.modal('hide');

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "Deposit",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    otpModal.modal('show');
                    $('#otpInput').val('').focus();
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                    btn.prop('disabled', false).text('Deposit');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to send OTP', 'error');
                btn.prop('disabled', false).text('Deposit');
            }
        });
    });

    // STEP 2 — VERIFY OTP
    $('#confirmOtpBtn').on('click', function () {

        let otp = $('#otpInput').val().trim();

        if (otp.length !== 6) {
            Swal.fire('Error', 'Enter valid 6-digit OTP', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...'
        );

        let formData = $('#depositForm').serialize() + '&otp=' + otp;

        $.ajax({
            url: "{{ route('admin.depositToAccount') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                clearInterval(otpTimerInterval);
                otpModal.modal('hide');

                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Invalid OTP',
                    'error'
                );

                btn.prop('disabled', false).text('Verify & Deposit');
            }
        });
    });

    // STEP 3 — RESEND OTP
    $('#resendOtpBtn').on('click', function () {

        let btn = $(this);

        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        $('#otpInput').val('').focus();
        $('#confirmOtpBtn').prop('disabled', false);

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "Deposit",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire('Success', 'New OTP sent to your email', 'success');
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to resend OTP', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Resend OTP');
            }
        });
    });

});


 $('#otpModal1').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).ready(function () {

    const withdrawalModal = $('#withdrawalModal');
    const otpModal = $('#otpModal1');
    let otpTimerInterval;

    function startOtpTimer(seconds = 60) {
        clearInterval(otpTimerInterval);
        let remaining = seconds;

        $('#otpTimer').text(`OTP expires in ${remaining}s`);
        $('#resendOtpBtn1').addClass('d-none');
        $('#confirmOtpBtn1').prop('disabled', false);

        otpTimerInterval = setInterval(() => {
            remaining--;
            $('#otpTimer').text(`OTP expires in ${remaining}s`);

            if (remaining <= 0) {
                clearInterval(otpTimerInterval);
                $('#otpTimer').text('OTP expired');
                $('#confirmOtpBtn').prop('disabled', true);
                $('#resendOtpBtn')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .text('Resend OTP');
            }
        }, 1000);
    }

    // STEP 1 — SEND OTP
    $('#withdrawalBtn').on('click', function (e) {
        e.preventDefault();

        let amount = $('#amount_withdrawal').val().trim();
        let description = $('#descriptionwithdrawal').val().trim();

        if (!amount) {
            Swal.fire('Error', 'Please enter Amount in USD', 'error');
            return;
        }

        if (!description) {
            Swal.fire('Error', 'Please enter Admin Remark', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        withdrawalModal.modal('hide');

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "Deposit",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    otpModal.modal('show');
                    $('#otpInput').val('').focus();
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                    btn.prop('disabled', false).text('Withdraw');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to send OTP', 'error');
                btn.prop('disabled', false).text('Withdraw');
            }
        });
    });

    // STEP 2 — VERIFY OTP
    $('#confirmOtpBtn1').on('click', function () {

        let otp = $('#otpInput1').val().trim();

        if (otp.length !== 6) {
            Swal.fire('Error', 'Enter valid 6-digit OTP', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...'
        );

        let formData = $('#withdrawalForm').serialize() + '&otp=' + otp;

        $.ajax({
            url: "{{ route('admin.withdrawFromAccount') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                clearInterval(otpTimerInterval);
                otpModal.modal('hide');

                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Invalid OTP',
                    'error'
                );

                btn.prop('disabled', false).text('Verify & Withdrawal');
            }
        });
    });

    // STEP 3 — RESEND OTP
    $('#resendOtpBtn1').on('click', function () {

        let btn = $(this);

        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        $('#otpInput1').val('').focus();
        $('#confirmOtpBtn1').prop('disabled', false);

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "Deposit",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire('Success', 'New OTP sent to your email', 'success');
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to resend OTP', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Resend OTP');
            }
        });
    });

});



 $('#otpModal2').on('hidden.bs.modal', function () {
    location.reload();
});

$(document).ready(function () {

    const bonusModal = $('#bonusModal');
    const otpModal = $('#otpModal2');
    let otpTimerInterval;

    function startOtpTimer(seconds = 60) {
        clearInterval(otpTimerInterval);
        let remaining = seconds;

        $('#otpTimer').text(`OTP expires in ${remaining}s`);
        $('#resendOtpBtn2').addClass('d-none');
        $('#confirmOtpBtn2').prop('disabled', false);

        otpTimerInterval = setInterval(() => {
            remaining--;
            $('#otpTimer').text(`OTP expires in ${remaining}s`);

            if (remaining <= 0) {
                clearInterval(otpTimerInterval);
                $('#otpTimer').text('OTP expired');
                $('#confirmOtpBtn').prop('disabled', true);
                $('#resendOtpBtn')
                    .removeClass('d-none')
                    .prop('disabled', false)
                    .text('Resend OTP');
            }
        }, 1000);
    }

    // STEP 1 — SEND OTP
    $('#bonusBtn').on('click', function (e) {
        e.preventDefault();

        let amount = $('#amount_bonus').val().trim();
        let description = $('#descriptionbonus').val().trim();

        if (!amount) {
            Swal.fire('Error', 'Please enter Amount in USD', 'error');
            return;
        }

        if (!description) {
            Swal.fire('Error', 'Please enter Admin Remark', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        bonusModal.modal('hide');

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "bonus",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    otpModal.modal('show');
                    $('#otpInput2').val('').focus();
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                    btn.prop('disabled', false).text('Bonus');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to send OTP', 'error');
                btn.prop('disabled', false).text('Bonus');
            }
        });
    });

    // STEP 2 — VERIFY OTP
    $('#confirmOtpBtn2').on('click', function () {

        let otp = $('#otpInput2').val().trim();

        if (otp.length !== 6) {
            Swal.fire('Error', 'Enter valid 6-digit OTP', 'error');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Verifying...'
        );

        let formData = $('#bonusForm').serialize() + '&otp=' + otp;

        $.ajax({
            url: "{{ route('admin.bonusToAccount') }}",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {

                clearInterval(otpTimerInterval);
                otpModal.modal('hide');

                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function (xhr) {
                Swal.fire(
                    'Error',
                    xhr.responseJSON?.message || 'Invalid OTP',
                    'error'
                );

                btn.prop('disabled', false).text('Verify & Bonus');
            }
        });
    });

    // STEP 3 — RESEND OTP
    $('#resendOtpBtn2').on('click', function () {

        let btn = $(this);

        btn.prop('disabled', true).html(
            '<span class="spinner-border spinner-border-sm me-2"></span> Sending OTP...'
        );

        $('#otpInput2').val('').focus();
        $('#confirmOtpBtn2').prop('disabled', false);

        $.ajax({
            url: "{{ route('admin.admingetOtp') }}",
            type: "POST",
            data: {
                action: "getPOotp",
                type: "bonus",
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if (res.success) {
                    Swal.fire('Success', 'New OTP sent to your email', 'success');
                    startOtpTimer(60);
                } else {
                    Swal.fire('Warning', res.message || 'OTP failed', 'warning');
                }
            },
            error: function () {
                Swal.fire('Error', 'Failed to resend OTP', 'error');
            },
            complete: function () {
                btn.prop('disabled', false).text('Resend OTP');
            }
        });
    });

});
// $('#depositBtn').on('click', function () {

//     let btn = $(this);
//     let form = $('#depositForm');

//     // Disable button + show processing
//     btn.prop('disabled', true);
//     btn.value = 'Processing...';

//     // Optional: add spinner
//     btn.innerHTML = 'Processing...';



//     $.ajax({
//         url: "{{ route('admin.depositToAccount') }}", // your route
//         type: "POST",
//         data: form.serialize(),
//         headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
//         success: function(response) {

//             // SweetAlert success
//             Swal.fire({
//                 icon: 'success',
//                 title: 'Success!',
//                 text: response.message || 'Trade Deposit Successful',
//                 confirmButtonText: 'OK'
//             }).then((result) => {
//                 if(result.isConfirmed){
//                     // Close the Bootstrap modal
//                     $('#depositModal').modal('hide');
//                     // Optional: reload page or refresh table
//                     location.reload();
//                 }
//             });

//         },
//         error: function(xhr) {
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error!',
//                 text: xhr.responseJSON?.message || 'Deposit failed. Try again.',
//                 confirmButtonText: 'OK'
//             });

//             // Re-enable button so user can retry
//             btn.prop('disabled', false);
//             btn.text('Deposit To Trade Account');
//         }
//     });
// });

// $('#bonusBtn').on('click', function () {
//     let btn = $(this);
//     let form = $('#bonusForm');

//     // Disable button + show processing
//     btn.prop('disabled', true);
//     btn.find('.btn-text').text('Processing...');
//     btn.find('.spinner-border').removeClass('d-none');

//     $.ajax({
//         url: "{{ route('admin.bonusToAccount') }}",
//         type: "POST",
//         data: form.serialize(),
//         headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
//         success: function(response) {

//             Swal.fire({
//                 icon: 'success',
//                 title: 'Success!',
//                 text: response.message || 'Trade Bonus Successful',
//                 confirmButtonText: 'OK'
//             }).then(() => {
//                 // Close modal and reload page
//                 $('#bonusModal').modal('hide');
//                 location.reload();
//             });

//         },
//         error: function(xhr) {
//             Swal.fire({
//                 icon: 'error',
//                 title: 'Error!',
//                 text: xhr.responseJSON?.message || 'Bonus operation failed.',
//                 confirmButtonText: 'OK'
//             });

//             // Re-enable button
//             btn.prop('disabled', false);
//             btn.find('.btn-text').text('Bonus To Trade Account');
//             btn.find('.spinner-border').addClass('d-none');
//         }
//     });
// });
</script>
