<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="<?php echo base_url('admin/announcements'); ?>" class="inline-flex items-center gap-2 text-rose-600 hover:text-rose-700 font-semibold mb-4">
            <i class="fas fa-arrow-left"></i> Back to announcements
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Announcement</h1>
        <p class="mt-2 text-gray-500">Update the announcement details below.</p>
    </div>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-alert-circle text-red-600"></i>
            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
        <form method="POST" action="<?php echo base_url('admin/announcements/update'); ?>" class="space-y-6">
            <input type="hidden" name="id" value="<?php echo (int)($announcement['id'] ?? 0); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Title</label>
                <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($announcement['title'] ?? ''); ?>" placeholder="Announcement title…" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
            </div>

            <div>
                <label for="content" class="block text-sm font-semibold text-gray-900 mb-2">Content</label>
                <textarea id="content" name="content" required rows="8" placeholder="Write your announcement…" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition font-mono text-sm"><?php echo htmlspecialchars($announcement['content'] ?? ''); ?></textarea>
                <p class="mt-2 text-xs text-gray-500">Markdown and line breaks are supported.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-900 mb-2">Priority</label>
                    <select id="priority" name="priority" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                        <option value="Normal" <?php echo ($announcement['priority'] ?? 'Normal') === 'Normal' ? 'selected' : ''; ?>>Normal</option>
                        <option value="High" <?php echo ($announcement['priority'] ?? '') === 'High' ? 'selected' : ''; ?>>High</option>
                        <option value="Urgent" <?php echo ($announcement['priority'] ?? '') === 'Urgent' ? 'selected' : ''; ?>>Urgent</option>
                    </select>
                </div>

                <div>
                    <label for="audience" class="block text-sm font-semibold text-gray-900 mb-2">Audience</label>
                    <select id="audience" name="audience" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                        <option value="All Beneficiaries" <?php echo ($announcement['audience'] ?? 'All Beneficiaries') === 'All Beneficiaries' ? 'selected' : ''; ?>>All Beneficiaries</option>
                        <option value="Approved Applicants" <?php echo ($announcement['audience'] ?? '') === 'Approved Applicants' ? 'selected' : ''; ?>>Approved Applicants</option>
                        <option value="Pending Review" <?php echo ($announcement['audience'] ?? '') === 'Pending Review' ? 'selected' : ''; ?>>Pending Review</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <a href="<?php echo base_url('admin/announcements'); ?>" class="flex-1 rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                <button type="submit" class="flex-1 rounded-xl bg-rose-600 py-3 text-sm font-semibold text-white hover:bg-rose-700 transition">Update Announcement</button>
            </div>
        </form>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
