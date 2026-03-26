<?php
$panel = $panel ?? ((($_SESSION['user']['type'] ?? '') === 'operator') ? 'operator' : 'admin');
$title = 'Advisory - ' . ucfirst($panel) . ' Panel';
$page_title = 'Travel Advisories';
ob_start();

$type_styles = [
    'info'    => ['bg' => '#dbeafe', 'color' => '#1d4ed8', 'icon' => 'fa-info-circle',         'label' => 'Info'],
    'warning' => ['bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-exclamation-triangle', 'label' => 'Warning'],
    'danger'  => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'fa-exclamation-circle',   'label' => 'Danger'],
    'success' => ['bg' => '#d1fae5', 'color' => '#059669', 'icon' => 'fa-check-circle',         'label' => 'Success'],
];
?>

<!-- Toolbar -->
<div class="bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <div>
        <span style="color:#64748b;font-size:0.82rem;font-weight:500;">
            <?php echo count($advisories ?? []); ?> advisory(ies) posted
        </span>
    </div>
    <button onclick="document.getElementById('add-advisory-modal').style.display='flex'" style="padding:8px 18px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.84rem;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;">
        <i class="fas fa-plus"></i> New Advisory
    </button>
</div>

<!-- Advisories List -->
<?php if (empty($advisories)): ?>
<div class="bg-white rounded-xl border border-gray-100 p-12" style="text-align:center;color:#94a3b8;">
    <i class="fas fa-bullhorn" style="font-size:3rem;margin-bottom:12px;display:block;"></i>
    <p style="font-size:1rem;font-weight:500;">No advisories posted yet.</p>
    <p style="font-size:0.85rem;margin-top:4px;">Click "New Advisory" to create one.</p>
</div>
<?php else: ?>
<div style="display:flex;flex-direction:column;gap:12px;">
    <?php foreach ($advisories as $a):
        $t = $type_styles[$a['type']] ?? $type_styles['info'];
    ?>
    <div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;<?php echo $a['is_active'] ? '' : 'opacity:0.55;'; ?>">
        <div style="display:flex;align-items:stretch;">
            <!-- Type color bar -->
            <div style="width:5px;background:<?php echo $t['color']; ?>;flex-shrink:0;"></div>
            <div style="flex:1;padding:16px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="background:<?php echo $t['bg']; ?>;width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fas <?php echo $t['icon']; ?>" style="color:<?php echo $t['color']; ?>;font-size:0.95rem;"></i>
                        </div>
                        <div>
                            <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;margin:0;"><?php echo htmlspecialchars($a['title']); ?></h3>
                            <div style="font-size:0.72rem;color:#94a3b8;margin-top:2px;">
                                <span class="badge" style="background:<?php echo $t['bg']; ?>;color:<?php echo $t['color']; ?>"><?php echo $t['label']; ?></span>
                                <?php if (!empty($a['status'])): ?>
                                &middot; <span class="badge" style="background:#fef3c7;color:#92400e;"><?php echo htmlspecialchars($a['status']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($a['bus_number'])): ?>
                                &middot; <span style="color:#2563eb;font-weight:600;"><i class="fas fa-bus" style="font-size:0.65rem;"></i> <?php echo htmlspecialchars($a['bus_number']); ?></span>
                                <span style="color:#64748b;">(<?php echo htmlspecialchars($a['bus_from'] . ' → ' . $a['bus_to']); ?>)</span>
                                <?php endif; ?>
                                &middot; by <?php echo htmlspecialchars($a['author_name'] ?? 'Unknown'); ?>
                                &middot; <?php echo date('M j, Y g:i A', strtotime($a['created_at'])); ?>
                                <?php if (!$a['is_active']): ?>
                                &middot; <span style="color:#dc2626;font-weight:600;">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex;gap:6px;flex-shrink:0;">
                        <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/advisory/<?php echo intval($a['id']); ?>/toggle" style="display:inline;">
                            <button type="submit" style="padding:6px 12px;background:<?php echo $a['is_active'] ? '#fef3c7' : '#d1fae5'; ?>;color:<?php echo $a['is_active'] ? '#d97706' : '#059669'; ?>;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="<?php echo $a['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                <i class="fas <?php echo $a['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                                <?php echo $a['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                        <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/advisory/<?php echo intval($a['id']); ?>/delete" style="display:inline;" onsubmit="return confirm('Delete this advisory?');">
                            <button type="submit" style="padding:6px 12px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div style="margin-top:12px;color:#374151;font-size:0.875rem;line-height:1.6;">
                    <?php echo nl2br(htmlspecialchars($a['message'])); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Add Advisory Modal -->
<div id="add-advisory-modal" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);overflow-y:auto;padding:40px 16px 40px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:560px;box-shadow:0 25px 50px rgba(0,0,0,0.25);margin:0 auto;max-height:calc(100vh - 80px);display:flex;flex-direction:column;">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
            <h2 style="font-size:1.1rem;font-weight:700;color:#0f172a;"><i class="fas fa-bullhorn text-blue-500 mr-2"></i>New Advisory</h2>
            <button onclick="document.getElementById('add-advisory-modal').style.display='none'" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:1.2rem;"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="/DMS_BOOKING/<?php echo $panel; ?>/advisory" style="padding:24px;overflow-y:auto;flex:1;">
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Title *</label>
                <input type="text" name="title" required placeholder="e.g. Route Delay Notice" style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;box-sizing:border-box;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Type</label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php foreach ($type_styles as $key => $ts): ?>
                    <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid #e2e8f0;border-radius:8px;cursor:pointer;font-size:0.82rem;font-weight:600;transition:all 0.2s;" onclick="this.parentElement.querySelectorAll('label').forEach(l=>l.style.borderColor='#e2e8f0');this.style.borderColor='<?php echo $ts['color']; ?>'">
                        <input type="radio" name="type" value="<?php echo $key; ?>" <?php echo $key==='info'?'checked':''; ?> style="display:none;">
                        <i class="fas <?php echo $ts['icon']; ?>" style="color:<?php echo $ts['color']; ?>;"></i>
                        <span style="color:<?php echo $ts['color']; ?>;"><?php echo $ts['label']; ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;"><i class="fas fa-bus text-blue-400 mr-1"></i>Affected Bus <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <select name="bus_id" id="adv-bus-select" style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;outline:none;box-sizing:border-box;background:#fff;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">— General (no specific bus) —</option>
                        <?php foreach ($buses ?? [] as $bus): ?>
                        <option value="<?php echo $bus['id']; ?>"><?php echo htmlspecialchars($bus['bus_number'] . ' — ' . $bus['from_location'] . ' → ' . $bus['to_location']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;"><i class="fas fa-signal text-orange-400 mr-1"></i>Status <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                    <select name="status" style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;outline:none;box-sizing:border-box;background:#fff;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
                        <option value="">— None —</option>
                        <option value="Delayed">🕐 Delayed</option>
                        <option value="Cancelled">❌ Cancelled</option>
                        <option value="Rerouted">🔀 Rerouted</option>
                        <option value="On Time">✅ On Time</option>
                        <option value="Maintenance">🔧 Maintenance</option>
                        <option value="Full">🈵 Fully Booked</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Message *</label>
                <textarea name="message" required rows="5" placeholder="Enter your advisory message..." style="width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;outline:none;resize:vertical;box-sizing:border-box;" onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('add-advisory-modal').style.display='none'" style="padding:10px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;cursor:pointer;">Cancel</button>
                <button type="submit" style="padding:10px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.875rem;font-weight:600;cursor:pointer;">
                    <i class="fas fa-paper-plane mr-1"></i> Post Advisory
                </button>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
$layout = (($_SESSION['user']['type'] ?? '') === 'operator')
    ? __DIR__ . '/../../operator/layouts/app.blade.php'
    : __DIR__ . '/../../admin/layouts/app.blade.php';
include $layout;
?>
