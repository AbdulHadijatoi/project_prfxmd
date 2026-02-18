@include('auth.headerauth')
	<div class="auth-container">
        <div class="geometric-bg"></div>
        <div class="auth-card">
            <div class="logo">
                <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
            </div>
            <h1 class="page-title">Sign In</h1>

            <div id="loginCredentialsStep" style="{{ session('customer_2fa_pending') ? 'display: none;' : '' }}">
			<form method="POST" action="{{ route('login') }}" id="signinForm">
            @csrf
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
                <div class="remember-me" style="display: flex; justify-content: space-between; align-items: center;">
                    <label>
                        <input type="checkbox" id="rememberMe" />
                        <span>Remember me</span>
                    </label>
                    <div class="links" style="margin-top: 25px;">
                        <a href="{{ url('forgot-password') }}" class="link">Forgot password?</a>
                    </div>
                </div>
                <div id="message"></div>
				@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
				@endif
				@if (session('error') && !session('customer_2fa_pending'))
					<div class="alert-danger">
						{{ session('error') }}
					</div>
				@endif
                <button type="submit" class="submit-btn">Sign In</button>
            </form>
            </div>

            @if (session('customer_2fa_pending'))
            <div id="login2FaStep">
                <p class="text-muted small mb-2">Enter the 6-digit code from your authenticator app.</p>
                <form method="POST" action="{{ route('customer.verify-2fa') }}" id="signin2FaForm">
                    @csrf
                    <div class="form-group">
                        <input type="text" id="customer2faCode" name="code" placeholder="000000" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" required style="text-align: center; letter-spacing: 0.5em; font-size: 1.25rem;" />
                    </div>
                    @if (session('error'))
                        <div class="alert-danger mb-2">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div id="message2fa"></div>
                    <button type="submit" class="submit-btn w-100">Verify and sign in</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}?cancel_2fa=1" class="link">Use different account</a>
                </div>
            </div>
            @endif
            <div class="auth-switch">
                <span>Don't have an account? <a href="{{ url('register') }}" class="auth-link">Register</a></span>
            </div>
        </div>
    </div>
	<script>
        const form = document.getElementById('signinForm');
		const emailInput = document.getElementById('email');
		const passwordInput = document.getElementById('password');
		const rememberMeCheckbox = document.getElementById('rememberMe');
		const messageDiv = document.getElementById('message');
		const togglePassword = document.getElementById('togglePassword');

		togglePassword.addEventListener('click', (e) => {
			e.preventDefault();
			const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
			passwordInput.setAttribute('type', type);
		});

		form.addEventListener('submit', (e) => {
			e.preventDefault(); // stop default submit for custom validation

			const email = emailInput.value.trim();
			const password = passwordInput.value;

			if (!email || !password) {
				showMessage('Please fill in all fields', 'error');
				return;
			}

			// Optional success message
			showMessage('Signing in...', 'success');

			// Redirect to form action (Laravel login route)
			setTimeout(() => {
				form.submit();   // THIS TRIGGERS REAL REDIRECT
			}, 500);
		});

		function showMessage(text, type) {
			messageDiv.textContent = text;
			messageDiv.className = type;
		}

		function clearForm() {
			emailInput.value = '';
			passwordInput.value = '';
			rememberMeCheckbox.checked = false;
			messageDiv.className = '';
			messageDiv.textContent = '';
			passwordInput.setAttribute('type', 'password');
		}
		
    </script>
@include('auth.footerauth')
