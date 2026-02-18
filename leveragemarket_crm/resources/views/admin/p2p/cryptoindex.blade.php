@extends('layouts.admin.admin')
@section('content')
    <div class="main-content app-content">
        <div class="container-fluid">
            <div class="page-header">
                <h1 class="page-title">Crypto Currency</h1>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">P2P</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crypto</li>
                </ol>
            </div>
           
            <div class="row">                
				<div class="col-md-3 col-sm-12">
                    <div class="card custom-card">
						<div class="card-header">
							<h6>Add/Edit Crypto</h6>
						</div>
                        <div class="card-body">
                            <form action="{{ isset($editCrypto) ? route('admin.p2p.cryptoupdate', $editCrypto->id) : route('admin.p2p.cryptostore') }}" method="POST" enctype="multipart/form-data">
								@csrf
								
								<div class="mb-3">
									<label class="form-label">Name</label>
									<input type="text" name="name" class="form-control" 
										   value="{{ $editCrypto->name ?? '' }}" required />
								</div>
								<div class="mb-3">
									<label class="form-label">Symbol</label>
									<input type="text" name="symbol" class="form-control" 
										   value="{{ $editCrypto->symbol ?? '' }}" />
								</div>
								
								<div class="mb-3">
									<label class="form-label">Min Price</label>
									<input type="text" name="minprice" class="form-control" 
										   value="{{ $editCrypto->minprice ?? '' }}" />
								</div>
								
								<div class="mb-3">
									<label class="form-label">Max Price</label>
									<input type="text" name="maxprice" class="form-control" 
										   value="{{ $editCrypto->maxprice ?? '' }}" />
								</div>
								
								<div class="mb-3">
									<label class="form-label">Default Price</label>
									<input type="text" name="defaultprice" class="form-control" 
										   value="{{ $editCrypto->defaultprice ?? '' }}" />
								</div>
								
								<div class="mb-3">
									<label class="form-label">Icon</label>
									
									@if(isset($editCrypto) && $editCrypto->icon)
										<div class="mb-2">
											<img src="{{ asset('storage/cryptos/'.$editCrypto->icon) }}" width="40" />
										</div>
									@endif

									<input type="file" name="icon" class="form-control" />
								</div> 
								<div class="mb-3">
									<label class="form-label">Status</label>
									<select name="status" class="form-control" required>
										<option value="1" {{ isset($editCrypto) && $editCrypto->status == 1 ? 'selected' : '' }}>Active</option>
										<option value="0" {{ isset($editCrypto) && $editCrypto->status == 0 ? 'selected' : '' }}>Inactive</option>
									</select>
								</div>

								<hr class="mb-3">
								<div class="d-flex gap-2">
									@if(isset($editCrypto))
										<a href="{{ route('admin.p2p.cryptoindex') }}" class="btn btn-secondary w-100 ">Cancel</a>
									@endif
									<button class="btn btn-primary w-100">
										{{ isset($editCrypto) ? 'Update' : 'Create' }}
									</button> 
								</div>
							</form>
                        </div> 
                    </div>
                </div>
				<div class="col-md-9 col-sm-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="" class="table table-bordered text-nowrap w-100">
									<thead>
										<tr>
											<th>Icon</th>
											<th>Symbol</th>
											<th>Name</th>
											<th>Price Details</th>
											<th width="150">Actions</th>
										</tr>
									</thead>
									<tbody>
										@forelse($cryptos as $c)
											<tr>
												<td>
													@if($c->icon)
														<img src="{{ asset('storage/cryptos/'.$c->icon) }}" width="35">
													@endif
												</td>
												<td>{{ $c->symbol }}</td>
												<td>{{ $c->name }}</td>
												<td>Min: {{ $c->minprice }} - Max: {{ $c->maxprice }} <br /> Default: {{ $c->defaultprice }} </td>
												<td>
													@if($c->status == 1)
														<span class="badge bg-success">Active</span>
													@else
														<span class="badge bg-danger">Inactive</span>
													@endif
												</td>
												<td>
													<a href="{{ route('admin.p2p.cryptoindex', ['id' => $c->id]) }}" class="btn btn-sm btn-warning">Edit</a>
												</td>
											</tr>
											@empty
											<tr>
												<td colspan="5" class="text-center text-muted">
													No records found
												</td>
											</tr>
											@endforelse
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
