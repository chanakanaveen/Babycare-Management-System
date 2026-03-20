@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-12">
            <div class="title"><h4>{{ $pageTitle }}</h4></div>
            <nav aria-label="breadcrumb"><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('midwife.appointment.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol></nav>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Appointment Information</h5>
                <span class="badge badge-pill
                    {{ $appointment->status === 'confirmed' ? 'badge-success' : '' }}
                    {{ $appointment->status === 'pending' ? 'badge-warning' : '' }}
                    {{ $appointment->status === 'rejected' ? 'badge-danger' : '' }}
                    {{ $appointment->status === 'completed' ? 'badge-info' : '' }}
                " style="font-size: 14px; padding: 8px 16px;">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <table class="table table-borderless">
                <tr><td style="width:150px; font-weight:600;">Date</td><td>{{ $appointment->appointment_date->format('l, M d, Y') }}</td></tr>
                <tr><td style="font-weight:600;">Time</td><td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td></tr>
                <tr><td style="font-weight:600;">Parent</td><td>{{ $appointment->parentUser->name ?? 'N/A' }}</td></tr>
                @if($appointment->baby)
                <tr><td style="font-weight:600;">Baby</td><td>{{ $appointment->baby->full_name }}</td></tr>
                @endif
                <tr><td style="font-weight:600;">Reason</td><td>{{ $appointment->reason }}</td></tr>
            </table>

            <hr>
            <div class="d-flex" style="gap: 8px;">
                @if($appointment->status === 'pending')
                    <form action="{{ route('midwife.appointment.confirm', $appointment->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-success"><i class="fa fa-check"></i> Confirm</button>
                    </form>
                    <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                        <i class="fa fa-times"></i> Reject
                    </button>
                @endif
                @if($appointment->status === 'confirmed')
                    <form action="{{ route('midwife.appointment.complete', $appointment->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-info"><i class="fa fa-flag-checkered"></i> Mark Complete</button>
                    </form>
                    @if($appointment->chatRoom)
                    <a href="{{ route('midwife.chat.show', $appointment->chatRoom->id) }}" class="btn btn-success">
                        <i class="fa fa-comments"></i> Open Chat
                    </a>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

@if($appointment->status === 'pending')
{{-- Rejection Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('midwife.appointment.reject', $appointment->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
