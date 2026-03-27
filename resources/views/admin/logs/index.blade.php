<?php
$title = 'Activity Logs - ' . ucfirst($panel ?? 'admin') . ' Panel';
$page_title = 'Activity Logs';
ob_start();
?>

<style>
    @media (max-width: 768px) {
        .logs-toolbar {
            align-items: stretch !important;
        }

        .logs-toolbar form {
            width: 100%;
        }

        .logs-toolbar .logs-count {
            width: 100%;
        }
    }
</style>

<div class="logs-toolbar bg-white rounded-xl p-4 border border-gray-100 mb-5" style="display:flex;flex-wrap:wrap;gap:10px;align-items:center;justify-content:space-between;">
    <form method="GET" action="/DMS_BOOKING/<?php echo htmlspecialchars($panel); ?>/logs" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
        <select name="role" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;outline:none;">
            <option value="">All Roles</option>
            <option value="admin" <?php echo ($role_filter ?? '') === 'admin' ? 'selected' : ''; ?>>Admin</option>
            <option value="operator" <?php echo ($role_filter ?? '') === 'operator' ? 'selected' : ''; ?>>Operator</option>
        </select>

        <select name="entity" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;outline:none;">
            <option value="">All Entities</option>
            <?php foreach (($entities ?? []) as $e):
                $entity = $e['entity'] ?? '';
                if ($entity === '') continue;
            ?>
            <option value="<?php echo htmlspecialchars($entity); ?>" <?php echo ($entity_filter ?? '') === $entity ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars(ucfirst($entity)); ?>
            </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="search" value="<?php echo htmlspecialchars($search ?? ''); ?>" placeholder="Search actor, action, details..."
               style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:0.82rem;min-width:220px;outline:none;">

        <button type="submit" style="padding:8px 14px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:0.82rem;font-weight:600;cursor:pointer;">
            <i class="fas fa-filter mr-1"></i>Apply
        </button>

        <a href="/DMS_BOOKING/<?php echo htmlspecialchars($panel); ?>/logs" style="padding:8px 12px;background:#f1f5f9;color:#475569;border-radius:8px;font-size:0.82rem;font-weight:600;text-decoration:none;">
            <i class="fas fa-rotate-left mr-1"></i>Reset
        </a>
    </form>

    <div class="logs-count" style="color:#64748b;font-size:0.82rem;font-weight:600;">
        <i class="fas fa-list-check mr-1 text-indigo-500"></i>
        <?php echo count($activity_logs ?? []); ?> log(s)
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100" style="overflow:hidden;">
    <div class="table-scroll" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:0.8rem;min-width:1000px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:10px 14px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Time</th>
                    <th style="padding:10px 14px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Actor</th>
                    <th style="padding:10px 14px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Role</th>
                    <th style="padding:10px 14px;text-align:left;color:#64748b;font-weight:700;white-space:nowrap;">Action</th>
                    <th style="padding:10px 14px;text-align:left;color:#64748b;font-weight:700;">Details</th>
                </tr>
            </thead>
            <tbody id="logs-table-body">
                <?php if (empty($activity_logs)): ?>
                <tr id="logs-empty-row">
                    <td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;">
                        <i class="fas fa-clipboard-list" style="font-size:1.6rem;margin-bottom:8px;display:block;"></i>
                        No activity logs found.
                    </td>
                </tr>
                <?php endif; ?>
                <?php foreach ($activity_logs as $log):
                    $details = [];
                    if (!empty($log['details'])) {
                        $decoded = json_decode($log['details'], true);
                        if (is_array($decoded)) $details = $decoded;
                    }
                ?>
                <tr data-log-id="<?php echo intval($log['id']); ?>" style="border-top:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                    <td style="padding:10px 14px;color:#475569;white-space:nowrap;font-size:0.75rem;">
                        <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                    </td>
                    <td style="padding:10px 14px;color:#0f172a;font-weight:600;white-space:nowrap;">
                        <?php echo htmlspecialchars($log['actor_name'] ?? 'Unknown'); ?>
                    </td>
                    <td style="padding:10px 14px;white-space:nowrap;">
                        <?php if (($log['actor_type'] ?? '') === 'admin'): ?>
                            <span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:9999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;">Admin</span>
                        <?php else: ?>
                            <span style="background:#e0f2fe;color:#0891b2;padding:3px 10px;border-radius:9999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;">Operator</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:10px 14px;color:#0f172a;white-space:nowrap;font-weight:600;">
                        <?php echo htmlspecialchars($log['action'] ?? ''); ?>
                    </td>
                    <td style="padding:10px 14px;color:#334155;min-width:260px;">
                        <?php if (empty($details)): ?>
                            <span style="color:#94a3b8;">-</span>
                        <?php else: ?>
                            <div style="display:flex;flex-wrap:wrap;gap:5px;">
                                <?php foreach ($details as $k => $v):
                                    if (is_array($v)) $v = implode(', ', $v);
                                ?>
                                <span style="background:#f1f5f9;color:#475569;padding:3px 8px;border-radius:6px;font-size:0.7rem;white-space:nowrap;">
                                    <?php echo htmlspecialchars($k); ?>: <?php echo htmlspecialchars((string)$v); ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function () {
    var logsBody = document.getElementById('logs-table-body');
    if (!logsBody) return;

    var countNode = document.querySelector('.logs-count');
    var params = new URLSearchParams(window.location.search);
    var latestRow = logsBody.querySelector('tr[data-log-id]');
    var latestId = latestRow ? parseInt(latestRow.getAttribute('data-log-id'), 10) : 0;
    var endpoint = '/DMS_BOOKING/<?php echo htmlspecialchars($panel); ?>/logs/live';
    var streamEndpoint = '/DMS_BOOKING/<?php echo htmlspecialchars($panel); ?>/logs/stream';
    var isPollingStarted = false;

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function roleBadge(role) {
        if (role === 'admin') {
            return '<span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:9999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;">Admin</span>';
        }
        return '<span style="background:#e0f2fe;color:#0891b2;padding:3px 10px;border-radius:9999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;">Operator</span>';
    }

    function formatTime(ts) {
        var d = new Date(ts.replace(' ', 'T'));
        if (isNaN(d.getTime())) return escapeHtml(ts);
        return d.toLocaleString(undefined, {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        });
    }

    function detailChips(detailsRaw) {
        var parsed;
        try {
            parsed = detailsRaw ? JSON.parse(detailsRaw) : null;
        } catch (e) {
            parsed = null;
        }

        if (!parsed || typeof parsed !== 'object') {
            return '<span style="color:#94a3b8;">-</span>';
        }

        var chips = [];
        Object.keys(parsed).forEach(function (key) {
            var val = parsed[key];
            if (Array.isArray(val)) val = val.join(', ');
            chips.push(
                '<span style="background:#f1f5f9;color:#475569;padding:3px 8px;border-radius:6px;font-size:0.7rem;white-space:nowrap;">' +
                escapeHtml(key) + ': ' + escapeHtml(String(val)) +
                '</span>'
            );
        });

        return chips.length ? '<div style="display:flex;flex-wrap:wrap;gap:5px;">' + chips.join('') + '</div>' : '<span style="color:#94a3b8;">-</span>';
    }

    function rowHtml(log) {
        return '<tr data-log-id="' + log.id + '" style="border-top:1px solid #f1f5f9;">' +
            '<td style="padding:10px 14px;color:#475569;white-space:nowrap;font-size:0.75rem;">' + formatTime(log.created_at) + '</td>' +
            '<td style="padding:10px 14px;color:#0f172a;font-weight:600;white-space:nowrap;">' + escapeHtml(log.actor_name || 'Unknown') + '</td>' +
            '<td style="padding:10px 14px;white-space:nowrap;">' + roleBadge(log.actor_type || '') + '</td>' +
            '<td style="padding:10px 14px;color:#0f172a;white-space:nowrap;font-weight:600;">' + escapeHtml(log.action || '') + '</td>' +
            '<td style="padding:10px 14px;color:#334155;min-width:260px;">' + detailChips(log.details || '') + '</td>' +
        '</tr>';
    }

    function updateCount() {
        if (!countNode) return;
        var rows = logsBody.querySelectorAll('tr[data-log-id]').length;
        countNode.innerHTML = '<i class="fas fa-list-check mr-1 text-indigo-500"></i> ' + rows + ' log(s)';
    }

    function ingestLogs(logs) {
        if (!logs || !logs.length) return;

        var emptyRow = document.getElementById('logs-empty-row');
        if (emptyRow) emptyRow.remove();

        logs.forEach(function (log) {
            logsBody.insertAdjacentHTML('afterbegin', rowHtml(log));
            if (log.id > latestId) latestId = log.id;
        });

        while (logsBody.querySelectorAll('tr[data-log-id]').length > 100) {
            var rows = logsBody.querySelectorAll('tr[data-log-id]');
            rows[rows.length - 1].remove();
        }

        updateCount();
    }

    function poll() {
        var q = new URLSearchParams(params.toString());
        q.set('since_id', String(latestId));

        fetch(endpoint + '?' + q.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (res) {
                if (!res.ok) throw new Error('poll-failed');
                return res.json();
            })
            .then(function (data) {
                var logs = (data && data.logs) ? data.logs : [];
                ingestLogs(logs);
            })
            .catch(function () {
                // Fail silently to avoid disrupting page use.
            });
    }

    function startPollingFallback() {
        if (isPollingStarted) return;
        isPollingStarted = true;
        setInterval(poll, 5000);
    }

    if (typeof EventSource !== 'undefined') {
        var q = new URLSearchParams(params.toString());
        q.set('since_id', String(latestId));
        var source = new EventSource(streamEndpoint + '?' + q.toString());

        source.addEventListener('log', function (event) {
            try {
                var log = JSON.parse(event.data);
                ingestLogs([log]);
            } catch (e) {
                // Ignore malformed event data.
            }
        });

        source.onerror = function () {
            try { source.close(); } catch (e) {}
            startPollingFallback();
        };
    } else {
        startPollingFallback();
    }
})();
</script>

<?php
$content = ob_get_clean();
$layout = (($panel ?? 'admin') === 'operator')
    ? __DIR__ . '/../../operator/layouts/app.blade.php'
    : __DIR__ . '/../layouts/app.blade.php';
include $layout;
?>
