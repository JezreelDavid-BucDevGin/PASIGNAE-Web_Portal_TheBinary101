<?php if (can_access('schedules')): ?>
<div class="bg-white rounded-xl border border-stone-100 p-6 mb-8 shadow-sm">
    <h2 class="font-display text-lg text-navy mb-4">Create Schedule</h2>
    <form method="POST" action="<?= base_url('schedules') ?>" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm mb-1">Parish</label>
            <select name="parish_id" required class="w-full px-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-gold/30">
                <?php foreach ($parishes as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Sacrament</label>
            <select name="sacrament_type_id" required class="w-full px-3 py-2 border rounded-lg outline-none">
                <?php foreach ($sacrament_types as $t): ?><option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">Date</label>
            <input type="date" name="event_date" required class="w-full px-3 py-2 border rounded-lg outline-none">
        </div>
        <div>
            <label class="block text-sm mb-1">Start Time</label>
            <input type="time" name="start_time" required class="w-full px-3 py-2 border rounded-lg outline-none">
        </div>
        <div>
            <label class="block text-sm mb-1">End Time</label>
            <input type="time" name="end_time" class="w-full px-3 py-2 border rounded-lg outline-none">
        </div>
        <div>
            <label class="block text-sm mb-1">Max Slots</label>
            <input type="number" name="max_slots" value="5" min="1" class="w-full px-3 py-2 border rounded-lg outline-none">
        </div>
        <div class="sm:col-span-2 lg:col-span-3">
            <button type="submit" class="px-6 py-2 bg-navy text-white rounded-lg hover:bg-navy/90 transition">Add Schedule</button>
        </div>
    </form>
</div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-stone-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream"><tr>
                <th class="text-left px-6 py-3">Parish</th>
                <th class="text-left px-6 py-3">Sacrament</th>
                <th class="text-left px-6 py-3">Date</th>
                <th class="text-left px-6 py-3">Time</th>
                <th class="text-left px-6 py-3">Slots</th>
                <th class="text-left px-6 py-3">Status</th>
            </tr></thead>
            <tbody>
                <?php foreach ($schedules as $s): ?>
                <tr class="border-t hover:bg-cream/30">
                    <td class="px-6 py-3"><?= e($s['parish_name']) ?></td>
                    <td class="px-6 py-3"><?= e($s['sacrament_name']) ?></td>
                    <td class="px-6 py-3"><?= format_date($s['event_date']) ?></td>
                    <td class="px-6 py-3"><?= date('g:i A', strtotime($s['start_time'])) ?><?= $s['end_time'] ? ' - ' . date('g:i A', strtotime($s['end_time'])) : '' ?></td>
                    <td class="px-6 py-3"><?= $s['booked_slots'] ?>/<?= $s['max_slots'] ?></td>
                    <td class="px-6 py-3"><?php $status = $s['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($schedules)): ?>
                <tr><td colspan="6" class="px-6 py-12 text-center text-stone-400">No schedules found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
