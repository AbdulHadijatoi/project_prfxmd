@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Merchant Account</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">P2P</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Merchant Account</li>
                </ol>
            </div>
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="" class="ajaxDataTable table table-bordered text-nowrap w-100">
									<thead>
										<th>Advertisers</th>
										<th>Price</th>
										<th>Available / Order Limit</th>
										<th>Payment</th>
										<th>Action</th>
									</thead>
									<tbody>
									@if(!empty($merchantlist))
									@foreach($merchantlist as $mercacc)
										<tr>
											<td><img src="https://ui-avatars.com/api/?name=LM&background=444&color=fff" class="rounded-circle me-2" width="35" height="35">
											<div>
												<h6 class="mb-0 fw-bold"> {{ $mercacc->merchantcompany }} <span class="badge bg-warning text-dark">Pro</span></h6>
												<small class="text-muted">0 orders | 100% completion</small><br>
												<small class="text-success">✔ 99.96% | ⏱ {{ $mercacc->time_limit }} min</small>
											</div></td>
											<td>₹ {{ $mercacc->quoteprice }}</td>
											<td>
												<div class="fw-semibold">{{ $mercacc->total_amount }} {{ $mercacc->cryptoval }}</div>
												<small class="text-muted">{{ $mercacc->min_limit }} – {{ $mercacc->max_limit }} INR</small>
											</td>
											<td>
												@foreach (json_decode($mercacc->payment_method, true) as $method)
													<span class="badge bg-secondary me-1">
														{{ ucfirst($method) }}
													</span>
												@endforeach 
											</td>
											<td></td>
										</tr>
									@endforeach
									@else
										<tr>
											<td colspan="5"><p>No Records Found!!</p></td>
										</tr>									
									@endif
									</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection
