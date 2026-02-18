@extends('layouts.admin.admin')
@section('content')
<div class="container-fluid">
    <div class="page-header">
        <h1 class="page-title">Activity Details</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"></li>
            <li class="breadcrumb-item active">View Details</li>
        </ol>
    </div>

    <div class="card">
        <div class="card-header">
            <a href="{{ route('activity.logs') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
        <div class="card-body">
            <h4>User: {{ $log->display_name ?? 'N/A' }}</h4>
            <p>Email: {{ $log->email }}</p>
            <p>IP: {{ $log->ip }}</p>
            <p>Action: {{ $log->action }}</p>
            <p>Status: {{ $log->status }}</p>
            <p>Created At: {{ $log->created_date_js }}</p>

            <hr>
            <h5>Datalog Details:</h5>
            @if(!empty($log->datalog))
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($log->datalog as $key => $detail)
                            <tr>
                                <td>{{ $detail['field'] ?? $key }}</td>
                                <td>{{ $detail['old'] ?? '-' }}</td>
                                <td>{{ $detail['new'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No details available.</p>
            @endif
        </div>
    </div>
</div>
@endsection
