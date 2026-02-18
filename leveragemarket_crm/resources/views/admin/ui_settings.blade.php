@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <!-- PAGE-HEADER -->
            <div class="page-header">
                <h1 class="page-title">UI Settings</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">UI Settings</li>
                </ol>
            </div>
            <!-- PAGE-HEADER END -->
            <!-- ROW-1 OPEN -->
            <div class="row">
                <div class="col-lg-4">
                    <form method="post" action="/admin/ui_settings" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">App Title</label>
                                    <input type="text" class="form-control" name="admin_title"
                                        value="{{ $settings['admin_title'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Icon</label>
                                    <?php if (!empty($settings['favicon'])): ?>
                                    <img src="/{{ $settings['favicon'] }}" alt="Icon" width="50" height="50"
                                        class="ml-4" style="object-fit: contain;">
                                    <?php endif; ?>
                                    <input class="form-control" type="file" name="favicon">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Logo</label>
                                    <?php if (!empty($settings['admin_sidebar_logo'])): ?>
                                    <img src="/{{ $settings['admin_sidebar_logo'] }}" alt="Logo" class="ml-4"
                                        style="object-fit: contain;max-width: 200px;height: auto;">
                                    <?php endif; ?>

                                    <input class="form-control" type="file" name="admin_sidebar_logo">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Dark Logo</label>
                                    <?php if (!empty($settings['admin_sidebar_logo_dark'])): ?>
                                    <img src="/{{ $settings['admin_sidebar_logo_dark'] }}" alt="Logo" class="ml-4"
                                        style="object-fit: contain;max-width: 200px;height: auto;">
                                    <?php endif; ?>

                                    <input class="form-control" type="file" name="admin_sidebar_logo_dark">
                                </div>

                                <div class="mb-0">
                                    <label class="form-label">Primary Color</label>
                                    <input class="form-control" type="color" name="sidebar_color"
                                        value="{{ $settings['sidebar_color'] }}">
                                </div>

                            </div>
                            <div class="card-footer text-end">
                                <input type="submit" class="btn btn-primary" value="Update" name="update">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4 col-sm-12">
                    <form method="post">
                        @csrf
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">SMTP Configuration</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Sender Name</label>
                                    <input type="text" class="form-control" name="sender_name"
                                        value="{{ $settings['sender_name'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sender Email Address</label>
                                    <input type="text" class="form-control" name="sender_email_address"
                                        value="{{ $settings['sender_email_address'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">API Key</label>
                                    <input type="text" class="form-control" name="api_key"
                                        value="{{ $settings['api_key'] }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">Partner Key</label>
                                    <input type="text" class="form-control" name="partner_key"
                                        value="{{ $settings['partner_key'] }}">
                                </div>
                            </div>
                            <div class="card-footer  text-end">
                                <input type="submit" class="btn btn-primary" value="Update" name="update">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <form method="post">
                        @csrf
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">MT5 Server Details</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">MT5 Company Name</label>
                                    <input type="text" class="form-control" name="mt5_company_name"
                                        value="{{ $settings['mt5_company_name'] }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">MT5 Server IP</label>
                                    <input type="text" class="form-control" name="mt5_server_ip"
                                        value="{{ $settings['mt5_server_ip'] }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">MT5 Server Port</label>
                                    <input type="text" class="form-control" name="mt5_server_port"
                                        value="{{ $settings['mt5_server_port'] }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">MT5 Server Web Login</label>
                                    <input type="text" class="form-control" name="mt5_server_web_login"
                                        value="{{ $settings['mt5_server_web_login'] }}" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">MT5 Server Web Password</label>
                                    <input type="text" class="form-control" name="mt5_server_web_password"
                                        value="{{ $settings['mt5_server_web_password'] }}" required>
                                </div>
                            </div>
                            <div class="card-footer  text-end">
                                <input type="submit" class="btn btn-primary" value="Update" name="update">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4 col-sm-12">
                    <form method="post">
                        @csrf
                        <div class="card custom-card">
                            <div class="card-header">
                                <div class="card-title">Platform Download</div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Windows</label>
                                    <input type="text" class="form-control" name="mt5_windows_platform"
                                        value="{{ $settings['mt5_windows_platform'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Android</label>
                                    <input type="text" class="form-control" name="mt5_android_platform"
                                        value="{{ $settings['mt5_android_platform'] }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Apple / iOS</label>
                                    <input type="text" class="form-control" name="mt5_ios_platform"
                                        value="{{ $settings['mt5_ios_platform'] }}">
                                </div>

                            </div>
                            <div class="card-footer  text-end">
                                <input type="submit" class="btn btn-primary" value="Update" name="update">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-lg-4 col-sm-12" id="two-factor">
                    <div class="card custom-card">
                        <div class="card-header">
                            <div class="card-title">Two Factor Authentication</div>
                        </div>
                        <div class="card-body">
                            <style>
                                .admin-2fa-enable-toggle .form-check-input { width: 2.5em; height: 1.25em; }
                                .admin-2fa-enable-toggle .form-check-label { font-size: 1.125rem; font-weight: 500; }
                            </style>
                            <div class="admin-2fa-enable-toggle form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="admin_2fa_enable" name="admin_2fa_enable"
                                    {{ $adminMfaEnabled ? 'checked' : '' }}>
                                <label class="form-check-label" for="admin_2fa_enable">Enable Two Factor Authentication</label>
                            </div>

                            <div class="admin-2fa-setup-wrap" style="{{ $adminMfaEnabled ? '' : 'display: none;' }}">
                                @if($adminMfaEnabled)
                                    <p class="text-muted mb-0">Two-factor authentication is enabled for your account. Uncheck the switch above to disable it.</p>
                                @elseif($adminHasMfaSecret)
                                    <div class="admin-2fa-reenable">
                                        <p class="text-muted small mb-2">You've already set up 2FA. Enter the current code from your authenticator app to turn it back on.</p>
                                        <div class="mb-2">
                                            <label for="admin-2fa-reenable-code" class="form-label">Enter 6-digit code</label>
                                            <input type="text" id="admin-2fa-reenable-code" class="form-control" placeholder="000000" maxlength="6" autocomplete="one-time-code">
                                        </div>
                                        <button type="button" class="btn btn-primary w-100" id="admin-2fa-reenable-btn">Re-enable 2FA</button>
                                    </div>
                                @else
                                    <div class="admin-2fa-setup-not-done">
                                        <div class="mb-3 text-center" id="admin-2fa-qr-container" style="display: none;">
                                            <img id="admin-2fa-qr" src="" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                        </div>
                                        <button type="button" class="btn btn-outline-primary w-100 mb-3" id="admin-2fa-generate-qr">Generate QR code</button>
                                        <p class="text-muted small mb-2">Scan the QR code with your authenticator app (e.g. Google Authenticator), then enter the 6-digit code below.</p>
                                        <div class="mb-2">
                                            <label for="admin-2fa-code" class="form-label">Enter 6-digit code</label>
                                            <input type="text" id="admin-2fa-code" class="form-control" placeholder="000000" maxlength="6" autocomplete="one-time-code">
                                        </div>
                                        <button type="button" class="btn btn-primary w-100" id="admin-2fa-verify-btn">Verify & Enable</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(function() {
    var baseUrl = '{{ url("/admin") }}';
    var $checkbox = $('#admin_2fa_enable');
    var $setupWrap = $('.admin-2fa-setup-wrap');
    var $qrContainer = $('#admin-2fa-qr-container');
    var $qrImg = $('#admin-2fa-qr');
    var $generateBtn = $('#admin-2fa-generate-qr');
    var $codeInput = $('#admin-2fa-code');
    var $verifyBtn = $('#admin-2fa-verify-btn');
    var setupWasEnabled = {{ $adminMfaEnabled ? 'true' : 'false' }};

    var $reenableCode = $('#admin-2fa-reenable-code');
    var $reenableBtn = $('#admin-2fa-reenable-btn');

    $checkbox.on('change', function() {
        if ($(this).is(':checked')) {
            $setupWrap.show();
            if (!$setupWrap.find('.admin-2fa-setup-not-done').length && !$setupWrap.find('.admin-2fa-reenable').length) return;
            $qrContainer.hide();
            $qrImg.attr('src', '');
            $codeInput.val('');
            if ($reenableCode.length) $reenableCode.val('');
        } else {
            $setupWrap.hide();
            if (setupWasEnabled) {
                $.ajax({
                    url: baseUrl + '/mfa-disable',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function() { setupWasEnabled = false; location.reload(); },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to disable 2FA.';
                        Swal.fire({ icon: 'error', title: msg });
                    }
                });
            }
        }
    });

    $generateBtn.on('click', function() {
        $generateBtn.prop('disabled', true);
        $.get(baseUrl + '/mfa-setup-qr', function(data) {
            $generateBtn.prop('disabled', false);
            if (data.qrCodeUrl) {
                $qrImg.attr('src', data.qrCodeUrl);
                $qrContainer.show();
            }
        }).fail(function() {
            $generateBtn.prop('disabled', false);
            Swal.fire({ icon: 'error', title: 'Failed to generate QR code.' });
        });
    });

    function updateVerifyBtnState() {
        $verifyBtn.prop('disabled', ($codeInput.val() || '').trim().length !== 6);
    }
    $codeInput.on('input', updateVerifyBtnState);

    $verifyBtn.on('click', function() {
        var code = ($codeInput.val() || '').trim();
        if (code.length !== 6) return;
        $verifyBtn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/mfa-authentication',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', code: code },
            success: function(data) {
                $verifyBtn.prop('disabled', false);
                if ((typeof data === 'string' && data.trim() === 'true') || (data && data.success)) {
                    Swal.fire({ icon: 'success', title: 'Two-step verification activated in your account' }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'warning', title: typeof data === 'string' ? data : (data.message || 'Verification failed') });
                }
            },
            error: function(xhr) {
                $verifyBtn.prop('disabled', false);
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired code.';
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });

    function updateReenableBtnState() {
        if ($reenableBtn.length) $reenableBtn.prop('disabled', ($reenableCode.val() || '').trim().length !== 6);
    }
    $reenableCode.on('input', updateReenableBtnState);
    if ($reenableBtn.length) $reenableBtn.prop('disabled', true);

    $reenableBtn.on('click', function() {
        var code = ($reenableCode.val() || '').trim();
        if (code.length !== 6) return;
        $reenableBtn.prop('disabled', true);
        $.ajax({
            url: baseUrl + '/mfa-reenable',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', code: code },
            success: function(data) {
                $reenableBtn.prop('disabled', false);
                if (data && data.success) {
                    Swal.fire({ icon: 'success', title: 'Two-factor authentication has been re-enabled.' }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire({ icon: 'warning', title: data.message || 'Verification failed' });
                }
            },
            error: function(xhr) {
                $reenableBtn.prop('disabled', false);
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired code.';
                Swal.fire({ icon: 'error', title: msg });
            }
        });
    });
});
</script>
@endsection
