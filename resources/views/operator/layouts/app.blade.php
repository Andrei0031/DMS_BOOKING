<?php
/**
 * Operator Layout — same design as admin but sidebar filtered by permissions.
 * Expects $content, $title, $page_title from including view.
 */
$user = $_SESSION['user'] ?? [];
$perms = [];
if (!empty($user['permissions'])) {
    $decoded = json_decode($user['permissions'], true);
    if (is_array($decoded)) $perms = $decoded;
}

// Map permissions to sidebar items
$sidebar_items = [
    ['perm' => null,               'href' => '/DMS_BOOKING/operator',          'icon' => 'fa-tachometer-alt', 'label' => 'Dashboard', 'match' => '/operator'],
    ['perm' => 'manage_bookings',  'href' => '/DMS_BOOKING/operator/bookings', 'icon' => 'fa-ticket-alt',     'label' => 'Bookings',  'match' => '/operator/bookings'],
    ['perm' => 'manage_buses',     'href' => '/DMS_BOOKING/operator/buses',    'icon' => 'fa-bus',            'label' => 'Buses',     'match' => '/operator/buses'],
    ['perm' => 'manage_users',     'href' => '/DMS_BOOKING/operator/users',    'icon' => 'fa-users',          'label' => 'Users',     'match' => '/operator/users'],
    ['perm' => 'manage_routes',    'href' => '/DMS_BOOKING/operator/routes',   'icon' => 'fa-route',          'label' => 'Routes',    'match' => '/operator/routes'],
    ['perm' => 'manage_advisory',  'href' => '/DMS_BOOKING/operator/advisory', 'icon' => 'fa-bullhorn',      'label' => 'Advisory',  'match' => '/operator/advisory'],
    ['perm' => 'view_activity_logs','href' => '/DMS_BOOKING/operator/logs',     'icon' => 'fa-clipboard-list', 'label' => 'Activity Logs', 'match' => '/operator/logs'],
    ['perm' => 'view_reports',     'href' => '/DMS_BOOKING/operator/reports',  'icon' => 'fa-chart-bar',      'label' => 'Reports',   'match' => '/operator/reports'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Operator Panel - Davao Metro Shuttle'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        #sidebar {
            width: 260px;
            background: linear-gradient(180deg,#0c2d48 0%,#145374 100%);
            min-height: 100vh;
            position: fixed; top: 0; left: 0; z-index: 50;
            transition: width 0.3s ease, transform 0.3s ease;
            display: flex; flex-direction: column;
            overflow: hidden;
        }
        #sidebar.collapsed { width: 68px; }
        #sidebar.collapsed .sidebar-label,
        #sidebar.collapsed .brand-text,
        #sidebar.collapsed .nav-section-label,
        #sidebar.collapsed .user-info-text,
        #sidebar.collapsed .logout-text { display: none !important; }
        #sidebar.collapsed .sidebar-link { justify-content: center; padding: 12px 0; }
        #sidebar.collapsed .sidebar-link i { width: auto; font-size: 1.15rem; }
        #sidebar.collapsed .brand-icon { margin: 0 auto; }
        #sidebar.collapsed .user-avatar { margin: 0 auto; }
        #sidebar.collapsed .logout-btn { padding: 10px 0; justify-content: center; }
        #sidebar.collapsed .logout-btn i { margin: 0; }
        #sidebar.collapsed .sidebar-link { position: relative; }
        #sidebar.collapsed .sidebar-link:hover::after {
            content: attr(data-tooltip);
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #145374; color: #fff; padding: 5px 10px; border-radius: 6px;
            font-size: 0.78rem; white-space: nowrap; pointer-events: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 100;
        }
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); width: 260px !important; }
            #sidebar.open { transform: translateX(0); }
            #content-wrapper { margin-left: 0 !important; }
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px;
            color: #94a3b8; border-radius: 10px; font-weight: 500; position: relative; overflow: hidden;
            text-decoration: none; margin-bottom: 4px; white-space: nowrap;
            transition: background 0.25s ease, color 0.25s ease, transform 0.15s ease, box-shadow 0.25s ease;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #fff; transform: translateX(4px); }
        .sidebar-link:active { transform: translateX(2px) scale(0.97); }
        .sidebar-link.active {
            background: linear-gradient(135deg, #0891b2, #06b6d4); color: #fff;
            box-shadow: 0 4px 15px rgba(8,145,178,0.4); transform: translateX(0);
        }
        .sidebar-link i { width: 20px; text-align: center; font-size: 1rem; flex-shrink: 0; transition: transform 0.2s ease; }
        .sidebar-link:hover i { transform: scale(1.2); }
        .sidebar-link.active i { transform: scale(1.1); }
        .sidebar-link .ripple {
            position: absolute; border-radius: 50%; background: rgba(255,255,255,0.25);
            transform: scale(0); animation: ripple-anim 0.5s linear; pointer-events: none;
        }
        @keyframes ripple-anim { to { transform: scale(4); opacity: 0; } }
        @keyframes toastTimer { from { width: 100%; } to { width: 0%; } }
        #content-wrapper {
            margin-left: 260px; flex: 1; display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        #content-wrapper.sidebar-collapsed { margin-left: 68px; }
        #page-body .table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        #page-body .table-scroll table {
            min-width: 860px;
        }
        @media (max-width: 768px) {
            main {
                padding: 14px !important;
            }
            #page-body div[style*="overflow-x:auto"] {
                -webkit-overflow-scrolling: touch;
            }
            #page-body div[style*="overflow-x:auto"] table {
                min-width: 860px;
            }
        }
        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge { display:inline-block; padding: 3px 10px; border-radius: 9999px; font-size:0.75rem; font-weight:600; text-transform:capitalize; }
    </style>
</head>
<body class="bg-gray-100 font-sans" style="display:flex; min-height:100vh;">

    <!-- Sidebar -->
    <div id="sidebar">
        <div style="padding:20px 14px 18px; border-bottom:1px solid rgba(255,255,255,0.08); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="brand-icon" style="width:40px;height:40px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#fff;display:flex;align-items:center;justify-content:center;">
                    <img src="/DMS_BOOKING/Images/Updated Company Logo.png" alt="DMS Logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div class="brand-text">
                    <div style="color:#fff;font-weight:700;font-size:0.9rem;line-height:1.2;">Davao Metro Shuttle</div>
                    <div style="color:#64748b;font-size:0.72rem;font-weight:500;">Operator Panel</div>
                </div>
            </div>
        </div>

        <nav style="flex:1; padding:16px 12px; overflow-y:auto; overflow-x:hidden;">
            <div class="nav-section-label" style="color:#475569;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px;padding:0 8px;">Menu</div>
            <?php foreach ($sidebar_items as $item):
                // Dashboard always shown; others require matching permission
                if ($item['perm'] !== null && !in_array($item['perm'], $perms)) continue;
                $is_active = ($item['match'] === '/operator')
                    ? (strpos($_SERVER['REQUEST_URI'], '/operator') !== false && strpos($_SERVER['REQUEST_URI'], '/operator/') === false)
                    : strpos($_SERVER['REQUEST_URI'], $item['match']) !== false;
            ?>
            <a href="<?php echo $item['href']; ?>" data-tooltip="<?php echo $item['label']; ?>"
               class="sidebar-link <?php echo $is_active ? 'active' : ''; ?>">
                <i class="fas <?php echo $item['icon']; ?>"></i><span class="sidebar-label"><?php echo $item['label']; ?></span>
            </a>
            <?php endforeach; ?>
        </nav>

        <div style="padding:14px 12px; border-top:1px solid rgba(255,255,255,0.08); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <div class="user-avatar" style="background:#0891b2;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-headset text-white" style="font-size:0.85rem;"></i>
                </div>
                <div class="user-info-text" style="overflow:hidden;">
                    <div style="color:#fff;font-weight:600;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?php echo htmlspecialchars($user['name'] ?? 'Operator'); ?>
                    </div>
                    <div style="color:#64748b;font-size:0.70rem;">Operator</div>
                </div>
            </div>
            <form action="/DMS_BOOKING/logout" method="POST">
                <button type="submit" class="logout-btn" style="width:100%;background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25);padding:8px 10px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:8px;justify-content:center;" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">
                    <i class="fas fa-sign-out-alt"></i><span class="logout-text">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <div id="content-wrapper">
        <header style="background:#fff;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:40;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:14px;">
                <button id="sidebar-toggle" onclick="toggleSidebar(event)" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:1.2rem;padding:6px;border-radius:8px;transition:all 0.2s;line-height:1;position:relative;z-index:60;" onmouseover="this.style.background='#f1f5f9';this.style.color='#0f172a'" onmouseout="this.style.background='none';this.style.color='#64748b'">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 id="page-heading" style="font-size:1.15rem;font-weight:700;color:#0f172a;"><?php echo htmlspecialchars($page_title ?? 'Operator Panel'); ?></h1>
                    <p style="font-size:0.72rem;color:#94a3b8;margin-top:1px;"><?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="background:#e0f2fe;color:#0891b2;padding:4px 12px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
                    <i class="fas fa-headset" style="margin-right:4px;"></i>Operator
                </span>
            </div>
        </header>

        <div id="page-body">
        <?php
        $flash_success = $_SESSION['success'] ?? '';
        $flash_error   = $_SESSION['error'] ?? '';
        $flash_warning = $_SESSION['warning'] ?? '';
        if ($flash_success) unset($_SESSION['success']);
        if ($flash_error) unset($_SESSION['error']);
        if ($flash_warning) unset($_SESSION['warning']);
        ?>

        <!-- Toast container -->
        <div id="toast-container" style="position:fixed;top:20px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;pointer-events:none;"></div>

        <?php if ($flash_success): ?>
        <script>document.addEventListener('DOMContentLoaded',function(){showToast(<?php echo json_encode($flash_success); ?>,'success');});</script>
        <?php endif; ?>
        <?php if ($flash_error): ?>
        <script>document.addEventListener('DOMContentLoaded',function(){showToast(<?php echo json_encode($flash_error); ?>,'error');});</script>
        <?php endif; ?>
        <?php if ($flash_warning): ?>
        <script>document.addEventListener('DOMContentLoaded',function(){showToast(<?php echo json_encode($flash_warning); ?>,'warning');});</script>
        <?php endif; ?>

        <main style="flex:1;padding:24px;">
            <?php if (isset($content)) echo $content; ?>
        </main>
        </div>

        <footer style="padding:16px 24px;border-top:1px solid #e2e8f0;text-align:center;color:#94a3b8;font-size:0.75rem;background:#fff;">
            &copy; <?php echo date('Y'); ?> Davao Metro Shuttle &mdash; Operator Panel
        </footer>
    </div>

    <script>
        // ── Toast notification system ──
        function showToast(msg, type) {
            var container = document.getElementById('toast-container');
            var toast = document.createElement('div');
            var isSuccess = type === 'success';
            var isWarning = type === 'warning';
            var bgColor = isSuccess ? '#065f46' : (isWarning ? '#92400e' : '#991b1b');
            var borderColor = isSuccess ? '#059669' : (isWarning ? '#d97706' : '#dc2626');
            var label = isSuccess ? 'Success' : (isWarning ? 'Warning' : 'Error');
            var icon = isSuccess ? 'fa-check-circle' : (isWarning ? 'fa-exclamation-triangle' : 'fa-exclamation-circle');
            
            toast.style.cssText = 'pointer-events:auto;min-width:320px;max-width:450px;padding:14px 20px 14px 16px;border-radius:12px;display:flex;align-items:center;gap:12px;font-size:0.875rem;font-weight:500;box-shadow:0 10px 40px rgba(0,0,0,0.18);transform:translateX(120%);transition:transform 0.4s cubic-bezier(.16,1,.3,1),opacity 0.3s;opacity:0;position:relative;overflow:hidden;'
                + 'background:'+bgColor+';color:#fff;border:1px solid '+borderColor+';';
            toast.innerHTML = '<div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:rgba(255,255,255,0.15);"><i class="fas '+icon+'" style="font-size:1rem;"></i></div>'
                + '<div style="flex:1;"><div style="font-weight:700;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.05em;opacity:0.8;margin-bottom:2px;">'+label+'</div><div>'+msg.replace(/</g,'&lt;')+'</div></div>'
                + '<button onclick="this.parentElement.style.transform=\'translateX(120%)\';this.parentElement.style.opacity=\'0\';setTimeout(function(){this.parentElement.remove()}.bind(this),400)" style="background:none;border:none;color:rgba(255,255,255,0.6);cursor:pointer;font-size:1.1rem;padding:4px;flex-shrink:0;">&times;</button>'
                + '<div style="position:absolute;bottom:0;left:0;height:3px;background:rgba(255,255,255,0.3);width:100%;animation:toastTimer 5s linear forwards;"></div>';
            container.appendChild(toast);
            requestAnimationFrame(function(){requestAnimationFrame(function(){toast.style.transform='translateX(0)';toast.style.opacity='1';});});
            setTimeout(function(){toast.style.transform='translateX(120%)';toast.style.opacity='0';setTimeout(function(){toast.remove();},400);},5000);
        }

        function toggleSidebar(event) {
            if (event) event.stopPropagation();
            var sidebar = document.getElementById('sidebar');
            var content = document.getElementById('content-wrapper');
            var isMobile = window.innerWidth <= 768;
            if (isMobile) { sidebar.classList.toggle('open'); }
            else {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('sidebar-collapsed');
                localStorage.setItem('opSidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            }
        }

        function closeSidebarOnMobile() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.remove('open');
            }
        }

        (function() {
            if (window.innerWidth > 768 && localStorage.getItem('opSidebarCollapsed') === '1') {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('content-wrapper').classList.add('sidebar-collapsed');
            }
        })();

        document.querySelectorAll('.sidebar-link').forEach(function(link) {
            link.addEventListener('mousedown', function(e) {
                var ripple = document.createElement('span');
                ripple.classList.add('ripple');
                var size = Math.max(this.offsetWidth, this.offsetHeight);
                var rect = this.getBoundingClientRect();
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = (e.clientX - rect.left - size / 2) + 'px';
                ripple.style.top  = (e.clientY - rect.top  - size / 2) + 'px';
                this.appendChild(ripple);
                ripple.addEventListener('animationend', function() { ripple.remove(); });
            });
        });
        // AJAX page transitions
        (function() {
            function loadPage(href, pushState) {
                var pageBody = document.getElementById('page-body');
                pageBody.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                pageBody.style.opacity = '0';
                pageBody.style.transform = 'translateY(10px)';
                fetch(href, { headers: { 'X-Fetch-Nav': '1' } })
                    .then(function(r) {
                        if (r.redirected && r.url.indexOf('/DMS_BOOKING/login') !== -1) {
                            window.location.href = '/DMS_BOOKING/login';
                            throw new Error('SESSION_REDIRECT');
                        }
                        return r.text();
                    })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newBody = doc.getElementById('page-body');
                        if (newBody) pageBody.innerHTML = newBody.innerHTML;
                        var newHeading = doc.getElementById('page-heading');
                        if (newHeading) document.getElementById('page-heading').textContent = newHeading.textContent;
                        document.title = doc.title;
                        // Extract and execute scripts from the fetched content
                        var scripts = doc.querySelectorAll('script');
                        scripts.forEach(function(script) {
                            var newScript = document.createElement('script');
                            if (script.src) {
                                newScript.src = script.src;
                            } else {
                                newScript.textContent = script.textContent;
                            }
                            document.body.appendChild(newScript);
                            document.body.removeChild(newScript);
                        });
                        document.querySelectorAll('.sidebar-link').forEach(function(l) { l.classList.remove('active'); });
                        document.querySelectorAll('.sidebar-link[href="' + href + '"]').forEach(function(l) { l.classList.add('active'); });
                        requestAnimationFrame(function() {
                            pageBody.style.opacity = '1';
                            pageBody.style.transform = 'translateY(0)';
                            setTimeout(function() {
                                pageBody.style.transform = 'none';
                            }, 220);
                        });
                        if (pushState) history.pushState({ href: href }, doc.title, href);
                    })
                    .catch(function(err) {
                        if (err && err.message === 'SESSION_REDIRECT') return;
                        window.location.href = href;
                    });
            }
            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    closeSidebarOnMobile();
                    var href = this.getAttribute('href');
                    if (!href || href === '#' || this.classList.contains('active')) return;
                    e.preventDefault();
                    loadPage(href, true);
                });
            });
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.href) loadPage(e.state.href, false);
            });
            history.replaceState({ href: window.location.href }, document.title, window.location.href);
        })();
    </script>
</body>
</html>
