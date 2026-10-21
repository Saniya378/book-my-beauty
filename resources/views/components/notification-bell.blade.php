{{-- Notification bell with its dropdown, shared by every navbar --}}
<div class="bell">
    <button type="button" id="notif-bell-btn" class="bell__button" onclick="toggleNotifications(event)"
        aria-label="Notifications">
        <x-icon name="bell" :size="19" />
        <span id="notif-badge" class="bell__badge">0</span>
    </button>

    <div id="notif-dropdown" class="bell__dropdown">
        <div class="bell__head">
            <div class="bell__title">
                Notifications
                <span id="notif-count-pill" class="bell__pill">0 new</span>
            </div>

            <button type="button" id="mark-all-read-btn" class="btn btn--sm btn--ghost" onclick="markAllNotificationsRead(event)">
                Mark all read
            </button>
        </div>

        <div id="notif-list" class="bell__list">
            <p class="bell__loading">Loading notifications...</p>
        </div>
    </div>
</div>

<script>
    let notifDropdownOpen = false;

    @auth
        @if (Auth::user()->role === 'beautician')
            const notifTargetUrl = @json(route('beautician.bookings'));
        @elseif (Auth::user()->role === 'admin')
            const notifTargetUrl = @json(route('admin.bookings'));
        @else
            const notifTargetUrl = @json(route('customer.bookings'));
        @endauth
    @else
        const notifTargetUrl = @json(route('home'));
    @endauth

    function toggleNotifications(event) {
        event.stopPropagation();
        notifDropdownOpen = !notifDropdownOpen;
        document.getElementById('notif-dropdown').classList.toggle('is-open', notifDropdownOpen);

        if (notifDropdownOpen) {
            fetchNotifications();
        }
    }

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.bell')) {
            document.getElementById('notif-dropdown').classList.remove('is-open');
            notifDropdownOpen = false;
        }
    });

    function setNotifBadge(count) {
        const badge = document.getElementById('notif-badge');
        const pill = document.getElementById('notif-count-pill');

        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';

        if (!pill) return;

        pill.textContent = count + ' new';
        pill.style.display = count > 0 ? 'inline-block' : 'none';
    }

    function fetchNotifications() {
        fetch(@json(route('notifications.get')))
            .then(response => response.json())
            .then(data => {
                setNotifBadge(data.count || 0);

                const list = document.getElementById('notif-list');

                if (!data.notifications || data.notifications.length === 0) {
                    list.innerHTML = '<p class="bell__empty">No notifications yet.</p>';
                    return;
                }

                list.innerHTML = data.notifications.map(notification => {
                    const unreadClass = notification.is_read ? '' : ' is-unread';
                    const date = new Date(notification.created_at)
                        .toLocaleDateString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                    return `
                        <button type="button" class="bell__item${unreadClass}"
                            onclick="openNotification(${notification.id})">
                            <span class="bell__text">${notification.message}</span>
                            <span class="bell__meta">${date}</span>
                        </button>
                    `;
                }).join('');
            })
            .catch(() => {
                document.getElementById('notif-list').innerHTML = '<p class="bell__empty">Could not load notifications.</p>';
            });
    }

    function openNotification(id) {
        markNotificationRead(id);
        window.location.href = notifTargetUrl;
    }

    function markNotificationRead(id) {
        return fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
            }
        }).catch(() => {});
    }

    function markAllNotificationsRead(event) {
        event.preventDefault();
        event.stopPropagation();

        const button = document.getElementById('mark-all-read-btn');
        button.disabled = true;

        fetch(@json(route('notifications.mark_read')), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
            }
        })
            .then(response => response.json())
            .then(() => {
                setNotifBadge(0);
                fetchNotifications();
            })
            .catch(() => {})
            .finally(() => {
                button.disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', fetchNotifications);
</script>