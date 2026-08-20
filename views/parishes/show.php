<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-stone-100 shadow-sm p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 rounded-xl bg-navy/10 flex items-center justify-center text-3xl">⛪</div>
            <div>
                <h2 class="font-display text-2xl text-navy"><?= e($parish['name']) ?></h2>
                <p class="text-burgundy"><?= e($parish['vicariate_name']) ?></p>
            </div>
        </div>
        <div class="gold-line mb-6"></div>
        <dl class="space-y-4 text-sm">
            <?php if ($parish['priest_name']): ?>
            <div><dt class="text-stone-500">Parish Priest</dt><dd class="font-medium"><?= e($parish['priest_name']) ?></dd></div>
            <?php endif; ?>
            <div><dt class="text-stone-500">Address</dt><dd><?= e(trim(($parish['street'] ?? '') . ', ' . ($parish['barangay'] ?? '') . ', ' . ($parish['city'] ?? ''), ', ')) ?></dd></div>
            <?php if ($parish['contact_number']): ?>
            <div><dt class="text-stone-500">Contact</dt><dd><?= e($parish['contact_number']) ?></dd></div>
            <?php endif; ?>
            <?php if ($parish['email']): ?>
            <div><dt class="text-stone-500">Email</dt><dd><?= e($parish['email']) ?></dd></div>
            <?php endif; ?>
        </dl>
        <a href="<?= base_url('parishes') ?>" class="inline-block mt-8 text-sm text-burgundy hover:text-gold">← All Parishes</a>
    </div>
</div>
