/**
 * Interactive right-side schedule panel for sacrament applications.
 */
(function () {
    const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    function baseUrl() {
        return document.querySelector('meta[name="base-url"]')?.content || '';
    }

    function pad(n) {
        return String(n).padStart(2, '0');
    }

    function toISODate(year, monthIndex, day) {
        return `${year}-${pad(monthIndex + 1)}-${pad(day)}`;
    }

    function parseISO(dateStr) {
        const [y, m, d] = dateStr.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    function formatLong(dateStr) {
        return parseISO(dateStr).toLocaleDateString('en-PH', {
            month: 'long', day: 'numeric', year: 'numeric'
        });
    }

    function formatShortRange(start, end) {
        const a = parseISO(start);
        const b = parseISO(end);
        if (a.getMonth() === b.getMonth() && a.getFullYear() === b.getFullYear()) {
            return `${a.toLocaleDateString('en-PH', { month: 'long' })} ${a.getDate()}–${b.getDate()}, ${b.getFullYear()}`;
        }
        return `${formatLong(start)} – ${formatLong(end)}`;
    }

    function formatTime(timeStr) {
        if (!timeStr) return '';
        const [h, m] = timeStr.split(':');
        const date = new Date();
        date.setHours(Number(h), Number(m), 0, 0);
        return date.toLocaleTimeString('en-PH', { hour: 'numeric', minute: '2-digit' });
    }

    function initSchedulePanel() {
        const picker = document.querySelector('[data-schedule-picker]');
        const parishSelect = document.getElementById('parish_id');
        const typeInput = document.getElementById('sacrament_type_id');
        const scheduleInput = document.getElementById('schedule_id');
        if (!picker || !parishSelect || !typeInput || !scheduleInput) return;

        const overlay = document.getElementById('schedule-overlay');
        const panel = document.getElementById('schedule-panel');
        if (overlay && panel && overlay.parentElement !== document.body) {
            document.body.append(overlay, panel);
        }
        const openBtn = document.getElementById('schedule-open-btn');
        const changeBtn = document.getElementById('schedule-change-btn');
        const closeBtn = document.getElementById('schedule-close-btn');
        const cancelBtn = document.getElementById('schedule-cancel-btn');
        const confirmBtn = document.getElementById('schedule-confirm-btn');
        const parishLabel = document.getElementById('schedule-panel-parish');
        const errorEl = document.getElementById('schedule-picker-error');
        const emptyEl = document.getElementById('schedule-empty');
        const confirmedEl = document.getElementById('schedule-confirmed');
        const calGrid = document.getElementById('cal-grid');
        const calLabel = document.getElementById('cal-month-label');
        const timesEl = document.getElementById('schedule-times');
        const feedbackEl = document.getElementById('schedule-date-feedback');
        const windowBox = document.getElementById('schedule-window-box');
        const draftEl = document.getElementById('schedule-draft');
        const draftText = document.getElementById('schedule-draft-text');

        const state = {
            view: new Date(),
            dates: {},
            meta: { window_days: 0, window_label: null, ceremony_label: 'Ceremony' },
            selectedDate: null,
            selectedSlot: null,
            loading: false,
        };

        function parishName() {
            const opt = parishSelect.options[parishSelect.selectedIndex];
            return opt && opt.value ? opt.textContent.trim() : '';
        }

        function showError(msg) {
            if (!errorEl) return;
            errorEl.textContent = msg;
            errorEl.classList.toggle('hidden', !msg);
        }

        function openPanel() {
            if (!parishSelect.value) {
                showError('Please select a parish first.');
                parishSelect.focus();
                return;
            }
            showError('');
            overlay.classList.remove('hidden');
            panel.hidden = false;
            requestAnimationFrame(() => panel.classList.add('is-open'));
            document.body.classList.add('schedule-panel-open');
            loadCalendar();
        }

        function closePanel() {
            panel.classList.remove('is-open');
            overlay.classList.add('hidden');
            document.body.classList.remove('schedule-panel-open');
            setTimeout(() => { panel.hidden = true; }, 280);
        }

        async function loadCalendar() {
            const parishId = parishSelect.value;
            const typeId = typeInput.value;
            if (!parishId || !typeId) return;

            parishLabel.textContent = parishName();
            timesEl.innerHTML = '<p class="text-sm text-stone-400">Loading schedules…</p>';
            state.loading = true;

            try {
                const res = await fetch(`${baseUrl()}/api/schedules?parish_id=${parishId}&sacrament_type_id=${typeId}`);
                const data = await res.json();
                state.dates = data.dates || {};
                state.meta = {
                    window_days: data.window_days || 0,
                    window_label: data.window_label,
                    ceremony_label: data.ceremony_label || 'Ceremony',
                };

                const keys = Object.keys(state.dates);
                if (keys.length && !state.selectedDate) {
                    const first = keys.find((d) => state.dates[d].selectable) || keys[0];
                    const dt = parseISO(first);
                    state.view = new Date(dt.getFullYear(), dt.getMonth(), 1);
                }
                renderCalendar();
                renderDateDetails();
            } catch {
                timesEl.innerHTML = '<p class="text-sm text-burgundy">Could not load schedules.</p>';
            } finally {
                state.loading = false;
            }
        }

        function renderCalendar() {
            const year = state.view.getFullYear();
            const month = state.view.getMonth();
            calLabel.textContent = `${MONTHS[month]} ${year}`;

            const firstDow = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const todayIso = toISODate(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());

            calGrid.innerHTML = '';

            for (let i = 0; i < firstDow; i++) {
                const padEl = document.createElement('div');
                padEl.className = 'cal-day is-pad';
                calGrid.appendChild(padEl);
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const iso = toISODate(year, month, day);
                const info = state.dates[iso];
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = String(day);
                btn.className = 'cal-day';
                btn.dataset.date = iso;

                if (iso === todayIso) btn.classList.add('is-today');
                if (iso === state.selectedDate) btn.classList.add('is-selected');

                if (!info) {
                    btn.classList.add('is-empty');
                    btn.disabled = true;
                } else if (!info.selectable) {
                    btn.classList.add('is-unavailable');
                    btn.title = info.reason || 'Unavailable';
                } else {
                    btn.classList.add('is-available');
                }

                btn.addEventListener('click', () => selectDate(iso));
                calGrid.appendChild(btn);
            }
        }

        function selectDate(iso) {
            const info = state.dates[iso];
            state.selectedDate = iso;
            state.selectedSlot = null;
            confirmBtn.disabled = true;
            renderCalendar();

            if (!info) {
                feedbackEl.classList.remove('hidden');
                feedbackEl.textContent = 'No parish schedule is offered on this date.';
                timesEl.innerHTML = '<p class="text-sm text-stone-400">Select an available date to see times.</p>';
                windowBox.classList.add('hidden');
                draftEl.classList.add('hidden');
                return;
            }

            if (!info.selectable) {
                feedbackEl.classList.remove('hidden');
                feedbackEl.textContent = info.reason || 'This date is unavailable.';
            } else {
                feedbackEl.classList.add('hidden');
                feedbackEl.textContent = '';
            }

            renderDateDetails();
        }

        function renderDateDetails() {
            const iso = state.selectedDate;
            const info = iso ? state.dates[iso] : null;

            if (!info) {
                windowBox.classList.add('hidden');
                draftEl.classList.add('hidden');
                if (!iso) {
                    timesEl.innerHTML = '<p class="text-sm text-stone-400">Select an available date to see times.</p>';
                }
                return;
            }

            if (info.window && state.meta.window_days > 0) {
                windowBox.classList.remove('hidden');
                document.getElementById('schedule-window-label').textContent = state.meta.window_label || 'Required period';
                document.getElementById('schedule-window-range').textContent =
                    `${formatLong(info.window.start)} – ${formatLong(info.window.end)}`;
                document.getElementById('schedule-window-ceremony-label').textContent = state.meta.ceremony_label || 'Ceremony';
                document.getElementById('schedule-window-ceremony').textContent = formatLong(iso);
            } else {
                windowBox.classList.add('hidden');
            }

            const openSlots = info.slots.filter((s) => !s.is_full);
            const fullSlots = info.slots.filter((s) => s.is_full);

            if (!info.selectable && openSlots.length === 0) {
                timesEl.innerHTML = '<p class="text-sm text-stone-400">No remaining times on this date.</p>';
                draftEl.classList.add('hidden');
                return;
            }

            timesEl.innerHTML = '';
            info.slots.forEach((slot) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'schedule-time-btn';
                btn.disabled = slot.is_full || !info.selectable;
                if (state.selectedSlot && state.selectedSlot.id === slot.id) {
                    btn.classList.add('is-selected');
                }
                const remaining = slot.is_full ? 'Fully booked' : `${slot.remaining} slot${slot.remaining === 1 ? '' : 's'} left`;
                btn.innerHTML = `<span>${formatTime(slot.start_time)}${slot.end_time ? ' – ' + formatTime(slot.end_time) : ''}</span><span class="text-xs">${remaining}</span>`;
                btn.addEventListener('click', () => selectSlot(slot));
                timesEl.appendChild(btn);
            });

            if (fullSlots.length && !openSlots.length) {
                const note = document.createElement('p');
                note.className = 'text-sm text-stone-400';
                note.textContent = 'All times on this date are fully booked.';
                timesEl.appendChild(note);
            }

            updateDraft();
        }

        function selectSlot(slot) {
            const info = state.dates[state.selectedDate];
            if (!info?.selectable || slot.is_full) return;
            state.selectedSlot = slot;
            confirmBtn.disabled = false;
            renderDateDetails();
        }

        function updateDraft() {
            if (!state.selectedDate || !state.selectedSlot) {
                draftEl.classList.add('hidden');
                return;
            }
            draftEl.classList.remove('hidden');
            draftText.textContent = `${formatLong(state.selectedDate)} — ${formatTime(state.selectedSlot.start_time)}`;
        }

        function confirm() {
            if (!state.selectedDate || !state.selectedSlot) return;
            scheduleInput.value = String(state.selectedSlot.id);

            document.getElementById('schedule-confirmed-date').textContent =
                `📅 ${formatLong(state.selectedDate)}`;
            document.getElementById('schedule-confirmed-time').textContent =
                `🕘 ${formatTime(state.selectedSlot.start_time)}`;

            const windowEl = document.getElementById('schedule-confirmed-window');
            const info = state.dates[state.selectedDate];
            if (info?.window && state.meta.window_label) {
                windowEl.classList.remove('hidden');
                windowEl.innerHTML = `<span class="font-medium">${state.meta.window_label}:</span> ${formatShortRange(info.window.start, info.window.end)}`;
            } else {
                windowEl.classList.add('hidden');
                windowEl.textContent = '';
            }

            emptyEl.classList.add('hidden');
            confirmedEl.classList.remove('hidden');
            closePanel();
        }

        function resetSelection() {
            scheduleInput.value = '';
            state.selectedDate = null;
            state.selectedSlot = null;
            confirmBtn.disabled = true;
            emptyEl.classList.remove('hidden');
            confirmedEl.classList.add('hidden');
        }

        openBtn?.addEventListener('click', openPanel);
        changeBtn?.addEventListener('click', openPanel);
        closeBtn?.addEventListener('click', closePanel);
        cancelBtn?.addEventListener('click', closePanel);
        overlay?.addEventListener('click', closePanel);
        confirmBtn?.addEventListener('click', confirm);

        document.getElementById('cal-prev')?.addEventListener('click', () => {
            state.view = new Date(state.view.getFullYear(), state.view.getMonth() - 1, 1);
            renderCalendar();
        });
        document.getElementById('cal-next')?.addEventListener('click', () => {
            state.view = new Date(state.view.getFullYear(), state.view.getMonth() + 1, 1);
            renderCalendar();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && panel.classList.contains('is-open')) {
                closePanel();
            }
        });

        parishSelect.addEventListener('change', () => {
            resetSelection();
            showError('');
            state.dates = {};
            if (panel.classList.contains('is-open') && parishSelect.value) {
                loadCalendar();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initSchedulePanel);
})();
