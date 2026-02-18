@include('auth.headerauth')
<style>
.submit-btn {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-loader {
    width: 18px;
    height: 18px;
    border: 3px solid #fff;
    border-top: 3px solid transparent;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    100% {
        transform: rotate(360deg);
    }
}

.d-none {
    display: none;
}

.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
<div class="auth-container">
    <div class="geometric-bg"></div>
    <div class="auth-card">
        <div class="logo">
            <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
        </div>
        <h3 class="page-title">Join Us Now</h3>
        <p class="text-center">Start Trading Today: Easy Account Setup!</p>

        <form method="POST" class="needs-validation" id="formRegister">
            @csrf
            <div class="form-group">
                <input type="text" id="fullname" name="fullname" placeholder="Your Name" required maxlength="100" pattern="^[a-zA-Z\s.'-]+$" title="Only letters and spaces allowed" />
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
		
		if (containsHTML(fullname)) {
			showMessage('Invalid characters in Full Name', 'error');
			return;
		}

        if (!hCaptcha) {
            showMessage('Please complete the CAPTCHA to submit the form.');
        }
        // Optional success message
        showMessage('Signing up...', 'success');

        // Redirect to form action (Laravel login route)
        setTimeout(() => {
            form.submit(); 
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
	
	function containsHTML(str) {
		return /<\/?[a-z][\s\S]*>/i.test(str);
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
