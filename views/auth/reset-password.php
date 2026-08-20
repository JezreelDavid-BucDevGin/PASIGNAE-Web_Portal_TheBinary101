<?php $layout = 'guest'; ?>

<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <h1 class="font-display text-3xl text-navy text-center mb-8">Reset Password</h1>
        <div class="bg-white rounded-2xl shadow-xl border border-stone-100 p-8">
            <?php require VIEW_PATH . '/components/alerts.php'; ?>
            <form method="POST" action="<?= base_url('reset-password') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">New Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 py-3 bg-burgundy text-white rounded-lg hover:bg-burgundy/90 transition">Reset Password</button>
            </form>
        </div>
    </div>
</section>
