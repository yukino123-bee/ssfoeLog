<?php
$mom_submissions_pct = $mom_submissions_pct ?? 0;
$program_breakdown = $program_breakdown ?? [];
$new_clients_month = $new_clients_month ?? 0;
$active_today = $active_today ?? 0;
$total = (int)($stats['total'] ?? 0);
$approved = (int)($stats['approved'] ?? 0);
$approval_rate = $total > 0 ? round(($approved / $total) * 100) : 0;
?>
<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<!-- Page header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-500 tracking-tight">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1 font-medium">Welcome back! Here's what's happening today.</p>
    </div>
    <div class="flex items-center gap-3">
        <div class="px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center gap-2 text-sm font-semibold text-gray-700">
            <i class="far fa-calendar text-blue-500"></i>
            <?php echo date('F d, Y'); ?>
        </div>
    </div>
</div>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="mb-8 p-4 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-100 text-sm font-medium flex items-center gap-3 animate-fade-in-up">
        <div class="h-8 w-8 rounded-full bg-emerald-200 flex items-center justify-center shrink-0">
            <i class="fas fa-check text-emerald-700"></i>
        </div>
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
            <h3 class="text-4xl font-extrabold text-gray-900 mb-1"><?php echo number_format($stats['total'] ?? 0); ?></h3>
            <p class="text-sm font-medium text-gray-500">Total Requests</p>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <h3 class="text-4xl font-extrabold text-gray-900 mb-1"><?php echo number_format($stats['pending'] ?? 0); ?></h3>
            <p class="text-sm font-medium text-gray-500">Awaiting Review</p>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <h3 class="text-4xl font-extrabold text-gray-900 mb-1"><?php echo number_format($stats['approved'] ?? 0); ?></h3>
            <p class="text-sm font-medium text-gray-500">Approved Requests</p>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div class="h-12 w-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
            <h3 class="text-4xl font-extrabold text-gray-900 mb-1"><?php echo $approval_rate; ?>%</h3>
            <p class="text-sm font-medium text-gray-500">Approval Rate</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Recent Applications Table (Spans 2 columns) -->
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-white/50 backdrop-blur-xl">
            <h2 class="text-lg font-bold text-gray-900">Recent Applications</h2>
            <a href="<?php echo base_url('admin/requests'); ?>" class="text-sm font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-4 py-2 rounded-xl transition-colors">View All</a>
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="py-4 px-8 font-semibold text-gray-500 text-xs uppercase tracking-wider">Applicant</th>
                        <th class="py-4 px-8 font-semibold text-gray-500 text-xs uppercase tracking-wider">Program</th>
                        <th class="py-4 px-8 font-semibold text-gray-500 text-xs uppercase tracking-wider">Status</th>
                        <th class="py-4 px-8 font-semibold text-gray-500 text-xs uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
                    $displayCount = min(5, count($recent_requests));
                    if ($displayCount === 0):
                    ?>
                    <tr>
                        <td colspan="4" class="py-12 text-center text-gray-500">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 mb-4">
                                <i class="fas fa-inbox text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-sm font-medium">No recent applications.</p>
                        </td>
                    </tr>
                    <?php else:
                        for ($j = 0; $j < $displayCount; $j++):
                            $req = $recent_requests[$j];
                            $statusText = htmlspecialchars($req['status'] ?? '');
                            
                            $statusBg = 'bg-gray-100 text-gray-700';
                            $dotColor = 'bg-gray-400';
                            if ($statusText === 'pending') { $statusBg = 'bg-amber-100 text-amber-800'; $dotColor = 'bg-amber-500'; }
                            elseif ($statusText === 'approved') { $statusBg = 'bg-emerald-100 text-emerald-800'; $dotColor = 'bg-emerald-500'; }
                            elseif ($statusText === 'rejected') { $statusBg = 'bg-red-100 text-red-800'; $dotColor = 'bg-red-500'; }
                            elseif ($statusText === 'completed') { $statusBg = 'bg-blue-100 text-blue-800'; $dotColor = 'bg-blue-500'; }
                            
                            $name = htmlspecialchars($req['fullname'] ?? '');
                            $initial = strtoupper(substr($name, 0, 1));
                    ?>
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="py-4 px-8">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700 font-bold flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                    <?php echo $initial; ?>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm"><?php echo $name; ?></div>
                                    <div class="text-xs text-gray-500 mt-0.5"><?php echo date('M d, Y', strtotime($req['created_at'] ?? 'now')); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-8 text-sm font-medium text-gray-600 capitalize">
                            <?php echo htmlspecialchars($req['request_type'] ?? ''); ?>
                        </td>
                        <td class="py-4 px-8">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?php echo $statusBg; ?>">
                                <span class="h-1.5 w-1.5 rounded-full <?php echo $dotColor; ?>"></span>
                                <?php echo ucfirst($statusText); ?>
                            </span>
                        </td>
                        <td class="py-4 px-8">
                            <a href="<?php echo base_url('admin/requests/view?id=' . (int)($req['id'] ?? 0)); ?>" class="text-gray-400 hover:text-blue-600 transition-colors">
                                <i class="fas fa-chevron-right bg-white shadow-sm border border-gray-100 p-2 rounded-lg group-hover:border-blue-200 group-hover:shadow-md"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endfor; endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Program Breakdown (1 Column) -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Program Breakdown</h2>
            <p class="text-sm text-gray-500 mt-1">Total requests by category.</p>
        </div>
        <div class="p-8 flex-1 flex flex-col justify-center space-y-6">
            <?php
            $types = ['educational' => 'blue', 'medical' => 'emerald', 'burial' => 'purple', 'employment' => 'amber', 'transportation' => 'rose'];
            
            // Find max for percentage calculation based on max program
            $maxTot = 1;
            foreach ($types as $prog => $color) {
                $row = $program_breakdown[$prog] ?? ['total' => 0];
                if ((int)($row['total'] ?? 0) > $maxTot) {
                    $maxTot = (int)($row['total'] ?? 0);
                }
            }

            foreach ($types as $prog => $color):
                $row = $program_breakdown[$prog] ?? ['total' => 0];
                $tot = (int)($row['total'] ?? 0);
                $pct = round(($tot / $maxTot) * 100);
            ?>
            <div>
                <div class="flex justify-between items-end mb-2">
                    <span class="text-sm font-bold text-gray-700 capitalize"><?php echo $prog; ?></span>
                    <span class="text-sm font-bold text-gray-900"><?php echo number_format($tot); ?></span>
                </div>
                <div class="h-2.5 w-full bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-<?php echo $color; ?>-500 rounded-full" style="width: <?php echo $pct; ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
