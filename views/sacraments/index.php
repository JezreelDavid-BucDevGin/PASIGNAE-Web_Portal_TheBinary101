<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <p class="text-stone-600">Manage and track sacrament requests</p>
    <?php if (has_role('parishioner')): ?>
    <div class="flex gap-2">
        <?php foreach ($types as $t): ?>
        <a href="<?= base_url('sacraments/' . $t['slug']) ?>" class="px-4 py-2 bg-burgundy text-white text-sm rounded-lg hover:bg-burgundy/90 transition">
            + <?= e($t['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<div class="bg-white rounded-xl border border-stone-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream text-stone-600">
                <tr>
                    <th class="text-left px-6 py-3">#</th>
                    <th class="text-left px-6 py-3">Sacrament</th>
                    <th class="text-left px-6 py-3">Applicant</th>
                    <th class="text-left px-6 py-3">Parish</th>
                    <th class="text-left px-6 py-3">Schedule</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <?php if (!has_role('parishioner')): ?><th class="text-left px-6 py-3">Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                <tr class="border-t border-stone-50 hover:bg-cream/30">
                    <td class="px-6 py-4"><?= $req['id'] ?></td>
                    <td class="px-6 py-4 font-medium"><?= e($req['sacrament_name']) ?></td>
                    <td class="px-6 py-4"><?= e($req['first_name'] . ' ' . $req['last_name']) ?></td>
                    <td class="px-6 py-4"><?= e($req['parish_name']) ?></td>
                    <td class="px-6 py-4"><?= $req['event_date'] ? format_date($req['event_date']) . ' ' . date('g:i A', strtotime($req['start_time'])) : '—' ?></td>
                    <td class="px-6 py-4"><?php $status = $req['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                    <?php if (!has_role('parishioner')): ?>
                    <td class="px-6 py-4">
                        <?php if ($req['status'] === 'pending'): ?>
                        <form method="POST" action="<?= base_url('sacraments/status') ?>" class="inline-flex gap-1">
                            <?= csrf_field() ?>
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <button name="status" value="approved" class="px-2 py-1 text-xs bg-emerald-100 text-emerald-700 rounded hover:bg-emerald-200">Approve</button>
                            <button name="status" value="rejected" class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200">Reject</button>
                        </form>
                        <?php endif; ?>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($requests)): ?>
                <tr><td colspan="7" class="px-6 py-12 text-center text-stone-400">No sacrament requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
