<div class="bg-white rounded-xl border border-stone-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream text-stone-600">
                <tr>
                    <th class="text-left px-6 py-3">Timestamp</th>
                    <th class="text-left px-6 py-3">User</th>
                    <th class="text-left px-6 py-3">Action</th>
                    <th class="text-left px-6 py-3">Module</th>
                    <th class="text-left px-6 py-3">Description</th>
                    <th class="text-left px-6 py-3">IP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr class="border-t hover:bg-cream/30">
                    <td class="px-6 py-3 whitespace-nowrap"><?= date('M d, Y g:i A', strtotime($log['created_at'])) ?></td>
                    <td class="px-6 py-3"><?= e(($log['first_name'] ?? 'System') . ' ' . ($log['last_name'] ?? '')) ?></td>
                    <td class="px-6 py-3"><span class="px-2 py-1 bg-stone-100 rounded text-xs"><?= e($log['action']) ?></span></td>
                    <td class="px-6 py-3"><?= e($log['module']) ?></td>
                    <td class="px-6 py-3"><?= e($log['description'] ?? '') ?></td>
                    <td class="px-6 py-3 text-stone-400"><?= e($log['ip_address'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                <tr><td colspan="6" class="px-6 py-12 text-center text-stone-400">No audit logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
