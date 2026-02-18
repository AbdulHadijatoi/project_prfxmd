</div>
<div id="addWalletModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLiveLabel">Add Wallet Details</h5><button type="button"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="walletDetailsForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group"><label class="form-label">Wallet Name</label><input type="text"
                                    class="form-control" autofocus name="wallet_name" required></div>
                            <div class="form-group"><label class="form-label">Wallet Crypto</label>
                                <select id="my-select" class="form-control" name="wallet_currency" required>
                                    <option value="USDT">USDT</option>
                                </select>
                            </div>
                            <div class="form-group"><label class="form-label">Wallet Network</label>
                                <select id="my-select" class="form-control" name="wallet_network" required>
                                    <option value="USDT-TRX">ERC20</option>
                                    <option value="USDT-TRX">TRC20</option>
                                </select>
                            </div>
                            <div class="form-group"><label class="form-label">Wallet Address</label><input
                                    type="text" class="form-control" name="wallet_address" required></div>
                            <div class="form-group"><label class="form-label">Status</label>
                                <select id="my-select" class="form-control" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">In-Active</option>
                                </select>
                            </div>
                            <hr class="my-3 border border-secondary-subtle">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Verification
                                    OTP
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text otp-request" data-type="Wallet_Creation">Send
                                        OTP</span>
                                    <input type="number" class="form-control" name="otp" disabled required
                                        data-type="Wallet_Creation">
                                    <span class="input-group-text">
                                        <i class="feather icon-info mb-auto mt-auto" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="You will receive OTP on your registered email address"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 text-end"><button type="button" class="btn btn-link-danger btn-pc-default"
                            data-bs-dismiss="modal">Cancel</button>
                        <input class="btn btn-primary" type="submit" name="add_wallet_details"
                            value="Add Wallet Details">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="addBankModal2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLiveLabel">Add Bank Details</h5><button type="button"
                    class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="bankDetailsForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group"><label class="form-label">Account Holder
                                    Name</label><input type="text" class="form-control"
                                    placeholder="Beneficiary Name" name="account_holder_name" required>
                            </div>
                            <div class="form-group"><label class="form-label">Account Number</label><input
                                    type="number" class="form-control" placeholder="Account Number"
                                    name="bank_account_no" required></div>
                            <div class="form-group"><label class="form-label">IBAN number</label><input type="text"
                                    class="form-control" placeholder="IBAN number" name="ifsc_code" required></div>
                            <div class="form-group"><label class="form-label">SWIFT Code</label><input type="text"
                                    class="form-control" placeholder="SWIFT Code" name="swift_code" required></div>
                            <div class="form-group"><label class="form-label">Bank Name</label><input type="text"
                                    class="form-control" placeholder="Bank Name" name="bank_name" required></div>
                            <hr class="my-3 border border-secondary-subtle">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label">Verification
                                    OTP
                                </label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text otp-request" data-type="Bank_Details">Send
                                        OTP</span>
                                    <input type="number" class="form-control" name="otp" disabled required
                                        data-type="Bank_Details">
                                    <span class="input-group-text">
                                        <i class="feather icon-info mb-auto mt-auto" data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="You will receive OTP on your registered email address"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow-1 text-end"><button type="button"
                            class="btn btn-link-danger btn-pc-default" data-bs-dismiss="modal">Cancel</button>
                        <input class="btn btn-primary" type="submit" name="add_bank_details"
                            value="Add Bank Details">
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>
<script>
    $("#bankDetailsForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('bank.store') }}",
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function() {
                $("#bankDetailsForm input,#bankDetailsForm select").attr("disabled", "true");
            },
            success: function(data) {
                $("#bankDetailsForm input,#bankDetailsForm select").attr("disabled", "true");
                if (data.success == true) {
                    Swal.fire({
                        title: "Bank Details Successfully Added",
                        icon: "success"
                    }).then((val) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Something went wrong",
                        icon: "error",
                        text: data.message ?? ''
                    }).then((val) => {
                        location.reload();
                    });
                }
            }
        });
    })
    $("#walletDetailsForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('wallet.store') }}",
            type: "POST",
            data: $(this).serialize(),
            beforeSend: function() {
                $("#walletDetailsForm input,#walletDetailsForm select").attr("disabled", "true");
            },
            success: function(data) {
                $("#walletDetailsForm input,#walletDetailsForm select").attr("disabled", "true");
                if (data.success == true) {
                    Swal.fire({
                        title: "Wallet Details Successfully Added",
                        icon: "success"
                    }).then((val) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Something went wrong",
                        icon: "error",
                        text: data.message ?? ''
                    }).then((val) => {
                        location.reload();
                    });
                }
            }
        });
    })
    $("#verify-user-kyc").click(function(e) {
        e.preventDefault();
        var iframe =
            "<iframe id='kyc_verification_frame' src='/sumsub' class='w-100' style='height: 100vh;'></iframe>";
        $(this).closest(".card-body").html(iframe);
    });
    $('input[type="file"][data-limit]').on('change', function() {
        if ($(this).data('limit') !== undefined) {
            var limit = $(this).data('limit');
            var file = this.files[0];
            var maxSize = limit * 1024 * 1024;
            if (file && file.size > maxSize) {
                Swal.fire({
                    title: "File size exceeds " + limit + "MB. Please select a smaller file.",
                    icon: "warning"
                });
                this.value = '';
            }
        }
    });
</script>
<script type="module" src="/assets/js/popper.min.js"></script>
<script type="module" src="/assets/js/simplebar.min.js"></script>
{{-- <script type="module" src="/assets/js/bootstrap.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script type="module" src="/assets/js/custom-font.js?v=20241122"></script>
{{-- <script src="/assets1/vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script> --}}
<script type="module" src="/assets/js/pcoded.js"></script>
<script type="module" src="/assets/js/feather.min.js?v=1"></script>
<script type="module" src="/assets/js/dashboard-default.js"></script>
@include('sweetalert::alert')
<script>
    $(document).on('click', '.otp-request', function(e) {
        e.preventDefault();
        var type = $(this).attr("data-type");
        if ($(this).attr("disabled")) {
            return true;
        }
        $.ajax({
            url: "/getOtp",
            type: "POST",
            data: {
                "type": type,
            },
            beforeSend: function() {
                $(".otp-request[data-type='" + type + "']").attr("disabled", "true");
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait while we process your request.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    backdrop: false,
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
                        text: "Kindly check in SPAM/JUNK folder for second case. Otherwise try again after sometime.",
                        backdrop: false
                    }).then((val) => {
                        // $("[name='otp'][data-type='" + type + "']")[0].focus();
                    });
                } else {
                    $(".otp-request[data-type='" + type + "']").attr("disabled", "true");
                    Swal.fire({
                        icon: 'warning',
                        title: data.message,
                        backdrop: false
                    });
                }
            }
        });
    });

    // $(document).ready(function() {
    //     $('[role="tab"]').click(function() {
    //         if ($(this).attr("href")) {
    //             location.hash = $(this).attr("href");
    //         } else if ($(this).data("bs-target")) {
    //             location.hash = $(this).data("bs-target");
    //         }
    //     });

    //     if (location.hash) {
    //         var tab = location.hash;

    //         if ($('a[href="' + tab + '"]').length) {
    //             const triggerEl = document.querySelector('a[href="' + tab + '"]');
    //             bootstrap.Tab.getInstance(triggerEl).show(); // Select tab by name
    //             //     $('a[href="' + tab + '"]').tab('show');
    //         }
    //     }
    // })

    if (location.hash) {
        var tab = location.hash;
        var tabElement = document.querySelector('a[href="' + tab + '"]');
        if (tabElement) {
            var tabInstance = new bootstrap.Tab(tabElement);
            tabInstance.show();
        }
    }
    $(document).on('click', ".showPassword", function() {
        var input = $(this).closest(".input-group").find("input");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
            $(this).find("i").removeClass("ti-eye-off");
            $(this).find("i").addClass("ti-eye");
        } else {
            input.attr("type", "password");
            $(this).find("i").removeClass("ti-eye");
            $(this).find("i").addClass("ti-eye-off");
        }
    });
</script>
<script>
$(function(){
    let interval;
    function startMarquee() {
        interval = setInterval(function () {
            $('#vertical-marquee a:first')
                .slideUp(600, function () {
                    $(this).appendTo('#vertical-marquee').show();
                });
        }, 2500);
    }

    function stopMarquee() {
        clearInterval(interval);
    }

    // Start scrolling
    startMarquee();

    // Pause on hover
    $('#vertical-marquee').hover(
        function() { stopMarquee(); },   // mouse enter
        function() { startMarquee(); }   // mouse leave
    );
});
</script>
<script type="module" src="/assets/js/custom.js?v=<?= time() ?>"></script>
@yield('scripts')
</body>

</html>
