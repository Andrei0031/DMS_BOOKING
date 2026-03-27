<?php
$panel = (($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin';
$title = 'Manage Routes - ' . ucfirst($panel) . ' Panel';
$page_title = 'Popular Routes';
ob_start();
?>

<style>
@keyframes modalBackdropIn  { from { opacity:0; } to { opacity:1; } }
@keyframes modalBackdropOut { from { opacity:1; } to { opacity:0; } }
@keyframes modalSlideIn  { from { opacity:0; transform:translateY(-24px) scale(0.97); } to { opacity:1; transform:translateY(0) scale(1); } }
@keyframes modalSlideOut { from { opacity:1; transform:translateY(0) scale(1); } to { opacity:0; transform:translateY(-16px) scale(0.97); } }
#edit-route-modal          { animation: modalBackdropIn  0.2s ease forwards; }
#edit-route-modal.closing  { animation: modalBackdropOut 0.18s ease forwards; }
#edit-route-modal .modal-box          { animation: modalSlideIn  0.22s cubic-bezier(.4,0,.2,1) forwards; }
#edit-route-modal.closing .modal-box  { animation: modalSlideOut 0.18s cubic-bezier(.4,0,.2,1) forwards; }
/* Action icon buttons */
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:none;cursor:pointer;font-size:0.82rem;transition:transform 0.15s,opacity 0.15s;position:relative; }
.action-btn:hover { transform:scale(1.12);opacity:0.85; }
.action-btn[data-tip]:hover::after { content:attr(data-tip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#0f172a;color:#fff;font-size:0.7rem;font-weight:600;padding:4px 8px;border-radius:5px;white-space:nowrap;pointer-events:none;z-index:10; }
.action-btn[data-tip]:hover::before { content:'';position:absolute;bottom:calc(100% + 1px);left:50%;transform:translateX(-50%);border:5px solid transparent;border-top-color:#0f172a;pointer-events:none;z-index:10; }

@media (max-width: 1024px) {
    .routes-layout {
        grid-template-columns: 1fr !important;
    }

    .route-add-card {
        position: static !important;
        top: auto !important;
    }
}

@media (max-width: 768px) {
    .route-toolbar {
        align-items: flex-start !important;
    }

    #edit-route-form > div:first-child {
        grid-template-columns: 1fr !important;
    }
}
</style>

<!-- Edit Route Modal -->
<div id="edit-route-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(15,23,42,0.55);align-items:center;justify-content:center;padding:16px;">
    <div class="modal-box" style="background:#fff;border-radius:16px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,0.2);overflow:hidden;">
        <!-- Modal Header -->
        <div style="padding:20px 24px 16px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:8px;">
                <i class="fas fa-edit text-blue-500"></i> Edit Route
            </h3>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.2rem;line-height:1;padding:4px;" onmouseover="this.style.color='#475569'" onmouseout="this.style.color='#94a3b8'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Modal Body -->
        <form id="edit-route-form" method="POST" action="" style="padding:24px;display:flex;flex-direction:column;gap:16px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">From Location <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="edit-from" name="from_location" required
                        style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
                <div>
                    <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">To Location <span style="color:#dc2626;">*</span></label>
                    <input type="text" id="edit-to" name="to_location" required
                        style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                        onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                </div>
            </div>
            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">Travel Time</label>
                <input type="text" id="edit-duration" name="duration" placeholder="e.g. ~4 hours"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">Price From (₱)</label>
                <input type="number" id="edit-price" name="price_from" min="0" step="0.01" placeholder="e.g. 450"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <!-- Modal Footer -->
            <div style="display:flex;gap:10px;padding-top:4px;">
                <button type="submit"
                    style="flex:1;padding:10px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.25);transition:opacity 0.2s;"
                    onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
                <button type="button" onclick="closeEditModal()"
                    style="padding:10px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;cursor:pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="routes-layout" style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start;">

    <!-- Route List -->
    <div>
        <!-- Toolbar -->
        <div class="route-toolbar bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;align-items:center;justify-content:space-between;">
            <span style="color:#64748b;font-size:0.84rem;font-weight:500;">
                <i class="fas fa-route mr-1 text-blue-500"></i>
                <?php echo count($routes); ?> popular route(s) configured
            </span>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
            <?php if (empty($routes)): ?>
            <div style="padding:60px;text-align:center;color:#94a3b8;">
                <i class="fas fa-route" style="font-size:3rem;margin-bottom:12px;display:block;color:#cbd5e1;"></i>
                <p style="font-size:1rem;font-weight:500;">No popular routes added yet.</p>
                <p style="font-size:0.85rem;margin-top:6px;">Use the form on the right to add your first route.</p>
            </div>
            <?php else: ?>
            <div class="table-scroll" style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:0.84rem;">
                    <thead>
                        <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                            <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Route</th>
                            <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Travel Time</th>
                            <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Price From</th>
                            <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Status</th>
                            <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $route): ?>
                        <tr style="border-top:1px solid #f1f5f9;<?php echo $route['is_active'] ? '' : 'opacity:0.55;'; ?>"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:700;color:#0f172a;font-size:0.9rem;">
                                    <?php echo htmlspecialchars($route['from_location']); ?>
                                    <span style="color:#94a3b8;margin:0 4px;">↔</span>
                                    <?php echo htmlspecialchars($route['to_location']); ?>
                                </div>
                            </td>
                            <td style="padding:12px 16px;color:#374151;">
                                <?php echo $route['duration'] ? htmlspecialchars($route['duration']) : '<span style="color:#cbd5e1;">—</span>'; ?>
                            </td>
                            <td style="padding:12px 16px;font-weight:700;color:#059669;">
                                <?php echo $route['price_from'] ? '₱'.number_format($route['price_from'],2) : '<span style="color:#cbd5e1;font-weight:400;">—</span>'; ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <?php if ($route['is_active']): ?>
                                <span style="background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;">Active</span>
                                <?php else: ?>
                                <span style="background:#f1f5f9;color:#64748b;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:600;">Hidden</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="display:flex;align-items:center;gap:5px;">
                                    <!-- Move Up -->
                                    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes/<?php echo intval($route['id']); ?>/move-up" style="margin:0;">
                                        <button type="submit" class="action-btn" data-tip="Move Up" style="background:#f8fafc;color:#64748b;">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                    </form>
                                    <!-- Move Down -->
                                    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes/<?php echo intval($route['id']); ?>/move-down" style="margin:0;">
                                        <button type="submit" class="action-btn" data-tip="Move Down" style="background:#f8fafc;color:#64748b;">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </form>
                                    <!-- Edit -->
                                    <button type="button" class="action-btn" data-tip="Edit"
                                        onclick="openEditModal(<?php echo intval($route['id']); ?>, <?php echo htmlspecialchars(json_encode($route['from_location'])); ?>, <?php echo htmlspecialchars(json_encode($route['to_location'])); ?>, <?php echo htmlspecialchars(json_encode($route['duration'] ?? '')); ?>, <?php echo floatval($route['price_from']); ?>)"
                                        style="background:#eff6ff;color:#2563eb;">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <!-- Toggle visibility -->
                                    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes/<?php echo intval($route['id']); ?>/toggle" style="margin:0;">
                                        <button type="submit" class="action-btn" data-tip="<?php echo $route['is_active'] ? 'Hide' : 'Show'; ?>"
                                            style="background:<?php echo $route['is_active'] ? '#fef9c3' : '#f1f5f9'; ?>;color:<?php echo $route['is_active'] ? '#92400e' : '#64748b'; ?>;">
                                            <i class="fas <?php echo $route['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                        </button>
                                    </form>
                                    <!-- Delete -->
                                    <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes/<?php echo intval($route['id']); ?>/delete" style="margin:0;"
                                          onsubmit="return confirm('Delete route <?php echo htmlspecialchars(addslashes($route['from_location'].' ↔ '.$route['to_location'])); ?>?');">
                                        <button type="submit" class="action-btn" data-tip="Delete"
                                            style="background:#fee2e2;color:#dc2626;">
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
    </div>

    <!-- Add Route Form -->
    <div class="route-add-card bg-white rounded-xl border border-gray-100 p-6" style="position:sticky;top:84px;">
        <h2 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:18px;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-plus-circle text-blue-500"></i> Add New Route
        </h2>
        <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/routes" style="display:flex;flex-direction:column;gap:14px;">

            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">From Location <span style="color:#dc2626;">*</span></label>
                <input type="text" name="from_location" placeholder="e.g. Davao City" required
                    style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">To Location <span style="color:#dc2626;">*</span></label>
                <input type="text" name="to_location" placeholder="e.g. General Santos" required
                    style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">Travel Time</label>
                <input type="text" name="duration" placeholder="e.g. ~4 hours"
                    style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <div>
                <label style="display:block;font-size:0.78rem;font-weight:700;color:#374151;margin-bottom:5px;">Price From (₱)</label>
                <input type="number" name="price_from" placeholder="e.g. 450" min="0" step="0.01"
                    style="width:100%;padding:9px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;color:#0f172a;outline:none;box-sizing:border-box;"
                    onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#e2e8f0'">
            </div>

            <button type="submit"
                style="padding:10px;background:linear-gradient(135deg,#1d4ed8,#2563eb);color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:700;cursor:pointer;box-shadow:0 4px 12px rgba(37,99,235,0.3);transition:opacity 0.2s;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <i class="fas fa-plus mr-2"></i>Add Route
            </button>
        </form>
    </div>

</div>

<script>
function openEditModal(id, from, to, duration, price) {
    document.getElementById('edit-from').value     = from;
    document.getElementById('edit-to').value       = to;
    document.getElementById('edit-duration').value = duration;
    document.getElementById('edit-price').value    = price || '';
    document.getElementById('edit-route-form').action = '/DMS_BOOKING/<?php echo $panel; ?>/routes/' + id + '/update';
    var modal = document.getElementById('edit-route-modal');
    modal.classList.remove('closing');
    modal.style.display = 'flex';
    setTimeout(function() { document.getElementById('edit-from').focus(); }, 60);
}
function closeEditModal() {
    var modal = document.getElementById('edit-route-modal');
    modal.classList.add('closing');
    setTimeout(function() {
        modal.style.display = 'none';
        modal.classList.remove('closing');
    }, 180);
}
document.getElementById('edit-route-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeEditModal();
});
</script>

<?php
$content = ob_get_clean();
$layout = (($_SESSION['user']['type'] ?? '') === 'operator')
    ? __DIR__ . '/../../operator/layouts/app.blade.php'
    : __DIR__ . '/../../admin/layouts/app.blade.php';
include $layout;
?>
