<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>
<?php
$rt = $report_type ?? 'individual';
$export_params = ['report_type' => $rt];
if ($rt === 'individual' && !empty($_GET['search'])) {
    $export_params['search'] = $_GET['search'];
}
if ($rt === 'program') {
    if (isset($_GET['program']) && $_GET['program'] !== '') {
        $export_params['program'] = $_GET['program'];
    }
    if (isset($_GET['prog_status']) && $_GET['prog_status'] !== '') {
        $export_params['prog_status'] = $_GET['prog_status'];
    }
}
$export_url = base_url('admin/reports/export?' . http_build_query($export_params));
?>

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight font-outfit">Reports</h1>
        <p class="mt-1 text-gray-500 text-sm">Search, filter, and export application data repository.</p>
    </div>
    <div class="flex items-center gap-2 bg-white/50 p-1.5 rounded-lg border border-gray-100 shadow-sm backdrop-blur-sm">
        <a href="<?php echo htmlspecialchars($export_url . '&format=html'); ?>" class="flex items-center gap-2 rounded-lg bg-indigo-50 text-indigo-700 px-4 py-2 text-[11px] font-black uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all duration-300">
            <i class="fas fa-file-export"></i> WEB VIEW
        </a>
    </div>
</div>

<div class="flex flex-wrap gap-2 mb-6">
    <a href="?report_type=individual" class="px-4 py-2 rounded-lg text-sm font-semibold transition <?php echo ($report_type ?? '') === 'individual' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-rose-200'; ?>">Individual Search</a>
    <a href="?report_type=program" class="px-4 py-2 rounded-lg text-sm font-semibold transition <?php echo ($report_type ?? '') === 'program' ? 'bg-rose-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-rose-200'; ?>">By Program</a>
</div>

<?php if (($report_type ?? '') === 'individual'): ?>
<div class="glass rounded-lg p-6 shadow-premium mb-8 animate-fade-in-up">
    <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <input type="hidden" name="report_type" value="individual">
        <div class="flex-1 relative group">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-rose-500 transition-colors"></i>
            <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search by name or email…" class="w-full rounded-lg border border-gray-100 bg-white/50 pl-11 pr-4 py-4 text-sm text-gray-900 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-500/10 outline-none transition-all shadow-sm">
        </div>
        <button type="submit" class="rounded-lg bg-gradient-to-r from-rose-600 to-red-600 px-8 py-4 text-sm font-bold text-white shadow-lg shadow-rose-500/20 hover:shadow-rose-500/40 hover:-translate-y-0.5 transition-all duration-300">Run Report</button>
    </form>
</div>
<?php elseif (($report_type ?? '') === 'program'): ?>
<?php
$sel_prog = $_GET['program'] ?? 'all';
$sel_stat = $_GET['prog_status'] ?? 'all';
?>
<div class="glass rounded-lg p-6 shadow-premium mb-8 animate-fade-in-up">
    <form method="get" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="report_type" value="program">
        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Program Type</label>
            <div class="relative">
                <select name="program" onchange="this.form.submit()" class="w-full appearance-none rounded-lg border border-gray-100 bg-white/50 px-4 py-4 text-sm text-gray-900 focus:border-rose-300 focus:ring-4 focus:ring-rose-500/10 outline-none transition-all shadow-sm">
                    <option value="all" <?php echo $sel_prog === 'all' ? 'selected' : ''; ?>>All programs</option>
                    <option value="educational" <?php echo $sel_prog === 'educational' ? 'selected' : ''; ?>>Educational</option>
                    <option value="medical" <?php echo $sel_prog === 'medical' ? 'selected' : ''; ?>>Medical</option>
                    <option value="burial" <?php echo $sel_prog === 'burial' ? 'selected' : ''; ?>>Burial</option>
                    <option value="employment" <?php echo $sel_prog === 'employment' ? 'selected' : ''; ?>>Employment</option>
                    <option value="transportation" <?php echo $sel_prog === 'transportation' ? 'selected' : ''; ?>>Transportation</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
            </div>
        </div>
        <div class="space-y-2">
            <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-widest ml-1">Application Status</label>
            <div class="relative">
                <select name="prog_status" onchange="this.form.submit()" class="w-full appearance-none rounded-lg border border-gray-100 bg-white/50 px-4 py-4 text-sm text-gray-900 focus:border-rose-300 focus:ring-4 focus:ring-rose-500/10 outline-none transition-all shadow-sm">
                    <option value="all" <?php echo $sel_stat === 'all' ? 'selected' : ''; ?>>All statuses</option>
                    <option value="pending" <?php echo $sel_stat === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $sel_stat === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $sel_stat === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    <option value="completed" <?php echo $sel_stat === 'completed' ? 'selected' : ''; ?>>Reviewed</option>
                </select>
                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>


<?php
$displayRequests = match ($report_type ?? 'individual') {
    'program' => $program_requests ?? [],
    default => $individual_results ?? [],
};
?>

<div class="rounded-lg border border-gray-100 bg-white shadow-sm overflow-hidden animate-fade-in-up [animation-delay:100ms]">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50/80 border-b border-gray-100">
                    <th class="text-left py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Name</th>
                    <th class="text-left py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Program</th>
                    <th class="text-center py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Status</th>
                    <th class="text-left py-4 px-6 font-bold text-gray-400 uppercase tracking-wider text-[10px]">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($displayRequests)): ?>
                    <?php foreach ($displayRequests as $req): ?>
                    <tr class="hover:bg-gray-50/60 transition-colors group">
                        <td class="py-4 px-6 font-bold text-gray-900 group-hover:text-rose-600 transition-colors"><?php echo htmlspecialchars($req['fullname'] ?? ''); ?></td>
                        <td class="py-4 px-6 capitalize text-gray-500 font-medium"><?php echo htmlspecialchars($req['request_type'] ?? ''); ?></td>
                        <td class="text-center py-4 px-6">
                            <span class="<?php echo getStatusBadgeClass($req['status'] ?? ''); ?> px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider ring-1 shadow-sm">
                                <?php echo htmlspecialchars($req['status'] ?? ''); ?>
                            </span>
                        </td>
                        <td class="py-4 px-6 text-gray-500 text-xs tabular-nums"><?php echo formatDate($req['created_at'] ?? ''); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500">No data to display</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
