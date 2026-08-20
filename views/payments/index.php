<div class="grid sm:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm sm:col-span-1">
        <p class="text-sm text-stone-500">Total Collected</p>
        <p class="text-2xl font-display text-burgundy"><?= format_currency($total_revenue) ?></p>
    </div>
</div>

<div class="bg-white rounded-xl border border-stone-100 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream text-stone-600">
                <tr>
                    <th class="text-left px-6 py-3">ID</th>
                    <th class="text-left px-6 py-3">Sacrament</th>
                    <th class="text-left px-6 py-3">Parishioner</th>
                    <th class="text-left px-6 py-3">Amount</th>
                    <th class="text-left px-6 py-3">Method</th>
                    <th class="text-left px-6 py-3">Status</th>
                    <th class="text-left px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $pay): ?>
                <tr class="border-t hover:bg-cream/30">
                    <td class="px-6 py-4">#<?= $pay['id'] ?></td>
                    <td class="px-6 py-4"><?= e($pay['sacrament_name']) ?></td>
                    <td class="px-6 py-4"><?= e($pay['first_name'] . ' ' . $pay['last_name']) ?></td>
                    <td class="px-6 py-4 font-medium"><?= format_currency((float)$pay['amount']) ?></td>
                    <td class="px-6 py-4 uppercase text-xs"><?= e($pay['payment_method']) ?></td>
                    <td class="px-6 py-4"><?php $status = $pay['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                    <td class="px-6 py-4">
                        <?php if ($pay['status'] === 'pending' && has_role('parishioner')): ?>
                        <form method="POST" action="<?= base_url('payments/pay') ?>" class="flex gap-2 items-center">
                            <?= csrf_field() ?>
                            <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                            <input type="text" name="reference_number" placeholder="GCash Ref #" required
                                   class="px-2 py-1 text-xs border rounded w-28">
                            <button class="px-3 py-1 text-xs bg-emerald-600 text-white rounded hover:bg-emerald-700">Pay</button>
                        </form>
                        <?php elseif ($pay['status'] === 'pending' && can_access('payments')): ?>
                        <form method="POST" action="<?= base_url('payments/confirm') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="payment_id" value="<?= $pay['id'] ?>">
                            <button class="px-3 py-1 text-xs bg-navy text-white rounded">Confirm</button>
                        </form>
                        <?php else: ?>
                        <span class="text-stone-400 text-xs"><?= $pay['paid_at'] ? format_date($pay['paid_at'], 'M d, Y g:i A') : '—' ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                <tr><td colspan="7" class="px-6 py-12 text-center text-stone-400">No payments found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
