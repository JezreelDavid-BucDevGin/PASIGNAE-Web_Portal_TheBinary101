<?php $layout = 'guest'; ?>

<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md text-center">
        <div class="w-16 h-16 rounded-full bg-gold/20 flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">📧</span>
        </div>
        <h1 class="font-display text-3xl text-navy mb-2">Verify Your Email</h1>
        <p class="text-stone-500 mb-8">Enter the 6-digit code sent to your email</p>

        <div class="bg-white rounded-2xl shadow-xl border border-stone-100 p-8">
            <?php require VIEW_PATH . '/components/alerts.php'; ?>

            <form method="POST" action="<?= base_url('verify-otp') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= (int)$user_id ?>">
                <input type="text" name="otp_code" maxlength="6" pattern="[0-9]{6}" required
                       class="w-full text-center text-3xl tracking-[0.5em] font-mono px-4 py-4 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none mb-6"
                       placeholder="000000" autocomplete="one-time-code">
                <button type="submit" class="w-full py-3 bg-navy text-white font-medium rounded-lg hover:bg-navy/90 transition">
                    Verify Email
                </button>
            </form>

            <form method="POST" action="<?= base_url('resend-otp') ?>" class="mt-4">
                <?= csrf_field() ?>
                <input type="hidden" name="user_id" value="<?= (int)$user_id ?>">
                <button type="submit" class="text-sm text-burgundy hover:text-gold transition">Resend OTP</button>
            </form>

            <p class="text-xs text-stone-400 mt-6">Check storage/logs/mail.log if email is not configured</p>
        </div>
    </div>
</section>
