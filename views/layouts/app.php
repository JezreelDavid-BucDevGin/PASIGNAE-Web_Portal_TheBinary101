<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= e(base_url()) ?>">
    <title><?= e($title ?? 'Dashboard') ?> — PASIGNAE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { navy: '#1e3a5f', burgundy: '#7c2d3e', gold: '#c9a227', cream: '#faf8f5' },
                    fontFamily: { display: ['Playfair Display', 'Georgia', 'serif'], sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="bg-cream text-navy antialiased">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-navy transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
            <div class="p-6 border-b border-white/10">
                <a href="<?= base_url('dashboard') ?>" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gold/20 flex items-center justify-center border border-gold/40">
                        <span class="text-gold">✝</span>
                    </div>
                    <div>
                        <span class="font-display text-lg text-white font-semibold">PASIGNAE</span>
                        <span class="block text-xs text-gold/70">Diocese of Pasig</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <a href="<?= base_url('dashboard') ?>" class="sidebar-link <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') ? 'active' : '' ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Dashboard
                </a>
                <a href="<?= base_url('sacraments') ?>" class="sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Sacraments
                </a>
                <?php if (can_access('schedules')): ?>
                <a href="<?= base_url('schedules') ?>" class="sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Schedules
                </a>
                <?php endif; ?>
                <?php if (can_access('payments')): ?>
                <a href="<?= base_url('payments') ?>" class="sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Payments
                </a>
                <?php endif; ?>
                <?php if (can_access('parishes')): ?>
                <a href="<?= base_url('parishes') ?>" class="sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Parishes
                </a>
                <?php endif; ?>
                <?php if (can_access('audit')): ?>
                <a href="<?= base_url('audit') ?>" class="sidebar-link">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Audit Logs
                </a>
                <?php endif; ?>
            </nav>

            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 px-2">
                    <div class="w-9 h-9 rounded-full bg-burgundy/30 flex items-center justify-center text-gold text-sm font-medium">
                        <?= strtoupper(substr(auth()['first_name'], 0, 1)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-white truncate"><?= e(auth()['first_name'] . ' ' . auth()['last_name']) ?></p>
                        <p class="text-xs text-stone-400 truncate"><?= e(auth()['role_name']) ?></p>
                    </div>
                </div>
                <a href="<?= base_url('logout') ?>" class="mt-3 block text-center text-xs text-stone-400 hover:text-gold transition">Sign Out</a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <header class="bg-white border-b border-stone-200 px-4 lg:px-8 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg hover:bg-stone-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="font-display text-2xl text-navy"><?= e($title ?? 'Dashboard') ?></h1>
                </div>
                <a href="<?= base_url() ?>" class="text-sm text-stone-500 hover:text-burgundy transition hidden sm:block">← Back to Portal</a>
            </header>

            <main class="flex-1 p-4 lg:p-8 overflow-auto">
                <?php require VIEW_PATH . '/components/alerts.php'; ?>
                <?= $viewContent ?>
            </main>
        </div>
    </div>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/schedule-panel.js') ?>"></script>
</body>
</html>
