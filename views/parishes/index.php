<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($parishes as $parish): ?>
    <a href="<?= base_url('parishes/' . $parish['id']) ?>" class="card-sacred block bg-white rounded-xl p-6 border border-stone-100 hover:border-gold/40">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-lg bg-navy/10 flex items-center justify-center text-xl">⛪</div>
            <div>
                <h3 class="font-display text-lg text-navy"><?= e($parish['name']) ?></h3>
                <p class="text-xs text-burgundy mt-1"><?= e($parish['vicariate_name']) ?></p>
                <p class="text-sm text-stone-500 mt-2"><?= e($parish['full_address'] ?? '') ?></p>
                <?php if ($parish['priest_name']): ?>
                <p class="text-xs text-stone-400 mt-2"><?= e($parish['priest_name']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </a>
    <?php endforeach; ?>
</div>
