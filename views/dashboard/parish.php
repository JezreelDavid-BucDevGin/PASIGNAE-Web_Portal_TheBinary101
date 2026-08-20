<div class="grid sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500">Parish Revenue</p>
        <p class="text-2xl font-display text-burgundy mt-1"><?= format_currency($stats['total_revenue']) ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500">Pending Requests</p>
        <p class="text-2xl font-display text-gold mt-1">
            <?php foreach ($stats['requests_by_status'] as $r) { if ($r['status']==='pending') echo $r['count']; } ?>
        </p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500">Your Parish</p>
        <p class="text-lg font-medium text-navy mt-1"><?= e(auth()['parish_name'] ?? '—') ?></p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-xl border border-stone-100 p-6">
        <h2 class="font-display text-xl text-navy mb-4">Requests by Sacrament</h2>
        <?php foreach ($stats['requests_by_type'] as $type): ?>
        <div class="flex justify-between py-2 border-b border-stone-50">
            <span><?= e($type['name']) ?></span>
            <span class="font-medium"><?= $type['count'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="bg-white rounded-xl border border-stone-100 p-6">
        <h2 class="font-display text-xl text-navy mb-4">Recent Payments</h2>
        <?php foreach ($stats['recent_payments'] as $pay): ?>
        <div class="flex justify-between py-2 border-b border-stone-50 text-sm">
            <span><?= e($pay['sacrament_name']) ?> — <?= e($pay['first_name']) ?></span>
            <span class="font-medium"><?= format_currency((float)$pay['amount']) ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($stats['recent_payments'])): ?><p class="text-stone-400 text-sm">No payments yet.</p><?php endif; ?>
    </div>
</div>

<div class="mt-8 bg-white rounded-xl border border-stone-100 overflow-hidden">
    <div class="p-6 border-b"><h2 class="font-display text-xl text-navy">Parish Requests</h2></div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream"><tr>
                <th class="text-left px-6 py-3">ID</th><th class="text-left px-6 py-3">Sacrament</th>
                <th class="text-left px-6 py-3">Applicant</th><th class="text-left px-6 py-3">Status</th>
                <th class="text-left px-6 py-3">Date</th>
            </tr></thead>
            <tbody>
                <?php foreach ($stats['recent_requests'] as $req): ?>
                <tr class="border-t hover:bg-cream/50">
                    <td class="px-6 py-3">#<?= $req['id'] ?></td>
                    <td class="px-6 py-3"><?= e($req['sacrament_name']) ?></td>
                    <td class="px-6 py-3"><?= e($req['first_name'].' '.$req['last_name']) ?></td>
                    <td class="px-6 py-3"><?php $status = $req['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                    <td class="px-6 py-3"><?= format_date($req['requested_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
