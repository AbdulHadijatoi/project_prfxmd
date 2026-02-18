@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">IB Withdrawal Details</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">IB Withdrawal Details</li>
                </ol>
            </div>
        </div>
        @if (isset($details) && !empty($details))
            <div class="row">
                <div class="col-10 mx-auto">
                    <div class="card custom-card">
                        <div class="card-body">
                            <h6 class="card-title fw-medium">IB WITHDRAW TICKET #{{ $details->id }}</h6>
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="wideget-user-desc d-flex align-items-center">
                                        <div class="wideget-user-img">
                                            <img src="/admin_assets/assets/images/users/client.png" alt="img"
                                                style="width:50px">
                                        </div>
                                        <div class="user-wrap">
                                            <h4 class="fw-normal">{{ $details->fullname }}</h4>
                                            <h6 class="text-muted mb-3 fw-normal">{{ $details->email }}</h6>
                                        </div>
                                    </div>
                                </div>
								
                            </div>
							<div class="table-responsive tbl-responsive">
                                <table class="table text-nowrap" cellpadding="10">
                                    <tbody>
                                        <tr></tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">Contact</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <span><i
                                                                    class="fa fa-phone text-primary px-2"></i>{{ $details->number }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">Withdraw Method</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            {{ $details->withdraw_type }}</span>
                                                        </div>
                                                        <div class="lh-1 mt-3">
                                                            <div class="mb-3"><b>Withdraw Currency:</b>
                                                                <span>INR</span>
                                                            </div>
                                                            <div class="mb-3"><b>Withdraw Amount in INR:</b>
                                                                <span>₹{{ $details->amount_in_other_currency }}</span>
                                                            </div>
                                                            <div class="mb-5"><b>Withdraw Amount in USD:</b>
                                                                <span>${{ $details->withdraw_amount }}</span>
                                                            </div>
															
															<div class="mb-2"><b>Account Transfer To:</b>
                                                                <span>{{ $details->withdraw_to }}</span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">PAYMENT STATUS</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <?php if ($details->Status == 1) { ?>
                                                            <span class="badge bg-success">APPROVED</span>
                                                            <?php } elseif ($details->Status == 2) { ?>
                                                            <span class="badge bg-danger">REJECTED</span>
                                                            <?php } elseif ($details->Status == 0) { ?>
                                                            <span class="badge bg-primary">WAITING FOR APPROVAL</span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>                                            
                                            <?php if ($details->Status == 0) {  ?>
                                            <td>
                                            </td>
                                            <td>
                                                <div class="btn-list ms-auto my-auto">
                                                    <button
                                                        onclick="takeAction('{{ $details->email }}', '{{ $details->amount_in_other_currency }}', '{{ $details->withdraw_amount }}', 1, {{ $details->id }})"
                                                        type="button"
                                                        class="btn btn-success btn-space m-1">Approve</button>
                                                    <button
                                                        onclick="takeAction('{{ $details->email }}', '{{ $details->amount_in_other_currency }}', '{{ $details->withdraw_amount }}', 2,{{ $details->id }})"
                                                        type="submit"
                                                        class="btn btn-danger btn-space m-1">Reject</button>
                                                </div>
                                            </td>
                                            <?php } else { ?>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">ADMIN REMARKS</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <span>{{ $details->AdminRemark }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
											<td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">Action By</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <span>{{ $details->admin_email }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <div class="lh-1">
                                                            <span class="fs-11 text-muted">ADMIN ACTION TAKEN</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <span>{{ $details->approved_name }}</span>
                                                        </div>
                                                        <div class="lh-1 mt-2">
                                                            <span>{{ $details->Js_Admin_Remark_Date }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php } ?>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-10 mx-3">
                    <h4>No details found or you are not authorized to access this page</h4>
                </div>
            </div>
        @endif
    </div>
    <script>
        function takeAction(email, amount, usdamount, status, rowId) {
            Swal.fire({
                title: `Are you sure you want to ${status === 1 ? "approve" : "reject"} this transaction?`,
                html: `
            <form id="updateTransactionForm" method="post" action="{{ route('admin.ibupdateWithdrawal') }}">
              @csrf
              <input type="hidden" name="email" value="${email}">
              <input type="hidden" name="rowId" value="${rowId}">
              <input type="hidden" name="amount" value="${amount}">
              <input type="hidden" name="usdamount" value="${usdamount}">
              <input type="hidden" name="status" value="${status}">
              <input type="hidden" name="did" value="{{ request('id') }}">
              <input type="hidden" name="action" value="update_transaction">
              <div class="col-12 mt-3 text-start">
                  <label for="transaction_id" class="form-label">Withdraw Reference ID</label>
                  <input type="text" id="transaction_id" name="transaction_id" class="form-control" placeholder="Add Reference ID">
              </div>
              <div class="col-12 mt-2 text-start">
                  <label for="description" class="form-label">Description</label>
                  <textarea id="description" name="description" rows="3" class="mt-2 form-control" placeholder="Add a description"></textarea>
              </div>
              </form>
          `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                preConfirm: () => {
                    const description = document.querySelector('#updateTransactionForm textarea').value;
                    const transaction_id = document.querySelector('#transaction_id').value;
                    if (!description || !transaction_id) {
                        Swal.showValidationMessage('Please fill out all fields');
                        return false;
                    }
                    return true;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('#updateTransactionForm').submit();
                }
            });
        }
    </script>
@endsection
