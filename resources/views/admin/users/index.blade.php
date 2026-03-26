<?php
$panel = (($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin';
$title = 'Manage Users - ' . ucfirst($panel) . ' Panel';
$page_title = 'Manage Users';
ob_start();
?>

<!-- Toolbar -->
<div class="bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <form method="GET" action="/DMS_BOOKING/<?php echo $panel; ?>/users" style="display:flex;gap:10px;align-items:center;">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.8rem;"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email..."
                   style="padding:8px 12px 8px 32px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;width:250px;outline:none;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <button type="submit" style="padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.84rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-search mr-1"></i>Search
        </button>
        <?php if ($search): ?>
        <a href="/DMS_BOOKING/<?php echo $panel; ?>/users" style="padding:8px 14px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.84rem;font-weight:600;text-decoration:none;">
            <i class="fas fa-times mr-1"></i>Clear
        </a>
        <?php endif; ?>
    </form>
    <span style="color:#64748b;font-size:0.82rem;font-weight:500;">
        <i class="fas fa-users mr-1 text-purple-500"></i><?php echo count($users); ?> user(s)
    </span>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <?php if (empty($users)): ?>
    <div style="padding:60px;text-align:center;color:#94a3b8;">
        <i class="fas fa-users" style="font-size:3rem;margin-bottom:12px;display:block;color:#cbd5e1;"></i>
        <p style="font-size:1rem;font-weight:500;">No users found.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.84rem;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">#</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Name</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Email</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Phone</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Bookings</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Role</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Joined</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;color:#94a3b8;font-size:0.78rem;"><?php echo intval($u['id']); ?></td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="background:#f1f5f9;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas fa-user" style="color:#94a3b8;font-size:0.85rem;"></i>
                            </div>
                            <div style="font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($u['name']); ?></div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#374151;"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td style="padding:12px 16px;color:#374151;"><?php echo $u['phone'] ? htmlspecialchars($u['phone']) : '<span style="color:#cbd5e1;">—</span>'; ?></td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="background:#eff6ff;color:#1d4ed8;padding:3px 12px;border-radius:9999px;font-weight:700;font-size:0.8rem;">
                            <?php echo intval($u['booking_count']); ?>
                        </span>
                    </td>
                    <td style="padding:12px 16px;">
                        <span style="background:#f1f5f9;color:#64748b;padding:4px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;">
                            Customer
                        </span>
                    </td>
                    <td style="padding:12px 16px;color:#64748b;white-space:nowrap;font-size:0.78rem;">
                        <?php echo date('M j, Y', strtotime($u['created_at'])); ?>
                    </td>
                    <td style="padding:12px 16px;">
                        <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/users/<?php echo intval($u['id']); ?>/delete" onsubmit="return confirm('Permanently delete <?php echo htmlspecialchars(addslashes($u['name'])); ?>? This will also delete all their bookings.');">
                            <button type="submit" style="padding:5px 11px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="Delete Customer">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$layout = (($_SESSION['user']['type'] ?? '') === 'operator')
    ? __DIR__ . '/../../operator/layouts/app.blade.php'
    : __DIR__ . '/../../admin/layouts/app.blade.php';
include $layout;
?>
