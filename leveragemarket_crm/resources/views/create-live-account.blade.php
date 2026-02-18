@extends('layouts.crm.crm')
<link rel="stylesheet" href="{{ asset('assets/css/accounts.css') }}">
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Create Live MT5 Account</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($user->acc_limit <= $acc_count)
                <div class="row mt-4">
                    <div class="col-11">
                        <div class="alert alert-danger">
                            You have reached the maximum account limit of {{ $user->acc_limit }}. If you need to create new
                            account, please contact our support team.
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                
				<div class="col-sm-11">
					<div class="card">
						<div class="container py-3">
							<h1 class="as-title">Setup Your Account</h1>
							<form method="post" enctype="multipart/form-data"
								action="{{ route('create-live-account') }}">
								@csrf
								<div class="as-grid">
									@foreach ($results as $i => $acc)
										<div class="as-card {{ $i == 0 ? 'as-active' : '' }}"
											data-account="{{ $acc->ac_name }}">
											<div class="as-badge"><i data-lucide="shield-check"></i></div>

											<!-- Radio input -->
											<input type="radio" class="btn-check acc-types" name="options"
												id="option{{ $acc->ac_index }}" value="{{ $acc->ac_index }}"
												data-group="{{ $acc->ac_name }}"
												data-inquiry="{{ $acc->inquiry_status }}"
												{{ $i == 0 ? 'checked' : '' }}>

											<h3>{{ $acc->ac_name }}</h3>
											<p class="as-desc">{{ $acc->ac_description }}</p>
											<strong>${{ $acc->ac_min_deposit }}</strong>
											<span>Minimum Deposit</span>

											<div class="as-footer">
												<div class="as-features">
													<div class="as-feature">
														<i data-lucide="check-circle" class="as-icon"></i>
														<span><strong>Spread:</strong> {{ $acc->ac_spread }}</span>
													</div>
													<div class="as-feature">
														<i data-lucide="check-circle" class="as-icon"></i>
														<span><strong>Swap:</strong> {{ $acc->ac_swap }}</span>
													</div>

												</div>
											   @if($acc->image)
												<img class="as-image" src="{{ '/storage/uploads/groupimg/' . $acc->image }}" alt="{{ $acc->ac_name }}">
											@endif
											</div>
										</div>
									@endforeach
								</div>

								<div class="as-select-group">
									<label for="leverage">Select Leverage *</label>
									<select id="leverage" class="as-select" name="leverage" required>
										<option value="">Loading...</option>
									</select>
								</div>

								<button type="submit" class="as-button create-account-btn">Create Account</button>
							</form>
						</div>
					</div>
			
				</div>
			</div>
    </div>
@if ($errors->has('limit'))
<script>
    Swal.fire({
        icon: "error",
        title: "Account Limit Reached",
        text: "{{ addslashes($errors->first('limit')) }}"
    });

    // Disable button
    document.querySelectorAll('.create-account-btn').forEach(btn => {
        btn.disabled = true;
    });
</script>
@endif

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}'
            }).then(() => {
                window.location.href = '{{ route('liveAccounts') }}';
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: "Something Went Wrong !!!!",
                text: '{{ session('error') }}',
            });
        </script>
    @endif

    <script>
        lucide.createIcons();

        // Handle card click + radio selection
        document.querySelectorAll('.as-card').forEach(card => {
            card.addEventListener('click', () => {
                // Remove active from all cards
                document.querySelectorAll('.as-card').forEach(c => c.classList.remove('as-active'));

                // Add active to clicked card
                card.classList.add('as-active');

                // Check the corresponding radio
                const radio = card.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    // Trigger change event to update leverage
                    $(radio).trigger('change'); // jQuery trigger
                }
            });
        });

        // Handle radio change to load leverage
        $(".acc-types").change(function() {
            var inquiry_status = $(this).data("inquiry");
            var inquiry = $(this).data("group");
            var selectedValue = $(this).val();

            if (inquiry_status == 0) {
                $(".is_account").removeClass("d-none");
                $(".is_inquiry").addClass("d-none");

                $("#leverage").html("<option value=''>Loading...</option>");
                $.ajax({
                    url: "{{ route('get-leverage') }}?id=" + selectedValue,
                    type: "GET",
                    success: function(data) {
                        $("#leverage").html(""); // clear old options
                        $.each(data, function(key, value) {
                            $("#leverage").append("<option value='" + value.account_leverage +
                                "'>" + value.account_leverage + "</option>");
                        });
                    },
                    error: function(xhr) {
                        console.error("Failed to load leverage:", xhr.responseText);
                        $("#leverage").html("<option value=''>Error loading leverage</option>");
                    }
                });
            } else {
                $(".is_account").addClass("d-none");
                $(".is_inquiry").removeClass("d-none");
                var href = "/support?reg=" + inquiry;
                $(".contactus-btn").attr("href", href);
            }
        });

        // Trigger initial load for first account
        $(".acc-types:checked").trigger("change");

        // Handle create account button with SweetAlert loading
        $(document).ready(function() {
            $(".create-account-btn").on("click", function(e) {
                e.preventDefault(); // stop immediate form submit
                let form = $(this).closest("form"); // get parent form

                // Make sure a radio is selected
                if (!$(".acc-types:checked").length) {
                    Swal.fire({
                        icon: "warning",
                        title: "Select an Account",
                        text: "Please select an account type first."
                    });
                    return;
                }

                // Make sure leverage is selected
                if (!$("#leverage").val()) {
                    Swal.fire({
                        icon: "warning",
                        title: "Select Leverage",
                        text: "Please select a leverage option first."
                    });
                    return;
                }

                // Disable button
                $(this).prop("disabled", true).html(
                    '<span class="spinner-border spinner-border-sm"></span> Creating...');

                // SweetAlert loading popup
                Swal.fire({
                    title: "Please wait...",
                    html: "Your live account is being created shortly.",
                    icon: "info",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form after showing loading
                form.submit();
            });
        });
    </script>

@endsection
