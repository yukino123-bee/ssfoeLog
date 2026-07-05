<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Programs</h1>
        <p class="mt-1 text-gray-500 text-sm sm:text-base">Manage assistance programs and their configurations.</p>
    </div>
    <button type="button" onclick="openAddProgramModal()" class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition">
        <i class="fas fa-plus"></i> Add Program
    </button>
</div>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="mb-6 p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 text-sm font-medium flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600"></i>
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
    <?php if (!empty($allPrograms)): ?>
        <?php foreach ($allPrograms as $program): ?>
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm hover:shadow-md hover:border-rose-100 transition-all group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:scale-105 transition-transform">
                        <i class="fas fa-<?php echo htmlspecialchars($program['icon'] ?? 'folder'); ?> text-lg"></i>
                    </div>
                    <div class="flex gap-1">
                        <a href="<?php echo base_url('admin/programs/edit?id=' . (int)$program['id']); ?>" class="p-2 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="<?php echo base_url('admin/programs/delete'); ?>" class="inline" onsubmit="return confirm('Are you sure you want to delete this program?');">
                            <input type="hidden" name="id" value="<?php echo (int)$program['id']; ?>">
                            <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($program['name'] ?? ''); ?></h3>
                <p class="text-sm text-gray-500 mb-4 line-clamp-2"><?php echo htmlspecialchars($program['description'] ?? 'No description'); ?></p>
                <div class="mb-4">
                    <span class="<?php echo ($program['status'] === 'active' ? 'bg-emerald-50 text-emerald-800 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200'); ?> px-2.5 py-1 rounded-full text-xs font-semibold uppercase ring-1 inline-block">
                        <?php echo htmlspecialchars($program['status'] ?? 'active'); ?>
                    </span>
                </div>
                <div class="rounded-xl bg-gray-50 border border-gray-100 p-4 mb-4">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-gray-500 font-medium">Total</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo number_format($program['stats_total'] ?? 0); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Approved</p>
                            <p class="text-lg font-bold text-emerald-600"><?php echo number_format($program['stats_approved'] ?? 0); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Pending</p>
                            <p class="text-lg font-bold text-amber-600"><?php echo number_format($program['stats_pending'] ?? 0); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-500 font-medium">Rejected</p>
                            <p class="text-lg font-bold text-red-600"><?php echo number_format($program['stats_rejected'] ?? 0); ?></p>
                        </div>
                    </div>
                </div>
                <a href="<?php echo base_url('admin/requests?type=' . urlencode($program['name'])); ?>" class="w-full block text-center rounded-xl border border-blue-200 bg-blue-50 py-2.5 text-sm font-semibold text-blue-800 hover:bg-blue-100 transition">
                    View applications
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-full text-center py-14 rounded-2xl border border-dashed border-gray-200 bg-white">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4 block"></i>
            <p class="text-gray-500 mb-4">No programs created yet</p>
            <button type="button" onclick="openAddProgramModal()" class="text-rose-600 hover:text-red-700 font-semibold">
                Create the first program →
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
    <h3 class="text-base font-semibold text-gray-900 mb-4">System program types</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3">
        <?php
        $systemPrograms = [
            ['name' => 'Educational', 'icon' => 'book', 'desc' => 'School assistance & scholarships'],
            ['name' => 'Medical', 'icon' => 'heartbeat', 'desc' => 'Health & medical support'],
            ['name' => 'Burial', 'icon' => 'cross', 'desc' => 'Funeral assistance'],
            ['name' => 'Employment', 'icon' => 'briefcase', 'desc' => 'Job placement & training'],
            ['name' => 'Transportation', 'icon' => 'bus', 'desc' => 'Travel assistance'],
        ];
        foreach ($systemPrograms as $prog):
        ?>
        <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 text-center hover:border-rose-100 hover:bg-white transition">
            <div class="text-2xl mb-2 text-rose-600">
                <i class="fas fa-<?php echo $prog['icon']; ?>"></i>
            </div>
            <p class="font-semibold text-gray-900 text-sm mb-1"><?php echo $prog['name']; ?></p>
            <p class="text-xs text-gray-500"><?php echo $prog['desc']; ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div id="programModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xl max-w-md w-full my-8">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
            <h2 class="text-lg font-semibold text-gray-900">Add Program</h2>
            <button type="button" onclick="closeModal()" class="p-2 rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-900">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form method="POST" action="<?php echo base_url('admin/programs/create'); ?>" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Program name</label>
                <input type="text" name="name" required placeholder="E.g., Scholarship Program" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Description</label>
                <textarea name="description" placeholder="Describe the program…" rows="3" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none"></textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Category</label>
                <input type="text" name="category" placeholder="E.g., Education" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Icon (Font Awesome name)</label>
                <input type="text" name="icon" placeholder="book, heart, bus…" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</label>
                <select name="status" class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm text-gray-900 focus:border-rose-300 focus:ring-2 focus:ring-rose-500/20 outline-none">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 rounded-xl border border-gray-200 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-gray-900 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">Create Program</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddProgramModal() {
    document.getElementById('programModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('programModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('programModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});
</script>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
