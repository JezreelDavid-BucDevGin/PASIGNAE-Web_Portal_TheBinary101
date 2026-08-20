<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-stone-100 shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b">
            <span class="text-4xl">🕊️</span>
            <div>
                <h2 class="font-display text-2xl text-navy">Funeral Service Request</h2>
                <p class="text-stone-500 text-sm">Fee: <?= format_currency((float)$type['fee']) ?></p>
            </div>
        </div>

        <form method="POST" action="<?= base_url('sacraments/funeral') ?>" data-loading>
            <?= csrf_field() ?>
            <input type="hidden" id="sacrament_type_id" value="<?= $type['id'] ?>">

            <div class="grid sm:grid-cols-2 gap-4 mb-8">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-1">Parish *</label>
                    <select name="parish_id" id="parish_id" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none">
                        <option value="">Select parish</option>
                        <?php foreach ($parishes as $p): ?><option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?>
                    </select>
                </div>
                <?php require VIEW_PATH . '/components/schedule-picker.php'; ?>
            </div>

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Deceased</legend>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-sm mb-1">First Name *</label><input name="deceased_first_name" required class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Middle Name</label><input name="deceased_middle_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Last Name *</label><input name="deceased_last_name" required class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Date of Death *</label><input type="date" name="date_of_death" required class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Time of Death</label><input type="time" name="time_of_death" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Cause of Death</label><input name="cause_of_death" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Informant</legend>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-sm mb-1">First Name</label><input name="informant_first_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Last Name</label><input name="informant_last_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Phone</label><input name="informant_phone" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
            </fieldset>

            <div class="mb-6"><label class="block text-sm mb-1">Funeral Location</label><input name="funeral_location" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
            <button type="submit" class="w-full py-3 bg-navy text-white font-medium rounded-lg hover:bg-navy/90 transition">Submit Funeral Request</button>
        </form>
    </div>
</div>
