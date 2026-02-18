@php
    use Carbon\Carbon;
@endphp
@extends('layouts.crm.crm')
@section('content')
<style>
        #wallet_transactions .td-wrap {
            max-width: 75px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .wallet-plus td {
            --bs-text-opacity: 1;
            color: rgba(var(--bs-success-rgb), var(--bs-text-opacity)) !important;
        }

        .wallet-minus td {
            --bs-text-opacity: 1;
            color: rgba(var(--bs-danger-rgb), var(--bs-text-opacity)) !important;
        }



        .fxtran-filter-form {
            display: flex;
            flex-wrap: wrap;
			margin-bottom: 0!important;
            gap: 10px;
            align-items: center;
        }

        .fxtran-input {
            padding: 8px 12px;
            border: 1px solid #12a300;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            min-width: 150px;
        }

        .fxtran-input:focus {
            box-shadow: 0 0 0 2px rgba(18, 163, 0, 0.2);
        }

        .fxtran-btn-outline {
            padding: 8px 16px;
            border: 1px solid #12a300;
            color: #12a300;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.25s ease;
            text-decoration: none;
        }

        .fxtran-btn-outline:hover {
            background: #12a300;
            color: #fff;
        }

        .fxtran-export-wrap {
            margin-top: 5px;
        }
    </style>
@section('content')
<div class="pc-container">
	<div class="pc-content">
		<div class="page-header mb-0 pb-0">
			<div class="page-block">
				<div class="row align-items-center">
					<div class="col-md-12">
						<div class="page-header-title h2">
							<h4 class="mb-0">All Transactions</h4>
						</div>
					</div>
				</div>
			</div>
		</div>
	
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body border-bottom pb-0">
						<div class="d-flex flex-wrap align-items-center justify-content-between"> 
							<div class="fxtran-filter-wrap">
								<form method="GET" class="fxtran-filter-form">								
									
									<select name="filter_type" class="fxtran-input">
										<option value="">All Type</option>
										<option value="Deposit" {{ request('filter_type') == 'Deposit' ? 'selected' : '' }}>Deposit</option>
										<option value="Withdrawal" {{ request('filter_type') == 'Withdrawal' ? 'selected' : '' }}>Withdrawal</option>
										<option value="Transfer" {{ request('filter_type') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
									</select>
									
									<select name="filter_paymode" class="fxtran-input">
										<option value="">Payment Method</option>
										<p>Deposit</p>
										<option value="Wallet Deposit" {{ request('filter_paymode') == 'Wallet Deposit' ? 'selected' : '' }}>Wallet Deposit</option>
										<option value="Trade Deposit" {{ request('filter_paymode') == 'Trade Deposit' ? 'selected' : '' }}>Trade Deposit</option>										
										<option value="Wallet Deposit (Admin)" {{ request('filter_paymode') == 'Wallet Deposit (Admin)' ? 'selected' : '' }}>Wallet Deposit (Admin)</option>
										<option value="Trade Deposit (Admin)" {{ request('filter_paymode') == 'Trade Deposit (Admin)' ? 'selected' : '' }}>Trade Deposit (Admin)</option>
										<option value="External Deposit" {{ request('filter_paymode') == 'External Deposit' ? 'selected' : '' }}>External Deposit</option>
										
										<p>Withdrawal</p>
										<option value="Wallet Withdrawal" {{ request('filter_paymode') == 'Wallet Withdrawal' ? 'selected' : '' }}>Wallet Withdrawal</option>								
										<option value="Wallet Withdrawal (Admin)" {{ request('filter_paymode') == 'Wallet Withdrawal (Admin)' ? 'selected' : '' }}>Wallet Withdrawal (Admin)</option>
										<option value="Trade Withdrawal (Admin)" {{ request('filter_paymode') == 'Trade Withdrawal (Admin)' ? 'selected' : '' }}>Trade Withdrawal (Admin)</option>
										<option value="External Withdrawal" {{ request('filter_paymode') == 'External Withdrawal' ? 'selected' : '' }}>External Withdrawal</option>
										
										<p>Transfer</p>
										<option value="Wallet to Account" {{ request('filter_paymode') == 'Wallet to Account' ? 'selected' : '' }}>Wallet to Account</option>								
										<option value="Account to Wallet" {{ request('filter_paymode') == 'Account to Wallet' ? 'selected' : '' }}>Account to Wallet</option>
										<option value="Account to Account" {{ request('filter_paymode') == 'Account to Account' ? 'selected' : '' }}>Account to Account</option>
										
									</select>
									
									<select name="filter_duration" class="fxtran-input filter_duration">
										<option value="">All Duration</option>
										<option value="today" {{ request('filter_duration') == 'today' ? 'selected' : '' }}>Today</option>
										<option value="yesterday" {{ request('filter_duration') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
										<option value="week" {{ request('filter_duration') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
										<option value="month" {{ request('filter_duration') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
										<option value="customrange" {{ request('filter_duration') == 'customrange' ? 'selected' : '' }}>Custom</option>
									</select>
									
									<div class="customrange" style="display:none;">									
										<input type="date" name="from" class="fxtran-input">
										<input type="date" name="to" class="fxtran-input">
									</div>
									<button type="submit" class="fxtran-btn-outline">
										Filter
									</button>

								</form>
							</div>

							<!--<div class="fxtran-export-wrap mt-3 mt-lg-0">
								<a href="" class="fxtran-btn-outline">
									Export Excel
								</a>
							</div> -->

						</div>
						
						<hr>

						<div class="row">
							<div class="col-md-6 col-lg-3">
								<div class="card">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<div class="avtar avtar-s bg-light-primary">
												<i class="feather icon-credit-card f-24"></i>
											</div>
											<div>
												<h4 class="text-center">Deposit</h4>
											</div>
										</div>
										<hr class="p-0 m-0" style="" />
										<h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalCredit }}</h4>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="card">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<div class="avtar avtar-s bg-light-primary">
												<i class="ti ti-wallet f-24"></i>
											</div>
											<div>
												<h4 class="text-center">Withdraw</h4>
											</div>
										</div>
										<hr class="p-0 m-0" style="" />
										<h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalDebit }}</h4>
									</div>
								</div>
							</div>
							<div class="col-md-6 col-lg-3">
								<div class="card">
									<div class="card-body">
										<div class="d-flex align-items-center justify-content-between mb-2">
											<div class="avtar avtar-s bg-light-primary">
												<i class="feather icon-shuffle f-24"></i>
											</div>
											<div>
												<h4 class="text-center">Transfer</h4>
											</div>
										</div>
										<hr class="p-0 m-0" style="" />
										<h4 class="mb-1 f-w-400 text-center mt-3">${{ $totalTransferCredit }}</h4>
									</div>
								</div>
							</div>
							
						</div>
					
						<div class="d-flex align-items-center justify-content-between mt-3">
						<table class="table table-responsive">
							<thead>
								<tr>
									<th>#ID</th>
									<th>Date</th>
									<th>Type</th>
									<th>Amount</th>
									<th>Currency</th>
									<th>Method</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($ledger as $row)
								<tr>
									<td>TXN0{{ $loop->iteration }}</td>									
									<td>
										<h6 class="f-w-500">{{Carbon::parse($row->created_at)->format('Y-m-d') }}</h6>
										<p class="text-muted mb-0">
										  <small>{{ Carbon::parse($row->created_at)->format('H:i A') }}</small>
										</p>
									</td>
									<td>{{ $row->transtype }}</td>
									<td><h6 class="f-w-500 f-16">{{ $row->valamount }}</h6></td>
									<td><h6 class="f-w-500 f-16">USD</h6></td>
									<td><h6 class="f-w-500 f-16">{{ $row->particulars }}</h6></td>
									<td class="{{ $row->Status == 0 ? 'text-warning' : ($row->Status == 1 ? 'text-success' : 'text-danger') }}">
										<p>{{ $row->Status == 0 ? 'Pending' : ($row->Status == 1 ? 'Success' : 'Rejected') }}</p>
									</td>
									<td></td>
								</tr>
								@endforeach
							</tbody>
						</table>
						</div>
						<div class="d-flex justify-content-between align-items-center mt-3">
							<div>
								Showing {{ $ledger->firstItem() }} to {{ $ledger->lastItem() }}
								of {{ $ledger->total() }} entries
							</div>
							<div>
								{{ $ledger->links() }}
							</div>
						</div>
						
						<div class="d-flex justify-content-between align-items-center mt-3">
							<div>
								Notes:
							</div>
						</div>
						<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
							<div><b>W2A</b> - Wallet to Account</div>
							<div><b>A2W</b> - Account to Wallet</div>
							<div><b>C2C</b> - Client to Client</div>
							<div><b>A2A</b> - Account to Account</div>
							<div><b>W2D</b> - Wallet to Deposit</div>
							<div><b>P2P</b> - Peer to Peer</div>
						</div>
					</div>
				</div>
			</div>
		</div>
  </div>
</div>
<script>
	$(document).ready(function () {
		let today = new Date().toISOString().split('T')[0];
		$('input[name="from"]').attr('max', today);
		$('input[name="to"]').attr('max', today);
		
		$(document).on("change", ".filter_duration", function () {
			let optionval = $(this).val();

			if (optionval === "customrange") {
				$(".customrange").show();
			} else {
				$(".customrange").hide();
			}
		});
		$(document).on("change", 'input[name="from"], input[name="to"]', function() {
			let from = $('input[name="from"]').val();
			let to   = $('input[name="to"]').val();

			if (from && to && from > to) {
				alert("From date cannot be greater than To date");
				$('input[name="to"]').val('');
			}
		});
		if ($(".filter_duration").val() === "customrange") {
			$(".customrange").show();
		}
	});
	
	$(document).on("submit", ".fxtran-filter-form", function () {

		Swal.fire({
			title: "Fetching data...",
			text: "Your request data will be shown shortly",
			allowOutsideClick: false,
			allowEscapeKey: false,
			showConfirmButton: false,
			didOpen: () => {
				Swal.showLoading();
			}
		});

	});
</script>
@endsection
