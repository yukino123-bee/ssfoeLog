<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <a href="<?php echo base_url('admin/programs'); ?>" class="inline-flex items-center gap-2 text-rose-600 hover:text-rose-700 font-semibold mb-4">
            <i class="fas fa-arrow-left"></i> Back to programs
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Program</h1>
        <p class="mt-2 text-gray-500">Update the program configuration and details.</p>
    </div>

    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-alert-circle text-red-600"></i>
            <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-100 bg-white p-8 shadow-sm">
                <form method="POST" action="<?php echo base_url('admin/programs/update'); ?>" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo (int)($program['id'] ?? 0); ?>">

                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">Program Name *</label>
                        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($program['name'] ?? ''); ?>" placeholder="E.g., Educational Assistance" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4" placeholder="Brief description of the program…" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition"><?php echo htmlspecialchars($program['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-semibold text-gray-900 mb-2">Category</label>
                            <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($program['category'] ?? ''); ?>" placeholder="E.g., Education" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                        </div>

                        <div>
                            <label for="icon" class="block text-sm font-semibold text-gray-900 mb-2">Icon (Font Awesome)</label>
                            <input type="text" id="icon" name="icon" value="<?php echo htmlspecialchars($program['icon'] ?? 'folder'); ?>" placeholder="book, heart, bus…" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                        </div>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">Status</label>
                        <select id="status" name="status" class="w-full rounded-lg border border-gray-200 px-4 py-3 text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none transition">
                            <option value="active" <?php echo ($program['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($program['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <a href="<?php echo base_url('admin/programs'); ?>" class="flex-1 rounded-xl border border-gray-200 py-3 text-center text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Cancel</a>
                        <button type="submit" class="flex-1 rounded-xl bg-rose-600 py-3 text-sm font-semibold text-white hover:bg-rose-700 transition">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Program Statistics</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-gray-600">Total Applications</span>
                        <span class="text-2xl font-bold text-gray-900"><?php echo number_format($program['stats_total'] ?? 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-gray-600">Pending</span>
                        <span class="text-lg font-bold text-amber-600"><?php echo number_format($program['stats_pending'] ?? 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-gray-600">Approved</span>
                        <span class="text-lg font-bold text-emerald-600"><?php echo number_format($program['stats_approved'] ?? 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <span class="text-gray-600">Rejected</span>
                        <span class="text-lg font-bold text-red-600"><?php echo number_format($program['stats_rejected'] ?? 0); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Reviewed</span>
                        <span class="text-lg font-bold text-blue-600"><?php echo number_format($program['stats_completed'] ?? 0); ?></span>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-gray-100">
                    <form method="POST" action="<?php echo base_url('admin/programs/delete'); ?>" onsubmit="return confirm('Are you sure you want to delete this program? This action cannot be undone.');">
                        <input type="hidden" name="id" value="<?php echo (int)($program['id'] ?? 0); ?>">
                        <button type="submit" class="w-full rounded-xl bg-red-50 py-2.5 text-sm font-semibold text-red-800 border border-red-200 hover:bg-red-100 transition">
                            <i class="fas fa-trash mr-2"></i> Delete Program
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
