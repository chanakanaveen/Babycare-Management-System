{{-- Notification Bell Component --}}
@php
    $guardType = null;
    $notifBaseUrl = '';
    if (Auth::guard('parent')->check()) {
        $guardType = 'parent';
        $notifBaseUrl = '/parent/notifications';
    } elseif (Auth::guard('midwife')->check()) {
        $guardType = 'midwife';
        $notifBaseUrl = '/midwife/notifications';
    }
@endphp

@if($guardType)
<div class="user-notification" id="notification-bell-wrapper">
    <div class="dropdown">
        <a class="dropdown-toggle no-arrow" href="#" role="button" id="notif-bell-toggle" data-toggle="dropdown" aria-expanded="false">
            <i class="icon-copy dw dw-notification"></i>
            <span class="badge notification-active" id="notif-badge" style="display:none;">0</span>
        </a>
        <div class="dropdown-menu dropdown-menu-right" style="width: 360px; max-height: 450px; padding: 0;" id="notif-dropdown">
            <div style="display:flex; justify-content:space-between; align-items:center; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                <h6 style="margin:0; font-weight:600; font-size:15px;">Notifications</h6>
                <button type="button" id="mark-all-read-btn" style="background:none; border:none; color:#2563eb; font-size:13px; cursor:pointer; font-weight:500;">Mark all read</button>
            </div>
            <div class="notification-list customscroll" style="max-height: 340px; overflow-y: auto;">
                <ul id="notif-list" style="list-style:none; margin:0; padding:0;">
                    <li style="padding: 20px; text-align:center; color:#9ca3af; font-size:14px;">Loading...</li>
                </ul>
            </div>
            <div style="padding: 10px 16px; border-top: 1px solid #e5e7eb; text-align:center; background: #f9fafb;">
                <a href="{{ $notifBaseUrl }}" style="color:#2563eb; font-size:13px; font-weight:500; text-decoration:none;">View All Notifications</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var guardType = '{{ $guardType }}';
    var baseUrl = '{{ $notifBaseUrl }}';
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function updateBadge(count) {
        var badge = document.getElementById('notif-badge');
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    function fetchUnreadCount() {
        $.ajax({
            url: baseUrl + '/unread-count',
            method: 'GET',
            success: function(res) {
                if (res.status === 1) updateBadge(res.count);
            }
        });
    }

    function renderNotifications(notifications) {
        var list = document.getElementById('notif-list');
        if (!notifications || notifications.length === 0) {
            list.innerHTML = '<li style="padding: 20px; text-align:center; color:#9ca3af; font-size:14px;">No notifications yet</li>';
            return;
        }
        var html = '';
        notifications.forEach(function(n) {
            var readStyle = n.is_read ? 'opacity: 0.6;' : 'background: #eff6ff;';
            var typeIcon = '🔔';
            if (n.type === 'vaccine_scheduled') typeIcon = '💉';
            else if (n.type === 'appointment_request') typeIcon = '📅';
            else if (n.type === 'appointment_confirmed') typeIcon = '✅';
            else if (n.type === 'appointment_rejected') typeIcon = '❌';
            else if (n.type === 'chat_message') typeIcon = '💬';

            html += '<li style="' + readStyle + ' border-bottom: 1px solid #f3f4f6;">';
            html += '<a href="#" class="notif-item" data-id="' + n.id + '" style="display:flex; gap:10px; padding:12px 16px; text-decoration:none; color:inherit;">';
            html += '<span style="font-size:20px; flex-shrink:0;">' + typeIcon + '</span>';
            html += '<div style="flex:1; min-width:0;">';
            html += '<strong style="display:block; font-size:13px; color:#1f2937; margin-bottom:2px;">' + n.title + '</strong>';
            html += '<p style="margin:0; font-size:12px; color:#6b7280; line-height:1.4; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">' + n.message + '</p>';
            html += '<span style="font-size:11px; color:#9ca3af;">' + timeAgo(n.created_at) + '</span>';
            html += '</div></a></li>';
        });
        list.innerHTML = html;

        // Bind click to mark as read
        list.querySelectorAll('.notif-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var id = this.dataset.id;
                $.ajax({
                    url: baseUrl + '/' + id + '/read',
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken },
                    success: function() {
                        fetchUnreadCount();
                        fetchNotifications();
                    }
                });
            });
        });
    }

    function fetchNotifications() {
        $.ajax({
            url: baseUrl,
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(res) {
                if (res.status === 1) renderNotifications(res.data);
            }
        });
    }

    function timeAgo(dateStr) {
        var now = new Date();
        var date = new Date(dateStr);
        var diff = Math.floor((now - date) / 1000);
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
        if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
        if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
        return date.toLocaleDateString();
    }

    // Mark all read
    document.getElementById('mark-all-read-btn').addEventListener('click', function() {
        $.ajax({
            url: baseUrl + '/mark-all-read',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            success: function() {
                updateBadge(0);
                fetchNotifications();
            }
        });
    });

    // On dropdown open: fetch notifications
    $('#notif-bell-toggle').on('click', function() {
        fetchNotifications();
    });

    // Initial load + polling
    fetchUnreadCount();
    setInterval(fetchUnreadCount, 30000);
})();
</script>
@endpush
@endif
