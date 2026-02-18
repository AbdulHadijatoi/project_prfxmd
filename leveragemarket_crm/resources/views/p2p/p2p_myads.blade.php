@extends('layouts.crm.crm')
@push('styles')
<link rel="stylesheet" 
href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush
@section('content')

<div class="pc-container">
    <div class="pc-content">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header d-flex justify-content-between align-items-center">
						<h4>P2P Merchant Listings</h4>
						<a href="{{ route('p2pmerchant') }}" class="btn btn-primary">Add New</a>
					</div>

					<div class="card-body">
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>ID</th>
									<th>Want</th>
									<th>Asset</th>
									<th>Fiat</th>
									<th>Price Type</th>
									<th>Price</th>
									<th>Total Amount</th>
									<th>Status</th>
									<th width="160px">Action</th>
								</tr>
							</thead>

							<tbody>
								@forelse($list as $row)
								<tr>
									<td>{{ $row->id }}</td>
									<td>{{ $row->wanttype }}</td>
									<td>{{ $row->cryptoval }}</td>
									<td>{{ $row->currency_code }}</td>
									<td>{{ ucfirst($row->pricetype) }}</td>
									<td>{{ $row->quoteprice }}</td>
									<td>{{ $row->total_amount }}</td>
									<td>{{ $row->transferstatus }}</td>

									<td>
										<!-- <a href="{{ route('p2pmerchantedit', $row->id) }}" class="btn btn-sm btn-warning text-black">Edit</a> 

										<form action="{{ route('p2pmerchantdelete', $row->id) }}" method="POST" class="d-inline">
											@csrf
											@method('DELETE')

											<button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger text-black">
												Delete
											</button>
										</form>-->
									</td>
								</tr>
								@empty
								<tr><td colspan="9" class="text-center">No Data Found</td></tr>
								@endforelse
							</tbody>

						</table>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
@endsection
@push('scripts')

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
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
@endpush('scripts')


