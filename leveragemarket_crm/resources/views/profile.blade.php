@extends('layouts.crm.crm')
@section('content')

<style>
    .social-profile .img-profile-avtar {
    width: 90px;
    height: 90px;
}
</style>
    <div class="modal fade" id="walletUpdateModal" tabindex="-1" aria-labelledby="walletModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <h5 class="modal-title" id="walletModalLabel"></h5>
                        <div class="input-group mb-3 mt-3">
                            <span class="input-group-text otp-request" data-type="Wallet_Update">Send OTP</span>
                            <input type="number" class="form-control" name="otp" disabled required
                                data-type="Wallet_Update">
                            <input type="hidden" name="wallet_delete_id" value="">
                            <span class="input-group-text">
                                <i class="feather icon-info mb-auto mt-auto" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="You will receive OTP on your registered email address"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link-danger btn-pc-default"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmWalletUpdate">Submit</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="bankDeleteModal" tabindex="-1" aria-labelledby="bankDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <h5 class="modal-title" id="bankDeleteModalLabel">Are you sure you want to delete the bank details?
                        </h5>
                        <div class="input-group mb-3 mt-3">
                            <span class="input-group-text otp-request" data-type="Bank_Delete">Send OTP</span>
                            <input type="number" class="form-control" name="otp" disabled required
                                data-type="Bank_Delete">
                            <input type="hidden" name="bank_delete_id" value="">
                            <span class="input-group-text">
                                <i class="feather icon-info mb-auto mt-auto" data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="You will receive OTP on your registered email address"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link-danger btn-pc-default"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmBankDelete">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">My Profile</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="card social-profile">
                        <img src="{{ asset('assets/img-profile-cover-DJAPkiCO.png') }}" alt=""
                            class="w-100 card-img-top">
                        <div class="card-body pt-0">
                            <div class="row align-items-end">
                                <div class="col-md-auto text-md-start">
										
										<img class="img-fluid img-profile-avtar"
											 src="{{ !empty($user->profile_image) 
													? asset('storage/uploads/profile/'.$user->profile_image) 
													: asset('assets/images/user.png') }}"
											 alt="User image" />
                                </div>
                                <div class="col">
                                    <div class="row justify-content-between align-items-end">
                                        <div class="col-md-auto soc-profile-data">
                                            <h5 class="mb-1">{{ ucfirst($user->fullname) }}</h5>
                                            <p class="mb-0">{{ $user->email }}</p>
                                        </div>
                                        <div class="col-md-auto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="card-body py-0">
                                    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="profile-tab-3" data-bs-toggle="tab"
                                                href="#personal" role="tab" aria-selected="true">
                                                <i class="ti ti-id me-2"></i> Personal Details
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab-2" data-bs-toggle="tab" href="#kyc"
                                                role="tab" aria-selected="false" tabindex="-1">
                                                <i class="ti ti-file-text me-2"></i> KYC Verification
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab-4" data-bs-toggle="tab" href="#security"
                                                role="tab" aria-selected="false" tabindex="-1">
                                                <i class="ti ti-lock me-2"></i> Change Password
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab-5" data-bs-toggle="tab" href="#wallets"
                                                role="tab" aria-selected="false" tabindex="-1">
                                                <i class="ti ti-wallet me-2"></i> Wallet Details
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab-6" data-bs-toggle="tab"
                                                href="#bankdetails" role="tab" aria-selected="false" tabindex="-1">
                                                <i class="ti ti-building-bank me-2"></i> Bank Details
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="profile-tab-7" data-bs-toggle="tab"
                                                href="#twofactor" role="tab" aria-selected="false" tabindex="-1">
                                                <i class="ti ti-shield-lock me-2"></i> Two Factor Authentication
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                           
                            <div class="tab-content">
                                <div class="tab-pane active show" id="personal" role="tabpanel"
                                    aria-labelledby="profile-tab-3">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Personal Information</h5>
                                                </div>
                                                <form id="updateProfileForm" enctype="multipart/form-data">
													@csrf
													<div class="card-body">
														<div class="row">

															<!-- Profile Image -->
														   <div class="col-sm-12">
																<div class="form-group">
																	<label class="form-label">Profile Image</label>
																	<div class="profile-avatar-wrapper position-relative w-lg-25 w-100">		
																		<img id="profileAvatar" class="img-fluid rounded-circle"
																		 src="{{ !empty($user->profile_image) 
																				? asset('storage/uploads/profile/'.$user->profile_image) 
																				: asset('assets/images/user.png') }}"
																		 alt="User image" style="width:120px; height:120px; object-fit:cover; border: 1px solid #12a300;" />

																		<!-- Upload Button -->
																		<label for="profileImageUpload" class="btn btn-sm btn-primary position-absolute bottom-0">
																			<i class="ti ti-pencil"></i> Upload
																		</label>
																		<input type="file" id="profileImageUpload" class="d-none" accept="image/*" name="profile_image">
																	</div>
																</div>
															</div>

															<!-- Full Name -->
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="form-label">Full Name</label>
																	<input type="text" class="form-control" value="{{ $user->fullname }}" disabled>
																</div>
															</div>

															<!-- Email -->
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="form-label">Account Email</label>
																	<input type="text" class="form-control" value="{{ $user->email }}" disabled>
																</div>
															</div>

															<!-- Contact -->
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="form-label">Contact Number</label>
																	<input type="text" class="form-control" value="{{ $user->number }}" disabled>
																</div>
															</div>

															<!-- Gender -->
															<div class="col-sm-6">
																<div class="form-group">
																	<label class="form-label">Gender</label>
																	<select class="form-control" name="gender">
																		<option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
																		<option value="Female" {{$user->gender == 'Female' ? 'selected' : '' }}>Female</option>
																		<option value="Others" {{ $user->gender == 'Others' || $user->gender == null ? 'selected' : '' }}>Others</option>
																	</select>
																</div>
															</div>

															<!-- Update Button -->
															<div class="col-sm-12 text-end mt-3">
																<button type="submit" class="btn btn-primary">Update Profile</button>
															</div>

														</div>
													</div>
												</form>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="kyc" role="tabpanel" aria-labelledby="profile-tab-2">
                                    <div class="card">
                                       <div class="card-header">
                                        <div class="row">
                                            <div class="col-6">
                                                <h5>KYC Verification</h5>
                                            </div>

                                       
                                                <div class="col-6 text-end">
                   
                                                   {{-- @if ($user->sumsub_verify == 0 &&  $user->kyc_verify == 0)
                                                    <a href="/sumsub" class="btn btn-primary btn-page me-2">
                                                        <i class="ti ti-plus f-18"></i> Apply for KYC
                                                    </a> 
                                                  
                                                     @endif
                                                     @if($user->sumsub_verify == 2 && $user->kyc_verify == 0 )
                                                    <a href="/user/documentUpload" class="btn btn-primary btn-page">
                                                        <i class="ti ti-plus f-18"></i> Apply for Manual KYC
                                                    </a>
                                                     @endif
                                                      @if( $user->kycdocumentRequest == 0 && $user->kycdocumentRequest == 3)
                                                    <a href="/user/documentUpload" class="btn btn-primary btn-page">
                                                        <i class="ti ti-plus f-18"></i> Apply for Manual KYC
                                                    </a>
                                                     @endif --}}

                                                     @if ($user->sumsub_verify == 0 && $user->kyc_verify == 0)
                                                        <a href="/sumsub" class="btn btn-primary btn-page me-2">
                                                            <i class="ti ti-plus f-18"></i> Apply for KYC
                                                        </a>
                                                    @elseif ($user->sumsub_verify == 2 && $user->kyc_verify == 0 && $user->kycdocumentRequest != 1 )
                                                        <a href="/user/documentUpload" class="btn btn-primary btn-page">
                                                            <i class="ti ti-plus f-18"></i> Apply for Manual KYC
                                                        </a>
                                                    @elseif ($user->kycdocumentRequest == 0 && $user->kycdocumentRequest == 3)
                                                        <a href="/user/documentUpload" class="btn btn-primary btn-page">
                                                            <i class="ti ti-plus f-18"></i> Apply for Manual KYC
                                                        </a>
                                                    @endif
                                                </div>
                                           
                                        </div>
                                    </div>
                                        <div class="card-body table-card text-center">
                                            @if ($user->kyc_verify == 0 )

                                    @if ($idProof)
                                <div class="col-xl-6">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title w-100 d-flex justify-content-between">
                                                <span>
                                                    {{ $idProof->kyc_type }}
                                                    @if ($idProof->Status == 1)
                                                        <span class="badge ms-2 bg-outline-success"style="color: green;">Approved</span>
                                                    @elseif ($idProof->Status == 2)
                                                        <span class="badge ms-2 bg-outline-danger"  style="color: red;">Rejected</span>
                                                    @else
                                                        <span class="badge ms-2 bg-outline-primary" style="color: red;">Waiting for Approval</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body w-100 d-flex justify-content-between flex-wrap">
                                            @if ($idProof->kyc_frontside)
                                                <a target="_blank" class="w-100" style="max-width:48%;margin-right: 2%" href="{{ $idProof->kyc_frontside }}">
                                                    <img src="{{ $idProof->kyc_frontside }}" class="w-100">
                                                </a>
                                            @endif
                                            @if ($idProof->kyc_backside)
                                                <a target="_blank" class="w-100" style="max-width:48%" href="{{ $idProof->kyc_backside }}">
                                                    <img src="{{ $idProof->kyc_backside }}" class="w-100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if ($addressProof)
                                <div class="col-xl-6">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title w-100 d-flex justify-content-between">
                                                <span>
                                                    {{ $addressProof->kyc_type }}
                                                    @if ($addressProof->Status == 1)
                                                        <span class="badge ms-2 bg-outline-success" style="color: green;">Approved</span>
                                                    @elseif ($addressProof->Status == 2)
                                                        <span class="badge ms-2 bg-outline-danger"  style="color: red;">Rejected</span>
                                                    @else
                                                        <span class="badge ms-2 bg-outline-primary" style="color: red;">Waiting for Approval</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body w-100 d-flex justify-content-between flex-wrap">
                                            @if ($addressProof->front_image)
                                                <a target="_blank" class="w-100" style="max-width:48%;margin-right: 2%" href="{{ $addressProof->front_image }}">
                                                    <img src="{{ $addressProof->front_image }}" class="w-100">
                                                </a>
                                            @endif
                                            @if ($addressProof->back_image)
                                                <a target="_blank" class="w-100" style="max-width:48%" href="{{ $addressProof->back_image }}">
                                                    <img src="{{ $addressProof->back_image }}" class="w-100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if (!$idProof && !$addressProof)
                                <div class="auth-main">
                                    <div class="card-body">
                                        <div class="text-center me-4">
                                            <a href="user-profile#">
                                                <img src="{{ asset('assets/images/empty.png') }}" class="w-25" alt="img">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                                                {{-- <div class="auth-main">
                                                    <div class="card-body">
                                                        <div class="text-center me-4">
                                                            <a href="user-profile#"><img
                                                                    src="{{ asset('assets/images/empty.png') }}"
                                                                    class="w-25" alt="img"></a>
                                                        </div>
                                                        </a>
                                                    </div>
                                                </div> --}}
                                            @elseif ($user->kyc_verify == 1)
                                     @if ($idProof)
                                   
                                <div class="col-xl-6">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title w-100 d-flex justify-content-between">
                                                <span>
                                                    {{ $idProof->kyc_type }}
                                                    @if ($idProof->Status == 1)
                                                        <span class="badge ms-2 bg-outline-success" style="color: green;">Approved</span>
                                                    @elseif ($idProof->Status == 2)
                                                        <span class="badge ms-2 bg-outline-danger" style="color: rgb(128, 0, 0);">Not Approved</span>
                                                    @else
                                                        <span class="badge ms-2 bg-outline-primary" style="color: red;">Waiting for Approval</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body w-100 d-flex justify-content-between flex-wrap">
                                            @if ($idProof->kyc_frontside)
                                                <a target="_blank" class="w-100" style="max-width:48%;margin-right: 2%" href="{{ $idProof->kyc_frontside }}">
                                                    <img src="{{ $idProof->kyc_frontside }}" class="w-100">
                                                </a>
                                            @endif
                                            @if ($idProof->kyc_backside)
                                                <a target="_blank" class="w-100" style="max-width:48%" href="{{ $idProof->kyc_backside }}">
                                                    <img src="{{ $idProof->kyc_backside }}" class="w-100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if ($addressProof)
                                <div class="col-xl-6">
                                    <div class="card custom-card">
                                        <div class="card-header justify-content-between">
                                            <div class="card-title w-100 d-flex justify-content-between">
                                                <span>
                                                    {{ $addressProof->kyc_type }}
                                                    @if ($addressProof->Status == 1)
                                                        <span class="badge ms-2 bg-outline-success" style="color: green;">Approved</span>
                                                    @elseif ($addressProof->Status == 2)
                                                        <span class="badge ms-2 bg-outline-danger">Not Approved</span>
                                                    @else
                                                        <span class="badge ms-2 bg-outline-primary" style="color: red;">Waiting for Approval</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body w-100 d-flex justify-content-between flex-wrap">
                                            @if ($addressProof->front_image)
                                                <a target="_blank" class="w-100" style="max-width:48%;margin-right: 2%" href="{{ $addressProof->front_image }}">
                                                    <img src="{{ $addressProof->front_image }}" class="w-100">
                                                </a>
                                            @endif
                                            @if ($addressProof->back_image)
                                                <a target="_blank" class="w-100" style="max-width:48%" href="{{ $addressProof->back_image }}">
                                                    <img src="{{ $addressProof->back_image }}" class="w-100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @if($user->sumsub_verify == 1 )
                                                <div class="auth-main">
                                                    <div class="card-body">
                                                        <div class="text-center me-4">
                                                            <a href="user-profile#"><img
                                                                    src="{{ asset('assets/images/ben-02.png') }}"
                                                                    class="w-25" alt="img"></a>
                                                        </div>
                                                        <h6 class="text-center btn btn-light-success font-bold ps-5 pe-5">
                                                            KYC
                                                            Verified by Sumsub</h6>
                                                    </div>
                                                </div>

                                            @endif    
                                            @else
                                                <div class="auth-main">
                                                    <div class="card-body">
                                                        <div class="text-center me-4">
                                                            <a href="user-profile#"><img
                                                                    src="{{ asset('assets/images/empty.png') }}"
                                                                    class="w-25" alt="img"></a>
                                                        </div>
                                                        <h6 class="text-center text-secondary f-w-400 mb-0 f-16">No
                                                            documents
                                                            added</h6>
                                                    </div>
                                                </div>
                                            @endif
                                   @if($user->kyc_verify == 1 && $user->sumsub_verify == 2)
                                             <div class="auth-main">
                                                    <div class="card-body">
                                                        <div class="text-center me-4">
                                                            <a href="user-profile#"><img
                                                                    src="{{ asset('assets/images/ben-02.png') }}"
                                                                    class="w-25" alt="img"></a>
                                                        </div>
                                                        <h6 class="text-center btn btn-light-success font-bold ps-5 pe-5">
                                                            KYC
                                                            Verified by Admin</h6>
                                                    </div>
                                                </div>
                                     @endif

                                     @if($user->kyc_verify == 1 && $user->sumsub_verify == 0)
                                             <div class="auth-main">
                                                    <div class="card-body">
                                                        <div class="text-center me-4">
                                                            <a href="user-profile#"><img
                                                                    src="{{ asset('assets/images/ben-02.png') }}"
                                                                    class="w-25" alt="img"></a>
                                                        </div>
                                                        <h6 class="text-center btn btn-light-success font-bold ps-5 pe-5">
                                                            KYC
                                                            Verified by Admin</h6>
                                                    </div>
                                                </div>
                                     @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="security" role="tabpanel" aria-labelledby="profile-tab-4">
                                    <form id="changePasswordForm" method="post">
                                        @csrf
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Change Password</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Current Password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control"
                                                                    name="current_password" required="">
                                                                <div class="input-group-prepend">
                                                                    <span
                                                                        class="input-group-text showPassword icon-show-paswd h-100">
                                                                        <i class="ti ti-eye-off"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">New Password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control"
                                                                    name="new_password" required>
                                                                <div class="input-group-prepend">
                                                                    <span
                                                                        class="input-group-text showPassword icon-show-paswd h-100">
                                                                        <i class="ti ti-eye-off"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="form-label">Confirm Password</label>
                                                            <input type="password" name="new_password_confirmation"
                                                                required class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <h5>New password must contain:</h5>
                                                        <ul class="list-group list-group-flush">
                                                            <li class="list-group-item"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                At
                                                                least 8 characters</li>
                                                            <li class="list-group-item mb-0"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                At
                                                                least 1 lower letter (a-z)</li>
                                                            <li class="list-group-item"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                At
                                                                least 1 uppercase letter (A-Z)</li>
                                                            <li class="list-group-item"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                At
                                                                least 1 number (0-9)</li>
                                                            <li class="list-group-item"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                At
                                                                least 1 special character</li>
                                                            <li class="list-group-item"><i
                                                                    class="ti ti-circle-minus text-danger f-16 me-2"></i>
                                                                Passwords do not match</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer text-end btn-page">
                                                <button class="btn btn-outline-secondary">Cancel</button>
                                                <button type="submit" name="password_changed"
                                                    class="btn btn-primary">Update
                                                    Password</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="wallets" role="tabpanel" aria-labelledby="profile-tab-5">
                                    <div>
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h5>Wallet Details</h5>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <a href="user-profile#"
                                                            class="btn btn-primary d-inline-flex align-item-center"
                                                            data-bs-toggle="modal" data-bs-target="#addWalletModal">
                                                            <i class="ti ti-plus f-18"></i> Add Wallet Information
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body table-card">
                                                @if (count($wallet_accounts) > 0)
                                                    <div class="card-body">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</th>
                                                                    <th>Wallet Name</th>
                                                                    <th>Currency</th>
                                                                    <th>Network</th>
                                                                    <th>Address</th>
                                                                    <th>Status / Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($wallet_accounts as $acc)
                                                                    <tr>
                                                                        <td>CWA{{ sprintf('%04u', $acc->client_wallet_id) }}
                                                                        </td>
                                                                        <td>{{ $acc->wallet_name }}</td>
                                                                        <td>{{ $acc->wallet_currency }}</td>
                                                                        <td>{{ $acc->wallet_network }}</td>
                                                                        <td>{{ $acc->wallet_address }}</td>
                                                                        <td
                                                                            class="text-start {{ $acc->status == 0 ? 'text-warning' : ($acc->status == 1 ? 'text-success' : ($acc->status == 2 ? 'text-danger' : '')) }}">
                                                                            @if ($acc->status == 0)
                                                                                <a class="wallet-action"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="Inactive Wallet Address"
                                                                                    data-status="Activate" data-toggle="{{ md5($acc->client_wallet_id) }}">
                                                                                    <i class="f-24 ti ti-toggle-left"></i>
                                                                                </a>
                                                                            @else
                                                                                <a class="wallet-action"
                                                                                    data-bs-toggle="tooltip"
                                                                                    title="Active Wallet Address"
                                                                                    data-status="Inactivate" data-toggle="{{ md5($acc->client_wallet_id) }}">
                                                                                    <i class="f-24 ti ti-toggle-right"></i>
                                                                                </a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="auth-main">
                                                        <div class="card-body">
                                                            <div class="text-center me-4">
                                                                <a href="user-profile#"><img
                                                                        src="{{ asset('assets/images/nowallet.png') }}"
                                                                        class="w-25" alt="img"></a>
                                                            </div>
                                                            <h6 class="text-center text-secondary f-w-400 mb-0 f-16">Please
                                                                add
                                                                your Wallet Details</h6>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="bankdetails" role="tabpanel" aria-labelledby="profile-tab-6">
                                    <div>
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h5>Bank Details</h5>
                                                    </div>
                                                    <div class="col-6 text-end">
                                                        <a href="user-profile#"
                                                            class="btn btn-primary d-inline-flex align-item-center"
                                                            data-bs-toggle="modal" data-bs-target="#addBankModal2">
                                                            <i class="ti ti-plus f-18"></i> Add Bank Details
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body table-card">
                                                @if (count($bank_accounts) > 0)
                                                    <div class="card-body">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>#</td>
                                                                    <th>Account Holder Name</td>
                                                                    <th>Bank Name</td>
                                                                    <th>Account Number</td>
                                                                    <th>IBAN number</td>
                                                                    <th>SWIFT Code</td>
                                                                    <th>Action</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($bank_accounts as $acc) { ?>
                                                                <tr>
                                                                    <td>CBA<?= sprintf('%04u', $acc->id) ?></td>
                                                                    <td><?= $acc->ClientName ?></td>
                                                                    <td><?= $acc->bankName ?></td>
                                                                    <td><?= $acc->accountNumber ?></td>
                                                                    <td><?= $acc->code ?></td>
                                                                    <td><?= $acc->swift_code ?></td>
                                                                    <td><a class="bank-delete"
                                                                            data-toggle="{{ md5($acc->id) }}">
                                                                            <i class="ti ti-trash"></i>
                                                                        </a></td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <div class="auth-main">
                                                        <div class="card-body">
                                                            <div class="text-center me-4">
                                                                <a href="user-profile#"><img
                                                                        src="{{ asset('assets/images/nobank.png') }}"
                                                                        class="w-25" alt="img"></a>
                                                            </div>
                                                            <h6 class="text-center text-secondary f-w-400 mb-0 f-16">Please add your Wallet Details</h6>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="twofactor" role="tabpanel" aria-labelledby="profile-tab-7">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title">Two Factor Authentication</div>
                                        </div>
                                        <div class="card-body">
                                            <style>
                                                .customer-2fa-enable-toggle .form-check-input { width: 2.5em; height: 1.25em; }
                                                .customer-2fa-enable-toggle .form-check-label { font-size: 1.125rem; font-weight: 500; }
                                            </style>
                                            <div class="customer-2fa-enable-toggle form-check form-switch mb-4">
                                                <input class="form-check-input" type="checkbox" id="customer_2fa_enable" name="customer_2fa_enable"
                                                    {{ $customerMfaEnabled ? 'checked' : '' }}>
                                                <label class="form-check-label" for="customer_2fa_enable">Enable Two Factor Authentication</label>
                                            </div>

                                            <div class="customer-2fa-setup-wrap" style="{{ $customerMfaEnabled ? '' : 'display: none;' }}">
                                                @if($customerMfaEnabled)
                                                    <p class="text-muted mb-0">Two-factor authentication is enabled for your account. Uncheck the switch above to disable it.</p>
                                                @elseif($customerHasMfaSecret)
                                                    <div class="customer-2fa-reenable">
                                                        <p class="text-muted small mb-2">You've already set up 2FA. Enter the current code from your authenticator app to turn it back on.</p>
                                                        <div class="mb-2">
                                                            <label for="customer-2fa-reenable-code" class="form-label">Enter 6-digit code</label>
                                                            <input type="text" id="customer-2fa-reenable-code" class="form-control" placeholder="000000" maxlength="6" autocomplete="one-time-code">
                                                        </div>
                                                        <button type="button" class="btn btn-primary w-100" id="customer-2fa-reenable-btn">Re-enable 2FA</button>
                                                    </div>
                                                @else
                                                    <div class="customer-2fa-setup-not-done">
                                                        <div class="mb-3 text-center" id="customer-2fa-qr-container" style="display: none;">
                                                            <img id="customer-2fa-qr" src="" alt="QR Code" class="img-fluid" style="max-width: 200px;">
                                                        </div>
                                                        <button type="button" class="btn btn-outline-primary w-100 mb-3" id="customer-2fa-generate-qr">Generate QR code</button>
                                                        <p class="text-muted small mb-2">Scan the QR code with your authenticator app (e.g. Google Authenticator), then enter the 6-digit code below.</p>
                                                        <div class="mb-2">
                                                            <label for="customer-2fa-code" class="form-label">Enter 6-digit code</label>
                                                            <input type="text" id="customer-2fa-code" class="form-control" placeholder="000000" maxlength="6" autocomplete="one-time-code">
                                                        </div>
                                                        <button type="button" class="btn btn-primary w-100" id="customer-2fa-verify-btn">Verify & Enable</button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
            }).then(() => {
                location.reload();
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
    <script>

        $("#updateGenderBtn").click(function () {
    $.ajax({
        url: "#",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            gender: $("#gender").val()
        },
        success: function (res) {
            Swal.fire({
                icon: 'success',
                title: res.message
            }).then(() => location.reload());
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Failed to update gender'
            });
        }
    });
});
        $("#changePasswordForm").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('password.change') }}",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.success,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.log(xhr);
                    const errorMessage = xhr.responseJSON?.message || 'Something went wrong';
                    Swal.fire({
                        icon: 'error',
                        title: errorMessage,
                    });
                }
            });
        });
		
		$("#profileupdate").submit(function(e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('password.change') }}",
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: response.success,
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    console.log(xhr);
                    const errorMessage = xhr.responseJSON?.message || 'Something went wrong';
                    Swal.fire({
                        icon: 'error',
                        title: errorMessage,
                    });
                }
            });
        });

        $(".wallet-action").click(function(e) {
            e.preventDefault();
            var trans = $(this).data("toggle");
            var status = $(this).data("status");
            $("[name='wallet_delete_id']").val(trans);
            var modalTitle = `Are You Sure You Want to ${status} The Wallet?`;
            $("#walletModalLabel").text(modalTitle);
            $("#walletUpdateModal").modal("show");
        });
        $("#confirmWalletUpdate").click(function() {
            var otp = $("[name='otp'][data-type='Wallet_Update']").val();
            var trans= $("[name='wallet_delete_id']").val();
            updateWalletStatus(trans, otp);
            $("#walletUpdateModal").modal("hide");
        });
        $(".bank-delete").click(function(e) {
            e.preventDefault();
            trans = $(this).data("toggle");
            $("[name='bank_delete_id']").val(trans);
            $("#bankDeleteModal").modal("show");
        });
        $("#confirmBankDelete").click(function() {
            var accountId = $("[name='bank_delete_id']").val();
            var otp = $("[name='otp'][data-type='Bank_Delete']").val();
            var deleteUrl = "{{ route('bank.delete', ['enc' => '__ENC__', 'otp' => '__OTP__']) }}"
                .replace('__ENC__', accountId)
                .replace('__OTP__', otp);
            window.location.href = deleteUrl;
            $("#bankDeleteModal").modal("hide");
        });

        function updateWalletStatus(trans, otp) {
            $.ajax({
                type: "POST",
                url: "{{ route('wallet.updateStatus') }}",
                data: {
                    toggle_wallet: "true",
                    id: trans,
                    otp: otp
                },
                beforeSend: function() {
                    swal.fire({
                        showConfirmButton: false,
                        showCancelButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        backdrop: false,
                        didOpen: function() {
                            swal.showLoading();
                        }
                    });
                },
                success: function(data) {
                    swal.close();
                    if (data.success == true) {
                        swal.fire({
                            icon: "success",
                            title: "Wallet Address Status Updated",
                            backdrop: false
                        }).then((val) => {
                            location.reload();
                        });
                    } else {
                        swal.fire({
                            icon: "warning",
                            title: "Something went wrong",
                            text: data.message ?? ''
                        }).then((val) => {
                            location.reload();
                        });
                    }
                }
            });
        }
 $("#updateProfileForm").on("submit", function (e) {
    e.preventDefault();

    const fileInput = document.getElementById("profileImageUpload");
    const file = fileInput.files[0];

    if (!file) {
        submitProfileAjax();
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {

        const img = new Image();
        img.src = e.target.result;

        img.onload = function () {

            const maxWidth = 1200;
            const maxHeight = 1300;

            if (img.width > maxWidth || img.height > maxHeight) {

                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Image Size',
                    text: 'Image must be 1200 × 1300 or below',
                });

                return; // 🚫 STOP HERE
            }

            // ✅ size ok → submit ajax
            submitProfileAjax();
        };
    };

    reader.readAsDataURL(file);
});


function submitProfileAjax() {

    let formData = new FormData(document.getElementById("updateProfileForm"));

    $.ajax({
        url: "{{ route('profileupate') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            Swal.fire({
                icon: 'success',
                title: 'Profile Updated',
                text: response.success ?? 'Your profile has been updated',
            }).then(() => {
                location.reload();
            });
        },
        error: function (xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: xhr.responseJSON?.message || 'Something went wrong',
            });
        }
    });
}

    // Preview image before upload
    $("#profileImageUpload").change(function() {
        const [file] = this.files;
        if (file) {
            $("#profileAvatar").attr("src", URL.createObjectURL(file));
        }
    });

    // Customer Two Factor Authentication
    (function() {
        var customerMfaBaseUrl = '{{ url("/customer") }}';
        var $cb = $('#customer_2fa_enable');
        var $setupWrap = $('.customer-2fa-setup-wrap');
        var $qrContainer = $('#customer-2fa-qr-container');
        var $qrImg = $('#customer-2fa-qr');
        var $generateBtn = $('#customer-2fa-generate-qr');
        var $codeInput = $('#customer-2fa-code');
        var $verifyBtn = $('#customer-2fa-verify-btn');
        var setupWasEnabled = {{ $customerMfaEnabled ? 'true' : 'false' }};
        var $reenableCode = $('#customer-2fa-reenable-code');
        var $reenableBtn = $('#customer-2fa-reenable-btn');

        if (!$cb.length) return;

        $cb.on('change', function() {
            if ($(this).is(':checked')) {
                $setupWrap.show();
                if (!$setupWrap.find('.customer-2fa-setup-not-done').length && !$setupWrap.find('.customer-2fa-reenable').length) return;
                if ($qrContainer.length) $qrContainer.hide();
                if ($qrImg.length) $qrImg.attr('src', '');
                if ($codeInput.length) $codeInput.val('');
                if ($reenableCode.length) $reenableCode.val('');
            } else {
                $setupWrap.hide();
                if (setupWasEnabled) {
                    $.ajax({
                        url: customerMfaBaseUrl + '/mfa-disable',
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function() { setupWasEnabled = false; location.reload(); },
                        error: function(xhr) {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to disable 2FA.';
                            Swal.fire({ icon: 'error', title: msg });
                        }
                    });
                }
            }
        });

        $generateBtn.on('click', function() {
            $generateBtn.prop('disabled', true);
            $.get(customerMfaBaseUrl + '/mfa-setup-qr', function(data) {
                $generateBtn.prop('disabled', false);
                if (data.qrCodeUrl) {
                    $qrImg.attr('src', data.qrCodeUrl);
                    $qrContainer.show();
                }
            }).fail(function() {
                $generateBtn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: 'Failed to generate QR code.' });
            });
        });

        function updateCustomerVerifyBtnState() {
            $verifyBtn.prop('disabled', ($codeInput.val() || '').trim().length !== 6);
        }
        $codeInput.on('input', updateCustomerVerifyBtnState);

        $verifyBtn.on('click', function() {
            var code = ($codeInput.val() || '').trim();
            if (code.length !== 6) return;
            $verifyBtn.prop('disabled', true);
            $.ajax({
                url: customerMfaBaseUrl + '/mfa-authentication',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', code: code },
                success: function(data) {
                    $verifyBtn.prop('disabled', false);
                    if (data && data.success) {
                        Swal.fire({ icon: 'success', title: 'Two-step verification activated in your account' }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'warning', title: (data && data.message) || 'Verification failed' });
                    }
                },
                error: function(xhr) {
                    $verifyBtn.prop('disabled', false);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired code.';
                    Swal.fire({ icon: 'error', title: msg });
                }
            });
        });

        function updateCustomerReenableBtnState() {
            if ($reenableBtn.length) $reenableBtn.prop('disabled', ($reenableCode.val() || '').trim().length !== 6);
        }
        $reenableCode.on('input', updateCustomerReenableBtnState);
        if ($reenableBtn.length) $reenableBtn.prop('disabled', true);

        $reenableBtn.on('click', function() {
            var code = ($reenableCode.val() || '').trim();
            if (code.length !== 6) return;
            $reenableBtn.prop('disabled', true);
            $.ajax({
                url: customerMfaBaseUrl + '/mfa-reenable',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', code: code },
                success: function(data) {
                    $reenableBtn.prop('disabled', false);
                    if (data && data.success) {
                        Swal.fire({ icon: 'success', title: 'Two-factor authentication has been re-enabled.' }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'warning', title: (data && data.message) || 'Verification failed' });
                    }
                },
                error: function(xhr) {
                    $reenableBtn.prop('disabled', false);
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired code.';
                    Swal.fire({ icon: 'error', title: msg });
                }
            });
        });
    })();
    </script>
@endsection
