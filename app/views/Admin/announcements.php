<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Announcements</h1>
        <p class="mt-1 text-gray-500 text-sm">Broadcast updates to beneficiaries.</p>
    </div>
    <button type="button" onclick="openAnnouncementModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition">
        <i class="fas fa-plus"></i> New announcement
    </button>
</div>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-medium flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<div class="space-y-4 max-w-4xl">
    <?php if (!empty($announcements)): ?>
        <?php foreach ($announcements as $ann): ?>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 sm:p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-4 mb-3">
                <div class="min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($ann['title'] ?? ''); ?></h3>
                    <div class="flex flex-wrap items-center gap-2 mt-2 text-xs">
                        <?php
                        $priority = $ann['priority'] ?? 'Normal';
                        $prClass = $priority === 'Urgent' ? 'bg-rose-100 text-rose-800 ring-rose-200' :
                            ($priority === 'High' ? 'bg-amber-100 text-amber-900 ring-amber-200' : 'bg-blue-100 text-blue-900 ring-blue-200');
                        ?>
                        <span class="<?php echo $prClass; ?> px-2 py-0.5 rounded-md font-semibold uppercase ring-1"><?php echo htmlspecialchars($priority); ?></span>
                        <span class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-700"><?php echo htmlspecialchars($ann['audience'] ?? 'All'); ?></span>
                        <span class="text-gray-400"><?php echo formatDate($ann['created_at'] ?? ''); ?></span>
                    </div>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="<?php echo base_url('admin/announcements/edit?id=' . (int)($ann['id'] ?? 0)); ?>" class="rounded-lg p-2 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="<?php echo base_url('admin/announcements/delete'); ?>" class="inline" onsubmit="return confirm('Delete this announcement?');">
                        <input type="hidden" name="id" value="<?php echo (int)($ann['id'] ?? 0); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <button type="submit" class="rounded-lg p-2 text-gray-400 hover:bg-red-50 hover:text-red-600 transition" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-sm text-gray-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($ann['content'] ?? '')); ?></div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="rounded-2xl border border-dashed border-gray-200 bg-white py-14 text-center text-gray-500">
            <i class="fas fa-bullhorn text-3xl text-gray-300 mb-3 block"></i>
            No announcements yet
        </div>
    <?php endif; ?>
</div>

<div id="announcementModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-2xl w-full my-8">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl">
            <h2 class="text-lg font-semibold text-gray-900">Create announcement</h2>
            <button type="button" onclick="closeAnnouncementModal()" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo base_url('admin/announcements'); ?>" class="p-5 space-y-4">
            <input type="hidden" name="action" value="create">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Title</label>
                <input type="text" name="title" required placeholder="Announcement title…" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Content</label>
                <textarea name="content" required rows="5" placeholder="Write your announcement…" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Priority</label>
                    <select name="priority" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900">
                        <option value="Normal">Normal</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Audience</label>
                    <select name="audience" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900">
                        <option value="All Beneficiaries">All Beneficiaries</option>
                        <option value="Approved Applicants">Approved Applicants</option>
                        <option value="Pending Review">Pending Review</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeAnnouncementModal()" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-gray-900 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Publish</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAnnouncementModal() {
    document.getElementById('announcementModal').classList.remove('hidden');
}

function closeAnnouncementModal() {
    document.getElementById('announcementModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('announcementModal').addEventListener('click', function(e) {
    if (e.target === this) closeAnnouncementModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAnnouncementModal();
    }
});
</script>

<script>
    function openAnnouncementModal() {
        document.getElementById('announcementModal').classList.remove('hidden');
    }
    function closeAnnouncementModal() {
        document.getElementById('announcementModal').classList.add('hidden');
    }
    document.getElementById('announcementModal').addEventListener('click', function(e) {
        if (e.target === this) closeAnnouncementModal();
    });
</script>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
