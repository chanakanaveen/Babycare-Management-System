@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Notifications')
@section('content')

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Notifications</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('midwife.home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Notifications
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card-box pd-20">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-20">
                <h5 class="mb-0">
                    <i class="dw dw-notification text-primary mr-2"></i>
                    All Notifications
                    @php
                        $unreadCount = $notifications->where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge badge-primary ml-2">{{ $unreadCount }} new</span>
                    @endif
                </h5>
                @if($notifications->isNotEmpty())
                    <form action="{{ route('midwife.notification.mark-all-read') }}" method="POST" id="mark-all-form">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary" id="mark-all-read-page-btn">
                            <i class="fa fa-check-double mr-1"></i> Mark All as Read
                        </button>
                    </form>
                @endif
            </div>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            {{-- Notification list --}}
            @if($notifications->isEmpty())
                <div class="text-center py-60">
                    <div style="font-size: 60px; opacity: 0.3;">🔔</div>
                    <h5 class="text-muted mt-15">No notifications yet</h5>
                    <p class="text-muted font-14">You'll see messages, appointment updates and other alerts here.</p>
                </div>
            @else
                <div class="notification-page-list">
                    @foreach($notifications as $notification)
                        @php
                            $isRead = $notification->is_read;
                            $type   = $notification->type ?? 'default';

                            $icon = '🔔';
                            $iconColor = '#6366f1';
                            if ($type === 'vaccine_scheduled')         { $icon = '💉'; $iconColor = '#2563eb'; }
                            elseif ($type === 'appointment_request')   { $icon = '📅'; $iconColor = '#16a34a'; }
                            elseif ($type === 'appointment_confirmed') { $icon = '✅'; $iconColor = '#16a34a'; }
                            elseif ($type === 'appointment_rejected')  { $icon = '❌'; $iconColor = '#dc2626'; }
                            elseif ($type === 'chat_message')          { $icon = '💬'; $iconColor = '#8b5cf6'; }
                            elseif ($type === 'reminder')              { $icon = '⏰'; $iconColor = '#ca8a04'; }
                        @endphp

                        <div class="notif-page-item {{ $isRead ? '' : 'notif-unread' }}" id="notif-item-{{ $notification->id }}">
                            <div class="d-flex align-items-start">
                                <div class="notif-icon-wrap mr-15">
                                    <span style="font-size: 28px; line-height: 1;">{{ $icon }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong class="notif-title">{{ $notification->title }}</strong>
                                            @if(!$isRead)
                                                <span class="badge badge-primary ml-2" style="font-size: 10px; vertical-align: middle;">New</span>
                                            @endif
                                        </div>
                                        <small class="text-muted notif-time ml-15" style="white-space: nowrap;">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="notif-message text-muted mb-10">{{ $notification->message }}</p>

                                    @if(!$isRead)
                                        <form action="{{ route('midwife.notification.mark-read', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-light border">
                                                <i class="fa fa-check mr-1"></i> Mark as read
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted font-12">
                                            <i class="fa fa-check-circle text-success mr-1"></i>
                                            Read {{ $notification->read_at ? $notification->read_at->diffForHumans() : '' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>

<style>
.notification-page-list {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.notif-page-item {
    padding: 18px 20px;
    border-bottom: 1px solid #f0f0f0;
    border-radius: 6px;
    transition: background 0.15s ease;
}

.notif-page-item:last-child {
    border-bottom: none;
}

.notif-page-item:hover {
    background: #f9fafb;
}

.notif-page-item.notif-unread {
    background: #eff6ff;
    border-left: 4px solid #2563eb;
    padding-left: 16px;
}

.notif-page-item.notif-unread:hover {
    background: #dbeafe;
}

.notif-icon-wrap {
    flex-shrink: 0;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 50%;
}

.notif-title {
    font-size: 14px;
    color: #1f2937;
}

.notif-message {
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 8px;
    color: #6b7280;
}

.notif-time {
    font-size: 12px;
    flex-shrink: 0;
}

.flex-1 {
    flex: 1;
    min-width: 0;
}

.py-60 {
    padding-top: 60px;
    padding-bottom: 60px;
}

.mr-15 { margin-right: 15px; }
.ml-15 { margin-left: 15px; }
.ml-2  { margin-left: 6px; }
.mr-2  { margin-right: 6px; }
.mb-20 { margin-bottom: 20px; }
.mt-15 { margin-top: 15px; }
.mr-1  { margin-right: 4px; }
</style>

@endsection
