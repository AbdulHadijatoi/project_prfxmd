@extends('layouts.crm.crm')
@push('styles')
<link rel="stylesheet" 
href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')

<div class="pc-container">
    <div class="pc-content">
        <div class="page-header mb-0 pb-0">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title h2">
                            <h4 class="mb-0">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		
		<div class="row">
			<div class="col-12">
				<div class="row text-muted px-2 mb-2">
					<table id="example" class="table table-striped">
						<thead>
							<tr>
								<th>Order From</th>
								<th>Type/Date</th>
								<th>Order number</th>
								<th>Price</th>
								<th>Fiat / Crypto Amount</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
						@if(!empty($orderlist))
						@foreach($orderlist as $list)
							<tr>
								<td>
									<a class="order-link">{{ $list->email }}</a><br>
								</td>
								<td>
									<div class=""><span class="{{ strtolower($list->wanttype) === 'buy' ? 'text-success' : 'text-danger' }} " >{{ $list->wanttype }} </span> USDT</div>
									<small>{{ date('Y-m-d', strtotime($list->created_at)) }}</small>
								</td>
								<td>
									<a href="#" class="order-link">{{ $list->orderId }}</a>
								</td>
								<td>{{ $list->orderprice }} {{ $list->orderpaycurrency }}</td>
								<td>
									{{ $list->orderpayamount }} {{ $list->orderpaycurrency }}<br>
									<small>{{ $list->orderreceiveamount }} {{ $list->orderreceivecurrency }}</small>
								</td>
								@php
									$statusClass = match (strtolower($list->status)) {
										'pending'   => 'text-info',
										'paid'      => 'text-success',
										'completed' => 'text-success',
										'cancelled' => 'text-danger',
										default     => 'text-secondary',
									};
								@endphp
								<td class="{{ $statusClass }} "><b>{{ ucfirst($list->status) }}</b></td>
								<td></td>
							</tr>
						@endforeach
						@else
						<tr>
							<td colspan="6"> No Orders Found!</td>
						</tr>
						@endif					
						</tbody>
					</table>
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
			showConfirmButton: true
		});
	</script>
@endif

@if (session('error'))
	<script>
		Swal.fire({
			icon: 'error',
			title: 'Error!',
			text: '{{ session('error') }}',
			showConfirmButton: true
		});
	</script>
@endif
@endsection
@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
@endpush('scripts')


