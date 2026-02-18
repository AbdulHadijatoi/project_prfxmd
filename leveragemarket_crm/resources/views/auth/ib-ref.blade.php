@include('auth.headerauth')
<div class="auth-container">
    <div class="geometric-bg"></div>
    <div class="auth-card">
        <div class="logo">
            <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
        </div>
        <h3 class="page-title">Join With Referral</h3>
        <p class="text-center padding-bottom: 5px">Start Trading Today: Easy Account Setup!</p>

		<div id="message"></div>
		@if (session('status'))
			<div class="alert alert-success mt-2">
				{{ session('status') }}
			</div>
		@endif
		@if ($errors->any())
			<div class="alert alert-danger mt-2">
				<span>{{ $errors->first() }}</span>
			</div>
		@endif

        <form method="POST" class="needs-validation" id="formRegister">
            @csrf
            <div class="form-group">
                <input type="text" id="fullname" name="fullname" placeholder="Your Name" required />
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Email" required />
            </div>
            <div class="form-group">
                <div class="password-input">
                    <input type="password" name="password" id="password" placeholder="Password" required />
                    <button type="button" class="toggle-password" id="togglePassword">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <div class="password-input">
                    <input type="password" name="password_confirmation" id="confirmpassword"
                        placeholder="Confirm Password" required />
                    <button type="button" class="toggle-password" id="toggleconPassword">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 5px">
                <select class="slform-input-2 me-2 mr-2 w-25" name="country_code" id="country_code" required>
                    <option value="">Country Code</option>
                    <?php foreach ($countries as $country) { ?>
                    <option value="+<?= $country['country_code'] ?>"
                        data-flag="<?= strtolower($country['country_alpha']) ?>">
                        +<?= $country['country_code'] ?>
                        (<?= $country['country_name'] ?>)</option>
                    <?php } ?>
                </select>
                <input type="number" id="telephone" name="telephone" placeholder="Enter a phone number"
                    class="slform-input" required />
            </div>

            <div class="form-group">
                <select class="slform-input-2 me-2 w-100" id="country" name="country" required>
                    <option value="">Select Country</option>
                    <?php foreach ($countries as $country) { ?>
                    <option value="<?= $country['country_name'] ?>">
                        <?= $country['country_name'] ?>
                    </option>
                    <?php } ?>
                </select>
            </div>
            {{-- <div class="form-group">
					<?php
					$i=0;
					foreach ($user_groups as $group):
					?>
					<div class="form-check form-check-inline" style="display: flex; align-items: center; gap: 5px">
						<input class="form-check-input" type="radio"
							name="group_id"
							id="user_group_{{ $group['user_group_id'] }}"
							value="<?= $group['user_group_id'] ?>" required>
						<label class="form-check-label"
							for="user_group_{{ $group['user_group_id'] }}">{{ $group['group_name'] }}</label>
					</div>
					<?php endforeach; ?>
				</div> --}}
            {{-- <div class="form-group user-group-wrapper">
                <?php foreach ($user_groups as $group): ?>
                <label class="radio-card">
                    <input type="radio" name="group_id" value="<?= $group['user_group_id'] ?>" required>
                    <span class="radio-circle"></span>
                    <span class="radio-text"><?= $group['group_name'] ?></span>
                </label>
                <?php endforeach; ?>
            </div> --}}
			<input type="hidden" name="group_id" value="1" />

            <div style="padding-bottom: 5px" class="h-captcha mb-2 h-captcha-register"data-sitekey="{{ $settings['hcaptcha_site_key'] ?? '' }}">
            </div>

            <button type="submit" class="submit-btn" name="register" id="registration1">Register Now</button>
        </form>
        <div class="auth-switch">
            <span>Back to <a href="{{ url('login') }}" class="auth-link">login?</a></span>
        </div>
    </div>
</div>

@include('auth.footerauth')
<script src="https://secure.fxleverage.com/assets/js/jquery-3.3.1.min.js"></script>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<script>
    const form = document.getElementById('formRegister');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const conpasswordInput = document.getElementById('confirmpassword');

    const messageDiv = document.getElementById('message');
    const togglePassword = document.getElementById('togglePassword');
    const toggleconPassword = document.getElementById('toggleconPassword');
   

    togglePassword.addEventListener('click', (e) => {
        e.preventDefault();
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
    });

    toggleconPassword.addEventListener('click', (e) => {
        e.preventDefault();
        const type = conpasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        conpasswordInput.setAttribute('type', type);
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault(); // stop default submit for custom validation

        const fullname = fullnameInput.value.trim();
        const email = emailInput.value.trim();
        const password = passwordInput.value;
        const confirmPassword = passwordInput.value;
		const hCaptcha = grecaptcha.getResponse();

        if (!fullname || !email || !password || !confirmPassword) {
            showMessage('Please fill in all fields', 'error');
            return;
        }

        if (!hCaptcha) {
            showMessage('Please complete the CAPTCHA to submit the form.');
        }

        // Optional success message
        showMessage('Signing up...', 'success');

        // Redirect to form action (Laravel login route)
        setTimeout(() => {
            form.submit(); // THIS TRIGGERS REAL REDIRECT
        }, 500);
    });

    function showMessage(text, type) {
        messageDiv.textContent = text;
        messageDiv.className = type;
    }

    function clearForm() {
        emailInput.value = '';
        passwordInput.value = '';
        messageDiv.className = '';
        messageDiv.textContent = '';
        passwordInput.setAttribute('type', 'password');
    }

    $(document).ready(function() {
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            var $option = $(
                '<span><span class="fi fis fi-' + $(option.element).data('flag') + '"></span>' + option
                .text + '</span>'
            );
            return $option;
        }
        $("#country_code").select2({
            placeholder: "Country Code",
            templateResult: formatOption,
            templateSelection: formatOption,
            selectionCssClass: "country-code-select"
        });
    });

    $.get("https://ipinfo.io", function(response) {
        var country = response.country.toLowerCase();
        var option = $("#country_code option[data-flag='" + country + "']").attr("value");
        $("#country_code").val(option).trigger("change")
    }, "jsonp");
</script>


<?PHP /*



@extends('layouts.app')
<style>
     .country-code-select {
        border: 1px solid white !important;
    }

    .country-code-wrapper .select2-container {
        max-width: 25%;
        display: block;
        padding: 0.6rem .75rem;
        font-size: .875rem;
        font-weight: 400;
        line-height: 1.5;
        color: #131920;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #bec8d0;
        border-radius: 8px;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }

 </style>
@section('content')
    <div id="app" data-v-app="">
        <div data-v-dde07c83="" class="auth-main">
            <div data-v-dde07c83="" class="auth-wrapper v3">
                <div data-v-97e32e5a="" class="auth-form" style="justify-content: unset;">
                    <div data-v-dde07c83="" class="auth-header row mt-3">
                        <div data-v-dde07c83="" class="col mt-5"><a data-v-dde07c83="" href="/login"><img
                                    data-v-dde07c83="" src="/<?php echo $settings['admin_sidebar_logo']; ?>" alt="Logo" style="height: 8vh;"></a>
                        </div>
                        <div data-v-97e32e5a="" class="col-auto my-1">
                            <h5 data-v-97e32e5a="" class="m-0 text-muted f-w-500"> Step <b data-v-97e32e5a="" class="h5"
                                    id="auth-active-slide"><?= isset($success) ? '3' : '1' ?></b> to 3 </h5>
                        </div>
                    </div>
                    <div data-v-97e32e5a="" class="card my-auto">
                        <div data-v-97e32e5a="" class="card-body">
                            <ul data-v-97e32e5a="" class="nav nav-tabs d-none" id="myTab" role="tablist">
                                <li data-v-97e32e5a="" class="nav-item" role="presentation"><a data-v-97e32e5a=""
                                        class="nav-link active" id="auth-tab-1" data-bs-toggle="tab" href="#"
                                        role="tab" data-slide-index="1" aria-controls="auth-1" aria-selected="true"></a>
                                </li>
                            </ul>
                            <form method="post" data-v-97e32e5a="" class="needs-validation" id="formRegister">
                                @csrf
                                <div data-v-97e32e5a="" class="tab-content">
                                    <div data-v-97e32e5a="" class="tab-pane <?= !isset($success) ? 'active show' : '' ?>"
                                        id="auth-1" role="tabpanel" aria-labelledby="auth-tab-1">
                                        <div data-v-97e32e5a="" class="text-center">
                                            <h3 data-v-97e32e5a="" class="text-center mb-2 f-w-600">Join Us Now
                                            </h3>
                                            <p data-v-97e32e5a="" class="mb-4 fs-5">Start Trading Today: Easy Account
                                                Setup!
                                            </p>
                                        </div>
                                        @if (session('status'))
                                            <div class="alert alert-success">
                                                {{ session('status') }}
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <span>{{ $errors->first() }}</span>
                                            </div>
                                        @endif

                                        <div data-v-97e32e5a="" class="row my-2">
                                            <div data-v-97e32e5a="" class="col-12">
                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                        class="form-label">Email Id</label><input data-v-97e32e5a=""
                                                        type="email" id="email" class="form-control"
                                                        placeholder="Email id" required="" name="email"><!----></div>
                                            </div>
                                            <div data-v-97e32e5a="" class="col-12">
                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                        class="form-label">Password</label><input data-v-97e32e5a=""
                                                        type="password" id="password" class="form-control"
                                                        placeholder="Password" required="" aria-autocomplete="list"
                                                        name="password"><!----></div>
                                            </div>
                                            <div data-v-97e32e5a="" class="col-12">
                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                        class="form-label">Confirm Password</label><input
                                                        data-v-97e32e5a="" id="confirmpassword" type="password"
                                                        class="form-control" placeholder="Confirm Password"
                                                        required="" name="password_confirmation"><!----></div>
                                            </div>
                                        </div>
                                        <div data-v-97e32e5a="" class="row g-3">
                                            <div data-v-97e32e5a="" class="col-sm-12">
                                                <div data-v-97e32e5a="" class="d-grid"><button id="registration1"
                                                        data-v-97e32e5a="" class="btn btn-primary" type="button"
                                                        role="tab" data-bs-toggle="tab" data-bs-target="#auth-2">
                                                        Continue </button></div>
                                            </div>
                                        </div>

                                        <div data-v-97e32e5a="" class="row">
                                            <div data-v-97e32e5a="" class="d-flex align-items-end mt-2">
                                                <h6 data-v-97e32e5a="" class="f-w-500 mb-0"
                                                    style="font-size: 12px !important;">Already have an Account? </h6><a
                                                    data-v-97e32e5a="" href="/login" class="link-primary"
                                                    style="font-size: 14px !important; padding-left: 10px; margin-top: 10px;">
                                                    Login</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-v-97e32e5a="" class="tab-pane" id="auth-2" role="tabpanel"
                                        aria-labelledby="auth-tab-2">
                                        <div data-v-97e32e5a="" class="">
                                            <h4 data-v-97e32e5a="" class="mb-3 f-w-600">Tell us a bit about yourself
                                            </h4>
                                        </div>
                                        <div data-v-97e32e5a="" class="row my-4">
                                            <div data-v-97e32e5a="" class="col-sm-12">
                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                        class="form-label">Full Name</label><input data-v-97e32e5a=""
                                                        type="text" class="form-control" placeholder="Full name"
                                                        required name="fullname"><!----><small data-v-97e32e5a="">*as it
                                                        appears on your ID Card / Proof of Identity</small></div>
                                            </div>
                                            <div data-v-97e32e5a="" class="col-12">
                                                <div data-v-97e32e5a="" class="form-group country-code-wrapper"><label
                                                        data-v-97e32e5a="" class="form-label">Phone Number</label>
                                                    <div class="d-flex align-items-center">
                                                        <select class="form-select me-2 w-25" name="country_code"
                                                            id="country_code" required>
                                                            <option value="">Country Code</option>
                                                            <?php foreach ($countries as $country) { ?>
                                                            <option value="+<?= $country['country_code'] ?>"
                                                                data-flag="<?= strtolower($country['country_alpha']) ?>">
                                                                +<?= $country['country_code'] ?>
                                                                (<?= $country['country_name'] ?>)</option>
                                                            <?php } ?>
                                                        </select>
                                                        <input type="number" id="telephone"
                                                            class="form-control w-75 ms-2" maxlength="25"
                                                            name="telephone" placeholder="Enter a phone number"
                                                            tabindex="0" aria-describedby="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div data-v-97e32e5a="" class="col-12">
                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                        class="form-label">Nationality</label>
                                                    <select class="form-select" id="country" name="country" required>
                                                        <option value="">Select Country</option>
                                                        <?php foreach ($countries as $country) { ?>
                                                        <option value="<?= $country['country_name'] ?>">
                                                            <?= $country['country_name'] ?>
                                                        </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- <div data-v-97e32e5a="" class="col-sm-12">
                                                                <div data-v-97e32e5a="" class="form-group"><label data-v-97e32e5a=""
                                                                        class="form-label">Referral Code<small data-v-97e32e5a="">(if
                                                                            any)</small></label><input data-v-97e32e5a="" type="text"
                                                                        class="form-control" placeholder="Referral Code"
                                                                        name="referral"></div>
                                                            </div> -->
                                        </div>
                                        <div class="h-captcha mb-2 h-captcha-register"
                                            data-sitekey="{{ $settings['hcaptcha_site_key']??'' }}">
                                        </div>
                                        <div data-v-97e32e5a="" class="row g-3">
                                            <div data-v-97e32e5a="" class="col-sm-12">
                                                <div data-v-97e32e5a="" class="d-grid"><button data-v-97e32e5a=""
                                                        class="btn btn-primary" type="submit" name="register"><!---->
                                                        Submit</button></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div data-v-97e32e5a="" class="tab-pane <?= isset($success) ? 'active show' : '' ?>"
                                        id="auth-3" role="tabpanel" aria-labelledby="auth-tab-3">
                                        <div data-v-97e32e5a="" class="text-center">
                                            <div data-v-97e32e5a="" class="text-center">
                                                <h3 data-v-97e32e5a="" class="text-center mb-3">Please verify your email
                                                    id
                                                </h3>
                                                @isset($success)
                                                    <p data-v-97e32e5a="" class="mb-4"> <?= $success??'' ?> </p>
                                                @endisset
                                                <a href="/login" class="btn btn-primary w-75">
                                                    Login</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if (!isset($success)) { ?>
                    <div data-v-97e32e5a="" class="auth-footer">
                        <p data-v-97e32e5a="" class="m-0 w-100 text-center" style="font-size: 11px;"> By signing up, I
                            acknowledge
                            that I have read, understood and agree to the Client Agreement <br data-v-97e32e5a="">and
                            give my consent
                            for <?= $settings['admin_title'] ?> to contact me for marketing purposes. <br
                                data-v-97e32e5a=""> By
                            registering you agree
                            to our <a data-v-97e32e5a="" href="#">Privacy Policy</a>, <a data-v-97e32e5a=""
                                href="#">Client
                                Agreement </a>&amp; <a data-v-97e32e5a="" href="#">Trading Risk
                                Warning</a>. </p>
                    </div>
                    <?php } ?>
                </div>
                <div data-v-dde07c83="" class="auth-sidecontent"
                    style="background: linear-gradient(45deg, rgb(25, 24, 76), rgb(var(--bs-primary-rgb))) !important;">
                    <div class="p-3 px-lg-5 text-center">
                        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active"><img src="/assets/images/acc-1.png" alt="user-image"
                                        class="hei-150 mb-3">
                                    <h5 class="text-white mb-0">Regulatory Excellence</h5>
                                    <p class="text-white text-opacity-50">Compliance Assurance</p>
                                    <div class="star f-20 my-4"><i class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star-half-alt text-warning"></i></div>
                                    <p class="text-white"> With meticulous attention to regulatory standards,
                                        <?= $settings['admin_title'] ?> guarantees
                                        compliance assurance, fostering transparency and confidence among traders with a
                                        commitment to
                                        ethical conduct and integrity.
                                    </p>
                                </div>
                                <div class="carousel-item"><img src="/assets/images/ben-02.png" alt="user-image"
                                        class="hei-150 mb-3">
                                    <h5 class="text-white mb-0">Transparent Pricing Policy</h5>
                                    <p class="text-white text-opacity-50">Clear Cost Commitment</p>
                                    <div class="star f-20 my-4"><i class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star-half-alt text-warning"></i></div>
                                    <p class="text-white"> At <?= $settings['admin_title'] ?>, transparency is paramount.
                                        Our pricing
                                        policy ensures
                                        clarity and fairness, empowering traders with transparent pricing structures and
                                        no hidden fees for
                                        a seamless trading experience. </p>
                                </div>
                                <div class="carousel-item"><img src="/assets/images/ben-03.png" alt="user-image"
                                        class="hei-150 mb-3">
                                    <h5 class="text-white mb-0">Swift and Precise Execution</h5>
                                    <p class="text-white text-opacity-50">Precision Trading</p>
                                    <div class="star f-20 my-4"><i class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star-half-alt text-warning"></i></div>
                                    <p class="text-white"> Experience lightning-fast trade execution with
                                        <?= $settings['admin_title'] ?>.
                                        Our advanced
                                        trading infrastructure ensures swift and precise order processing, enabling
                                        traders to capitalize on
                                        market opportunities instantly. </p>
                                </div>
                                <div class="carousel-item"><img src="/assets/images/ben-04.png" alt="user-image"
                                        class="hei-150 mb-3">
                                    <h5 class="text-white mb-0">Competitive Spreads</h5>
                                    <p class="text-white text-opacity-50">Cost Efficiency</p>
                                    <div class="star f-20 my-4"><i class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star text-warning"></i><i
                                            class="fas fa-star-half-alt text-warning"></i></div>
                                    <p class="text-white"> Gain a competitive edge with <?= $settings['admin_title'] ?>'
                                        tight spreads.
                                        Our low-cost
                                        advantage ensures competitive pricing on all trading instruments, allowing
                                        traders to maximize
                                        profitability and minimize trading costs. </p>
                                </div>
                            </div>
                            <div class="carousel-indicators position-relative mt-3"><button type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                                    aria-current="true" aria-label="Slide 1"></button><button type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                                    aria-label="Slide 2"></button><button type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                                    aria-label="Slide 3"></button><button type="button"
                                    data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3"
                                    aria-label="Slide 4"></button></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://js.hcaptcha.com/1/api.js" async defer></script>

    <script>
        $("#formRegister").on("submit", function(e) {
            e.preventDefault();
            const hCaptcha = grecaptcha.getResponse();
            if (!hCaptcha) {
                toastr.error('Please complete the CAPTCHA to submit the form.');
            }else{
                $("#formRegister")[0].submit();
            }
        });
        $(document).ready(function() {

            function formatOption(option) {
                if (!option.id) {
                    return option.text;
                }
                var $option = $(
                    '<span><span class="fi fis fi-' + $(option.element).data('flag') + '"></span>' + option
                    .text + '</span>'
                );
                return $option;
            }
            $("#country_code").select2({
                placeholder: "Country Code",
                templateResult: formatOption,
                templateSelection: formatOption,
                selectionCssClass: "country-code-select"
            });
            $('#registration1').click(function() {
                const email = $('#email').val();
                const password = $('#password').val();
                const confirmPassword = $('#confirmpassword').val();
                if (!email || !password || !confirmPassword) {
                    swal.fire({
                        icon: "error",
                        title: "Please fill all required fields.",
                    })
                    return;
                }
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailPattern.test(email) == false) {
                    swal.fire({
                        icon: "error",
                        title: "Invalid email address",
                    });
                    return;
                }
                if (password !== confirmPassword) {
                    swal.fire({
                        icon: "error",
                        title: "Passwords do not match",
                    })
                    return;
                }
                $('#auth-1').removeClass('show active');
                $('#auth-2').addClass('show active');
                $('#auth-active-slide').html(2);
            });
        });
        $.get("https://ipinfo.io", function(response) {
            var country = response.country.toLowerCase();
            var option = $("#country_code option[data-flag='" + country + "']").attr("value");
            $("#country_code").val(option).trigger("change")
        }, "jsonp");
    </script>
@endsection
*/ ?>