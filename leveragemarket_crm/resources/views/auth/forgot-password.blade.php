@include('auth.headerauth')
	<div class="auth-container">
        <div class="geometric-bg"></div>
        <div class="auth-card">
            <div class="logo">
                <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
            </div>
            <h4 class="forgotpage-title">If you forgot your password, we’ll email you instructions to reset your password.</h4>
			<form method="POST" id="forgotpassform">
            @csrf
                <div class="form-group">
                    <input type="email" id="email" name="txtemail" placeholder="Email" required />
                </div>                
                <div id="message"></div>
				@if (isset($msg))
					{!! $msg !!}
				@endif
                <button type="submit" class="submit-btn">Send Reset Link</button>
            </form>
            <div class="auth-switch">
                <span>Back to <a href="{{ url('login') }}" class="auth-link">login?</a></span>
            </div>
        </div>
    </div>
	
	<script>
        const form = document.getElementById('forgotpassform');
		const emailInput = document.getElementById('email');
		const messageDiv = document.getElementById('message');

		form.addEventListener('submit', (e) => {
			e.preventDefault(); // stop default submit for custom validation

			const email = emailInput.value.trim();

			if (!email) {
				showMessage('Please fill in all fields', 'error');
				return;
			}

			// Optional success message
			showMessage('Reset link sending...', 'success');

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
		}
		
    </script>

@include('auth.footerauth')
