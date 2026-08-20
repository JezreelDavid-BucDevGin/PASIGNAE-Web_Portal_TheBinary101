<!-- Stats Cards -->
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500 mb-1">Active Parishes</p>
        <p class="text-3xl font-display text-navy"><?= $stats['total_parishes'] ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500 mb-1">Registered Users</p>
        <p class="text-3xl font-display text-navy"><?= $stats['total_users'] ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500 mb-1">Total Revenue</p>
        <p class="text-3xl font-display text-burgundy"><?= format_currency($stats['total_revenue']) ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500 mb-1">Pending Requests</p>
        <p class="text-3xl font-display text-gold">
            <?php
            $pending = 0;
            foreach ($stats['requests_by_status'] as $r) {
                if ($r['status'] === 'pending') $pending = $r['count'];
            }
            echo $pending;
            ?>
        </p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <!-- Requests by Type -->
    <div class="bg-white rounded-xl border border-stone-100 shadow-sm p-6">
        <h2 class="font-display text-xl text-navy mb-4">Sacraments Overview</h2>
        <div class="space-y-3">
            <?php foreach ($stats['requests_by_type'] as $type): ?>
            <div class="flex items-center justify-between">
                <span class="text-stone-600"><?= e($type['name']) ?></span>
                <div class="flex items-center gap-3">
                    <div class="w-32 h-2 bg-stone-100 rounded-full overflow-hidden">
                        <div class="h-full bg-burgundy rounded-full" style="width: <?= min(100, ($type['count'] ?: 0) * 20) ?>%"></div>
                    </div>
                    <span class="text-sm font-medium text-navy w-8 text-right"><?= $type['count'] ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl border border-stone-100 shadow-sm p-6">
        <h2 class="font-display text-xl text-navy mb-4">Recent Activity</h2>
        <div class="space-y-3 max-h-64 overflow-y-auto">
            <?php foreach ($stats['recent_activity'] as $log): ?>
            <div class="flex items-start gap-3 text-sm border-b border-stone-50 pb-3">
                <div class="w-2 h-2 rounded-full bg-gold mt-2 flex-shrink-0"></div>
                <div>
                    <p class="text-stone-700"><?= e($log['description'] ?? $log['action']) ?></p>
                    <p class="text-xs text-stone-400"><?= e(($log['first_name'] ?? 'System') . ' — ' . date('M d, g:i A', strtotime($log['created_at']))) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($stats['recent_activity'])): ?>
            <p class="text-stone-400 text-sm">No activity yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Requests Table -->
<div class="mt-8 bg-white rounded-xl border border-stone-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-stone-100">
        <h2 class="font-display text-xl text-navy">Recent Sacrament Requests</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream text-stone-600">
                <tr>
                    <th class="text-left px-6 py-3">ID</th>
                    <th class="text-left px-6 py-3">Sacrament</th>
                    <th class="text-left px-6 py-3">Parishioner</th>
                    <th class="text-left px-6 py-3">Parish</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats['recent_requests'] as $req): ?>
                <tr class="border-t border-stone-50 hover:bg-cream/50">
                    <td class="px-6 py-3">#<?= $req['id'] ?></td>
                    <td class="px-6 py-3"><?= e($req['sacrament_name']) ?></td>
                    <td class="px-6 py-3"><?= e($req['first_name'] . ' ' . $req['last_name']) ?></td>
                    <td class="px-6 py-3"><?= e($req['parish_name']) ?></td>
                    <td class="px-6 py-3"><?php $status = $req['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                    <td class="px-6 py-3"><?= format_date($req['requested_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
