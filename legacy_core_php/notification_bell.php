<?php
$base_path = str_repeat("../", substr_count($_SERVER["PHP_SELF"], "/") - 2);
?>
<div id="notif-wrapper" style="position:relative; display:inline-block; margin-left:15px; vertical-align:middle;">
    <div onclick="toggleNotif()" style="cursor:pointer; position:relative;">
        <span style="font-size:18px; color:white;">🔔</span>
        <span id="notif-count" style="
            display:none; position:absolute; top:-8px; right:-8px;
            background:var(--accent); color:var(--primary);
            width:16px; height:16px; border-radius:50%;
            font-size:9px; font-weight:700;
            align-items:center; justify-content:center; text-align:center;
            line-height:16px;">0</span>
    </div>
    <div id="notif-dropdown" style="
        display:none; position:absolute; right:0; top:35px;
        width:300px; background:white;
        border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.15);
        z-index:9999; overflow:hidden;
        border-top:3px solid var(--accent);">
        <div style="padding:12px 18px; background:var(--primary); color:white; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:13px; font-weight:600;">Notifications</span>
            <span onclick="markAllRead()" style="font-size:11px; color:var(--accent); cursor:pointer; text-transform:uppercase; letter-spacing:0.5px;">Mark all read</span>
        </div>
        <div id="notif-list" style="max-height:280px; overflow-y:auto;">
            <div style="padding:20px; text-align:center; color:#aaa; font-size:13px;">Loading...</div>
        </div>
    </div>
</div>

<script>
var notifBase = '<?php echo $base_path; ?>';
var notifOpen = false;

function loadNotifications() {
    fetch(notifBase + 'get_notifications.php')
    .then(r => r.json())
    .then(data => {
        var countEl = document.getElementById('notif-count');
        if (data.count > 0) {
            countEl.style.display = 'flex';
            countEl.textContent = data.count;
        } else {
            countEl.style.display = 'none';
        }
        var list = document.getElementById('notif-list');
        if (!list) return;
        if (data.notifications.length === 0) {
            list.innerHTML = '<div style="padding:20px; text-align:center; color:#aaa; font-size:13px;">No notifications</div>';
        } else {
            list.innerHTML = data.notifications.map(function(n) {
                return '<div style="padding:12px 18px; border-bottom:1px solid #f5f5f5; background:' + (n.is_read == 0 ? '#fdf9f4' : 'white') + ';">' +
                    '<div style="font-size:12px; color:#333; line-height:1.5;">' + n.message + '</div>' +
                    '<div style="font-size:10px; color:#aaa; margin-top:4px;">' + n.created_at + '</div>' +
                '</div>';
            }).join('');
        }
    }).catch(function() {});
}

function toggleNotif() {
    notifOpen = !notifOpen;
    document.getElementById('notif-dropdown').style.display = notifOpen ? 'block' : 'none';
    if (notifOpen) loadNotifications();
}

function markAllRead() {
    fetch(notifBase + 'get_notifications.php?mark_read=1').then(function() { loadNotifications(); });
}

document.addEventListener('click', function(e) {
    var wrapper = document.getElementById('notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        notifOpen = false;
        var dropdown = document.getElementById('notif-dropdown');
        if (dropdown) dropdown.style.display = 'none';
    }
});

setInterval(loadNotifications, 30000);
loadNotifications();
</script>