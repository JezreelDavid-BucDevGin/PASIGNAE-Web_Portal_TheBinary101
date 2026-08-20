<?php $layout = 'guest'; ?>

<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-full bg-navy/10 flex items-center justify-center mx-auto mb-4 border border-gold/30">
                <span class="text-2xl text-burgundy">✝</span>
            </div>
            <h1 class="font-display text-3xl text-navy mb-2">Welcome Back</h1>
            <p class="text-stone-500">Sign in to your PASIGNAE account</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-navy/5 border border-stone-100 p-8">
            <?php require VIEW_PATH . '/components/alerts.php'; ?>

            <form method="POST" action="<?= base_url('login') ?>" data-loading>
                <?= csrf_field() ?>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="<?= e(old('email')) ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none transition"
                               placeholder="you@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Password</label>
                        <input type="password" name="password" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none transition"
                               placeholder="••••••••">
                    </div>
                    <div class="flex justify-end">
                        <a href="<?= base_url('forgot-password') ?>" class="text-sm text-burgundy hover:text-gold transition">Forgot password?</a>
                    </div>
                    <button type="submit" class="w-full py-3 bg-navy text-white font-medium rounded-lg hover:bg-navy/90 transition shadow-lg shadow-navy/20">
                        Sign In
                    </button>
                </div>
            </form>

            <p class="text-center text-sm text-stone-500 mt-6">
                Don't have an account? <a href="<?= base_url('register') ?>" class="text-burgundy font-medium hover:text-gold transition">Register</a>
            </p>
        </div>

        <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200 text-sm text-amber-800">
            <strong>Demo accounts:</strong><br>
            admin@pasignae.local / password<br>
            parishioner@pasignae.local / password
        </div>
    </div>
</section>
