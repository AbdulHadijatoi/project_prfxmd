@extends('layouts.admin')
@section('content')
    <body>
        <div id="app" data-v-app="">
            <div id="layout-wrapper">
                <div id="app" class="login-page">
                    <div
                        class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
                        <div class="bg-overlay"></div>
                        <div class="auth-page-content overflow-hidden pt-lg-5">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card overflow-hidden card-bg-fill border-0 card-border-effect-none" style="max-width:500px; margin:auto;" >
                                            <div class="row g-0">
                                                <div class="col-12">
                                                    <form method="POST" id="admin-login-otp-form">
                                                        @csrf
                                                        <div class="p-lg-5 p-4">
                                                            <div class="position-relative h-100 text-center">
																<div class="mb-4">
																	<a href="{{ url('admin/login') }}" class="d-block">
																		<img src="{{ asset($settings['admin_sidebar_logo_dark']) }}"
																			alt="" height="70">
																	</a>
																</div>
																<div>
																	<h5 class="text-primary">Welcome Back!</h5>
																	<p class="text-muted">Sign in to continue to Staff Portal.
																	</p>
																</div>
															</div>
                                                            @if (session('msg'))
                                                                <div>
                                                                    <strong
                                                                        class="text-danger">{{ session('msg') }}</strong>
                                                                </div>
                                                            @endif
                                                            <div class="mt-4">
                                                                <div class="card-body adminloginform">
                                                                    <div class="admin-credentials-step">
                                                                        <div class="form-group">
                                                                            <label for="email"
                                                                                class="form-label">Email</label>
                                                                            <input id="email" type="email"
                                                                                class="form-control" name="username"
                                                                                tabindex="1" required autofocus>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="d-block">
                                                                                <label for="password"
                                                                                    class="control-label">Password</label>
                                                                                <div class="float-right">
                                                                                    <a href="#" class="text-small"> Forgot
                                                                                        Password?</a>
                                                                                </div>
                                                                            </div>
                                                                            <input id="password" type="password"
                                                                                class="form-control" name="password"
                                                                                tabindex="2" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox" name="remember"
                                                                                    class="form-check-input me-2" tabindex="3"
                                                                                    id="remember-me">
                                                                                <label class="form-check-label"
                                                                                    for="remember-me">Remember Me</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group admin-otp-step" style="display: none;">
                                                                        <label class="form-label d-block text-center">OTP</label>
                                                                        <div class="pin-input-wrap justify-content-center mb-1">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="0" tabindex="4" aria-label="OTP digit 1">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="1" aria-label="OTP digit 2">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="2" aria-label="OTP digit 3">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="3" aria-label="OTP digit 4">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="4" aria-label="OTP digit 5">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="otp" data-idx="5" aria-label="OTP digit 6">
                                                                        </div>
                                                                        <input id="admin-otp" type="hidden" name="otp" value="">
                                                                        <span class="admin-otp-countdown text-muted small d-block mt-1 text-center"></span>
                                                                    </div>
                                                                    <div class="form-group admin-2fa-step" style="display: none;">
                                                                        <label class="form-label d-block text-center">Google Authenticator Code</label>
                                                                        <div class="pin-input-wrap justify-content-center mb-1">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="0" tabindex="5" autocomplete="one-time-code" aria-label="2FA digit 1">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="1" aria-label="2FA digit 2">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="2" aria-label="2FA digit 3">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="3" aria-label="2FA digit 4">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="4" aria-label="2FA digit 5">
                                                                            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="pin-digit form-control text-center" data-pin="2fa" data-idx="5" aria-label="2FA digit 6">
                                                                        </div>
                                                                        <input id="admin-2fa-code" type="hidden" name="code" value="">
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <input type="submit" class="btn btn-dark w-100" id="admin-login-submit"
                                                                            name="signin" value="Send OTP">
                                                                        <button type="button" class="btn btn-dark w-100 admin-resend-otp mt-2" style="display: none;">Resend OTP</button>
                                                                        <button class="btn btn-honor w-100 btn-load"
                                                                            disabled style="display: none;">
                                                                            <span class="d-flex align-items-center">
                                                                                <span class="spinner-border flex-shrink-0"
                                                                                    role="status">
                                                                                    <span class="visually-hidden">Logging
                                                                                        In...</span>
                                                                                </span>
                                                                                <span class="flex-grow-1 ms-2"> Logging
                                                                                    In...</span>
                                                                            </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
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
        <style>
            .pin-input-wrap { display: flex; flex-wrap: nowrap; gap: 0.5rem; }
            .pin-input-wrap .pin-digit {
                width: 2.75rem; height: 2.75rem; font-size: 1.25rem; font-weight: 600;
                padding: 0; border: 2px solid var(--input-border, #dce3e9); border-radius: 0.375rem;
            }
            .pin-input-wrap .pin-digit:focus { border-color: var(--bs-primary, #0d6efd); box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); outline: 0; }
            .pin-input-wrap .pin-digit:disabled { background-color: var(--bs-secondary-bg, #e9ecef); cursor: not-allowed; }
        </style>
        <script src="{{ asset('admin_assets/assets/admin_files/jquery.min.js') }}"></script>
        <script src="{{ asset('admin_assets/assets/admin_files/sweetalert-2.all.min.js') }}"></script>
        <script>
            $(function() {
                var $form = $('#admin-login-otp-form');
                var $otpStep = $('.admin-otp-step');
                var $2faStep = $('.admin-2fa-step');
                var $submitBtn = $('#admin-login-submit');
                var $countdown = $('.admin-otp-countdown');
                var $resendBtn = $('.admin-resend-otp');
                var baseUrl = '{{ url("/admin") }}';
                var otpCountdownInterval = null;

                var $otpInput = $('#admin-otp');
                var $2faInput = $('#admin-2fa-code');

                function getPinDigits(pinType) { return $('.pin-digit[data-pin="' + pinType + '"]'); }
                function syncPinToHidden(pinType) {
                    var digits = getPinDigits(pinType);
                    var val = digits.map(function() { return $(this).val(); }).get().join('');
                    if (pinType === 'otp') $otpInput.val(val); else $2faInput.val(val);
                }
                function clearPin(pinType) {
                    getPinDigits(pinType).val('');
                    syncPinToHidden(pinType);
                }
                function focusFirstPin(pinType) { getPinDigits(pinType).first().focus(); }

                $(document).on('input', '.pin-digit', function() {
                    var $el = $(this); var pin = $el.data('pin'); var idx = parseInt($el.data('idx'), 10);
                    var v = $el.val().replace(/\D/g, '').slice(0, 1); $el.val(v);
                    syncPinToHidden(pin);
                    if (pin === 'otp') updateVerifyButtonState(); else update2FaButtonState();
                    if (v && idx < 5) $el.closest('.pin-input-wrap').find('.pin-digit[data-idx="' + (idx + 1) + '"]').focus();
                });
                $(document).on('keydown', '.pin-digit', function(e) {
                    var $el = $(this); var pin = $el.data('pin'); var idx = parseInt($el.data('idx'), 10);
                    if (e.key === 'Backspace' && !$el.val() && idx > 0) {
                        e.preventDefault();
                        $el.closest('.pin-input-wrap').find('.pin-digit[data-idx="' + (idx - 1) + '"]').focus().val('');
                        syncPinToHidden(pin);
                        if (pin === 'otp') updateVerifyButtonState(); else update2FaButtonState();
                    }
                });
                $(document).on('paste', '.pin-digit', function(e) {
                    e.preventDefault();
                    var pin = $(this).data('pin');
                    var paste = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                    var $wrap = $(this).closest('.pin-input-wrap');
                    for (var i = 0; i < 6; i++) { $wrap.find('.pin-digit[data-idx="' + i + '"]').val(paste[i] || ''); }
                    syncPinToHidden(pin);
                    if (pin === 'otp') updateVerifyButtonState(); else update2FaButtonState();
                    $wrap.find('.pin-digit[data-idx="' + Math.min(paste.length, 5) + '"]').focus();
                });

                function setOtpPinDisabled(disabled) { getPinDigits('otp').prop('disabled', disabled); }
                function set2faPinDisabled(disabled) { getPinDigits('2fa').prop('disabled', disabled); }

                function startOtpCountdown() {
                    if (otpCountdownInterval) clearInterval(otpCountdownInterval);
                    $resendBtn.hide();
                    setOtpPinDisabled(false);
                    updateVerifyButtonState();
                    var seconds = 60;
                    $countdown.text('Resend OTP in ' + seconds + 's').show();
                    otpCountdownInterval = setInterval(function() {
                        seconds--;
                        if (seconds <= 0) {
                            clearInterval(otpCountdownInterval);
                            otpCountdownInterval = null;
                            $countdown.text('OTP expired');
                            $resendBtn.show();
                            $submitBtn.prop('disabled', true);
                            setOtpPinDisabled(true);
                        } else {
                            $countdown.text('Resend OTP in ' + seconds + 's');
                        }
                    }, 1000);
                }

                function updateVerifyButtonState() {
                    if ($resendBtn.is(':visible')) {
                        $submitBtn.prop('disabled', true);
                        setOtpPinDisabled(true);
                        return;
                    }
                    setOtpPinDisabled(false);
                    var otpVal = ($otpInput.val() || '').trim();
                    $submitBtn.prop('disabled', otpVal.length !== 6);
                }

                function update2FaButtonState() {
                    var codeVal = ($2faInput.val() || '').trim();
                    $submitBtn.prop('disabled', codeVal.length !== 6);
                }


                $resendBtn.on('click', function() {
                    var token = $form.find('input[name="_token"]').val();
                    $resendBtn.prop('disabled', true);
                    $.ajax({
                        url: baseUrl + '/getOtp',
                        type: 'POST',
                        data: { _token: token, action: 'getPOotp', type: 'Admin_Verification' },
                        success: function(otpData) {
                            $resendBtn.prop('disabled', false);
                            if (otpData.success) {
                                Swal.fire({ icon: 'success', title: 'New OTP sent to your email.', timer: 2000, showConfirmButton: false });
                                clearPin('otp');
                                startOtpCountdown();
                                focusFirstPin('otp');
                            } else {
                                Swal.fire({ icon: 'warning', title: otpData.message || 'Failed to send OTP' });
                            }
                        },
                        error: function(xhr) {
                            $resendBtn.prop('disabled', false);
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Please try again later.';
                            Swal.fire({ icon: 'error', title: msg });
                        }
                    });
                });

                $form.on('submit', function(e) {
                    e.preventDefault();
                    var token = $form.find('input[name="_token"]').val();
                    var isOtpStep = $otpStep.is(':visible');
                    var is2FaStep = $2faStep.is(':visible');

                    if (is2FaStep) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we verify your code.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: function() { Swal.showLoading(); }
                        });
                        $.ajax({
                            url: baseUrl + '/verify-2fa',
                            type: 'POST',
                            data: {
                                _token: token,
                                code: $2faInput.val().trim()
                            },
                            success: function(data) {
                                Swal.close();
                                if (data.success && data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    Swal.fire({ icon: 'error', title: data.message || 'Verification failed' });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired code.';
                                Swal.fire({ icon: 'error', title: msg });
                            }
                        });
                        return;
                    }

                    if (!isOtpStep) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: function() { Swal.showLoading(); }
                        });
                        $.ajax({
                            url: baseUrl + '/validate-login-otp',
                            type: 'POST',
                            data: {
                                _token: token,
                                username: $form.find('[name="username"]').val(),
                                password: $form.find('[name="password"]').val()
                            },
                            success: function(data) {
                                if (data.success) {
                                    $.ajax({
                                        url: baseUrl + '/getOtp',
                                        type: 'POST',
                                        data: {
                                            _token: token,
                                            action: 'getPOotp',
                                            type: 'Admin_Verification'
                                        },
                                        success: function(otpData) {
                                            Swal.close();
                                            if (otpData.success) {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Please check the Email and Put the OTP here',
                                                    text: 'Kindly check in SPAM/JUNK folder for second case. Otherwise try again after sometime.'
                                                }).then(function() {
                                                    $('.admin-credentials-step').hide();
                                                    $otpStep.show();
                                                    $submitBtn.val('Verify & Login').prop('disabled', true);
                                                    clearPin('otp');
                                                    setOtpPinDisabled(false);
                                                    focusFirstPin('otp');
                                                    startOtpCountdown();
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'warning',
                                                    title: otpData.message || 'Failed to send OTP'
                                                });
                                            }
                                        },
                                        error: function(xhr) {
                                            Swal.close();
                                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Please try again later or contact support.';
                                            Swal.fire({ icon: 'error', title: msg });
                                        }
                                    });
                                } else {
                                    Swal.close();
                                    Swal.fire({
                                        icon: 'error',
                                        title: data.message || 'Invalid credentials'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Login details are invalid.';
                                if (xhr.responseJSON && xhr.responseJSON.errors) {
                                    var first = Object.values(xhr.responseJSON.errors)[0];
                                    if (Array.isArray(first)) msg = first[0];
                                }
                                Swal.fire({ icon: 'error', title: msg });
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Please wait while we process your request.',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            didOpen: function() { Swal.showLoading(); }
                        });
                        $.ajax({
                            url: baseUrl + '/verify-otp',
                            type: 'POST',
                            data: {
                                _token: token,
                                otp: ($otpInput.val() || '').trim()
                            },
                            success: function(data) {
                                Swal.close();
                                if (data.success && data.require_2fa) {
                                    $otpStep.hide();
                                    $2faStep.show();
                                    $submitBtn.val('Verify 2FA & Login').prop('disabled', true);
                                    clearPin('2fa');
                                    focusFirstPin('2fa');
                                    update2FaButtonState();
                                } else if (data.success && data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    Swal.fire({ icon: 'error', title: data.message || 'Verification failed' });
                                }
                            },
                            error: function(xhr) {
                                Swal.close();
                                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired OTP.';
                                Swal.fire({ icon: 'error', title: msg });
                            }
                        });
                    }
                });
            });
        </script>
    </body>
@endsection
