<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-stone-100 shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-stone-100">
            <span class="text-4xl">💧</span>
            <div>
                <h2 class="font-display text-2xl text-navy">Baptism Application</h2>
                <p class="text-stone-500 text-sm">Fee: <?= format_currency((float)$type['fee']) ?></p>
            </div>
        </div>

        <form method="POST" action="<?= base_url('sacraments/baptism') ?>" data-loading>
            <?= csrf_field() ?>
            <input type="hidden" id="sacrament_type_id" value="<?= $type['id'] ?>">

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Parish & Schedule</legend>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium mb-1">Parish *</label>
                        <select name="parish_id" id="parish_id" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none">
                            <option value="">Select parish</option>
                            <?php foreach ($parishes as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= e($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php require VIEW_PATH . '/components/schedule-picker.php'; ?>
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Child (Catechumen)</legend>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-sm mb-1">First Name *</label><input name="child_first_name" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none"></div>
                    <div><label class="block text-sm mb-1">Middle Name</label><input name="child_middle_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none"></div>
                    <div><label class="block text-sm mb-1">Last Name *</label><input name="child_last_name" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none"></div>
                    <div><label class="block text-sm mb-1">Birth Date *</label><input type="date" name="child_birth_date" required class="w-full px-4 py-3 rounded-lg border border-stone-200 focus:ring-2 focus:ring-gold/30 outline-none"></div>
                    <div><label class="block text-sm mb-1">Gender</label><select name="child_gender" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div><label class="block text-sm mb-1">Place of Birth</label><input name="child_place_of_birth" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Parents</legend>
                <p class="text-sm text-stone-500 mb-3">Father</p>
                <div class="grid sm:grid-cols-3 gap-4 mb-4">
                    <div><label class="block text-sm mb-1">First Name</label><input name="father_first_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Middle Name</label><input name="father_middle_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Last Name</label><input name="father_last_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
                <p class="text-sm text-stone-500 mb-3">Mother</p>
                <div class="grid sm:grid-cols-3 gap-4">
                    <div><label class="block text-sm mb-1">First Name</label><input name="mother_first_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Middle Name</label><input name="mother_middle_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Last Name</label><input name="mother_last_name" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
            </fieldset>

            <fieldset class="mb-8">
                <legend class="font-display text-lg text-burgundy mb-4">Godparents</legend>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div><label class="block text-sm mb-1">Godfather</label><input name="godfather_first_name" placeholder="First" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none mb-2"><input name="godfather_last_name" placeholder="Last" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                    <div><label class="block text-sm mb-1">Godmother</label><input name="godmother_first_name" placeholder="First" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none mb-2"><input name="godmother_last_name" placeholder="Last" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></div>
                </div>
            </fieldset>

            <div class="mb-6">
                <label class="block text-sm font-medium mb-1">Remarks</label>
                <textarea name="remarks" rows="3" class="w-full px-4 py-3 rounded-lg border border-stone-200 outline-none"></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-burgundy text-white font-medium rounded-lg hover:bg-burgundy/90 transition">Submit Baptism Request</button>
        </form>
    </div>
</div>
