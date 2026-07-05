<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">All requests</h1>
    <p class="mt-1 text-gray-500 text-sm sm:text-base">Review and manage all client applications across programs.</p>
</div>

<div class="glass rounded-lg p-6 shadow-premium mb-8 animate-fade-in-up">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
        <!-- Search -->
        <div class="space-y-2">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Search Database</label>
            <div class="relative group">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-rose-500 transition-colors"></i>
                <input type="text" id="searchInput" placeholder="Name, email..." class="w-full rounded-lg border border-gray-100 bg-white/50 pl-12 pr-5 py-4 text-sm text-gray-900 placeholder:text-gray-300 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-500/10 transition-all outline-none shadow-sm" onkeyup="filterTable()">
            </div>
        </div>

        <!-- Program -->
        <div class="space-y-2">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Filter Program</label>
            <div class="relative">
                <select id="typeFilter" class="w-full appearance-none rounded-lg border border-gray-100 bg-white/50 pl-6 pr-10 py-4 text-sm text-gray-900 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-500/10 transition-all outline-none shadow-sm" onchange="filterTable()">
                    <option value="">All Programs</option>
                    <option value="educational">Educational</option>
                    <option value="medical">Medical</option>
                    <option value="burial">Burial</option>
                    <option value="employment">Employment</option>
                    <option value="transportation">Transportation</option>
                </select>
                <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
            </div>
        </div>

        <!-- Status -->
        <div class="space-y-2">
            <label class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] ml-4">Current Status</label>
            <div class="relative">
                <select id="statusFilter" class="w-full appearance-none rounded-lg border border-gray-100 bg-white/50 pl-6 pr-10 py-4 text-sm text-gray-900 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-500/10 transition-all outline-none shadow-sm" onchange="filterTable()">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Reviewed</option>
                </select>
                <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
            </div>
        </div>


    </div>
</div>


<div class="rounded-lg border border-gray-100 bg-white shadow-premium overflow-hidden animate-fade-in-up [animation-delay:200ms]">
    <div class="overflow-x-auto">
        <table class="w-full text-sm" id="applicationsTable">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="text-left py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Applicant Details</th>
                    <th class="text-left py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Program Type</th>
                    <th class="text-center py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Current Status</th>
                    <th class="text-center py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Submission Date</th>
                    <th class="text-center py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Management</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                if (!empty($requests)):
                    foreach ($requests as $request):
                        $statusColors = [
                            'pending' => 'bg-amber-50 text-amber-700 ring-amber-200/50',
                            'approved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200/50',
                            'rejected' => 'bg-rose-50 text-rose-700 ring-rose-200/50',
                            'completed' => 'bg-blue-50 text-blue-700 ring-blue-200/50'
                        ];
                        $statusColor = $statusColors[$request['status']] ?? 'bg-gray-50 text-gray-600 ring-gray-200';
                ?>
                <tr class="group hover:bg-gray-50/50 transition-all duration-300" data-search="<?php echo strtolower($request['fullname'] . ' ' . $request['email']); ?>" data-type="<?php echo htmlspecialchars($request['request_type'] ?? ''); ?>" data-status="<?php echo htmlspecialchars($request['status'] ?? ''); ?>">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-bold text-xs shadow-inner">
                                <?php echo strtoupper(substr($request['fullname'], 0, 1)); ?>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 group-hover:text-rose-600 transition-colors"><?php echo htmlspecialchars($request['fullname'] ?? ''); ?></p>
                                <p class="text-[11px] text-gray-400 font-medium"><?php echo htmlspecialchars($request['email'] ?? ''); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center gap-1.5 capitalize rounded-xl bg-gray-100/80 px-3 py-1.5 text-[11px] font-bold text-gray-600 ring-1 ring-inset ring-gray-200/50">
                            <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                            <?php echo htmlspecialchars($request['request_type'] ?? ''); ?>
                        </span>
                    </td>
                    <td class="text-center py-4 px-6">
                        <span class="<?php echo $statusColor; ?> inline-flex px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider ring-1 shadow-sm">
                            <?php echo htmlspecialchars($request['status'] === 'completed' ? 'reviewed' : ($request['status'] ?? '')); ?>
                        </span>
                    </td>
                    <td class="text-center py-4 px-6">
                        <span class="text-gray-500 font-semibold tabular-nums text-xs">
                            <?php echo date('M d, Y', strtotime($request['created_at'] ?? 'now')); ?>
                        </span>
                    </td>
                    <td class="text-center py-4 px-6">
                        <div class="flex items-center justify-center gap-3">
                            <a href="<?php echo base_url('admin/requests/view?id=' . $request['id']); ?>" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Review">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <button type="button" onclick="quickApprove(<?php echo (int)$request['id']; ?>)" class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Quick Approve">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="5" class="py-20 text-center">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 text-gray-300 mb-4 shadow-inner">
                            <i class="fas fa-inbox text-2xl"></i>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No applications found</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<script>
    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const typeFilter = document.getElementById('typeFilter').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const table = document.getElementById('applicationsTable');
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

        for (let row of rows) {
            if (row.dataset.search) {
                const matchesSearch = row.dataset.search.includes(searchTerm);
                const matchesType = !typeFilter || row.dataset.type.toLowerCase() === typeFilter;
                const matchesStatus = !statusFilter || row.dataset.status.toLowerCase() === statusFilter;
                row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
            }
        }
    }

    function quickApprove(id) {
        if (confirm('Approve this application?')) {
            alert('Application approved!');
            location.href = '<?php echo base_url('admin/requests/view?id='); ?>' + id;
        }
    }

    function exportData() {
        window.location.href = '<?php echo base_url('admin/export/csv'); ?>';
    }


</script>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
