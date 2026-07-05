<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="max-w-4xl">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">System Status</h1>
    <p class="mt-1 text-gray-500 text-sm mb-8">Manage the operational state of the SSFO eLog.</p>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-medium flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600"></i>
            <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-tools text-gray-500"></i> Maintenance Mode
        </h2>
        <p class="text-sm text-gray-500 mb-4">When maintenance mode is enabled, clients will see a maintenance screen instead of the application forms. Admins can still access the dashboard.</p>
        <form method="POST" action="<?php echo base_url('admin/system-status'); ?>" class="space-y-4">
            <input type="hidden" name="action" value="toggle_maintenance">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <label class="flex items-center gap-3 cursor-pointer p-4 border rounded-xl <?php echo ($maintenanceMode ?? false) ? 'border-rose-200 bg-rose-50' : 'border-gray-200'; ?> transition-colors">
                <input type="checkbox" name="maintenance" value="on" class="rounded border-gray-300 text-rose-600 focus:ring-rose-500 w-5 h-5" <?php echo ($maintenanceMode ?? false) ? 'checked' : ''; ?>>
                <span class="text-sm font-semibold text-gray-800">Enable Maintenance Mode</span>
            </label>
            <button type="submit" class="rounded-xl bg-gray-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800 transition-colors">Update Status</button>
        </form>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
