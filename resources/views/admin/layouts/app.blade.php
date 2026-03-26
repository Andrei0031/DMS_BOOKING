<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Admin Panel - Davao Metro Shuttle'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }

        /* ── Sidebar ── */
        #sidebar {
            width: 260px;
            background: linear-gradient(180deg,#0f172a 0%,#1e293b 100%);
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

        /* Tooltip when collapsed */
        #sidebar.collapsed .sidebar-link { position: relative; }
        #sidebar.collapsed .sidebar-link:hover::after {
            content: attr(data-tooltip);
            position: absolute; left: calc(100% + 12px); top: 50%; transform: translateY(-50%);
            background: #1e293b; color: #fff; padding: 5px 10px; border-radius: 6px;
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
        .sidebar-link:hover {
            background: rgba(255,255,255,0.08); color: #fff;
            transform: translateX(4px);
        }
        .sidebar-link:active {
            transform: translateX(2px) scale(0.97);
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #2563eb, #3b82f6); color: #fff;
            box-shadow: 0 4px 15px rgba(37,99,235,0.4);
            transform: translateX(0);
        }
        .sidebar-link i {
            width: 20px; text-align: center; font-size: 1rem; flex-shrink: 0;
            transition: transform 0.2s ease;
        }
        .sidebar-link:hover i { transform: scale(1.2); }
        .sidebar-link.active i { transform: scale(1.1); }

        /* Ripple effect */
        .sidebar-link .ripple {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.25);
            transform: scale(0); animation: ripple-anim 0.5s linear;
            pointer-events: none;
        }
        @keyframes ripple-anim {
            to { transform: scale(4); opacity: 0; }
        }

        #content-wrapper {
            margin-left: 260px;
            flex: 1; display: flex; flex-direction: column; min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        #content-wrapper.sidebar-collapsed { margin-left: 68px; }

        .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,0.12); }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .badge { display:inline-block; padding: 3px 10px; border-radius: 9999px; font-size:0.75rem; font-weight:600; text-transform:capitalize; }

        /* ── Page transition ── */
        @keyframes pageIn {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes pageOut {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(-12px); }
        }

    </style>
</head>
<body class="bg-gray-100 font-sans" style="display:flex; min-height:100vh;">

    <!-- Sidebar -->
    <div id="sidebar">
        <!-- Brand -->
        <div style="padding:20px 14px 18px; border-bottom:1px solid rgba(255,255,255,0.08); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:12px;">
                <div class="brand-icon" style="width:40px;height:40px;border-radius:10px;overflow:hidden;flex-shrink:0;background:#fff;display:flex;align-items:center;justify-content:center;">
                    <img src="/DMS_BOOKING/Images/Updated Company Logo.png" alt="DMS Logo" style="width:100%;height:100%;object-fit:contain;">
                </div>
                <div class="brand-text">
                    <div style="color:#fff;font-weight:700;font-size:0.9rem;line-height:1.2;">Davao Metro Shuttle</div>
                    <div style="color:#64748b;font-size:0.72rem;font-weight:500;">Admin Panel</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav style="flex:1; padding:16px 12px; overflow-y:auto; overflow-x:hidden;">
            <div class="nav-section-label" style="color:#475569;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:8px;padding:0 8px;">Main Menu</div>
            <a href="/DMS_BOOKING/admin" data-tooltip="Dashboard"
               class="sidebar-link <?php echo (strpos($_SERVER['REQUEST_URI'],'/admin') !== false && strpos($_SERVER['REQUEST_URI'],'/admin/') === false) ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i><span class="sidebar-label">Dashboard</span>
            </a>
            <a href="/DMS_BOOKING/admin/bookings" data-tooltip="Bookings"
               class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'],'/admin/bookings') !== false ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i><span class="sidebar-label">Bookings</span>
            </a>
            <a href="/DMS_BOOKING/admin/buses" data-tooltip="Buses"
               class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'],'/admin/buses') !== false ? 'active' : ''; ?>">
                <i class="fas fa-bus"></i><span class="sidebar-label">Buses</span>
            </a>
            <a href="/DMS_BOOKING/admin/users" data-tooltip="Users"
               class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'],'/admin/users') !== false ? 'active' : ''; ?>">
                <i class="fas fa-users"></i><span class="sidebar-label">Users</span>
            </a>
            <a href="/DMS_BOOKING/admin/routes" data-tooltip="Routes"
               class="sidebar-link <?php echo strpos($_SERVER['REQUEST_URI'],'/admin/routes') !== false ? 'active' : ''; ?>">
                <i class="fas fa-route"></i><span class="sidebar-label">Routes</span>
            </a>

        </nav>

        <!-- Admin user info + logout -->
        <div style="padding:14px 12px; border-top:1px solid rgba(255,255,255,0.08); flex-shrink:0;">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <div class="user-avatar" style="background:#1e40af;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-user-shield text-white" style="font-size:0.85rem;"></i>
                </div>
                <div class="user-info-text" style="overflow:hidden;">
                    <div style="color:#fff;font-weight:600;font-size:0.8rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?php echo htmlspecialchars($_SESSION['user']['name'] ?? 'Admin'); ?>
                    </div>
                    <div style="color:#64748b;font-size:0.70rem;">Administrator</div>
                </div>
            </div>
            <form action="/DMS_BOOKING/logout" method="POST">
                <button type="submit" class="logout-btn" style="width:100%;background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25);padding:8px 10px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:8px;justify-content:center;" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">
                    <i class="fas fa-sign-out-alt"></i><span class="logout-text">Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main content wrapper -->
    <div id="content-wrapper">

        <!-- Top header -->
        <header style="background:#fff;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:40;box-shadow:0 1px 3px rgba(0,0,0,0.06);">
            <div style="display:flex;align-items:center;gap:14px;">
                <button id="sidebar-toggle" onclick="toggleSidebar()" style="background:none;border:none;cursor:pointer;color:#64748b;font-size:1.2rem;padding:6px;border-radius:8px;transition:all 0.2s;line-height:1;" onmouseover="this.style.background='#f1f5f9';this.style.color='#0f172a'" onmouseout="this.style.background='none';this.style.color='#64748b'">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1 id="page-heading" style="font-size:1.15rem;font-weight:700;color:#0f172a;"><?php echo htmlspecialchars($page_title ?? 'Admin Panel'); ?></h1>
                    <p style="font-size:0.72rem;color:#94a3b8;margin-top:1px;"><?php echo date('l, F j, Y'); ?></p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
                <span style="background:#dbeafe;color:#1d4ed8;padding:4px 12px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
                    <i class="fas fa-shield-alt" style="margin-right:4px;"></i>Admin
                </span>
            </div>
        </header>

        <!-- Flash messages + Page Content -->
        <div id="page-body">
        <?php if (!empty($_SESSION['success'])): ?>
        <div style="margin:16px 24px 0;background:#d1fae5;border:1px solid #6ee7b7;color:#065f46;padding:12px 16px;border-radius:8px;display:flex;align-items:center;gap:8px;font-size:0.875rem;font-weight:500;">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['error'])): ?>
        <div style="margin:16px 24px 0;background:#fee2e2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;display:flex;align-items:center;gap:8px;font-size:0.875rem;font-weight:500;">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <main style="flex:1;padding:24px;">
            <?php if (isset($content)) echo $content; ?>
        </main>

        </div><!-- end #page-body -->

        <footer style="padding:16px 24px;border-top:1px solid #e2e8f0;text-align:center;color:#94a3b8;font-size:0.75rem;background:#fff;">
            &copy; <?php echo date('Y'); ?> Davao Metro Shuttle &mdash; Admin Panel
        </footer>
    </div>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById('sidebar');
            var content = document.getElementById('content-wrapper');
            var isMobile = window.innerWidth <= 768;
            if (isMobile) {
                sidebar.classList.toggle('open');
            } else {
                sidebar.classList.toggle('collapsed');
                content.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            }
        }
        (function() {
            if (window.innerWidth > 768 && localStorage.getItem('sidebarCollapsed') === '1') {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('content-wrapper').classList.add('sidebar-collapsed');
            }
        })();

        // Ripple effect on sidebar links
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

        // Page transition on sidebar link clicks — AJAX navigation
        (function() {
            function loadPage(href, pushState) {
                var pageBody = document.getElementById('page-body');
                // Animate out
                pageBody.style.transition = 'opacity 0.18s ease, transform 0.18s ease';
                pageBody.style.opacity = '0';
                pageBody.style.transform = 'translateY(10px)';

                fetch(href, { headers: { 'X-Fetch-Nav': '1' } })
                    .then(function(r) { return r.text(); })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');

                        // Swap content
                        var newBody = doc.getElementById('page-body');
                        if (newBody) pageBody.innerHTML = newBody.innerHTML;

                        // Update heading + title
                        var newHeading = doc.getElementById('page-heading');
                        if (newHeading) document.getElementById('page-heading').textContent = newHeading.textContent;
                        document.title = doc.title;

                        // Update active link
                        document.querySelectorAll('.sidebar-link').forEach(function(l) {
                            l.classList.remove('active');
                        });
                        document.querySelectorAll('.sidebar-link[href="' + href + '"]').forEach(function(l) {
                            l.classList.add('active');
                        });

                        // Animate in
                        requestAnimationFrame(function() {
                            pageBody.style.opacity = '1';
                            pageBody.style.transform = 'translateY(0)';
                        });

                        if (pushState) history.pushState({ href: href }, doc.title, href);
                    })
                    .catch(function() { window.location.href = href; });
            }

            document.querySelectorAll('.sidebar-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
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
