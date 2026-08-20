<?php
$slug = $type['slug'] ?? 'baptism';
$pickerTitle = match ($slug) {
    'matrimony' => 'Marriage Schedule',
    'funeral' => 'Funeral Schedule',
    default => 'Preferred Schedule',
};
$panelTitle = match ($slug) {
    'matrimony' => 'Marriage Schedule',
    'funeral' => 'Funeral Schedule',
    default => 'Baptism Schedule',
};
?>
<div class="sm:col-span-2" data-schedule-picker>
    <input type="hidden" name="schedule_id" id="schedule_id" value="">
    <input type="hidden" id="sacrament_slug" value="<?= e($slug) ?>">

    <label class="block text-sm font-medium mb-1"><?= e($pickerTitle) ?></label>

    <div id="schedule-empty" class="rounded-xl border border-dashed border-stone-300 bg-cream/60 p-4">
        <button type="button" id="schedule-open-btn" class="w-full flex items-center justify-between gap-3 text-left group">
            <span>
                <span class="block text-sm font-medium text-navy">Select Schedule</span>
                <span class="block text-sm text-stone-500">Choose Date &amp; Schedule</span>
            </span>
            <span class="shrink-0 px-4 py-2 rounded-lg bg-navy text-white text-sm font-medium group-hover:bg-navy/90 transition">Open</span>
        </button>
        <p id="schedule-picker-error" class="hidden mt-3 text-sm text-burgundy"></p>
    </div>

    <div id="schedule-confirmed" class="hidden rounded-xl border border-gold/40 bg-white p-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-gold font-semibold mb-2"><?= e($pickerTitle) ?></p>
                <p id="schedule-confirmed-date" class="text-navy font-medium"></p>
                <p id="schedule-confirmed-time" class="text-stone-600 text-sm mt-1"></p>
                <p id="schedule-confirmed-window" class="hidden text-sm text-burgundy mt-3"></p>
            </div>
            <button type="button" id="schedule-change-btn" class="text-sm text-burgundy hover:text-gold font-medium whitespace-nowrap">
                Change Schedule
            </button>
        </div>
    </div>
</div>

<div id="schedule-overlay" class="schedule-overlay hidden" aria-hidden="true"></div>
<aside id="schedule-panel" class="schedule-panel" role="dialog" aria-modal="true" aria-labelledby="schedule-panel-title" hidden>
    <div class="flex items-start justify-between gap-3 pb-4 border-b border-stone-100">
        <div>
            <p class="text-xs uppercase tracking-wide text-gold font-semibold">PASIGNAE</p>
            <h2 id="schedule-panel-title" class="font-display text-2xl text-navy"><?= e($panelTitle) ?></h2>
            <p id="schedule-panel-parish" class="text-sm text-stone-500 mt-1">Select a parish first</p>
        </div>
        <button type="button" id="schedule-close-btn" class="p-2 rounded-lg hover:bg-stone-100 text-stone-500" aria-label="Close">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto py-5 space-y-5">
        <div>
            <div class="flex items-center justify-between mb-3">
                <button type="button" id="cal-prev" class="p-2 rounded-lg hover:bg-cream text-navy" aria-label="Previous month">‹</button>
                <h3 id="cal-month-label" class="font-display text-lg text-navy"></h3>
                <button type="button" id="cal-next" class="p-2 rounded-lg hover:bg-cream text-navy" aria-label="Next month">›</button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs text-stone-400 mb-2">
                <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
            </div>
            <div id="cal-grid" class="grid grid-cols-7 gap-1"></div>
            <div class="flex flex-wrap gap-3 mt-3 text-xs text-stone-500">
                <span class="inline-flex items-center gap-1"><i class="cal-legend cal-legend-available"></i> Available</span>
                <span class="inline-flex items-center gap-1"><i class="cal-legend cal-legend-selected"></i> Selected</span>
                <span class="inline-flex items-center gap-1"><i class="cal-legend cal-legend-full"></i> Full / unavailable</span>
            </div>
        </div>

        <p id="schedule-date-feedback" class="hidden text-sm rounded-lg px-3 py-2 bg-burgundy/10 text-burgundy"></p>

        <div id="schedule-window-box" class="hidden rounded-xl border border-gold/30 bg-cream p-4">
            <p id="schedule-window-label" class="text-xs uppercase tracking-wide text-burgundy font-semibold"></p>
            <p id="schedule-window-range" class="font-display text-navy mt-1"></p>
            <div class="gold-line my-3"></div>
            <p id="schedule-window-ceremony-label" class="text-xs uppercase tracking-wide text-stone-500"></p>
            <p id="schedule-window-ceremony" class="text-navy font-medium"></p>
        </div>

        <div>
            <p class="text-sm font-medium text-navy mb-2">Available Time</p>
            <div id="schedule-times" class="space-y-2">
                <p class="text-sm text-stone-400">Select an available date to see times.</p>
            </div>
        </div>

        <div id="schedule-draft" class="hidden rounded-xl bg-navy text-white p-4">
            <p class="text-xs text-gold uppercase tracking-wide">Selected Schedule</p>
            <p id="schedule-draft-text" class="mt-1 font-display text-lg"></p>
        </div>
    </div>

    <div class="pt-4 border-t border-stone-100 flex gap-3">
        <button type="button" id="schedule-cancel-btn" class="flex-1 py-3 rounded-lg border border-stone-200 text-navy font-medium hover:bg-cream transition">Cancel</button>
        <button type="button" id="schedule-confirm-btn" class="flex-1 py-3 rounded-lg bg-burgundy text-white font-medium hover:bg-burgundy/90 transition disabled:opacity-40 disabled:cursor-not-allowed" disabled>Confirm Schedule</button>
    </div>
</aside>
