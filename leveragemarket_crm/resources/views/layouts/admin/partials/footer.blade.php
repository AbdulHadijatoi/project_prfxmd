<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel"
    aria-hidden="true" data-bs-backdrop="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-top" role="document"
        style="margin-top: 0 !important;position: absolute;top: 0;left: 50%;transform: translateX(-50%);">
        <div class="modal-content">
            <div class="modal-body d-flex align-items-center">
                <div class="row">
                    <div class="col-12 col-lg-2 d-flex justify-content-center">
                        <img src="/{{ $settings['favicon'] }}" alt="Notification" class="img-fluid me-3"
                            style="height:50px;">
                    </div>
                    <div class="col-12 col-lg-10 mt-1">
                        <p class="mb-0 fs-6">
                            {{ $settings['admin_title'] }} would like to send you updates. Would you like to enable push
                            notifications?
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-2 justify-content-center">
                <button type="button" class="btn border-0 text-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="enable-notifications">Allow</button>
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
            icon: 'warning',
            title: 'Something went wrong',
            text: '{{ session('error') }}',
            showConfirmButton: true
        });
    </script>
@endif
@if ($errors->any())
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Something went wrong',
            text: '{{ $errors->first() }}',
            showConfirmButton: true
        });
    </script>
@endif
<script src="/admin_assets/assets/libs/@popperjs/core/umd/popper.min.js"></script>
<script src="/admin_assets/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/admin_assets/assets/js/defaultmenu.min.js"></script>
<script src="/admin_assets/assets/libs/node-waves/waves.min.js"></script>
<script src="/admin_assets/assets/js/sticky.js"></script>
<script src="/admin_assets/assets/libs/simplebar/simplebar.min.js"></script>
<script src="/admin_assets/assets/js/simplebar.js"></script>
<script src="/admin_assets/assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js"></script>
<script src="/admin_assets/assets/libs/@simonwep/pickr/pickr.es5.min.js"></script>
<script src="/admin_assets/assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="/admin_assets/assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
    $(document).ready(function() {
        $('.menu-item-main.has-sub').each(function() {
            if ($(this).find('.menu-item-sub').length === 0) {
                $(this).hide();
            }
        });
        $('#dtStartDate, #dtEndDate').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            container: 'body',
            orientation: 'bottom left',
            endDate: new Date(),
        });
    });


    $("#ibRequestForm").submit(function(e) {
        e.preventDefault();
        var formData = $("#ibRequestForm").serializeArray();
        formData.push({
            name: 'action',
            value: 'requestIB'
        });
        $.ajax({
            url: "/admin/ajax",
            type: "POST",
            data: formData,
            responseType: 'json',
            success: function(data) {
                data = JSON.parse(data.trim());
                if (data.status == true) {
                    swal.fire({
                        icon: "success",
                        title: "IB Request Successfully Updated",
                    }).then((val) => {
                        location.reload();
                    });
                } else {
                    swal.fire({
                        icon: "error",
                        title: "Something went wrong.",
                        text: "Please try again or contact support."
                    }).then((val) => {
                        location.reload();
                    });
                }
            }
        });
    });
    $(document).on('click', ".showPassword", function() {
        var input = $(this).closest(".input-group").find("input");
        if (input.attr("type") == "password") {
            input.attr("type", "text");
            $(this).find("i").removeClass("fa-eye-slash");
            $(this).find("i").addClass("fa-eye");
        } else {
            input.attr("type", "password");
            $(this).find("i").removeClass("fa-eye");
            $(this).find("i").addClass("fa-eye-slash");
        }
    });

    $(".ajaxSubmit").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: $(this).serialize(),
            beforeSend: function() {
                // Swal.fire({
                //     showConfirmButton: false,
                //     showCancelButton: false,
                //     allowOutsideClick: false,
                //     allowEscapeKey: false,
                //     didOpen: function() {
                //         Swal.enableLoading();
                //     }
                // });
            },
            success: function(data) {
                swal.close();
                if (data.status) {
                    swal.fire({
                        icon: "success",
                        title: data.message
                    }).then((val) => {
                        location.href = location.href;
                    });
                } else {
                    swal.fire({
                        icon: "warning",
                        title: data.message
                    })
                }
            }
        });
    });

    $('.usersDD').select2({
        // dropdownParent: $('#addUserModal'),
        ajax: {
            url: '/admin/dd/users',
            dataType: 'json',
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.fullname + " [" + item.email + "]",
                            id: item.id
                        }
                    })
                };
            }
        }
    });
    $('.stusersDD').select2({
        dropdownParent: $('#createAccountModel'),
        ajax: {
            url: '/admin/dd/stusers',
            dataType: 'json',
            processResults: function(data) {
                return {
                    results: $.map(data.data, function(item) {
                        return {
                            text: item.login,
                            id: item.id
                        }
                    })
                };
            }
        }
    });
</script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        showNotificationModal();
    });


    function showNotificationModal() {
        if (Notification.permission === "default") {
            $('#notificationModal').modal('show');
        }
    }
    $('#enable-notifications').on('click', function() {
        requestNotificationPermission();
        $('#notificationModal').modal('hide');
    });
    var pusher = new Pusher(`{{ $settings['pusher_key'] }}`, {
        cluster: `{{ $settings['pusher_cluster'] }}`
    });
    console.log(pusher);
    if (true) {
        var channel = pusher.subscribe(`channel-{{ md5(session('alogin')) }}`);
        channel.bind('my-event', function(data) {
            if (Notification.permission === "granted") {
                showNotification(data.message);
            } else {
                requestNotificationPermission(function() {
                    showNotification(data.message);
                });
            }
        });
    }


    function showNotification(data) {
        var navigator_info = window.navigator;
        var screen_info = window.screen;
        var uid = navigator_info.mimeTypes.length;
        uid += navigator_info.userAgent.replace(/\D+/g, '');
        uid += navigator_info.plugins.length;
        uid += screen_info.height || '';
        uid += screen_info.width || '';
        uid += screen_info.pixelDepth || '';
        data.check_notification = true;
        data.id = uid + "-" + data.id
        $.ajax({
            url: "/admin/ajax",
            type: "POST",
            cache: false,
            data: {
                "action": "checkNotification",
                "id": data.id
            },
            success: function(response) {
                if (response == 0) {
                    var notification = new Notification(data.type, {
                        body: data.message,
                        icon: "/{{ $settings['favicon'] }}"
                    });
                    notification.onclick = function() {
                        window.open(`{{ $settings['copyright_site_name_text'] }}` + data.link,
                            "_blank");
                        window.focus();
                    };
                }
            }
        });
    }

    function requestNotificationPermission(callback) {
        if (Notification.permission === "default") {
            Notification.requestPermission().then(function(permission) {
                if (permission === "granted" && callback) {
                    callback();
                }
            });
        }
    }
</script>
@yield('scripts')
@include('sweetalert::alert')
