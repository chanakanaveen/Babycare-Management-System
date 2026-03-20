@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle)
@section('content')
<div class="page-header">
    <div class="row">
        <div class="col-md-12"><div class="title"><h4>{{ $pageTitle }}</h4></div></div>
    </div>
</div>

@if($chatRooms->isEmpty())
<div class="card-box text-center py-5">
    <i class="fa fa-comments" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
    <h5 class="text-muted">No active chats yet</h5>
    <p class="text-muted">Chat rooms are created when appointments are confirmed.</p>
</div>
@else
<div class="card-box" style="padding: 0;">
    @foreach($chatRooms as $room)
    @php
        $otherUser = $userType === 'parent' ? $room->midwife : $room->parentUser;
        $unreadCount = $room->messages()->where('sender_type', '!=', $userType)->where('is_read', false)->count();
        $routeName = $userType === 'parent' ? 'parent.chat.show' : 'midwife.chat.show';
    @endphp
    <a href="{{ route($routeName, $room->id) }}"
       style="display: flex; align-items: center; padding: 16px 20px; border-bottom: 1px solid #f3f4f6; text-decoration: none; color: inherit; transition: background 0.15s;
              {{ $unreadCount > 0 ? 'background: #eff6ff;' : '' }}"
       onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $unreadCount > 0 ? '#eff6ff' : '' }}'">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #16a34a, #15803d); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600; font-size: 18px; flex-shrink: 0; margin-right: 14px;">
            {{ strtoupper(substr($otherUser->name ?? 'U', 0, 1)) }}
        </div>
        <div style="flex: 1; min-width: 0;">
            <div class="d-flex justify-content-between align-items-center">
                <strong style="font-size: 15px;">{{ $otherUser->name ?? 'Unknown' }}</strong>
                @if($room->last_message_at)
                <span style="font-size: 12px; color: #9ca3af;">{{ $room->last_message_at->diffForHumans() }}</span>
                @endif
            </div>
            @if($room->latestMessage)
            <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 400px;">
                {{ Str::limit($room->latestMessage->message, 60) }}
            </p>
            @endif
        </div>
        @if($unreadCount > 0)
        <span class="badge badge-success badge-pill" style="font-size: 12px; min-width: 24px;">{{ $unreadCount }}</span>
        @endif
    </a>
    @endforeach
</div>
@endif
@endsection
