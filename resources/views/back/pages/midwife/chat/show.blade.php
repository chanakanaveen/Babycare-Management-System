@extends('back.layout.pages-layout')
@section('pagetitle', $pageTitle . ' - ' . ($otherUser->name ?? ''))
@section('content')
<style>
.chat-container { display: flex; flex-direction: column; height: calc(100vh - 200px); min-height: 500px; border-radius: 12px; overflow: hidden; background: #f8fafc; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.chat-header { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; padding: 16px 20px; display: flex; align-items: center; gap: 12px; }
.chat-header .avatar { width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px; }
.chat-header h5 { margin: 0; font-size: 16px; }
.chat-header small { opacity: 0.8; font-size: 12px; }
.chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 8px; }
.msg-bubble { max-width: 70%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.5; position: relative; word-wrap: break-word; }
.msg-bubble.sent { align-self: flex-end; background: #16a34a; color: #fff; border-bottom-right-radius: 4px; }
.msg-bubble.received { align-self: flex-start; background: #fff; color: #1f2937; border: 1px solid #e5e7eb; border-bottom-left-radius: 4px; }
.msg-bubble .msg-time { font-size: 11px; opacity: 0.7; margin-top: 4px; display: block; text-align: right; }
.msg-bubble.received .msg-time { color: #9ca3af; }
.msg-bubble .msg-attachment { margin-top: 6px; }
.msg-bubble .msg-attachment img { max-width: 200px; border-radius: 8px; cursor: pointer; }
.msg-bubble .msg-attachment a { color: inherit; text-decoration: underline; }
.msg-date-sep { text-align: center; color: #9ca3af; font-size: 12px; margin: 12px 0; }
.msg-date-sep span { background: #f1f5f9; padding: 4px 12px; border-radius: 12px; }
.chat-input-bar { background: #fff; border-top: 1px solid #e5e7eb; padding: 12px 16px; display: flex; align-items: end; gap: 10px; }
.chat-input-bar textarea { flex: 1; border: 1px solid #d1d5db; border-radius: 20px; padding: 10px 16px; resize: none; font-size: 14px; outline: none; max-height: 100px; min-height: 40px; line-height: 1.4; }
.chat-input-bar textarea:focus { border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.15); }
.chat-input-bar .send-btn { width: 40px; height: 40px; border-radius: 50%; background: #16a34a; border: none; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; transition: background 0.2s; }
.chat-input-bar .send-btn:hover { background: #15803d; }
.chat-input-bar .attach-btn { width: 40px; height: 40px; border-radius: 50%; background: #f3f4f6; border: 1px solid #d1d5db; color: #6b7280; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.chat-input-bar .attach-btn:hover { background: #e5e7eb; }
</style>

<div class="chat-container">
    <div class="chat-header">
        <a href="{{ route('midwife.chat.index') }}" style="color: #fff; margin-right: 4px;"><i class="fa fa-arrow-left"></i></a>
        <div class="avatar">{{ strtoupper(substr($otherUser->name ?? 'P', 0, 1)) }}</div>
        <div>
            <h5>{{ $otherUser->name ?? 'Parent' }}</h5>
            <small>Appointment: {{ $chatRoom->appointment ? $chatRoom->appointment->appointment_date->format('M d, Y') : '' }}</small>
        </div>
    </div>

    <div class="chat-messages" id="chat-messages">
        @php $lastDate = null; @endphp
        @foreach($messages as $msg)
            @php
                $msgDate = $msg->created_at->format('Y-m-d');
                $isSent = ($msg->sender_type === $userType && $msg->sender_id === $userId);
            @endphp
            @if($lastDate !== $msgDate)
                <div class="msg-date-sep"><span>{{ $msg->created_at->format('M d, Y') }}</span></div>
                @php $lastDate = $msgDate; @endphp
            @endif
            <div class="msg-bubble {{ $isSent ? 'sent' : 'received' }}">
                @if($msg->message)
                    {!! nl2br(e($msg->message)) !!}
                @endif
                @if($msg->attachment_path)
                <div class="msg-attachment">
                    @if($msg->attachment_type === 'image')
                        <img src="{{ asset('storage/' . $msg->attachment_path) }}" alt="Image" onclick="window.open(this.src)">
                    @else
                        <a href="{{ asset('storage/' . $msg->attachment_path) }}" target="_blank"><i class="fa fa-file"></i> Attachment</a>
                    @endif
                </div>
                @endif
                <span class="msg-time">{{ $msg->created_at->format('h:i A') }}</span>
            </div>
        @endforeach
    </div>

    <div class="chat-input-bar">
        <label class="attach-btn" title="Attach file">
            <i class="fa fa-paperclip"></i>
            <input type="file" id="chat-attachment" style="display:none;" accept="image/*,.pdf,.doc,.docx">
        </label>
        <textarea id="chat-input" placeholder="Type a message..." rows="1"></textarea>
        <button class="send-btn" id="send-btn" title="Send"><i class="fa fa-paper-plane"></i></button>
    </div>
</div>

<div id="attachment-preview" style="display:none; padding: 8px 16px; background: #fef3c7; font-size: 13px; border-top: 1px solid #fbbf24;">
    <i class="fa fa-file"></i> <span id="attachment-name"></span>
    <button type="button" style="background:none; border:none; color: #dc2626; cursor:pointer; margin-left: 8px;" id="remove-attachment">&times;</button>
</div>
@endsection

@section('myscript')
<script>
$(function() {
    var chatRoomId = {{ $chatRoom->id }};
    var sendUrl = '{{ route("midwife.chat.send", $chatRoom->id) }}';
    var messagesUrl = '{{ route("midwife.chat.messages", $chatRoom->id) }}';
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var currentFile = null;

    function scrollToBottom() {
        var el = document.getElementById('chat-messages');
        el.scrollTop = el.scrollHeight;
    }
    scrollToBottom();

    $('#chat-input').on('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    $('#chat-attachment').change(function() {
        if (this.files[0]) {
            currentFile = this.files[0];
            $('#attachment-name').text(currentFile.name);
            $('#attachment-preview').show();
        }
    });
    $('#remove-attachment').click(function() {
        currentFile = null;
        $('#chat-attachment').val('');
        $('#attachment-preview').hide();
    });

    function sendMessage() {
        var message = $('#chat-input').val().trim();
        if (!message && !currentFile) return;

        var formData = new FormData();
        formData.append('_token', csrfToken);
        if (message) formData.append('message', message);
        if (currentFile) formData.append('attachment', currentFile);

        $.ajax({
            url: sendUrl,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.status === 1) {
                    appendMessage(res.data, true);
                    scrollToBottom();
                }
            },
            error: function() { toastr.error('Failed to send message'); }
        });

        $('#chat-input').val('').css('height', 'auto');
        currentFile = null;
        $('#chat-attachment').val('');
        $('#attachment-preview').hide();
    }

    $('#send-btn').click(sendMessage);
    $('#chat-input').keydown(function(e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    });

    function appendMessage(msg, isSent) {
        var html = '<div class="msg-bubble ' + (isSent ? 'sent' : 'received') + '">';
        if (msg.message) html += msg.message.replace(/\n/g, '<br>');
        if (msg.attachment_path) {
            html += '<div class="msg-attachment">';
            if (msg.attachment_type === 'image') {
                html += '<img src="/storage/' + msg.attachment_path + '" style="max-width:200px; border-radius:8px;" onclick="window.open(this.src)">';
            } else {
                html += '<a href="/storage/' + msg.attachment_path + '" target="_blank"><i class="fa fa-file"></i> Attachment</a>';
            }
            html += '</div>';
        }
        var time = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
        html += '<span class="msg-time">' + time + '</span></div>';
        $('#chat-messages').append(html);
    }

    var lastMsgId = {{ $messages->last() ? $messages->last()->id : 0 }};
    setInterval(function() {
        $.get(messagesUrl, function(res) {
            if (res.status === 1 && res.data.length > 0) {
                res.data.forEach(function(msg) {
                    if (msg.id > lastMsgId) {
                        var isSent = (msg.sender_type === '{{ $userType }}' && msg.sender_id === {{ $userId }});
                        if (!isSent) { appendMessage(msg, false); scrollToBottom(); }
                        lastMsgId = msg.id;
                    }
                });
            }
        });
    }, 5000);
});
</script>
@endsection
