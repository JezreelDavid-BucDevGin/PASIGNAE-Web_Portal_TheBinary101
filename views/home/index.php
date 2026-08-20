<?php $layout = 'guest'; ?>

<!-- Hero Section -->
<section class="hero-gradient relative min-h-[90vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-64 h-64 rounded-full bg-gold blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-burgundy blur-3xl"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur border border-gold/30 text-gold text-sm mb-8">
            <span>✝</span> Diocese of Pasig Official Portal
        </div>
        <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl text-white font-bold leading-tight mb-6">
            Welcome to <span class="text-gold">PASIGNAE</span>
        </h1>
        <p class="text-xl text-stone-300 max-w-2xl mx-auto mb-4 font-light">
            Parish Information & Sacramental Network Administration Engine
        </p>
        <p class="text-stone-400 max-w-xl mx-auto mb-10">
            Digitizing church services across all parishes — baptism, matrimony, funeral, scheduling, and more.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php if (is_auth()): ?>
                <a href="<?= base_url('dashboard') ?>" class="px-8 py-4 bg-gold text-navy font-semibold rounded-lg hover:bg-gold/90 transition shadow-lg shadow-gold/20">
                    Go to Dashboard
                </a>
                <a href="<?= base_url('sacraments/baptism') ?>" class="px-8 py-4 border-2 border-white/30 text-white rounded-lg hover:bg-white/10 transition">
                    Request a Sacrament
                </a>
            <?php else: ?>
                <a href="<?= base_url('register') ?>" class="px-8 py-4 bg-gold text-navy font-semibold rounded-lg hover:bg-gold/90 transition shadow-lg shadow-gold/20">
                    Get Started
                </a>
                <a href="<?= base_url('login') ?>" class="px-8 py-4 border-2 border-white/30 text-white rounded-lg hover:bg-white/10 transition">
                    Sign In
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 gold-line"></div>
</section>

<!-- Services Section -->
<section id="services" class="py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl text-navy mb-4">Sacramental Services</h2>
            <div class="gold-line w-24 mx-auto mb-4"></div>
            <p class="text-stone-600 max-w-lg mx-auto">Request and manage sacraments online across the Diocese of Pasig</p>
        </div>
        <div class="grid md:grid-cols-3 gap-8">
            <?php
            $icons = ['baptism' => '💧', 'matrimony' => '💍', 'funeral' => '🕊️'];
            $descriptions = [
                'baptism' => 'Welcome your child into the Catholic faith with online baptism applications and scheduling.',
                'matrimony' => 'Plan your sacred union with matrimony applications, witness records, and wedding scheduling.',
                'funeral' => 'Arrange funeral masses and burial services with compassion and dignity.',
            ];
            foreach ($sacraments as $sacrament):
            ?>
            <div class="card-sacred relative cross-watermark bg-white rounded-2xl p-8 border border-stone-100 shadow-sm">
                <div class="text-4xl mb-4"><?= $icons[$sacrament['slug']] ?? '✝' ?></div>
                <h3 class="font-display text-2xl text-navy mb-3"><?= e($sacrament['name']) ?></h3>
                <p class="text-stone-600 text-sm mb-6 leading-relaxed"><?= e($descriptions[$sacrament['slug']] ?? '') ?></p>
                <div class="flex items-center justify-between">
                    <span class="text-burgundy font-semibold"><?= format_currency((float)$sacrament['fee']) ?></span>
                    <?php if (is_auth()): ?>
                        <a href="<?= base_url('sacraments/' . $sacrament['slug']) ?>" class="text-sm text-gold hover:text-burgundy font-medium transition">Apply Now →</a>
                    <?php else: ?>
                        <a href="<?= base_url('register') ?>" class="text-sm text-gold hover:text-burgundy font-medium transition">Register →</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Parishes Section -->
<section id="parishes" class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl text-navy mb-4">Our Parishes</h2>
            <div class="gold-line w-24 mx-auto mb-4"></div>
            <p class="text-stone-600">Serving the faithful across vicariates in the Diocese of Pasig</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($parishes as $parish): ?>
            <div class="card-sacred bg-cream rounded-xl p-6 border border-stone-100">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-lg bg-navy/10 flex items-center justify-center flex-shrink-0">
                        <span class="text-navy text-xl">⛪</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-navy mb-1"><?= e($parish['name']) ?></h3>
                        <p class="text-xs text-burgundy mb-2"><?= e($parish['vicariate_name']) ?></p>
                        <p class="text-sm text-stone-500"><?= e($parish['full_address'] ?? 'Pasig City') ?></p>
                        <?php if ($parish['priest_name']): ?>
                        <p class="text-xs text-stone-400 mt-2"><?= e($parish['priest_name']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-24 bg-navy text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-display text-4xl text-gold mb-4">Why PASIGNAE?</h2>
            <div class="gold-line w-24 mx-auto"></div>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php
            $features = [
                ['icon' => '📋', 'title' => 'Online Applications', 'desc' => 'Submit sacrament requests from anywhere'],
                ['icon' => '📅', 'title' => 'Smart Scheduling', 'desc' => 'Book available dates across parishes'],
                ['icon' => '💳', 'title' => 'GCash Payments', 'desc' => 'Secure and convenient fee processing'],
                ['icon' => '📊', 'title' => 'Diocesan Reports', 'desc' => 'Real-time analytics for church leaders'],
            ];
            foreach ($features as $f):
            ?>
            <div class="text-center p-6">
                <div class="text-3xl mb-4"><?= $f['icon'] ?></div>
                <h3 class="font-display text-lg text-gold mb-2"><?= $f['title'] ?></h3>
                <p class="text-stone-400 text-sm"><?= $f['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
