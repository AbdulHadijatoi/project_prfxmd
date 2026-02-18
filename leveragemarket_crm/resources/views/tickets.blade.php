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

        .small-input {
            height: 32px;
            padding: 4px 8px;
            font-size: 13px;
        }

        /* Animated MT5 logo */
        .rotate-img {
            animation: rotateLogo 3s linear infinite;
        }

        @keyframes rotateLogo {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Card & Table Styles */
        .custom-card {
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        #ajaxDatatable tbody tr:hover {
            background-color: rgba(18, 163, 0, 0.05);
        }

        /* Rounded Select for LengthMenu */
        .form-select-sm {
            border-radius: 8px;
        }

        /* Pagination Button Styling */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 6px;
            margin: 0 2px;
            padding: 4px 12px;
            font-size: 0.875rem;
            transition: 0.2s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #12a300 !important;
            color: #fff !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        /* Search box styling */
        .dataTables_filter input {
            border-radius: 8px;
            height: 36px;
        }

        /* Small input for date filter */
        .small-input {
            height: 36px;
            padding: 4px 8px;
            font-size: 13px;
            border-radius: 6px;
        }

        /* MT5 Logo Animation */
        .rotate-img {
            display: block;
            /* ensures inline spacing doesn’t affect layout */
            margin: auto;
            /* center within container */
            animation: rotateLogo 3s linear infinite;
            transform-origin: center center;
            /* rotation around center */
            width: 30px;
            /* fixed width */
            height: 30px;
            /* fixed height */
        }

        @keyframes rotateLogo {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>

    {{-- Add Ticket Modal --}}
    <div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="addTicketModalLabel1">Add New Ticket</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="POST" action="{{ url('/support') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row gy-4">
                            <div class="col-6">
                                <label for="input-subject_name" class="form-label">Name</label>
                                <input type="text" class="form-control" value="{{ Auth::user()->fullname }}" disabled>
                                <input type="hidden" name="subject_name" value="{{ Auth::user()->fullname }}">
                            </div>
                            <div class="col-6">
                                <label for="input-type" class="form-label">Type</label>
                                <select class="form-control" name="ticket_type_id" required>
                                    <option value="">Select Type</option>
                                    @foreach ($ticket_types as $type)
                                        <option value="{{ $type['id'] }}">{{ $type['ticket_type'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label for="input-type" class="form-label">Trade Id </label>
                                <select class="form-control" name="live_account">
                                    <option value="">Select Type</option>
                                    @foreach ($liveaccounts as $tid)
                                        <option value="{{ $tid->trade_id }}">{{ $tid->trade_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="input-description" class="form-label">Description</label>
                                <textarea class="form-control" name="discription" required rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="email" value="{{ session('clogin') }}" />
                        <input type="submit" class="btn bg-primary text-white" name="add_ticket" value="Add">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tickets Page --}}
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-0 pb-0">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <div class="page-header-title h2">
                                <h4 class="mb-0">Tickets</h4>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="d-grid">
                                <button data-bs-toggle="modal" data-bs-target="#addTicketModal"
                                    class="btn btn-primary d-grid">
                                    <span class="text-truncate w-100">Create new Ticket</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tickets Table --}}
            <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <div class="card-header justify-content-end">
                            <div class="d-flex justify-content-end mx-1">
                                <input type="date" class="form-control small-input w-25" name="dtStartDate" id="dtStartDate"
                                    placeholder="Start Date" value="{{ $_GET['startdate'] ?? '' }}">
                                <input type="date" class="ms-2 form-control small-input w-25" name="dtEndDate"
                                    id="dtEndDate" placeholder="End Date" value="{{ $_GET['enddate'] ?? '' }}">
                                <button type="button" class="ms-2 btn btn-dark btn-sm dtDateFilter"
                                    style="color: #12a300">Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ajaxDatatable" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr style="font-weight: 800;">
                                            <td>Name</td>
                                            <td>Trade ID</td>
                                            <td>Ticket Type</td>
                                            <td>Description</td>
                                            <td>Created At</td>
                                             <td>Status</td>
                                            <td>Last Follow-Up Date</td>
                                            <td>Last Follow-Up By</td>
                                            <td>Action</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->subject_name }}</td>
                                                <td>
    <div class="row align-items-center">
        <div class="col-auto pe-0 text-center">
            @if($ticket->live_account)
                <img src="{{ asset('assets/images/mt5.png') }}" alt="user-image" class="rotate-img rounded">
            @endif
        </div>

        <div class="col ps-2 text-center fw-bold">
            @if($ticket->live_account)
                {{ $ticket->live_account }}
            @else
                General
            @endif
        </div>
    </div>
</td>

                                                <td>{{ $ticket->ticket_type }}</td>
                                                <td>{{ $ticket->discription }}</td>
                                                <td>{{ $ticket->created_at }}</td>
                                                <td>{{ $ticket->Status? $ticket->Status : 'wait' }}</td>
                                                <td>{{ $ticket->last_followup }}</td>
                                                <td>{{ !empty($ticket->last_assign_up) ? $ticket->last_assign_up : 'Processing_Follow-Up' }}
                                                </td>
                                                <td>
                                                    <a href="{{ url('/ticket_details?id=' . md5($ticket->ticket_id)) }}"
                                                        class="btn bg-info text-white d-grid">
                                                        View <i
                                                            class="ri-arrow-right-line ms-2 d-inline-block align-middle"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <div class="text-center me-4">
                                                        <a href="/transactions/deposit#">
                                                            <img src="/assets/images/mt5.png" alt="user-image"
                                                                class="wid-50 hei-50 rounded rotate-img">
                                                        </a>
                                                    </div>
                                                    <h6 class="text-secondary fw-400 mb-0 f-16">No Tickets Added!</h6>
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

    {{-- SweetAlert Notifications --}}
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

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            var table = $('#ajaxDatatable').DataTable({
                pageLength: 10,
                lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],

                language: {
                    lengthMenu: "Show _MENU_ entries",
                    paginate: {
                        previous: '<button class="btn btn-outline-dark btn-sm me-2">Previous</button>',
                        next: '<button class="btn btn-outline-dark btn-sm">Next</button>'
                    },
                    info: "Showing _START_ to _END_ of _TOTAL_ tickets"
                },

                dom: "<'row mb-3'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f>>" +
                    "<'row'<'col-12'tr>>" +
                    "<'row mt-3'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",

                createdRow: function (row, data, dataIndex) {
                    $(row).addClass('align-middle');
                }
            });

            // Fix Bootstrap 5 length menu styling
            $('#ajaxDatatable_length select').addClass('form-select form-select-sm');

            // Date Filter using DataTables API
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var startDate = $('#dtStartDate').val();
                    var endDate = $('#dtEndDate').val();
                    var createdAt = data[4]; // Assuming 'Created At' is in 5th column (index 4)

                    if (!startDate && !endDate) return true;

                    var created = new Date(createdAt);
                    if (startDate && created < new Date(startDate)) return false;
                    if (endDate && created > new Date(endDate)) return false;

                    return true;
                }
            );

            $('#dtStartDate, #dtEndDate').on('change', function () {
                table.draw();
            });

        });
    </script>
@endsection