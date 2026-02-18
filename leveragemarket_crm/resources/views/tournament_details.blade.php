@extends('layouts.crm.crm')
@section('content')
    <style>
        .ribbon-top-left {
            top: -7px;
            inset-inline-start: -7px;
        }

        .ribbon {
            width: 80px;
            height: 80px;
            overflow: hidden;
            position: absolute;
            z-index: 1;
        }

        .ribbon.ribbon-danger span {
            background-color: rgb(var(--danger-rgb));
        }

        .ribbon-top-left span {
            inset-inline-end: -12px;
            top: 20px;
            transform: rotate(-45deg);
        }

        .ribbon span {
            position: absolute;
            display: block;
            width: 120px;
            padding: 6px 0;
            z-index: 2;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            color: #fff;
            font: 500 12px / 1 "Lato", sans-serif;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
            text-transform: uppercase;
            text-align: center;
        }

        #wallet_transactions .td-wrap {
            max-width: 75px;
            overflow: hidden;
            white-space: nowrap;
            overflow: hidden;
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

        .chat .msg_cotainer {
            margin-block-start: auto;
            margin-block-end: 15px;
            margin-inline-start: 10px;
            background-color: #edeef3;
            padding: 10px;
            position: relative;
            border-radius: 20px;
        }

        .chat .msg_time {
            position: absolute;
            inset-inline-start: 0;
            inset-block-end: -18px;
            color: var(--text-muted);
            font-size: 10px;
        }
    </style>
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentModalLabel">Attachment Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <embed id="attachmentFile" src="" type="" width="100%">
                </div>
            </div>
        </div>
    </div>
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Tournament Details</h4>
                            </div>
                        </div>
                    </div>
                    <div class="row chat p-3">
                        <div class="card" id="chat-card">
                            <div class="action-header clearfix">
                                <div class="col-12 mt-3">
                                    <div class="card custom-card shadow-none mb-0 ribbon-card">
                                        <div class="card-body p-4">
                                            <div class="card-subtitle fw-semibold mb-2">
                                                <div class="row">
                                                    <div class="col-8">
                                                        <div class="ms-5">
                                                            <h3>{{ $tournament->name }}
                                                            </h3>
                                                            <div class="text-muted">
                                                                <span><i
                                                                        data-feather="calendar"></i>{{ $tournament->date }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 ">
                                                        <div class="d-flex w-100 my-4">
                                                            @if (isset($tournament->image) &&
                                                                    !empty($tournament->image) &&
                                                                    file_exists(public_path('storage/' . $tournament->image)))
                                                                <img style="max-height: 200px;"
                                                                    src="{{ asset('storage/' . $tournament->image) }}"
                                                                    alt="Tournament Image">
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">

                                            </div>
                                        </div>
                                        <div class="card-footer justify-content-between d-flex">
                                            <div>Registration Start Date: <span
                                                    class="fw-semibold badge bg-outline-info text-dark">{{ $tournament->starts_at }}</span>
                                            </div>
                                            <div>Registration End Date: <span
                                                    class="fw-semibold badge bg-outline-success text-dark">{{ $tournament->ends_at }}</span>
                                            </div>
                                            <div>
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
                                </div>
                            </div>
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
