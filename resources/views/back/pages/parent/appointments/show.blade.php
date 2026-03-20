@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-12">
            <div class="title"><h4>{{ $pageTitle }}</h4></div>
            <nav aria-label="breadcrumb"><ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('parent.appointment.index') }}">Appointments</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol></nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card-box">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Appointment Information</h5>
                <span class="badge badge-pill
                    {{ $appointment->status === 'confirmed' ? 'badge-success' : '' }}
                    {{ $appointment->status === 'pending' ? 'badge-warning' : '' }}
                    {{ $appointment->status === 'rejected' ? 'badge-danger' : '' }}
                    {{ $appointment->status === 'cancelled' ? 'badge-secondary' : '' }}
                    {{ $appointment->status === 'completed' ? 'badge-info' : '' }}
                " style="font-size: 14px; padding: 8px 16px;">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <table class="table table-borderless">
                <tr><td style="width:150px; font-weight:600;">Date</td><td>{{ $appointment->appointment_date->format('l, M d, Y') }}</td></tr>
                <tr><td style="font-weight:600;">Time</td><td>{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</td></tr>
                <tr><td style="font-weight:600;">Duration</td><td>{{ $appointment->duration_minutes }} minutes</td></tr>
                <tr><td style="font-weight:600;">Midwife</td><td>{{ $appointment->midwife->name ?? 'N/A' }}</td></tr>
                @if($appointment->baby)
                <tr><td style="font-weight:600;">Baby</td><td>{{ $appointment->baby->full_name }}</td></tr>
                @endif
                <tr><td style="font-weight:600;">Reason</td><td>{{ $appointment->reason }}</td></tr>
                @if($appointment->midwife_notes)
                <tr><td style="font-weight:600;">Midwife Notes</td><td>{{ $appointment->midwife_notes }}</td></tr>
                @endif
                @if($appointment->rejection_reason)
                <tr>
                    <td style="font-weight:600; color: #dc2626;">Rejection Reason</td>
                    <td style="color: #dc2626;">{{ $appointment->rejection_reason }}</td>
                </tr>
                @endif
            </table>

            @if($appointment->status === 'confirmed' && $appointment->chatRoom)
            <div class="mt-3">
                <a href="{{ route('parent.chat.show', $appointment->chatRoom->id) }}" class="btn btn-success">
                    <i class="fa fa-comments"></i> Open Chat with Midwife
                </a>
            </div>
            @endif

            @if($appointment->status === 'pending')
            <div class="mt-3">
                <form action="{{ route('parent.appointment.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="fa fa-times"></i> Cancel Appointment</button>
                </form>
            </div>
            @endif
        </div>
    </div>

    {{-- Status Timeline --}}
    <div class="col-md-4">
        <div class="card-box">
            <h5 class="mb-4">Status Timeline</h5>
            @php
                $steps = [
                    ['label' => 'Requested', 'icon' => 'fa-paper-plane', 'done' => true, 'date' => $appointment->created_at],
                    ['label' => 'Confirmed', 'icon' => 'fa-check-circle', 'done' => in_array($appointment->status, ['confirmed', 'completed']), 'date' => $appointment->confirmed_at],
                    ['label' => 'Completed', 'icon' => 'fa-flag-checkered', 'done' => $appointment->status === 'completed', 'date' => $appointment->status === 'completed' ? $appointment->updated_at : null],
                ];
                if ($appointment->status === 'rejected') {
                    $steps[1] = ['label' => 'Rejected', 'icon' => 'fa-times-circle', 'done' => true, 'date' => $appointment->updated_at];
                }
                if ($appointment->status === 'cancelled') {
                    $steps[1] = ['label' => 'Cancelled', 'icon' => 'fa-ban', 'done' => true, 'date' => $appointment->cancelled_at];
                }
            @endphp

            @foreach($steps as $step)
            <div class="d-flex align-items-start mb-3" style="opacity: {{ $step['done'] ? '1' : '0.4' }};">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: {{ $step['done'] ? '#16a34a' : '#e5e7eb' }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 12px;">
                    <i class="fa {{ $step['icon'] }}" style="color: {{ $step['done'] ? '#fff' : '#9ca3af' }}; font-size: 14px;"></i>
                </div>
                <div>
                    <strong style="font-size: 14px;">{{ $step['label'] }}</strong>
                    @if($step['date'])
                        <p class="text-muted mb-0" style="font-size: 12px;">{{ $step['date']->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
