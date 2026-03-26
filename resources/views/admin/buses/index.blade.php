<?php
$title = 'Manage Buses - Admin Panel';
$page_title = 'Manage Buses';
ob_start();
?>

<!-- Toolbar -->
<div class="bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <span style="color:#64748b;font-size:0.84rem;font-weight:500;">
        <i class="fas fa-bus mr-1 text-blue-500"></i> <?php echo count($buses); ?> bus(es) in fleet
    </span>
    <a href="/DMS_BOOKING/admin/buses/create" style="padding:9px 18px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border-radius:8px;font-size:0.84rem;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:7px;box-shadow:0 4px 12px rgba(37,99,235,0.3);transition:opacity 0.2s;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
        <i class="fas fa-plus"></i> Add New Bus
    </a>
</div>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <?php if (empty($buses)): ?>
    <div style="padding:60px;text-align:center;color:#94a3b8;">
        <i class="fas fa-bus" style="font-size:3rem;margin-bottom:12px;display:block;color:#cbd5e1;"></i>
        <p style="font-size:1rem;font-weight:500;">No buses added yet.</p>
        <a href="/DMS_BOOKING/admin/buses/create" style="display:inline-block;margin-top:14px;background:#2563eb;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.875rem;">
            <i class="fas fa-plus mr-2"></i>Add First Bus
        </a>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.84rem;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Bus #</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Route</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Journey Date</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Departure</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Seats (Avail/Total)</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Price/Seat</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Type</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($buses as $bus): ?>
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;">
                        <span style="font-weight:700;color:#1d4ed8;font-size:0.9rem;"><?php echo htmlspecialchars($bus['bus_number']); ?></span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($bus['from_location']); ?></div>
                        <div style="font-size:0.75rem;color:#94a3b8;display:flex;align-items:center;gap:4px;"><i class="fas fa-arrow-down" style="font-size:0.6rem;"></i><?php echo htmlspecialchars($bus['to_location']); ?></div>
                    </td>
                    <td style="padding:12px 16px;color:#374151;white-space:nowrap;"><?php echo htmlspecialchars($bus['journey_date']); ?></td>
                    <td style="padding:12px 16px;color:#374151;white-space:nowrap;"><?php echo htmlspecialchars($bus['journey_time']); ?></td>
                    <td style="padding:12px 16px;">
                        <?php
                        $avail = intval($bus['available_seats']);
                        $total = intval($bus['total_seats']);
                        $pct   = $total > 0 ? ($avail / $total) : 0;
                        $bar_color = $pct > 0.5 ? '#059669' : ($pct > 0.2 ? '#d97706' : '#dc2626');
                        ?>
                        <div style="font-weight:600;color:#374151;"><?php echo $avail; ?> / <?php echo $total; ?></div>
                        <div style="margin-top:4px;background:#e2e8f0;border-radius:9999px;height:4px;width:80px;">
                            <div style="background:<?php echo $bar_color; ?>;height:4px;border-radius:9999px;width:<?php echo round($pct*100); ?>%;"></div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;font-weight:700;color:#059669;white-space:nowrap;">₱<?php echo number_format($bus['price_per_seat'], 2); ?></td>
                    <td style="padding:12px 16px;">
                        <?php
                        $type_colors = ['standard'=>'#e0f2fe:#0369a1', 'ac'=>'#d1fae5:#065f46', 'sleeper'=>'#ede9fe:#6d28d9'];
                        $tc = explode(':', $type_colors[$bus['bus_type']] ?? '#f1f5f9:#475569');
                        ?>
                        <span style="background:<?php echo $tc[0]; ?>;color:<?php echo $tc[1]; ?>;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;text-transform:capitalize;"><?php echo htmlspecialchars($bus['bus_type']); ?></span>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:6px;">
                            <a href="/DMS_BOOKING/admin/buses/<?php echo intval($bus['id']); ?>/edit" style="padding:6px 12px;background:#eff6ff;color:#2563eb;border-radius:6px;font-size:0.78rem;font-weight:600;text-decoration:none;" title="Edit">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </a>
                            <form method="POST" action="/DMS_BOOKING/admin/buses/<?php echo intval($bus['id']); ?>/delete" onsubmit="return confirm('Delete bus <?php echo htmlspecialchars(addslashes($bus['bus_number'])); ?>?');">
                                <button type="submit" style="padding:6px 12px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:0.78rem;font-weight:600;cursor:pointer;" title="Delete">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete
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
include __DIR__ . '/../../admin/layouts/app.blade.php';
?>
