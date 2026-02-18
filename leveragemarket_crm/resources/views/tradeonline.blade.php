@include('auth.headerauth')
<script>
window.addEventListener("message", function (event) {
    if (event.data.action === "fillLoginAuto") {

        const loginField = document.querySelector('input[name="login"]');
        const passField  = document.querySelector('input[name="password"]');

        if (loginField) loginField.value = event.data.login;

        if (passField) {
            passField.setAttribute("autocomplete", "on");
            passField.value = event.data.password;
        }
    }
});
</script>
	<div class="container-fuild">
        <div class="geometric-bg"></div>
		<div class="logo">
			<img style="max-width: 250px; margin-top:10px;" class="" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
		</div>
		<iframe src="https://webtrading.leveragemarkets.com/terminal?utm_source=www.fxleverage.com&mode=connect&lang=en&theme-mode=0&theme=greenRed&login={{ $login }}&password={{ $password }}" width="100%" height="600px"></iframe>
	</div>

	<!--<div class="auth-container">
        <div class="geometric-bg"></div>
        <div class="auth-card" style="min-width:70%">
            <div class="logo">
                <img style="max-width: 250px;" src="{{ asset($settings['admin_sidebar_logo_dark']) }}" alt="">
            </div>

			
        </div>
    </div>-->
@include('auth.footerauth')
