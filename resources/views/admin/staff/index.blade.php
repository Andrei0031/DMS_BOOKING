<?php
$title = 'Staff Management - Admin Panel';
$page_title = 'Staff Management';

// Available permissions
$all_permissions = [
    'manage_routes' => ['label' => 'Manage Routes', 'icon' => 'fa-route', 'desc' => 'Create, edit, delete bus routes'],
    'manage_buses' => ['label' => 'Manage Buses', 'icon' => 'fa-bus', 'desc' => 'Add, edit, remove buses'],
    'manage_bookings' => ['label' => 'Manage Bookings', 'icon' => 'fa-ticket-alt', 'desc' => 'View and update booking status'],
    'manage_advisory' => ['label' => 'Manage Advisory', 'icon' => 'fa-bullhorn', 'desc' => 'Post travel advisories and announcements'],
    'view_reports' => ['label' => 'View Reports', 'icon' => 'fa-chart-bar', 'desc' => 'Access reports and analytics'],
    'manage_users' => ['label' => 'Manage Users', 'icon' => 'fa-users', 'desc' => 'View and manage customer accounts'],
];

ob_start();
?>

<style>
    .staff-modal-overlay {
        display:none;position:fixed;top:0;left:0;right:0;bottom:0;
        background:rgba(15,23,42,0.6);z-index:999;
        align-items:center;justify-content:center;
        padding:40px 20px;
        overflow-y:auto;
        backdrop-filter:blur(4px);
    }
    .staff-modal-overlay.active { display:flex; }
    .staff-modal {
        background:#fff;border-radius:16px;width:100%;max-width:520px;
        margin:auto 0;
        box-shadow:0 25px 60px rgba(0,0,0,0.25);
        animation: modalIn 0.2s ease-out;
        flex-shrink:0;
    }
    @keyframes modalIn {
        from { opacity:0; transform:scale(0.95) translateY(10px); }
        to { opacity:1; transform:scale(1) translateY(0); }
    }
    .staff-modal-header {
        padding:20px 24px;border-bottom:1px solid #e2e8f0;
        display:flex;justify-content:space-between;align-items:center;
        background:#fff;border-radius:16px 16px 0 0;
    }
    .staff-modal-body { padding:20px 24px; }
    .staff-modal-footer {
        padding:16px 24px;border-top:1px solid #e2e8f0;
        display:flex;gap:10px;justify-content:flex-end;
        background:#fff;border-radius:0 0 16px 16px;
    }
    .staff-field { margin-bottom:16px; }
    .staff-field label {
        display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;
    }
    .staff-field input, .staff-field select {
        width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;
        font-size:0.88rem;outline:none;box-sizing:border-box;transition:border-color 0.2s;
    }
    .staff-field input:focus, .staff-field select:focus { border-color:#2563eb; }
    .staff-field .hint { font-size:0.72rem;color:#94a3b8;margin-top:4px; }
    .perm-grid { display:grid;grid-template-columns:1fr 1fr;gap:8px; }
    .perm-item {
        display:flex;align-items:flex-start;gap:8px;padding:10px 12px;
        border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;
        transition:all 0.15s;user-select:none;
    }
    .perm-item:hover { border-color:#93c5fd;background:#eff6ff; }
    .perm-item.checked { border-color:#2563eb;background:#eff6ff; }
    .perm-item input[type=checkbox] { margin-top:2px;flex-shrink:0;accent-color:#2563eb; }
    .perm-item .perm-label { font-size:0.8rem;font-weight:600;color:#1e293b; }
    .perm-item .perm-desc { font-size:0.7rem;color:#64748b;margin-top:1px; }
    .btn-primary {
        padding:10px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;
        font-size:0.88rem;font-weight:600;cursor:pointer;transition:background 0.2s;
    }
    .btn-primary:hover { background:#1d4ed8; }
    .btn-secondary {
        padding:10px 20px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;
        font-size:0.88rem;font-weight:600;cursor:pointer;transition:background 0.2s;
    }
    .btn-secondary:hover { background:#e2e8f0; }
    .btn-close {
        background:none;border:none;width:32px;height:32px;border-radius:8px;
        font-size:1.2rem;color:#94a3b8;cursor:pointer;display:flex;align-items:center;
        justify-content:center;transition:all 0.15s;
    }
    .btn-close:hover { background:#f1f5f9;color:#0f172a; }
    @media (max-width: 640px) {
        .perm-grid { grid-template-columns:1fr; }
    }
</style>

<!-- Toolbar -->
<div class="bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <form method="GET" action="/DMS_BOOKING/admin/staff" style="display:flex;gap:10px;align-items:center;">
            <div style="position:relative;">
                <i class="fas fa-search" style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:0.8rem;"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search staff..."
                       style="padding:8px 12px 8px 32px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.84rem;width:220px;outline:none;"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'">
            </div>
            <?php if (isset($role_filter) && $role_filter): ?>
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role_filter); ?>">
            <?php endif; ?>
            <button type="submit" style="padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.84rem;font-weight:600;cursor:pointer;">
                <i class="fas fa-search" style="margin-right:4px;"></i>Search
            </button>
            <?php if ($search || $role_filter): ?>
            <a href="/DMS_BOOKING/admin/staff" style="padding:8px 14px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.84rem;font-weight:600;text-decoration:none;">
                <i class="fas fa-times" style="margin-right:4px;"></i>Clear
            </a>
            <?php endif; ?>
        </form>
        <div style="display:flex;gap:4px;margin-left:8px;">
            <a href="/DMS_BOOKING/admin/staff" style="padding:6px 14px;border-radius:8px;font-size:0.78rem;font-weight:600;text-decoration:none;<?php echo !$role_filter ? 'background:#2563eb;color:#fff;' : 'background:#f1f5f9;color:#64748b;'; ?>">All</a>
            <a href="/DMS_BOOKING/admin/staff?role=admin" style="padding:6px 14px;border-radius:8px;font-size:0.78rem;font-weight:600;text-decoration:none;<?php echo $role_filter === 'admin' ? 'background:#7c3aed;color:#fff;' : 'background:#f1f5f9;color:#64748b;'; ?>">Admins</a>
            <a href="/DMS_BOOKING/admin/staff?role=operator" style="padding:6px 14px;border-radius:8px;font-size:0.78rem;font-weight:600;text-decoration:none;<?php echo $role_filter === 'operator' ? 'background:#0891b2;color:#fff;' : 'background:#f1f5f9;color:#64748b;'; ?>">Operators</a>
        </div>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        <span style="color:#64748b;font-size:0.82rem;font-weight:500;">
            <i class="fas fa-user-tie" style="margin-right:4px;color:#6366f1;"></i><?php 
                $staffCount = 0;
                if (is_object($staff) && isset($staff->num_rows)) {
                    $staffCount = $staff->num_rows;
                } elseif (is_array($staff)) {
                    $staffCount = count($staff);
                }
                echo $staffCount;
            ?> staff member(s)
        </span>
        <button onclick="openModal('add-staff-modal')" class="btn-primary" style="padding:8px 16px;font-size:0.84rem;">
            <i class="fas fa-plus" style="margin-right:4px;"></i>Add Staff
        </button>
    </div>
</div>

<!-- Staff Table -->
<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <?php if (empty($staff)): ?>
    <div style="padding:60px;text-align:center;color:#94a3b8;">
        <i class="fas fa-user-tie" style="font-size:3rem;margin-bottom:12px;display:block;color:#cbd5e1;"></i>
        <p style="font-size:1rem;font-weight:500;">No staff members found.</p>
    </div>
    <?php else: ?>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.84rem;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">#</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Name</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Email</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Phone</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Role</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Permissions</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Joined</th>
                    <th style="padding:12px 16px;text-align:left;color:#64748b;font-weight:700;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff as $s):
                    $current_user_id = $_SESSION['user']['id'] ?? 0;
                    $is_self = (intval($s['id']) === intval($current_user_id));
                    $perms = $s['permissions'] ? json_decode($s['permissions'], true) : [];
                    if (!is_array($perms)) $perms = [];
                ?>
                <tr style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:12px 16px;color:#94a3b8;font-size:0.78rem;"><?php echo intval($s['id']); ?></td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="background:<?php echo $s['role'] === 'admin' ? '#ede9fe' : '#e0f2fe'; ?>;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas <?php echo $s['role'] === 'admin' ? 'fa-user-shield' : 'fa-headset'; ?>" style="color:<?php echo $s['role'] === 'admin' ? '#7c3aed' : '#0891b2'; ?>;font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;color:#0f172a;"><?php echo htmlspecialchars($s['name']); ?></div>
                                <?php if ($is_self): ?>
                                <div style="font-size:0.7rem;color:#2563eb;font-weight:500;">You</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#374151;"><?php echo htmlspecialchars($s['email']); ?></td>
                    <td style="padding:12px 16px;color:#374151;"><?php echo $s['phone'] ? htmlspecialchars($s['phone']) : '<span style="color:#cbd5e1;">—</span>'; ?></td>
                    <td style="padding:12px 16px;">
                        <?php if ($s['role'] === 'admin'): ?>
                        <span style="background:#ede9fe;color:#7c3aed;padding:4px 12px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;">
                            <i class="fas fa-shield-alt" style="margin-right:3px;"></i>Admin
                        </span>
                        <?php else: ?>
                        <span style="background:#e0f2fe;color:#0891b2;padding:4px 12px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;">
                            <i class="fas fa-headset" style="margin-right:3px;"></i>Operator
                        </span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;">
                        <?php if ($s['role'] === 'admin'): ?>
                        <span style="color:#7c3aed;font-size:0.72rem;font-weight:600;">All Access</span>
                        <?php elseif (empty($perms)): ?>
                        <span style="color:#cbd5e1;font-size:0.72rem;">No permissions</span>
                        <?php else: ?>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            <?php foreach ($perms as $p):
                                $info = $all_permissions[$p] ?? null;
                                if (!$info) continue;
                            ?>
                            <span style="background:#f1f5f9;color:#475569;padding:2px 8px;border-radius:6px;font-size:0.68rem;font-weight:500;white-space:nowrap;">
                                <i class="fas <?php echo $info['icon']; ?>" style="margin-right:2px;font-size:0.6rem;"></i><?php echo $info['label']; ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;color:#64748b;white-space:nowrap;font-size:0.78rem;">
                        <?php echo date('M j, Y', strtotime($s['created_at'])); ?>
                    </td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;gap:6px;">
                            <button onclick='openEditModal(<?php echo json_encode([
                                "id" => intval($s["id"]),
                                "name" => $s["name"],
                                "email" => $s["email"],
                                "phone" => $s["phone"] ?? "",
                                "role" => $s["role"],
                                "permissions" => $perms,
                                "is_self" => $is_self
                            ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)' style="padding:5px 11px;background:#eff6ff;color:#2563eb;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="Edit Staff">
                                <i class="fas fa-pen"></i>
                            </button>
                            <?php if (!$is_self): ?>
                            <form method="POST" action="/DMS_BOOKING/admin/staff/<?php echo intval($s['id']); ?>/role" style="display:inline;">
                                <?php if ($s['role'] === 'operator'): ?>
                                <input type="hidden" name="role" value="admin">
                                <button type="submit" title="Promote to Admin" style="padding:5px 11px;background:#ede9fe;color:#7c3aed;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    <i class="fas fa-arrow-up"></i>
                                </button>
                                <?php else: ?>
                                <input type="hidden" name="role" value="operator">
                                <button type="submit" title="Demote to Operator" style="padding:5px 11px;background:#e0f2fe;color:#0891b2;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                                <?php endif; ?>
                            </form>
                            <form method="POST" action="/DMS_BOOKING/admin/staff/<?php echo intval($s['id']); ?>/delete" style="display:inline;" onsubmit="return confirm('Delete staff member <?php echo htmlspecialchars(addslashes($s['name'])); ?>?');">
                                <button type="submit" style="padding:5px 11px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:0.75rem;font-weight:600;cursor:pointer;" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════ ADD STAFF MODAL ═══════════════ -->
<div id="add-staff-modal" class="staff-modal-overlay">
    <div class="staff-modal">
        <div class="staff-modal-header">
            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;">
                <i class="fas fa-user-plus" style="margin-right:8px;color:#2563eb;"></i>Add Staff Member
            </h3>
            <button class="btn-close" onclick="closeModal('add-staff-modal')">&times;</button>
        </div>
        <form method="POST" action="/DMS_BOOKING/admin/staff/create">
            <div class="staff-modal-body">
                <div class="staff-field">
                    <label>Full Name</label>
                    <input type="text" name="name" required placeholder="Enter full name">
                </div>
                <div class="staff-field">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Enter email address">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="staff-field">
                        <label>Phone <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
                        <input type="tel" name="phone" placeholder="+63 XXX XXX XXXX" oninput="this.value=this.value.replace(/[^0-9+]/g,'')">
                    </div>
                    <div class="staff-field">
                        <label>Role</label>
                        <select name="role" required id="add-role-select" onchange="toggleAddPerms()">
                            <option value="operator">Operator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="staff-field">
                    <label>Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimum 6 characters">
                </div>

                <!-- Permissions (only for operators) -->
                <div id="add-perms-section">
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:8px;">
                        <i class="fas fa-key" style="margin-right:4px;color:#f59e0b;"></i>Permissions
                    </label>
                    <div class="perm-grid">
                        <?php foreach ($all_permissions as $key => $p): ?>
                        <label class="perm-item" onclick="this.classList.toggle('checked')">
                            <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>">
                            <div>
                                <div class="perm-label"><i class="fas <?php echo $p['icon']; ?>" style="margin-right:4px;font-size:0.7rem;color:#64748b;"></i><?php echo $p['label']; ?></div>
                                <div class="perm-desc"><?php echo $p['desc']; ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="staff-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('add-staff-modal')">Cancel</button>
                <button type="submit" class="btn-primary"><i class="fas fa-plus" style="margin-right:4px;"></i>Create Staff</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════ EDIT STAFF MODAL ═══════════════ -->
<div id="edit-staff-modal" class="staff-modal-overlay">
    <div class="staff-modal">
        <div class="staff-modal-header">
            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;">
                <i class="fas fa-user-edit" style="margin-right:8px;color:#2563eb;"></i>Edit Staff Member
            </h3>
            <button class="btn-close" onclick="closeModal('edit-staff-modal')">&times;</button>
        </div>
        <form method="POST" id="edit-staff-form" action="">
            <div class="staff-modal-body">
                <div class="staff-field">
                    <label>Full Name</label>
                    <input type="text" name="name" id="edit-name" required>
                </div>
                <div class="staff-field">
                    <label>Email</label>
                    <input type="email" name="email" id="edit-email" required>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="staff-field">
                        <label>Phone</label>
                        <input type="tel" name="phone" id="edit-phone" oninput="this.value=this.value.replace(/[^0-9+]/g,'')">
                    </div>
                    <div class="staff-field">
                        <label>Role</label>
                        <select name="role" id="edit-role" onchange="toggleEditPerms()">
                            <option value="operator">Operator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="staff-field">
                    <label>New Password <span style="color:#94a3b8;font-weight:400;">(leave blank to keep current)</span></label>
                    <input type="password" name="password" id="edit-password" minlength="6" placeholder="Enter new password or leave blank">
                </div>

                <!-- Permissions -->
                <div id="edit-perms-section">
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:8px;">
                        <i class="fas fa-key" style="margin-right:4px;color:#f59e0b;"></i>Permissions
                    </label>
                    <div class="perm-grid">
                        <?php foreach ($all_permissions as $key => $p): ?>
                        <label class="perm-item" id="edit-perm-<?php echo $key; ?>" onclick="this.classList.toggle('checked')">
                            <input type="checkbox" name="permissions[]" value="<?php echo $key; ?>" id="edit-perm-cb-<?php echo $key; ?>">
                            <div>
                                <div class="perm-label"><i class="fas <?php echo $p['icon']; ?>" style="margin-right:4px;font-size:0.7rem;color:#64748b;"></i><?php echo $p['label']; ?></div>
                                <div class="perm-desc"><?php echo $p['desc']; ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="staff-modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('edit-staff-modal')">Cancel</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save" style="margin-right:4px;"></i>Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.add('active');
}
function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}
function toggleAddPerms() {
    var role = document.getElementById('add-role-select').value;
    document.getElementById('add-perms-section').style.display = (role === 'admin') ? 'none' : 'block';
}
function toggleEditPerms() {
    var role = document.getElementById('edit-role').value;
    document.getElementById('edit-perms-section').style.display = (role === 'admin') ? 'none' : 'block';
}
function openEditModal(data) {
    document.getElementById('edit-staff-form').action = '/DMS_BOOKING/admin/staff/' + data.id + '/edit';
    document.getElementById('edit-name').value = data.name;
    document.getElementById('edit-email').value = data.email;
    document.getElementById('edit-phone').value = data.phone || '';
    document.getElementById('edit-role').value = data.role;
    document.getElementById('edit-password').value = '';

    // Disable role change for self
    document.getElementById('edit-role').disabled = data.is_self;

    // Reset all permission checkboxes
    var allPerms = <?php echo json_encode(array_keys($all_permissions)); ?>;
    allPerms.forEach(function(p) {
        var cb = document.getElementById('edit-perm-cb-' + p);
        var item = document.getElementById('edit-perm-' + p);
        if (cb) { cb.checked = false; }
        if (item) { item.classList.remove('checked'); }
    });

    // Check the ones the user has
    if (data.permissions && Array.isArray(data.permissions)) {
        data.permissions.forEach(function(p) {
            var cb = document.getElementById('edit-perm-cb-' + p);
            var item = document.getElementById('edit-perm-' + p);
            if (cb) { cb.checked = true; }
            if (item) { item.classList.add('checked'); }
        });
    }

    toggleEditPerms();
    openModal('edit-staff-modal');
}

// Init: check add-modal permissions visibility
toggleAddPerms();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../admin/layouts/app.blade.php';
?>
