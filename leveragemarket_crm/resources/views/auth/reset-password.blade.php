@include('auth.headerauth')
	<div class="auth-container">
        <div class="geometric-bg"></div>
        <div class="auth-card">
            <div class="logo">
                <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
            </div>
            <h1 class="page-title">Sign In</h1>
			<form method="POST" id="resetpassword" >
			@csrf
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
                        <input type="password" name="password_confirmation" id="conpassword" placeholder="Confirm Password" required />
                        <button type="button" class="toggle-password" id="toggleconPassword">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div id="message"></div>
				@if (session('status'))
					<div class="alert alert-success">
						{{ session('status') }}
					</div>
				@endif
				@if ($errors->any())
					<div class="alert alert-danger">
						@foreach ($errors->all() as $error)
							<span>{{ $error }}</span>
						@endforeach
					</div>
				@endif
                <button type="submit" class="submit-btn">Reset Your Password</button>
            </form>
            <div class="auth-switch">
                <span>Back to <a href="{{ url('login') }}" class="auth-link">Login?</a></span>
            </div>
        </div>
    </div>
	<script>
        const form = document.getElementById('resetpassword');
		const passwordInput = document.getElementById('password');
		const conpasswordInput = document.getElementById('conpassword');
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
			
			const password = passwordInput.value;
			const conpassword = conpasswordInput.value;

			if (!password || !conpassword) {
				showMessage('Please fill in all fields', 'error');
				return;
			}

			// Optional success message
			showMessage('Reseting...', 'success');

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
			passwordInput.value = '';
			conpasswordInput.value = '';
			messageDiv.className = '';
			messageDiv.textContent = '';
			passwordInput.setAttribute('type', 'password');
			conpasswordInput.setAttribute('type', 'password');
		}
		
    </script>
@include('auth.footerauth')


@include('auth.headerauth')
	<div class="slform-container">
		<!-- Left section -->
		<div class="slform-left">
			<img style="max-width: 45%; margin: auto;" class="img-fluid" src="{{ asset('public/'.$settings['admin_sidebar_logo_dark']) }}" alt="logo">
			@if (session('status'))
				<div class="alert alert-success">
					{{ session('status') }}
				</div>
			@endif
			@if ($errors->any())
				<div class="alert alert-danger">
					@foreach ($errors->all() as $error)
						<span>{{ $error }}</span>
					@endforeach
				</div>
			@endif
			<form method="POST" >
				@csrf
				<!-- Inputs -->
				<input type="password" name="password" placeholder="Password" class="slform-input" required />
				<input type="password" name="password_confirmation" placeholder="Password" class="slform-input" required />

				<!-- Remember Me -->
				<div class="d-flex justify-content-between align-items-center mb-3">	
					<div><a data-v-dde07c83="" href="/login" class="link-primary">Back to Login</a></div>
				</div>

				<!-- Button -->
				<button class="btn btn-primary btn--megaeffect w-100" type="submit" name="signin">Reset Your Password</button>
			</form>
		</div>

		<!-- Right section (Image Slider) -->
		<div class="slform-right" id="slformSlider">
			<button class="slform-arrow left" onclick="prevSlide()">&#10094;</button>
			<button class="slform-arrow right" onclick="nextSlide()">&#10095;</button>
		</div>
	</div>

@include('auth.footerauth')
