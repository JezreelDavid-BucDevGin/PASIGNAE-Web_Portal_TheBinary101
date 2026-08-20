<?php $layout = 'guest'; ?>

<section class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <h1 class="font-display text-3xl text-navy mb-2">Create Account</h1>
            <p class="text-stone-500">Join the PASIGNAE parish community</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-navy/5 border border-stone-100 p-8">
            <?php require VIEW_PATH . '/components/alerts.php'; ?>

            <form method="POST" action="<?= base_url('register') ?>" data-loading>
                <?= csrf_field() ?>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" value="<?= e(old('first_name')) ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" value="<?= e(old('last_name')) ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-stone-700 mb-1">Middle Name</label>
                        <input type="text" name="middle_name" value="<?= e(old('middle_name')) ?>"
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-stone-700 mb-1">Email *</label>
                        <input type="email" name="email" value="<?= e(old('email')) ?>" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Phone</label>
                        <input type="tel" name="phone" value="<?= e(old('phone')) ?>"
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Parish</label>
                        <select name="parish_id" class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                            <option value="">Select parish</option>
                            <?php foreach ($parishes as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= old('parish_id') == $p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Password *</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Confirm Password *</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 focus:border-gold outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 py-3 bg-burgundy text-white font-medium rounded-lg hover:bg-burgundy/90 transition">
                    Create Account
                </button>
            </form>

            <p class="text-center text-sm text-stone-500 mt-6">
                Already registered? <a href="<?= base_url('login') ?>" class="text-burgundy font-medium">Sign In</a>
            </p>
        </div>
    </div>
</section>
