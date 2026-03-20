@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <div class="title"><h4>{{ $pageTitle }}</h4></div>
        </div>
        <div class="col-md-6 col-sm-12 text-right">
            <a href="{{ route('parent.appointment.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus"></i> Book Appointment
            </a>
        </div>
    </div>
</div>

{{-- Status Tabs --}}
<div class="card-box mb-4">
    <div class="d-flex gap-2" style="gap: 8px;">
        @foreach(['all' => 'All', 'pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $key => $label)
            <a href="{{ route('parent.appointment.index', ['status' => $key]) }}"
               class="btn btn-sm {{ $currentStatus === $key ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $label }}
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
@if(session('fail'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('fail') }}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
@endif

@if($appointments->isEmpty())
    <div class="card-box text-center py-5">
        <i class="fa fa-calendar-times" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
        <h5 class="text-muted">No appointments found</h5>
        <p class="text-muted">Book your first appointment with your midwife</p>
        <a href="{{ route('parent.appointment.create') }}" class="btn btn-primary mt-2">Book Appointment</a>
    </div>
@else
    <div class="row">
        @foreach($appointments as $appt)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card-box" style="border-left: 4px solid {{ $appt->status === 'confirmed' ? '#16a34a' : ($appt->status === 'pending' ? '#f59e0b' : ($appt->status === 'rejected' ? '#dc2626' : '#6b7280')) }};">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="mb-1" style="font-size: 16px;">
                            <i class="fa fa-calendar"></i>
                            {{ $appt->appointment_date->format('M d, Y') }}
                        </h5>
                        <p class="text-muted mb-0" style="font-size: 14px;">
                            <i class="fa fa-clock"></i> {{ \Carbon\Carbon::parse($appt->appointment_time)->format('h:i A') }}
                        </p>
                    </div>
                    <span class="badge badge-pill
                        {{ $appt->status === 'confirmed' ? 'badge-success' : '' }}
                        {{ $appt->status === 'pending' ? 'badge-warning' : '' }}
                        {{ $appt->status === 'rejected' ? 'badge-danger' : '' }}
                        {{ $appt->status === 'cancelled' ? 'badge-secondary' : '' }}
                        {{ $appt->status === 'completed' ? 'badge-info' : '' }}
                    " style="font-size: 12px;">
                        {{ ucfirst($appt->status) }}
                    </span>
                </div>

                <p class="mb-1" style="font-size: 13px;">
                    <strong>Midwife:</strong> {{ $appt->midwife->name ?? 'N/A' }}
                </p>
                @if($appt->baby)
                <p class="mb-1" style="font-size: 13px;">
                    <strong>Baby:</strong> {{ $appt->baby->full_name }}
                </p>
                @endif
                <p class="text-muted mb-3" style="font-size: 13px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $appt->reason }}
                </p>

                <div class="d-flex" style="gap: 8px;">
                    <a href="{{ route('parent.appointment.show', $appt->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    @if($appt->status === 'pending')
                        <form action="{{ route('parent.appointment.destroy', $appt->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Cancel</button>
                        </form>
                    @endif
                    @if($appt->status === 'confirmed' && $appt->chatRoom)
                        <a href="{{ route('parent.chat.show', $appt->chatRoom->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="fa fa-comments"></i> Chat
                        </a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif
@endsection
