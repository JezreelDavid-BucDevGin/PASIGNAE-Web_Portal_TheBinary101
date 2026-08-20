<?php foreach (['success' => 'emerald', 'error' => 'red', 'info' => 'blue'] as $type => $color): ?>
    <?php if ($msg = flash($type)): ?>
    <div role="alert" class="mb-4 flex items-center justify-between rounded-lg border border-<?= $color ?>-200 bg-<?= $color ?>-50 px-4 py-3 text-<?= $color ?>-800">
        <span><?= e($msg) ?></span>
        <button data-dismiss class="ml-4 text-<?= $color ?>-500 hover:text-<?= $color ?>-700">&times;</button>
    </div>
    <?php endif; ?>
<?php endforeach; ?>
