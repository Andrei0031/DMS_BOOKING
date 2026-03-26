<?php
$panel = (($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin';
$title = 'Edit Bus - ' . ucfirst($panel) . ' Panel';
$page_title = 'Edit Bus';
ob_start();
?>

<div class="bg-white rounded-xl border border-gray-100 p-6" style="max-width:720px;">
    <div style="margin-bottom:24px;">
        <a href="/DMS_BOOKING/<?php echo $panel; ?>/buses" style="color:#64748b;font-size:0.82rem;text-decoration:none;font-weight:500;">
            <i class="fas fa-arrow-left mr-1"></i>Back to Buses
        </a>
        <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin-top:10px;">
            <i class="fas fa-edit text-blue-500 mr-2"></i>Edit Bus &mdash; <?php echo htmlspecialchars($bus['bus_number']); ?>
        </h2>
    </div>

    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/buses/<?php echo intval($bus['id']); ?>/update" style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
        <!-- Bus Number -->
        <div style="grid-column:span 2;">
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Bus Number <span style="color:#dc2626;">*</span></label>
            <input type="text" name="bus_number" required value="<?php echo htmlspecialchars($bus['bus_number']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- From -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">From Location <span style="color:#dc2626;">*</span></label>
            <input type="text" name="from_location" required value="<?php echo htmlspecialchars($bus['from_location']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- To -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">To Location <span style="color:#dc2626;">*</span></label>
            <input type="text" name="to_location" required value="<?php echo htmlspecialchars($bus['to_location']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Journey Date -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Journey Date <span style="color:#dc2626;">*</span></label>
            <input type="date" name="journey_date" required value="<?php echo htmlspecialchars($bus['journey_date']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Journey Time -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Departure Time</label>
            <input type="time" name="journey_time" value="<?php echo htmlspecialchars($bus['journey_time']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Total Seats -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Total Seats <span style="color:#dc2626;">*</span></label>
            <input type="number" name="total_seats" required min="1" max="100" value="<?php echo intval($bus['total_seats']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Available Seats -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Available Seats <span style="color:#dc2626;">*</span></label>
            <input type="number" name="available_seats" required min="0" value="<?php echo intval($bus['available_seats']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Price per Seat -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Price per Seat (₱) <span style="color:#dc2626;">*</span></label>
            <input type="number" name="price_per_seat" required min="1" step="0.01" value="<?php echo htmlspecialchars($bus['price_per_seat']); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Bus Type -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Bus Type</label>
            <select name="bus_type" style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;background:#fff;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                <option value="standard" <?php echo $bus['bus_type']==='standard' ? 'selected' : ''; ?>>Standard</option>
                <option value="ac"       <?php echo $bus['bus_type']==='ac'       ? 'selected' : ''; ?>>AC (Air-Conditioned)</option>
                <option value="sleeper"  <?php echo $bus['bus_type']==='sleeper'  ? 'selected' : ''; ?>>Sleeper</option>
            </select>
        </div>

        <!-- Buttons -->
        <div style="grid-column:span 2;display:flex;gap:10px;margin-top:6px;">
            <button type="submit" style="padding:11px 28px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);">
                <i class="fas fa-save mr-2"></i>Update Bus
            </button>
            <a href="/DMS_BOOKING/<?php echo $panel; ?>/buses" style="padding:11px 22px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
$layout = (($_SESSION['user']['type'] ?? '') === 'operator')
    ? __DIR__ . '/../../operator/layouts/app.blade.php'
    : __DIR__ . '/../../admin/layouts/app.blade.php';
include $layout;
?>
