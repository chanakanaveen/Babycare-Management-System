{{-- Notification Banner Zone --}}
<div id="notification-banner-zone" class="banner-zone" aria-live="polite"></div>

<style>
.banner-zone {
    position: sticky;
    top: 0;
    z-index: 999;
    pointer-events: none;
}
.banner-zone > * {
    pointer-events: all;
}
.notif-banner {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 20px;
    margin-bottom: 4px;
    border-left: 4px solid;
    border-radius: 4px;
    animation: bannerSlideDown 0.3s ease;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
    font-size: 14px;
    transition: opacity 0.3s ease, transform 0.3s ease;
}
.notif-banner.banner-dismissing {
    animation: bannerSlideUp 0.3s ease forwards;
}
.notif-banner--vaccine_scheduled,
.notif-banner--vaccine {
    border-color: #2563eb;
}
.notif-banner--appointment_request,
.notif-banner--appointment_confirmed,
.notif-banner--appointment {
    border-color: #16a34a;
}
.notif-banner--reminder {
    border-color: #ca8a04;
}
.notif-banner--default {
    border-color: #6366f1;
}
.notif-banner .banner-icon {
    font-size: 22px;
    flex-shrink: 0;
}
.notif-banner .banner-content {
    flex: 1;
    min-width: 0;
}
.notif-banner .banner-content strong {
    display: block;
    font-size: 14px;
    color: #1f2937;
    margin-bottom: 2px;
}
.notif-banner .banner-content p {
    margin: 0;
    font-size: 13px;
    color: #4b5563;
    line-height: 1.4;
}
.notif-banner .banner-close {
    background: none;
    border: none;
    color: #9ca3af;
    font-size: 18px;
    cursor: pointer;
    padding: 4px 8px;
    flex-shrink: 0;
    line-height: 1;
}
.notif-banner .banner-close:hover {
    color: #374151;
}
@keyframes bannerSlideDown {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
@keyframes bannerSlideUp {
    from { transform: translateY(0); opacity: 1; }
    to { transform: translateY(-100%); opacity: 0; }
}
</style>

<script>
window.BannerNotification = {
    show: function(title, message, type) {
        var zone = document.getElementById('notification-banner-zone');
        if (!zone) return;

        var typeClass = 'notif-banner--' + (type || 'default');
        var icon = '🔔';
        if (type === 'vaccine_scheduled') icon = '💉';
        else if (type === 'appointment_request' || type === 'appointment_confirmed') icon = '📅';
        else if (type === 'appointment_rejected') icon = '❌';
        else if (type === 'chat_message') icon = '💬';
        else if (type === 'reminder') icon = '⏰';

        var banner = document.createElement('div');
        banner.className = 'notif-banner ' + typeClass;
        banner.setAttribute('role', 'alert');
        banner.innerHTML =
            '<span class="banner-icon">' + icon + '</span>' +
            '<div class="banner-content">' +
                '<strong>' + title + '</strong>' +
                '<p>' + message + '</p>' +
            '</div>' +
            '<button class="banner-close" aria-label="Dismiss">&times;</button>';

        zone.appendChild(banner);

        // Close handler
        banner.querySelector('.banner-close').addEventListener('click', function() {
            dismissBanner(banner);
        });

        // Auto-dismiss after 8 seconds
        setTimeout(function() {
            dismissBanner(banner);
        }, 8000);

        function dismissBanner(el) {
            if (el.classList.contains('banner-dismissing')) return;
            el.classList.add('banner-dismissing');
            setTimeout(function() { el.remove(); }, 300);
        }
    }
};
</script>
