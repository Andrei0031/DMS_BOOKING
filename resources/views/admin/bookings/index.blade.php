<?php
$panel = (($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin';
$title = 'Manage Bookings - ' . ucfirst($panel) . ' Panel';
$page_title = 'Manage Bookings';
ob_start();
?>

<!-- Toolbar -->
<div class="bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <!-- Search + Filter -->
    <form method="GET" action="/DMS_BOOKING/<?php echo $panel; ?>/bookings" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.8rem;"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search passenger / route..." style="padding:8px 12px 8px 32px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;width:230px;outline:none;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>
        <select name="status" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;outline:none;background:#fff;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending"   <?php echo $status_filter==='pending'   ? 'selected' : ''; ?>>Pending</option>
            <option value="confirmed" <?php echo $status_filter==='confirmed' ? 'selected' : ''; ?>>Confirmed</option>
            <option value="cancelled" <?php echo $status_filter==='cancelled' ? 'selected' : ''; ?>>Cancelled</option>
        </select>
        <button type="submit" style="padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.84rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-filter mr-1"></i>Filter
        </button>
        <?php if ($search || $status_filter): ?>
        <a href="/DMS_BOOKING/<?php echo $panel; ?>/bookings" style="padding:8px 14px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.84rem;font-weight:600;text-decoration:none;">
            <i class="fas fa-times mr-1"></i>Clear
        </a>
        <?php endif; ?>
    </form>
    <span style="color:#64748b;font-size:0.82rem;font-weight:500;">
        <?php echo count($bookings); ?> booking(s) found
    </span>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <?php if (empty($bookings)): ?>
    <div style="padding:60px;text-align:center;color:#94a3b8;">
        <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:12px;display:block;"></i>
        <p style="font-size:1rem;font-weight:500;">No bookings found.</p>
        <?php if ($search || $status_filter): ?>
        <a href="/DMS_BOOKING/<?php echo $panel; ?>/bookings" style="display:inline-block;margin-top:12px;color:#2563eb;font-size:0.875rem;text-decoration:none;font-weight:600;">Clear filters</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.84rem;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">#</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Passenger</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">From</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">To</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Journey Date</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Seats</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Bus Type</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Price</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Status</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;color:#94a3b8;font-size:0.78rem;">#<?php echo intval($b['id']); ?></td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($b['user_name']); ?></div>
                        <div style="font-size:0.75rem;color:#94a3b8;"><?php echo htmlspecialchars($b['user_email']); ?></div>
                    </td>
                    <td style="padding:12px 16px;color:#374151;font-weight:500;"><?php echo htmlspecialchars($b['from_location']); ?></td>
                    <td style="padding:12px 16px;color:#374151;font-weight:500;"><?php echo htmlspecialchars($b['to_location']); ?></td>
                    <td style="padding:12px 16px;color:#374151;white-space:nowrap;"><?php echo htmlspecialchars($b['journey_date']); ?></td>
                    <td style="padding:12px 16px;color:#374151;text-align:center;"><?php echo intval($b['number_of_seats']); ?></td>
                    <td style="padding:12px 16px;">
                        <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;text-transform:capitalize;"><?php echo htmlspecialchars($b['bus_type']); ?></span>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:#059669;white-space:nowrap;">₱<?php echo number_format($b['total_price'], 2); ?></td>
                    <td style="padding:12px 16px;">
                        <?php
                        $status_colors = ['pending'=>'badge-pending','confirmed'=>'badge-confirmed','cancelled'=>'badge-cancelled'];
                        $sc = $status_colors[$b['status']] ?? 'badge-pending';
                        ?>
                        <span class="badge <?php echo $sc; ?>"><?php echo htmlspecialchars($b['status']); ?></span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:6px;align-items:center;flex-wrap:wrap;">
                            <!-- Status update -->
                            <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/bookings/<?php echo intval($b['id']); ?>/status" style="display:inline;">
                                <select name="status" onchange="this.form.submit()" style="padding:5px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:0.75rem;background:#fff;cursor:pointer;outline:none;" title="Change Status">
                                    <option value="pending"   <?php echo $b['status']==='pending'   ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $b['status']==='confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="cancelled" <?php echo $b['status']==='cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </form>
                            <!-- Delete -->
                            <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/bookings/<?php echo intval($b['id']); ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this booking permanently?');">
                                <button type="submit" style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
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
