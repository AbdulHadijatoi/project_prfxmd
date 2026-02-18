@extends('layouts.admin.admin')
@section('styles')
    <style>
        .modal-body::-webkit-scrollbar {
            width: 15px;
        }
    </style>
@endsection
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="modal fade" id="addUserGrpModal" tabindex="-1" aria-labelledby="addUserGrpModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="addUserGrpModalLabel">Add User Group</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.saveUserGroup') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row gy-4">
                                    <div class="col-lg-8 col-12">
                                        <label for="group_name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="group_name" required>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <label for="group_code" class="form-label">Code</label>
                                        <input type="text" class="form-control" name="group_code" pattern="[A-Z0-9]+"
                                            minlength="4"
                                            title="Only uppercase letters (A-Z) and numbers (0-9) are allowed." required>
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label for="username" class="form-label">BankWire</label>
                                        <textarea class="form-control bankwire" name="bankwire" id="bankwire" rows="3"></textarea>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch" id="switch-sm"
                                                name="bankwire_status">
                                            <label class="form-check-label" for="switch-sm">Enable</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Now Payments</label>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="now_payment_api" class="form-label">API Key</label>
                                                <input type="text" class="form-control" name="now_payment_api">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="now_payment_security" class="form-label">Security Key</label>
                                                <input type="text" class="form-control" name="now_payment_security">
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="now_payment_status" name="now_payment_status">
                                                    <label class="form-check-label" for="now_payment_status">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Match2Pay</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label for="crypto_payment_api" class="form-label">API Token</label>
                                                <input type="text" class="form-control" name="crypto_payment_api">
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="crypto_payment_security" class="form-label">API Secret</label>
                                                <input type="text" class="form-control" name="crypto_payment_security">
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="crypto_payment_status" name="crypto_payment_status">
                                                    <label class="form-check-label" for="crypto_payment_status">Deposit
                                                        Enable</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="crypto_payment_withdraw" name="crypto_payment_withdraw">
                                                    <label class="form-check-label"
                                                        for="crypto_payment_withdraw">Withdrawal
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">USDT Deposit</label>
                                        <div class="row">
                                            <div class="col-lg-4 col-12">
                                                <label for="usdt_wallet_qr" class="form-label">Wallet QR</label>
                                                <input type="file" accept="image/png,image/jpeg" class="form-control"
                                                    name="usdt_wallet_qr">
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <label for="usdt_wallet_id" class="form-label">Wallet ID</label>
                                                <input type="text" class="form-control" name="usdt_wallet_id">
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="usdt_status" name="usdt_status">
                                                    <label class="form-check-label" for="usdt_status">
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">PayIssa</label>
                                        <div class="row">
                                            <div class="col-lg-6 col-12">
                                                <label for="payissa_wallet" class="form-label">Wallet Address</label>
                                                <input type="text" class="form-control" name="payissa_wallet">
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="payissa_status">
                                                    <label class="form-check-label" for="payissa_status">
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Bank Deposit</label>
                                        <div class="row">
                                            <div class="col-lg-6 col-12">
                                                <label for="bank_account_details" class="form-label">Account
                                                    Details</label>
                                                <textarea class="form-control bankaccount" name="bank_account_details"  rows="3"></textarea>
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="bank_deposit_status">
                                                    <label class="form-check-label" for="bank_deposit_status">
                                                        Enable Bank Deposit</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Agent Account</label>
                                        <select name="agent_account" id="agent_account"
                                            class="form-control agent-account">
                                            <option value="">Select</option>
                                            @foreach ($live_accounts as $account)
                                                <option value="{{ $account->trade_id }}">
                                                    {{ $account->trade_id }} ({{ $account->name }}-{{ $account->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="agent_status" name="agent_status">
                                            <label class="form-check-label" for="agent_status">Agent Withdrawal</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="input-label" class="form-label">Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="switch-sm" name="status">
                                            <label class="form-check-label" for="switch-sm">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="input-label" class="form-label">Is Visible</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="switch-sm" name="is_visible">
                                            <label class="form-check-label" for="switch-sm">Visible</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="btn btn-primary" value="Add">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="updateUserGrpModal" aria-labelledby="updateUserGroupModalLabel">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="updateUserGroupModalLabel">Update User Group</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.saveUserGroup') }}" id="update_usergrp_form"
                            enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_group_id" id="user_group_id">
                            <div class="modal-body">
                                <div class="row gy-4">
                                    <div class="col-lg-8 col-12">
                                        <label for="username" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="group_name" id="group_name"
                                            required>
                                    </div>
                                    <div class="col-lg-4 col-12">
                                        <label for="group_code" class="form-label">Code</label>
                                        <input type="text" class="form-control" name="group_code" id="group_code"
                                            pattern="[A-Z0-9]+" minlength="4"
                                            title="Only uppercase letters (A-Z) and numbers (0-9) are allowed." required>
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label for="bankwire" class="form-label">BankWire</label>
                                        <textarea class="form-control bankwire-edit" name="bankwire" id="bankwire" rows="3"></textarea>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="bankwire_status" name="bankwire_status">
                                            <label class="form-check-label" for="switch-sm">Enable</label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="agent_account" class="form-label">Agent Account</label>
                                        <select name="agent_account" id="agent_account"
                                            class="form-control agent-account-edit">
                                            <option value="">Select</option>
                                            @foreach ($live_accounts as $account)
                                                <option value="{{ $account->trade_id }}">
                                                    {{ $account->trade_id }}({{ $account->name }}-{{ $account->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-check form-switch mt-1">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="agent_status" name="agent_status">
                                            <label class="form-check-label" for="agent_status">Agent
                                                Withdrawal</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label class="form-label">Now Payments</label>
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <label for="now_payment_api" class="form-label">API Key</label>
                                                <input type="text" class="form-control" name="now_payment_api"
                                                    id="now_payment_api">
                                            </div>
                                            <div class="col-lg-4">
                                                <label for="now_payment_security" class="form-label">Security Key</label>
                                                <input type="text" class="form-control" name="now_payment_security"
                                                    id="now_payment_security">
                                            </div>
                                            <div class="col-lg-4">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="now_payment_status" name="now_payment_status">
                                                    <label class="form-check-label"
                                                        for="now_payment_status">Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Match2Pay</label>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <label for="crypto_payment_api" class="form-label">API Token</label>
                                                <input type="text" class="form-control" name="crypto_payment_api"
                                                    id="crypto_payment_api">
                                            </div>
                                            <div class="col-lg-6">
                                                <label for="crypto_payment_security" class="form-label">API Secret</label>
                                                <input type="text" class="form-control" name="crypto_payment_security"
                                                    id="crypto_payment_security">
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="crypto_payment_status" name="crypto_payment_status">
                                                    <label class="form-check-label" for="crypto_payment_status">Deposit
                                                        Enable</label>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="crypto_payment_withdraw" name="crypto_payment_withdraw">
                                                    <label class="form-check-label"
                                                        for="crypto_payment_withdraw">Withdrawal
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">USDT Deposit</label>
                                        <div class="row">
                                            <div class="col-lg-4 col-12">
                                                <label for="usdt_wallet_qr" class="form-label">Wallet QR</label>
                                                <input type="file" accept="image/png,image/jpeg" class="form-control"
                                                    name="usdt_wallet_qr">
                                                <div class="row mt-1">
                                                    <a class="text-info text-decoration-underline" id="usdt-wallet-qr"
                                                        href="#" target="_blank">Wallet QR
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <label for="usdt_wallet_id" class="form-label">Wallet ID</label>
                                                <input type="text" class="form-control" name="usdt_wallet_id"
                                                    id="usdt_wallet_id">
                                            </div>
                                            <div class="col-lg-4 col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="usdt_status" name="usdt_status">
                                                    <label class="form-check-label" for="usdt_status">
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">PayIssa</label>
                                        <div class="row">
                                            <div class="col-lg-6 col-12">
                                                <label for="payissa_wallet" class="form-label">Wallet Address</label>
                                                <input type="text" class="form-control" name="payissa_wallet"
                                                    id="payissa_wallet">
                                            </div>
                                            <div class="col-lg-6 col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        id="payissa_status" name="payissa_status">
                                                    <label class="form-check-label" for="payissa_status">
                                                        Enable</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <label for="username" class="form-label">Bank Deposit</label>
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="bank_account_details" class="form-label">Account
                                                    Details</label>
                                                <textarea class="form-control bankaccount-edit" name="bank_account_details"  rows="3"></textarea>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check form-switch mt-4">
                                                    <input class="form-check-input" type="checkbox" role="switch"
                                                        name="bank_deposit_status" id="bank_deposit_status">
                                                    <label class="form-check-label" for="bank_deposit_status">
                                                        Enable Bank Deposit</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="input-label" class="form-label">Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="status" id="status">
                                            <label class="form-check-label" for="switch-sm">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="input-label" class="form-label">Is Visible</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="is_visible" id="is_visible">
                                            <label class="form-check-label" for="switch-sm">Is Visible</label>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                    <label for="international_bank_details" class="form-label">International Bank Details</label>
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="international_bank_account_details" class="form-label">Account Details</label>
                                            <textarea class="form-control ibankaccount-edit" name="international_bank_account_details" rows="3"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check form-switch mt-4">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                                    name="international_bank_status" id="international_bank_status">
                                                <label class="form-check-label" for="international_bank_status">
                                                    Enable International Bank</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <div class="col-6">
                                        <label for="input-label" class="form-label">Status</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="status" id="status">
                                            <label class="form-check-label" for="switch-sm">Active</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label for="input-label" class="form-label">Is Visible</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                name="is_visible" id="is_visible">
                                            <label class="form-check-label" for="switch-sm">Is Visible</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <?php if (session('userData.role_id')== 1) { ?>
                                <input type="submit" class="btn btn-primary" name="update_user" value="Update">
                                <?php } ?>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="actionOtpModal" tabindex="-1" aria-labelledby="actionOtpModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title" id="actionOtpModalLabel">Verify OTP</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-2">An OTP has been sent to your email. Enter it below.</p>
                            <div class="mb-2">
                                <label for="action_otp_input" class="form-label">OTP</label>
                                <input type="text" id="action_otp_input" class="form-control" placeholder="Enter 6-digit OTP" maxlength="6" autocomplete="off">
                            </div>
                            <span class="action-otp-countdown text-muted small d-block mb-2"></span>
                            <button type="button" class="btn btn-outline-secondary btn-sm action-otp-resend d-none">Resend OTP</button>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="action_otp_verify_btn" disabled>Verify</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-header">
                <h1 class="page-title">User Groups</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User Groups</li>
                </ol>
            </div>
            <div class="d-flex justify-content-end mb-3">
                <button type="button" class="btn btn-primary" id="btnAddUserGroup">
                    Add New User Group
                </button>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tableUserGrps" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
    <script>
        var editor1cfg = {}
        editor1cfg.toolbar = "basic";
        var bankwire = new RichTextEditor(".bankwire", editor1cfg);
        var bankwire_edit = new RichTextEditor(".bankwire-edit", editor1cfg);
        var bankaccount = new RichTextEditor(".bankaccount", editor1cfg);
        var bankaccount_edit = new RichTextEditor(".bankaccount-edit", editor1cfg);
        var ibankaccount_edit = new RichTextEditor(".ibankaccount-edit", editor1cfg);

        
        const copyright_site_name_text = @json(settings()['copyright_site_name_text'] ?? '');
        // $(document).on("change", ".role_id", function() {
        //     let id = $(this).val();
        //     $('.mt5-group-id').val([]).trigger('change');
        //     $('.user-group-id').val([]).trigger('change');
        //     if (id == 8) {
        //         $('.mt5-group-id').prop('disabled', false);
        //         $('.form-rm-list').addClass('d-none');
        //     } else if (id == 9) {
        //         $('.mt5-group-id').prop('disabled', true);
        //         $('.form-rm-list').removeClass('d-none');
        //     } else {
        //         $('.user-group-id').prop('disabled', false);
        //         $('.form-rm-list').addClass('d-none');
        //         $('.mt5-group-id').prop('disabled', true);
        //     }
        // });
        $('#addUserGrpModal').on('shown.bs.modal', function() {
            $('.agent-account').select2({
                dropdownParent: $('#addUserGrpModal')
            });
        });
        $('#updateUserGrpModal').on('shown.bs.modal', function() {
            $('.agent-account-edit').select2({
                dropdownParent: $('#updateUserGrpModal')
            });
        });

        var actionOtpContext = '';
        var pendingEditUserGroupId = null;
        var actionOtpCountdownInterval = null;
        var actionOtpModalEl = document.getElementById('actionOtpModal');

        function actionOtpRequest(module, action, onSuccess) {
            var token = $('input[name="_token"]').first().val() || $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: '{{ url("/admin/action-otp/request") }}',
                type: 'POST',
                data: { _token: token, module: module, action: action },
                success: function(res) {
                    if (res.success) {
                        if (onSuccess) onSuccess();
                    } else {
                        if (typeof swal !== 'undefined') swal.fire({ icon: 'error', title: res.message || 'Failed to send OTP' });
                    }
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Please try again later.';
                    if (typeof swal !== 'undefined') swal.fire({ icon: 'error', title: msg });
                }
            });
        }

        function actionOtpStartCountdown() {
            if (actionOtpCountdownInterval) clearInterval(actionOtpCountdownInterval);
            var $countdown = $('.action-otp-countdown');
            var $resend = $('.action-otp-resend');
            var $input = $('#action_otp_input');
            var $verify = $('#action_otp_verify_btn');
            $resend.addClass('d-none');
            $input.prop('disabled', false);
            var seconds = 60;
            $countdown.text('Resend OTP in ' + seconds + 's').removeClass('d-none');
            actionOtpCountdownInterval = setInterval(function() {
                seconds--;
                if (seconds <= 0) {
                    clearInterval(actionOtpCountdownInterval);
                    actionOtpCountdownInterval = null;
                    $countdown.text('OTP expired');
                    $resend.removeClass('d-none');
                    $verify.prop('disabled', true);
                    $input.prop('disabled', true);
                } else {
                    $countdown.text('Resend OTP in ' + seconds + 's');
                }
            }, 1000);
            var otpVal = ($input.val() || '').trim();
            $verify.prop('disabled', otpVal.length !== 6);
        }

        function actionOtpUpdateVerifyState() {
            var $resend = $('.action-otp-resend');
            var $input = $('#action_otp_input');
            var $verify = $('#action_otp_verify_btn');
            if (!$resend.hasClass('d-none')) {
                $verify.prop('disabled', true);
                $input.prop('disabled', true);
                return;
            }
            $input.prop('disabled', false);
            var otpVal = ($input.val() || '').trim();
            $verify.prop('disabled', otpVal.length !== 6);
        }

        $(document).ready(function() {
            var otpContext = '';
            var pendingEditAfterOtpClose = null;
            var pendingAddAfterOtpClose = false;
            $('#actionOtpModal').on('shown.bs.modal', function() {
                otpContext = $('#actionOtpModal').data('otp-context') || '';
                var action = (otpContext === 'update') ? 'update' : 'view';
                $('#action_otp_input').val('').prop('disabled', false);
                $('#action_otp_verify_btn').prop('disabled', true);
                $('.action-otp-resend').addClass('d-none');
                actionOtpRequest('usergroup', action, function() {
                    actionOtpStartCountdown();
                });
            });
            function actionOtpCleanupBackdrop() {
                $('body').removeClass('modal-open').css({ 'padding-right': '', 'overflow': '' });
                $('.modal-backdrop').remove();
            }
            $('#actionOtpModal').on('hidden.bs.modal', function() {
                if (actionOtpCountdownInterval) clearInterval(actionOtpCountdownInterval);
                actionOtpCountdownInterval = null;
                actionOtpCleanupBackdrop();
                if (pendingAddAfterOtpClose) {
                    pendingAddAfterOtpClose = false;
                    $('#addUserGrpModal').modal('show');
                } else if (pendingEditAfterOtpClose != null) {
                    var id = pendingEditAfterOtpClose;
                    pendingEditAfterOtpClose = null;
                    $.ajax({
                        url: "/admin/ajax",
                        type: "GET",
                        data: { action: 'getUserGroupDetails', id: id },
                        success: function(response) {
                            response = JSON.parse(response.trim());
                            $.each(response, function(key, value) {
                                if (key == "bankwire" && value != null) bankwire_edit.setHTMLCode(value);
                                if (key == "bank_account_details" && value != null) bankaccount_edit.setHTMLCode(value);
                                if (key == "international_bank_account_details" && value != null) ibankaccount_edit.setHTMLCode(value);
                                $('#update_usergrp_form #' + key).val(value);
                            });
                            $('#update_usergrp_form #status').prop('checked', response.status == 1);
                            $('#update_usergrp_form #is_visible').prop('checked', response.is_visible == 1);
                            $('#update_usergrp_form #bankwire_status').prop('checked', response.bankwire_status == 1);
                            $('#update_usergrp_form #agent_status').prop('checked', response.agent_status == 1);
                            $('#update_usergrp_form #now_payment_status').prop('checked', response.now_payment_status == 1);
                            $('#update_usergrp_form #crypto_payment_status').prop('checked', response.crypto_payment_status == 1);
                            $('#update_usergrp_form #crypto_payment_withdraw').prop('checked', response.crypto_payment_withdraw == 1);
                            $('#update_usergrp_form #usdt_status').prop('checked', response.usdt_status == 1);
                            $('#update_usergrp_form #payissa_status').prop('checked', response.payissa_status == 1);
                            $('#update_usergrp_form #bank_deposit_status').prop('checked', response.bank_deposit_status == 1);
                            $('#update_usergrp_form #international_bank_status').prop('checked', response.international_bank_status == 1);
                            if (response.usdt_wallet_qr) {
                                $('#usdt-wallet-qr').attr('href', '/storage/' + response.usdt_wallet_qr).show();
                            } else {
                                $('#usdt-wallet-qr').hide();
                            }
                            $('#updateUserGrpModal').modal('show');
                        },
                        error: function(xhr, status, error) { console.error('AJAX Error:', status, error); }
                    });
                }
            });
            $('#action_otp_input').on('input', function() { actionOtpUpdateVerifyState(); });
            $('.action-otp-resend').on('click', function() {
                var action = (otpContext === 'update') ? 'update' : 'view';
                var $btn = $(this);
                $btn.prop('disabled', true);
                actionOtpRequest('usergroup', action, function() {
                    $('#action_otp_input').val('');
                    actionOtpStartCountdown();
                    $btn.prop('disabled', false);
                });
            });
            $('#action_otp_verify_btn').on('click', function() {
                var otp = $('#action_otp_input').val().trim();
                if (otp.length !== 6) return;

                if (otpContext === 'update') {
                    if (typeof bootstrap !== 'undefined' && actionOtpModalEl) {
                        var inst = bootstrap.Modal.getInstance(actionOtpModalEl);
                        if (inst) inst.hide(); else $('#actionOtpModal').modal('hide');
                    } else {
                        $('#actionOtpModal').modal('hide');
                    }
                    $('#update_usergrp_form').find('input[name="action_otp"]').remove();
                    $('#update_usergrp_form').append('<input type="hidden" name="action_otp" value="' + otp.replace(/"/g, '&quot;') + '">');
                    $('#update_usergrp_form').off('submit').submit();
                    return;
                }

                var token = $('input[name="_token"]').first().val() || $('meta[name="csrf-token"]').attr('content');
                var action = 'view';
                $.ajax({
                    url: '{{ url("/admin/action-otp/verify") }}',
                    type: 'POST',
                    data: { _token: token, module: 'usergroup', action: action, otp: otp },
                    success: function(res) {
                        if (res.success) {
                            if (otpContext === 'add') {
                                pendingAddAfterOtpClose = true;
                            } else {
                                pendingEditAfterOtpClose = pendingEditUserGroupId;
                                pendingEditUserGroupId = null;
                            }
                            if (typeof bootstrap !== 'undefined' && actionOtpModalEl) {
                                var inst = bootstrap.Modal.getInstance(actionOtpModalEl);
                                if (inst) inst.hide(); else $(actionOtpModalEl).modal('hide');
                            } else {
                                $('#actionOtpModal').modal('hide');
                            }
                        } else {
                            if (typeof swal !== 'undefined') swal.fire({ icon: 'error', title: res.message || 'Invalid or expired OTP' });
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Verification failed.';
                        if (typeof swal !== 'undefined') swal.fire({ icon: 'error', title: msg });
                    }
                });
            });

            $('#btnAddUserGroup').on('click', function(e) {
                e.preventDefault();
                otpContext = 'add';
                $('#actionOtpModal').data('otp-context', 'add');
                if (typeof bootstrap !== 'undefined' && actionOtpModalEl) {
                    new bootstrap.Modal(actionOtpModalEl).show();
                } else {
                    $('#actionOtpModal').modal('show');
                }
            });

            $('#update_usergrp_form').on('submit', function(e) {
                if ($(this).find('input[name="action_otp"]').length) return;
                e.preventDefault();
                otpContext = 'update';
                $('#actionOtpModal').data('otp-context', 'update');
                if (typeof bootstrap !== 'undefined' && actionOtpModalEl) {
                    new bootstrap.Modal(actionOtpModalEl).show();
                } else {
                    $('#actionOtpModal').modal('show');
                }
            });

            $('#tableUserGrps').on("draw.dt", dTSelection).DataTable({
                order: [
                    [0, "desc"]
                ],
                "ajax": {
                    "url": "/admin/ajax",
                    "type": "GET",
                    data: {
                        action: 'getUserGroups',
                    },
                },
                columns: [{
                        data: 'user_group_id',
                        title: '#'
                    },
                    {
                        data: 'group_name',
                        title: 'Name'
                    },
                    {
                        data: 'group_code',
                        title: 'Code'
                    },
                    {
                        data: 'mt5_group_count',
                        title: 'MT5 Groups'
                    },
                    {
                        data: 'status',
                        title: 'Status'
                    },
                    {
                        data: 'is_visible',
                        title: 'Is Visible'
                    },
                    {
                        data: 'action',
                        title: 'Action',
                        render: function(data, row, row_data) {
                            var return_data = '';
                            var admin_role_id = @json(session('userData')['role_id']);
                            if (admin_role_id == 1 || true) {
                                return_data += '<a href="javascript:void(0)" data-id="' + row_data.user_group_id +
                                    '" class="update-user-grp btn btn-primary"><i class="fa fa-edit"></i></a>';
                                return_data +=
                                    `<input type="hidden" class="form-control" id="pc-clipboard-` +
                                    row_data
                                    .user_group_id + `" value="` + copyright_site_name_text +
                                    `/register/` +
                                    row_data.group_code +
                                    `" readonly=""><a class="ms-3 btn btn-default cursor-pointer cb
                                    " data-clipboard-target="#pc-clipboard-` +
                                    row_data.user_group_id +
                                    `"><i class="fa fa-link"></i></a>`;
                            }
                            return return_data;
                        },
                        orderable: false,
                        searchable: false
                    },
                    // { data: 'action', title: 'action', orderable: false, searchable: false },
                ]
            });

            function dTSelection() {
                $(document).on("click", ".update-user-grp", function(e) {
                    e.preventDefault();
                    pendingEditUserGroupId = $(this).data("id");
                    otpContext = 'edit';
                    $('#actionOtpModal').data('otp-context', 'edit');
                    if (typeof bootstrap !== 'undefined' && actionOtpModalEl) {
                        new bootstrap.Modal(actionOtpModalEl).show();
                    } else {
                        $('#actionOtpModal').modal('show');
                    }
                });
                var clipboard = new ClipboardJS('.cb');
                clipboard.on('success', function(e) {
                    swal.fire({
                        icon: "success",
                        title: "User Group Link Copied"
                    });
                });
            }


        });
        var clipboard = new ClipboardJS('.cb');
        clipboard.on('success', function(e) {
            swal.fire({
                icon: "success",
                title: "User Group Link Copied"
            });
        });
    </script>
@endsection
