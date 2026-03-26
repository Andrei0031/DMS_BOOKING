<?php
$panel = (($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin';
$title = 'Edit Route - ' . ucfirst($panel) . ' Panel';
$page_title = 'Edit Popular Route';
ob_start();
?>

<div class="bg-white rounded-xl border border-gray-100 p-6" style="max-width:560px;">
    <div style="margin-bottom:24px;">
        <a href="/DMS_BOOKING/<?php echo $panel; ?>/routes" style="color:#64748b;font-size:0.82rem;text-decoration:none;font-weight:500;">
            <i class="fas fa-arrow-left mr-1"></i>Back to Routes
        </a>
        <h2 style="font-size:1.25rem;font-weight:700;color:#0f172a;margin-top:10px;">
            <i class="fas fa-edit text-blue-500 mr-2"></i>Edit Route &mdash;
            <?php echo htmlspecialchars($route['from_location']); ?> ↔ <?php echo htmlspecialchars($route['to_location']); ?>
        </h2>
    </div>

    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes/<?php echo intval($route['id']); ?>/update"
          style="display:flex;flex-direction:column;gap:18px;">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <!-- From -->
            <div>
                <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">
                    From Location <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="from_location" required
                       value="<?php echo htmlspecialchars($route['from_location']); ?>"
                       style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <!-- To -->
            <div>
                <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">
                    To Location <span style="color:#dc2626;">*</span>
                </label>
                <input type="text" name="to_location" required
                       value="<?php echo htmlspecialchars($route['to_location']); ?>"
                       style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
        </div>

        <!-- Travel Time -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Travel Time</label>
            <input type="text" name="duration" placeholder="e.g. ~4 hours"
                   value="<?php echo htmlspecialchars($route['duration'] ?? ''); ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <!-- Price From -->
        <div>
            <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Price From (₱)</label>
            <input type="number" name="price_from" placeholder="e.g. 450" min="0" step="0.01"
                   value="<?php echo $route['price_from'] ? number_format($route['price_from'], 2, '.', '') : ''; ?>"
                   style="width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;"
                   onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
        </div>

        <div style="display:flex;gap:12px;padding-top:6px;">
            <button type="submit"
                style="padding:11px 28px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
            <a href="/DMS_BOOKING/<?php echo $panel; ?>/routes"
               style="padding:11px 22px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.875rem;font-weight:600;text-decoration:none;">
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
