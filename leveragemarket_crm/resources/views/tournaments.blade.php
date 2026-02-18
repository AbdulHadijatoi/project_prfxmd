@extends('layouts.crm.crm')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title h5">
                                <h2 class="mb-0">Tournaments</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card support-tickets ribbon-box border ribbon-fill shadow-none pb-1">
                        <div class="row p-3">
                            @foreach ($tournaments as $tournament)
                                <div class="card-body mt-3">
                                    <div class="card custom-card shadow-none mb-0 ribbon-card">
                                        <div class="card-body p-4">
                                            <div class="ribbon ribbon-top-left">
                                                {{-- <span class="bg-{{ $tournament->ticket_label }}">{{ $tournament->ticket_status }}</span> --}}
                                            </div>
                                            <div class="card-subtitle fw-semibold mb-2">
                                                <div class="row">
                                                    <div class="col-7">
                                                        <div class="ms-5">
                                                            <h3>
                                                                {{ $tournament->name }}
                                                            </h3>
                                                            <div class="text-muted">
                                                                <span><i
                                                                        data-feather="calendar"></i>{{ $tournament->date }}</span>
                                                                {{-- <span class="ms-3"><i data-feather="user"></i>{{ $tournament->created_user }}</span> --}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 ">
                                                        <div class="d-flex w-100 my-4">
                                                            @if (isset($tournament->image) &&
                                                                    !empty($tournament->image))
                                                                <img style="max-height: 200px;"
                                                                    src="{{ asset('storage/' . $tournament->image) }}"
                                                                    alt="Tournament Image">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-8">
                                                    <div class="d-flex w-100 ms-5">
                                                        <div
                                                            class="d-flex align-items-center justify-content-between w-100 flex-wrap align-items-center">
                                                            <div class="me-3">
                                                                <p class="fs-16 mb-0">{{ $tournament->description }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer justify-content-between d-flex">
                                            <div>Registration Start Date: <span
                                                    class="fw-semibold badge bg-outline-info text-dark">{{ $tournament->starts_at }}</span>
                                            </div>
                                            <div>Registration End Date: <span
                                                    class="fw-semibold badge bg-outline-success text-dark">{{ $tournament->ends_at }}</span>
                                            </div>
                                            {{-- {{$tournament->id."           -  ".md5($tournament->id)}} --}}
                                            <a href="{{ url('/tournament_details?id=' . md5($tournament->id)) }}"
                                                class="btn bg-info text-white d-grid">
                                                Learn More <i
                                                    class="ri-arrow-right-line ms-2 d-inline-block align-middle"></i>
                                            </a>
                                            @if (empty($tournament->trade_id))
                                                <a href="#" data-id="{{ md5($tournament->id) }}"
                                                    class="btn bg-primary text-white d-grid enroll">
                                                    Enroll Now
                                                </a>
                                            @else
                                                <a href="#" class="btn bg-success text-white d-grid">
                                                    Enrolled
                                                </a>
                                            @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if ($tournaments->isEmpty())
                            <div class="card-body">
                                <div class="text-center me-4">
                                    <a href="/transactions/deposit#">
                                        <img src="/assets/images/empty.png" class="w-25" alt="img">
                                    </a>
                                </div>
                                <h6 class="text-center text-secondary f-w-400 mb-0 f-16">No Tournaments Available!</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('.enroll').on('click', function(e) {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure you want to enroll?',
            showCancelButton: true,
            confirmButtonText: 'Enroll'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('enroll_tournament') }}",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: response.success
                        }).then(() => {
                            window.location.href = '{{ route('tournaments') }}';
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: xhr.responseJSON.message,
                            text: xhr.responseJSON.error
                        });
                    }
                });
            }
        });
    });
</script>
@endsection
