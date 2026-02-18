@extends('layouts.admin.admin')
@section('content')
<style>
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
<div class="main-content app-content">
    <div class="container-fluid">
      <div class="page-header">
        <h1 class="page-title">{{ $pageTitle  }} <span class="text-primary"> ({{ $ticketCount }})</span></h1>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page"> {{ $pageTitle }}</li>
        </ol>
      </div>
  
      
      <div class="row">
                <div class="col-xl-12">
                    <div class="card custom-card">
                        <!-- <div class="card-header justify-content-end">
                            <div class="d-flex justify-content-end mx-1">
                                <input type="text" class="form-control small-input w-25" name="dtStartDate" id="dtStartDate"
                                    placeholder="Start Date" value="{{ $_GET['startdate'] ?? '' }}">
                                <input type="text" class="ms-2 form-control small-input w-25" name="dtEndDate"
                                    id="dtEndDate" placeholder="End Date" value="{{ $_GET['enddate'] ?? '' }}">
                                <button type="submit" class="ms-2 btn btn-dark btn-sm dtDateFilter">Filter</button>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="ajaxDatatable" class="ajaxDataTable table table-bordered text-nowrap w-100">
                                    <thead>
                                        <tr style="font-weight: 800;">
                                            <td>Action</td>
                                            <td>Name</td>
                                            <td>Trade ID</td>
                                            <td>Ticket Type</td>
                                            <td>Description</td>
                                            <td>Created At</td>
                                               <td>Status</td>
                                            <td>Last Follow-Up Date</td>
                                            <td>Last Follow-Up By</td>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tickets as $ticket)
                                            <tr>
                                                 <td>
                                                    <a href="{{ route('admin.ticket_details', ['id' => md5($ticket->ticket_id)]) }}" class="btn btn-primary">
                                                      <i class="fa fa-eye"></i></a>
                                                </td>
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
                                                <td>{{ !empty($ticket->Status) ? $ticket->Status : 'Processing' }}</td>
                                                <td>{{ !empty($ticket->last_followup) ? $ticket->last_followup : 'No_Follow_Up' }}</td>
                                             
                                                <td>{{ !empty($ticket->last_assign_up) ? $ticket->last_assign_up : 'Processing_Follow_Up' }}
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
    <div class="row mb-5 p-3">
        <nav>
            <ul class="pagination mb-0 d-flex justify-content-end">
            @for ($i = 1; $i <= $total_pages; $i++)
                <li class="page-item {{ $current_page == $i ? 'active' : '' }}">
                    <a class="page-link"
                       href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor
        </ul>
        </nav>
    </div>

    </div>
  </div>
    @include('admin.shared.script');
@endsection



 