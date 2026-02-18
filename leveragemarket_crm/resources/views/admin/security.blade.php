@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">MFA Authentication</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">2 Step Authentication</li>
                </ol>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-5 col-xl-4">
                    <div class="card custom-card">
                        <div class="card-body">
                            <!-- <h6 class="card-title fw-medium">DEPOSIT TICKET #$details->id ?></h6> -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <div class="text-center">
                                                <div class="userprofile">
                                                    <div class="avatar userpic avatar-rounded">
                                                        <img src="/admin_assets/assets/images/users/client.jpeg"
                                                            alt="img" style="width:100px">
                                                    </div>
                                                    <h3 class="username mb-2"><?= session('userData')['username'] ?></h3>
                                                    <p class="mb-1 text-muted"><?= session('userData')['email'] ?></p>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body text-center">
                            <form action="{{ route('admin.mfa-authentication') }}" method="post" id="form-2step">
                                <div class="row justify-content-center">
                                    <div class="col-lg-9">
                                        <p>Scan the QR Code with your Authenticator App to Enable Multi-Factor Authentication (MFA).</p>
                                    </div>
                                    <div class="col-lg-4 text-center">
                                        <img src="{{ $qrCode }}" alt="MFA QR Code" class="w-100">
                                    </div>
                                    <div class="col-lg-4 mb-auto mt-auto">
                                        <ul class="list-unstyled text-start">
                                            <li>Step 1: Open your authenticator app.</li>
                                            <li class="mt-1 mb-1">Step 2: Scan this QR code. </li>
                                            <li>Step 3: Enter the generated code to verify.</li>
                                        </ul>

                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-lg-6">
                                        <label for="validationCustom05" class="form-label">Enter OTP Code</label>
                                        <input type="text" name="code" class="form-control" id="validationCustom05"
                                            required>
                                    </div>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-lg-12">
                                        <input type="submit" value="Verify & Enable" name="submission"
                                            class="btn btn-primary">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $("#form-2step").submit(function(e) {
            e.preventDefault();
            var action = $(this).attr("action");
            $.ajax({
                url: action,
                data: $(this).serialize(),
                type: "POST",
                beforeSend: function() {
                    Swal.fire({
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: function() {
                            swal.enableLoading();
                        }
                    });
                },
                success: function(data) {
                    if (data.trim() == "true") {
                        swal.fire({
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            icon: "success",
                            title: "Two-Step verification activated in your account"
                        }).then((val) => {
                            location.reload()
                        });
                    } else {
                        swal.fire({
                            icon: "warning",
                            title: data,
                            showConfirmButton: true,
                        })
                    }
                }
            });
        });
    </script>
@endsection
