<?php
$title = 'Dashboard - Operator Panel';
$page_title = 'Dashboard Overview';

$user = $_SESSION['user'] ?? [];
$perms = [];
if (!empty($user['permissions'])) {
    $decoded = json_decode($user['permissions'], true);
    if (is_array($decoded)) $perms = $decoded;
}

ob_start();
?>

<!-- Stats Cards — only show stats relevant to permissions -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">

<?php if (in_array('manage_bookings', $perms)): ?>
    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#dbeafe;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-ticket-alt" style="color:#2563eb;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Total</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#0f172a;line-height:1;"><?php echo intval($total_bookings ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Total Bookings</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#fef3c7;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-hourglass-half" style="color:#d97706;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Pending</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#d97706;line-height:1;"><?php echo intval($pending ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Awaiting Confirmation</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#d1fae5;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-check-circle" style="color:#059669;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Confirmed</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#059669;line-height:1;"><?php echo intval($confirmed ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Confirmed Bookings</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#fee2e2;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times-circle" style="color:#dc2626;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Cancelled</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#dc2626;line-height:1;"><?php echo intval($cancelled ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Cancelled Bookings</div>
    </div>
<?php endif; ?>

<?php if (in_array('manage_users', $perms)): ?>
    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#ede9fe;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-users" style="color:#7c3aed;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Users</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#7c3aed;line-height:1;"><?php echo intval($total_users ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Registered Users</div>
    </div>
<?php endif; ?>

<?php if (in_array('manage_buses', $perms)): ?>
    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#e0f2fe;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-bus" style="color:#0284c7;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Fleet</span>
        </div>
        <div style="font-size:2rem;font-weight:800;color:#0284c7;line-height:1;"><?php echo intval($total_buses ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Active Buses</div>
    </div>
<?php endif; ?>

<?php if (in_array('view_reports', $perms)): ?>
    <div class="stat-card rounded-xl p-5 border border-gray-100" style="background:linear-gradient(135deg,#1e40af,#2563eb);">
        <div class="flex items-center justify-between mb-3">
            <div style="background:rgba(255,255,255,0.2);width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-peso-sign" style="color:#fff;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:rgba(255,255,255,0.7);font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Revenue</span>
        </div>
        <div style="font-size:1.6rem;font-weight:800;color:#fff;line-height:1;">₱<?php echo number_format(floatval($revenue ?? 0), 2); ?></div>
        <div style="color:rgba(255,255,255,0.7);font-size:0.8rem;margin-top:4px;">From Confirmed Bookings</div>
    </div>
<?php endif; ?>

</div>

<!-- Quick Actions + Recent Bookings -->
<div style="display:grid;grid-template-columns:1fr 2fr;gap:20px;align-items:start;">

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-5 border border-gray-100">
        <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;margin-bottom:16px;">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
        </h3>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php if (in_array('manage_buses', $perms)): ?>
            <a href="/DMS_BOOKING/operator/buses" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#eff6ff;border-radius:8px;text-decoration:none;color:#1d4ed8;font-weight:600;font-size:0.85rem;transition:background 0.2s;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                <i class="fas fa-bus"></i> Manage Buses
            </a>
            <?php endif; ?>
            <?php if (in_array('manage_bookings', $perms)): ?>
            <a href="/DMS_BOOKING/operator/bookings" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#fffbeb;border-radius:8px;text-decoration:none;color:#d97706;font-weight:600;font-size:0.85rem;transition:background 0.2s;" onmouseover="this.style.background='#fef3c7'" onmouseout="this.style.background='#fffbeb'">
                <i class="fas fa-clock"></i> View Pending Bookings
            </a>
            <?php endif; ?>
            <?php if (in_array('manage_users', $perms)): ?>
            <a href="/DMS_BOOKING/operator/users" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f5f3ff;border-radius:8px;text-decoration:none;color:#7c3aed;font-weight:600;font-size:0.85rem;transition:background 0.2s;" onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#f5f3ff'">
                <i class="fas fa-user-cog"></i> Manage Users
            </a>
            <?php endif; ?>
            <?php if (in_array('manage_routes', $perms)): ?>
            <a href="/DMS_BOOKING/operator/routes" style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f0fdf4;border-radius:8px;text-decoration:none;color:#059669;font-weight:600;font-size:0.85rem;transition:background 0.2s;" onmouseover="this.style.background='#d1fae5'" onmouseout="this.style.background='#f0fdf4'">
                <i class="fas fa-route"></i> Manage Routes
            </a>
            <?php endif; ?>
            <?php if (empty($perms)): ?>
            <div style="padding:20px;text-align:center;color:#94a3b8;">
                <i class="fas fa-lock" style="font-size:1.5rem;margin-bottom:8px;"></i>
                <p style="font-size:0.85rem;">No permissions assigned yet. Contact your administrator.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Bookings (only if has manage_bookings permission) -->
    <?php if (in_array('manage_bookings', $perms)): ?>
    <div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;">
                <i class="fas fa-history text-blue-500 mr-2"></i>Recent Bookings
            </h3>
            <a href="/DMS_BOOKING/operator/bookings" style="font-size:0.78rem;color:#2563eb;text-decoration:none;font-weight:600;">View All</a>
        </div>
        <?php if (empty($recent_bookings)): ?>
        <div style="padding:40px;text-align:center;color:#94a3b8;">
            <i class="fas fa-inbox" style="font-size:2rem;margin-bottom:8px;"></i>
            <p style="font-size:0.875rem;">No bookings yet.</p>
        </div>
        <?php else: ?>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">Passenger</th>
                        <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">Route</th>
                        <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">Date</th>
                        <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">Price</th>
                        <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;white-space:nowrap;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_bookings as $b): ?>
                    <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <td style="padding:10px 16px;font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($b['user_name']); ?></td>
                        <td style="padding:10px 16px;color:#475569;"><?php echo htmlspecialchars($b['from_location']); ?> &rarr; <?php echo htmlspecialchars($b['to_location']); ?></td>
                        <td style="padding:10px 16px;color:#475569;white-space:nowrap;"><?php echo htmlspecialchars($b['journey_date']); ?></td>
                        <td style="padding:10px 16px;font-weight:600;color:#059669;">₱<?php echo number_format($b['total_price'], 2); ?></td>
                        <td style="padding:10px 16px;">
                            <span class="badge badge-<?php echo htmlspecialchars($b['status']); ?>"><?php echo htmlspecialchars($b['status']); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl border border-gray-100 p-8" style="text-align:center;color:#94a3b8;">
        <i class="fas fa-info-circle" style="font-size:2rem;margin-bottom:10px;"></i>
        <p style="font-size:0.9rem;font-weight:500;">Welcome to your Operator Panel</p>
        <p style="font-size:0.8rem;margin-top:4px;">Use the sidebar to navigate to your assigned sections.</p>
    </div>
    <?php endif; ?>

</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/app.blade.php';
?>
