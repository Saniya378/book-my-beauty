<!-- Notification Bell Component -->
<div class="notification-wrapper" style="position: relative; display: inline-block;">
    <button type="button" id="notif-bell-btn" onclick="toggleNotifications(event)" style="
        background: transparent; border: none; cursor: pointer; position: relative;
        color: rgba(255,255,255,0.85); display: flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 50%; transition: background 0.2s;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
        </svg>
        <span id="notif-badge" style="
            display: none; position: absolute; top: 2px; right: 2px;
            background: #c62828; color: white; font-size: 10px; font-weight: bold;
            padding: 2px 5px; border-radius: 10px; min-width: 17px; text-align: center;
            line-height: 1;">0</span>
    </button>

    <div id="notif-dropdown" style="
        display: none; position: absolute; right: 0; top: 48px;
        width: 320px; background: white; border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.18); border: 1px solid #e8e8e8;
        z-index: 1000; overflow: hidden;">
        <div style="background: #1a1a2e; color: white; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #c9a96e;">
            <span style="font-weight: 600; font-size: 14px;">Notifications</span>
            <button type="button" onclick="markAllNotificationsRead()" style="
                background: none; border: none; color: #c9a96e; font-size: 11px;
                cursor: pointer; text-decoration: underline; padding: 0;">Mark all read</button>
        </div>

        <div id="notif-list" style="max-height: 300px; overflow-y: auto; color: #333;">
            <div style="padding: 25px; text-align: center; color: #999; font-size: 13px;">Loading notifications...</div>
        </div>
    </div>
</div>

<script>
let notifDropdownOpen = false;

function toggleNotifications(event) {
    if (event) event.stopPropagation();
    const dd = document.getElementById('notif-dropdown');
    notifDropdownOpen = !notifDropdownOpen;
    dd.style.display = notifDropdownOpen ? 'block' : 'none';

    if (notifDropdownOpen) {
        fetchNotifications();
    }
}

document.addEventListener('click', function(e) {
    const wrapper = document.querySelector('.notification-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = 'none';
        notifDropdownOpen = false;
    }
});

function fetchNotifications() {
    fetch("{{ route('notifications.get') }}")
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notif-badge');
            const list = document.getElementById('notif-list');

            if (data.count > 0) {
                badge.innerText = data.count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }

            if (!data.notifications || data.notifications.length === 0) {
                list.innerHTML = '<div style="padding: 25px; text-align: center; color: #888; font-size: 13px;">No notifications yet.</div>';
                return;
            }

            let html = '';
            data.notifications.forEach(n => {
                const unreadBg = !n.is_read ? 'background: #faf7f2;' : 'background: #ffffff;';
                const dot = !n.is_read ? '<span style="display:inline-block; width:7px; height:7px; border-radius:50%; background:#c9a96e; margin-right:6px;"></span>' : '';
                const dateStr = new Date(n.created_at).toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                html += `
                    <div style="${unreadBg} padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 13px; line-height: 1.4;">
                        <div style="color: #222; font-weight: ${!n.is_read ? '600' : '400'};">${dot}${n.message}</div>
                        <div style="color: #999; font-size: 11px; margin-top: 4px;">${dateStr}</div>
                    </div>
                `;
            });
            list.innerHTML = html;
        })
        .catch(() => {});
}

function markAllNotificationsRead() {
    fetch("{{ route('notifications.mark_read') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(() => {
        const badge = document.getElementById('notif-badge');
        if (badge) badge.style.display = 'none';
        fetchNotifications();
    })
    .catch(() => {});
}

// Initial fetch on page load
document.addEventListener('DOMContentLoaded', fetchNotifications);
</script>
