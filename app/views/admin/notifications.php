<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Notifications</h1>
        <p class="mt-1 text-sm text-gray-500">View all system notifications and updates.</p>
    </div>
    <form method="POST" action="<?php echo base_url('admin/notifications'); ?>">
        <input type="hidden" name="mark_all_read" value="1">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <button type="submit" class="text-sm font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 px-4 py-2 rounded-lg transition-colors">
            Mark all as read
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <?php if (empty($notifications)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                <i class="fas fa-bell-slash text-2xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">No notifications</h3>
            <p class="text-gray-500">You're all caught up!</p>
        </div>
    <?php else: ?>
        <ul class="divide-y divide-gray-100">
            <?php foreach ($notifications as $n): ?>
                <li class="p-4 sm:p-6 hover:bg-gray-50 transition-colors <?php echo !$n['is_read'] ? 'bg-rose-50/20' : ''; ?>">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full <?php echo !$n['is_read'] ? 'bg-rose-100 text-rose-600' : 'bg-gray-100 text-gray-500'; ?> flex items-center justify-center shrink-0">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 <?php echo !$n['is_read'] ? 'font-bold' : 'font-medium'; ?>">
                                <?php echo htmlspecialchars($n['message']); ?>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo date('F j, Y, g:i a', strtotime($n['created_at'])); ?>
                            </p>
                        </div>
                        <?php if (!empty($n['link'])): ?>
                            <a href="<?php echo htmlspecialchars($n['link']); ?>" class="text-sm font-medium text-rose-600 hover:text-rose-700">View Details</a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
