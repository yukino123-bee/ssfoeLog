<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Inbox</h1>
        <p class="mt-1 text-sm text-gray-500">Manage inquiries and messages from clients.</p>
    </div>
    <div class="flex space-x-3">
        <form method="POST" action="<?php echo base_url('admin/inquiries'); ?>">
            <input type="hidden" name="mark_all_read" value="1">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-50 hover:text-rose-600 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                <i class="fas fa-check-double mr-2"></i> Mark all as read
            </button>
        </form>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between bg-gray-50/50">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">All Messages</h3>
    </div>
    
    <div class="divide-y divide-gray-100">
        <?php if (empty($inquiries)): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-inbox text-2xl"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Your inbox is empty</h3>
                <p class="text-gray-500 text-sm">You don't have any messages right now.</p>
            </div>
        <?php else: ?>
            <?php foreach ($inquiries as $inq): ?>
                <div class="p-6 transition-all hover:bg-gray-50 <?php echo $inq['is_read'] ? 'opacity-80' : 'bg-rose-50/30'; ?>" id="inq-<?php echo $inq['id']; ?>">
                    <div class="flex flex-col md:flex-row md:items-start space-y-4 md:space-y-0 md:space-x-6">
                        <div class="flex-shrink-0 pt-1">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-gray-100 to-gray-200 border border-gray-200 flex items-center justify-center text-gray-500 text-sm font-bold shadow-sm">
                                <?php echo strtoupper(substr($inq['name'], 0, 1)); ?>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h4 class="text-base font-bold <?php echo $inq['is_read'] ? 'text-gray-700' : 'text-gray-900'; ?> flex items-center gap-2">
                                    <?php echo htmlspecialchars($inq['name']); ?>
                                    <?php if (!$inq['is_read']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200 uppercase tracking-wider">New</span>
                                    <?php endif; ?>
                                </h4>
                                <span class="text-xs text-gray-500 flex items-center gap-1.5 font-medium whitespace-nowrap">
                                    <i class="far fa-clock text-gray-400"></i>
                                    <?php echo date('M d, Y h:i A', strtotime($inq['created_at'])); ?>
                                </span>
                            </div>
                            <div class="text-sm font-medium text-gray-500 mb-2 flex items-center gap-2">
                                <i class="far fa-envelope text-gray-400"></i>
                                <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="hover:text-rose-600 transition-colors"><?php echo htmlspecialchars($inq['email']); ?></a>
                            </div>
                            <?php if (!empty($inq['subject'])): ?>
                            <div class="text-sm font-bold text-gray-800 mb-2">
                                Subject: <?php echo htmlspecialchars($inq['subject']); ?>
                            </div>
                            <?php endif; ?>
                            <div class="text-gray-600 text-sm leading-relaxed bg-white border border-gray-100 p-4 rounded-lg mt-3 shadow-sm whitespace-pre-wrap"><?php echo htmlspecialchars($inq['message']); ?></div>
                        </div>
                        <div class="flex-shrink-0 pt-2 flex space-x-2">
                            <a href="mailto:<?php echo htmlspecialchars($inq['email']); ?>" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 border border-transparent rounded-lg text-xs font-bold transition-colors">
                                <i class="fas fa-reply mr-1.5"></i> Reply
                            </a>
                            <?php if (!$inq['is_read']): ?>
                                <button onclick="markAsRead(<?php echo $inq['id']; ?>)" class="inline-flex items-center px-3 py-1.5 bg-white text-gray-600 border border-gray-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 rounded-lg text-xs font-bold transition-all shadow-sm group">
                                    <i class="fas fa-check text-gray-400 group-hover:text-rose-500 mr-1.5"></i> Mark Read
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch('<?php echo base_url('admin/inquiries/read'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&csrf_token=<?php echo generate_csrf_token(); ?>`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        }
    });
}
</script>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
