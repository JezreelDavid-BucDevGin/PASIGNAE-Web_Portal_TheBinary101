<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(base_url()) ?>">
    <title><?= e($title ?? 'PASIGNAE') ?> — Diocese of Pasig</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#1e3a5f',
                        burgundy: '#7c2d3e',
                        gold: '#c9a227',
                        cream: '#faf8f5',
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'Georgia', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-cream text-navy antialiased min-h-screen">
    <nav class="fixed top-0 w-full z-50 bg-navy/95 backdrop-blur-md border-b border-gold/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= base_url() ?>" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center border border-gold/40">
                        <span class="text-gold text-lg">✝</span>
                    </div>
                    <div>
                        <span class="font-display text-xl text-white font-semibold tracking-wide">PASIGNAE</span>
                        <span class="hidden sm:block text-xs text-gold/80 -mt-1">Diocese of Pasig</span>
                    </div>
                </a>
                <div class="hidden md:flex items-center gap-6">
                    <a href="<?= base_url() ?>#services" class="text-stone-300 hover:text-gold transition text-sm">Sacraments</a>
                    <a href="<?= base_url() ?>#parishes" class="text-stone-300 hover:text-gold transition text-sm">Parishes</a>
                    <?php if (is_auth()): ?>
                        <a href="<?= base_url('dashboard') ?>" class="text-stone-300 hover:text-gold transition text-sm">Dashboard</a>
                        <a href="<?= base_url('logout') ?>" class="px-4 py-2 rounded-lg border border-gold/40 text-gold hover:bg-gold/10 transition text-sm">Sign Out</a>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="text-stone-300 hover:text-gold transition text-sm">Sign In</a>
                        <a href="<?= base_url('register') ?>" class="px-4 py-2 rounded-lg bg-gold text-navy font-medium hover:bg-gold/90 transition text-sm">Register</a>
                    <?php endif; ?>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-navy border-t border-gold/10 px-4 py-4 space-y-3">
            <a href="<?= base_url() ?>#services" class="block text-stone-300 py-2">Sacraments</a>
            <a href="<?= base_url() ?>#parishes" class="block text-stone-300 py-2">Parishes</a>
            <?php if (is_auth()): ?>
                <a href="<?= base_url('dashboard') ?>" class="block text-stone-300 py-2">Dashboard</a>
                <a href="<?= base_url('logout') ?>" class="block text-gold py-2">Sign Out</a>
            <?php else: ?>
                <a href="<?= base_url('login') ?>" class="block text-stone-300 py-2">Sign In</a>
                <a href="<?= base_url('register') ?>" class="block text-gold py-2">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="pt-16">
        <?= $viewContent ?>
    </main>

    <footer class="bg-navy text-stone-400 py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="gold-line w-24 mx-auto mb-6"></div>
            <p class="font-display text-gold text-lg mb-2">Diocese of Pasig</p>
            <p class="text-sm">PASIGNAE — Parish Information & Sacramental Network Administration Engine</p>
            <p class="text-xs mt-4 text-stone-500">&copy; <?= date('Y') ?> Diocese of Pasig. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
