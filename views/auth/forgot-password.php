<?php $layout = 'guest'; ?>

<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <h1 class="font-display text-3xl text-navy text-center mb-8">Forgot Password</h1>
        <div class="bg-white rounded-2xl shadow-xl border border-stone-100 p-8">
            <?php require VIEW_PATH . '/components/alerts.php'; ?>
            <form method="POST" action="<?= base_url('forgot-password') ?>">
                <?= csrf_field() ?>
                <label class="block text-sm font-medium text-stone-700 mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none mb-4">
                <button type="submit" class="w-full py-3 bg-navy text-white rounded-lg hover:bg-navy/90 transition">Send Reset Link</button>
            </form>
            <a href="<?= base_url('login') ?>" class="block text-center text-sm text-burgundy mt-4">← Back to Sign In</a>
        </div>
    </div>
</section>
