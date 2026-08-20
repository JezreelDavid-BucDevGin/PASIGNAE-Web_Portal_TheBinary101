<div class="grid sm:grid-cols-2 gap-6 mb-8">
    <div class="bg-gradient-to-br from-navy to-burgundy rounded-xl p-6 text-white">
        <p class="text-gold text-sm mb-1">Welcome back</p>
        <h2 class="font-display text-2xl"><?= e(auth()['first_name']) ?>!</h2>
        <p class="text-stone-300 text-sm mt-2"><?= e(auth()['parish_name'] ?? 'Diocese of Pasig') ?></p>
    </div>
    <div class="bg-white rounded-xl p-6 border border-stone-100 shadow-sm">
        <p class="text-sm text-stone-500">My Requests</p>
        <p class="text-3xl font-display text-navy"><?= count($stats['my_requests']) ?></p>
        <a href="<?= base_url('sacraments/baptism') ?>" class="inline-block mt-3 text-sm text-burgundy hover:text-gold transition">+ New Request</a>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid sm:grid-cols-3 gap-4 mb-8">
    <?php foreach (['baptism' => ['💧','Baptism'], 'matrimony' => ['💍','Matrimony'], 'funeral' => ['🕊️','Funeral']] as $slug => [$icon, $name]): ?>
    <a href="<?= base_url("sacraments/{$slug}") ?>" class="card-sacred bg-white rounded-xl p-6 border border-stone-100 text-center hover:border-gold/40">
        <span class="text-3xl"><?= $icon ?></span>
        <p class="font-display text-lg text-navy mt-2"><?= $name ?></p>
        <p class="text-xs text-stone-400 mt-1">Apply Online</p>
    </a>
    <?php endforeach; ?>
</div>

<!-- My Requests -->
<div class="bg-white rounded-xl border border-stone-100 overflow-hidden">
    <div class="p-6 border-b"><h2 class="font-display text-xl text-navy">My Sacrament Requests</h2></div>
    <?php if (empty($stats['my_requests'])): ?>
    <p class="p-8 text-center text-stone-400">No requests yet. Start by applying for a sacrament above.</p>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-cream"><tr>
                <th class="text-left px-6 py-3">Sacrament</th><th class="text-left px-6 py-3">Parish</th>
                <th class="text-left px-6 py-3">Status</th><th class="text-left px-6 py-3">Date</th>
            </tr></thead>
            <tbody>
                <?php foreach ($stats['my_requests'] as $req): ?>
                <tr class="border-t hover:bg-cream/50">
                    <td class="px-6 py-3"><?= e($req['sacrament_name']) ?></td>
                    <td class="px-6 py-3"><?= e($req['parish_name']) ?></td>
                    <td class="px-6 py-3"><?php $status = $req['status']; require VIEW_PATH . '/components/status-badge.php'; ?></td>
                    <td class="px-6 py-3"><?= format_date($req['requested_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
