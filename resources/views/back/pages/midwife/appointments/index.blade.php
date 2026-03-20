@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title"><h4>{{ $pageTitle }}</h4></div>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <a href="{{ route('midwife.availability') }}" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-clock"></i> Manage Availability
            </a>
        </div>
    </div>
</div>

{{-- Status Tabs --}}
<div class="card-box mb-4">
    <div class="d-flex" style="gap: 8px;">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'rejected' => 'Rejected'] as $key => $label)
            <a href="{{ route('midwife.appointment.index', ['status' => $key]) }}"
               class="btn btn-sm {{ $currentStatus === $key ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $label }}
                @if($key === 'pending')
                    @php $pendingCount = $appointments->where('status', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="badge badge-danger ml-1">{{ $pendingCount }}</span>
                    @endif
                @endif
            </a>
        @endforeach
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if($appointments->isEmpty())
<div class="card-box text-center py-5">
    <i class="fa fa-calendar" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
    <h5 class="text-muted">No appointments found</h5>
</div>
@else
<div class="table-responsive card-box">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Parent</th>
                <th>Baby</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($appointments as $appt)
            <tr>
                <td>
                    <strong>{{ $appt->appointment_date->format('M d, Y') }}</strong><br>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}</small>
                </td>
                <td>{{ $appt->parentUser->name ?? 'N/A' }}</td>
                <td>{{ $appt->baby->full_name ?? 'N/A' }}</td>
                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $appt->reason }}</td>
                <td>
                    <span class="badge
                        {{ $appt->status === 'confirmed' ? 'badge-success' : '' }}
                        {{ $appt->status === 'pending' ? 'badge-warning' : '' }}
                        {{ $appt->status === 'rejected' ? 'badge-danger' : '' }}
                        {{ $appt->status === 'cancelled' ? 'badge-secondary' : '' }}
                        {{ $appt->status === 'completed' ? 'badge-info' : '' }}
                    ">{{ ucfirst($appt->status) }}</span>
                </td>
                <td>
                    <div class="d-flex" style="gap: 4px;">
                        @if($appt->status === 'pending')
                            <form action="{{ route('midwife.appointment.confirm', $appt->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-sm btn-success" title="Confirm"><i class="fa fa-check"></i></button>
                            </form>
                            <button class="btn btn-sm btn-danger reject-btn" title="Reject" data-id="{{ $appt->id }}"><i class="fa fa-times"></i></button>
                        @endif
                        @if($appt->status === 'confirmed')
                            <form action="{{ route('midwife.appointment.complete', $appt->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn btn-sm btn-info" title="Complete"><i class="fa fa-flag-checkered"></i></button>
                            </form>
                            @if($appt->chatRoom)
                            <a href="{{ route('midwife.chat.show', $appt->chatRoom->id) }}" class="btn btn-sm btn-outline-success" title="Chat">
                                <i class="fa fa-comments"></i>
                            </a>
                            @endif
                        @endif
                        <a href="{{ route('midwife.appointment.show', $appt->id) }}" class="btn btn-sm btn-outline-primary" title="Details"><i class="fa fa-eye"></i></a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Rejection Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="reject-form" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Reason for rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required minlength="5" placeholder="Please provide a reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('myscript')
<script>
$(function() {
    $('.reject-btn').click(function() {
        var id = $(this).data('id');
        $('#reject-form').attr('action', '{{ url("midwife/appointments") }}/' + id + '/reject');
        $('#rejectModal').modal('show');
    });
});
</script>
@endsection
