<?php
$title = 'Reports - Operator Panel';
$page_title = 'Reports';
ob_start();
?>

<!-- Summary Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:24px;">
    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#d1fae5;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-peso-sign" style="color:#059669;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Revenue</span>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:#059669;line-height:1;">₱<?php echo number_format(floatval($total_revenue ?? 0), 2); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Confirmed Bookings</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#dbeafe;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-ticket-alt" style="color:#2563eb;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Bookings</span>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:#2563eb;line-height:1;"><?php echo intval($total_bookings ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Total Bookings</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#fef3c7;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-hourglass-half" style="color:#d97706;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Pending</span>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:#d97706;line-height:1;"><?php echo intval($pending ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Awaiting Confirmation</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#d1fae5;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-check-circle" style="color:#059669;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Confirmed</span>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:#059669;line-height:1;"><?php echo intval($confirmed ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Confirmed Bookings</div>
    </div>

    <div class="stat-card bg-white rounded-xl p-5 border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div style="background:#fee2e2;width:44px;height:44px;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-times-circle" style="color:#dc2626;font-size:1.1rem;"></i>
            </div>
            <span style="font-size:0.7rem;color:#6b7280;font-weight:500;text-transform:uppercase;letter-spacing:0.05em;">Cancelled</span>
        </div>
        <div style="font-size:1.8rem;font-weight:800;color:#dc2626;line-height:1;"><?php echo intval($cancelled ?? 0); ?></div>
        <div style="color:#6b7280;font-size:0.8rem;margin-top:4px;">Cancelled Bookings</div>
    </div>
</div>

<!-- Recent Confirmed Bookings (Revenue Breakdown) -->
<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
        <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;">
            <i class="fas fa-chart-line text-green-500 mr-2"></i>Recent Confirmed Bookings
        </h3>
        <span style="font-size:0.78rem;color:#64748b;font-weight:500;"><?php echo count($confirmed_bookings ?? []); ?> booking(s)</span>
    </div>
    <?php if (empty($confirmed_bookings)): ?>
    <div style="padding:40px;text-align:center;color:#94a3b8;">
        <i class="fas fa-chart-bar" style="font-size:2rem;margin-bottom:8px;"></i>
        <p style="font-size:0.875rem;">No confirmed bookings yet.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;">Passenger</th>
                    <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;">Route</th>
                    <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;">Date</th>
                    <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;">Seats</th>
                    <th style="padding:10px 16px;text-align:left;color:#64748b;font-weight:600;">Type</th>
                    <th style="padding:10px 16px;text-align:right;color:#64748b;font-weight:600;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($confirmed_bookings as $b): ?>
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:10px 16px;font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($b['user_name']); ?></td>
                    <td style="padding:10px 16px;color:#475569;"><?php echo htmlspecialchars($b['from_location']); ?> &rarr; <?php echo htmlspecialchars($b['to_location']); ?></td>
                    <td style="padding:10px 16px;color:#475569;white-space:nowrap;"><?php echo htmlspecialchars($b['journey_date']); ?></td>
                    <td style="padding:10px 16px;color:#475569;text-align:center;"><?php echo intval($b['number_of_seats']); ?></td>
                    <td style="padding:10px 16px;">
                        <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;text-transform:capitalize;"><?php echo htmlspecialchars($b['bus_type']); ?></span>
                    </td>
                    <td style="padding:10px 16px;font-weight:700;color:#059669;text-align:right;white-space:nowrap;">₱<?php echo number_format($b['total_price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="border-top:2px solid #e2e8f0;background:#f8fafc;">
                    <td colspan="5" style="padding:12px 16px;font-weight:700;color:#0f172a;text-align:right;">Total Revenue</td>
                    <td style="padding:12px 16px;font-weight:800;color:#059669;text-align:right;font-size:0.95rem;">₱<?php echo number_format(floatval($total_revenue ?? 0), 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layouts/app.blade.php';
?>
