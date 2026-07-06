<?php
$detail_rows = program_detail_display_rows($request['details'] ?? '{}');
$detail_fields = array_values(array_filter($detail_rows, fn($r) => ($r['type'] ?? '') === 'text'));
$detail_files = array_values(array_filter($detail_rows, fn($r) => ($r['type'] ?? '') === 'file'));
?>
<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<!-- Top Navigation / Breadcrumb -->
<div class="mb-8">
    <a href="<?php echo base_url('admin/requests'); ?>" class="group inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors">
        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors mr-3">
            <i class="fas fa-arrow-left text-xs"></i>
        </span>
        Back to Requests
    </a>
</div>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 text-sm font-medium flex items-center gap-4 shadow-sm animate-fade-in-down">
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 shrink-0">
            <i class="fas fa-check"></i>
        </div>
        <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<div class="flex flex-col lg:flex-row gap-8">
    
    <!-- LEFT COLUMN: Main Details -->
    <div class="flex-1 space-y-8">
        
        <!-- Applicant Profile Header -->
        <div class="bg-white rounded-3xl border border-slate-200/60 p-8 shadow-sm hover:shadow-md transition-shadow duration-300 relative overflow-hidden">
            <!-- Decorative background blob -->
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-gradient-to-br from-indigo-50 to-blue-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 relative z-10">
                <div class="flex items-center gap-6">
                    <?php 
                    $name = htmlspecialchars($request['fullname'] ?? '');
                    $initial = strtoupper(substr($name, 0, 1));
                    ?>
                    <div class="h-24 w-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-blue-500/30 flex items-center justify-center text-white text-4xl font-black shrink-0 transform -rotate-3 hover:rotate-0 transition-transform duration-300 ring-4 ring-white">
                        <?php echo $initial; ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-4 flex-wrap">
                            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight"><?php echo $name; ?></h1>
                            <?php
                                $statusText = htmlspecialchars($request['status'] ?? '');
                                $statusBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                $dotColor = 'bg-slate-400';
                                if ($statusText === 'pending') { $statusBg = 'bg-amber-50 text-amber-700 border-amber-200 shadow-sm'; $dotColor = 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]'; }
                                elseif ($statusText === 'approved') { $statusBg = 'bg-emerald-50 text-emerald-700 border-emerald-200 shadow-sm'; $dotColor = 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]'; }
                                elseif ($statusText === 'rejected') { $statusBg = 'bg-rose-50 text-rose-700 border-rose-200 shadow-sm'; $dotColor = 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]'; }
                                elseif ($statusText === 'completed') { $statusBg = 'bg-blue-50 text-blue-700 border-blue-200 shadow-sm'; $dotColor = 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]'; }
                            ?>
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide border <?php echo $statusBg; ?>">
                                <span class="h-2 w-2 rounded-full <?php echo $dotColor; ?> animate-pulse"></span>
                                <?php echo ucfirst($statusText); ?>
                            </span>
                        </div>
                        <p class="text-slate-500 text-sm font-medium">Request ID <span class="text-slate-700 font-bold">#<?php echo htmlspecialchars((string) ($request['id'] ?? '')); ?></span> &bull; Submitted <?php echo formatDate($request['created_at'] ?? ''); ?></p>
                        <div class="flex flex-wrap items-center gap-6 text-sm pt-1">
                            <a href="mailto:<?php echo htmlspecialchars($request['email'] ?? ''); ?>" class="group inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition-colors font-medium">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 group-hover:bg-indigo-50 transition-colors">
                                    <i class="fas fa-envelope text-slate-400 group-hover:text-indigo-500 text-xs"></i>
                                </span>
                                <?php echo htmlspecialchars($request['email'] ?? ''); ?>
                            </a>
                            <span class="inline-flex items-center gap-2 text-slate-600 font-medium">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100">
                                    <i class="fas fa-layer-group text-slate-400 text-xs"></i>
                                </span>
                                <span class="capitalize"><?php echo htmlspecialchars($request['request_type'] ?? ''); ?> Program</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <?php if (($request['status'] ?? '') === 'pending'): ?>
                <div class="flex items-center gap-3 w-full sm:w-auto pt-2 sm:pt-0">
                    <button type="button" onclick="rejectRequest(<?php echo (int) $request['id']; ?>)" class="flex-1 sm:flex-none inline-flex items-center justify-center px-5 py-2.5 bg-white border border-rose-200 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-50 hover:border-rose-300 shadow-sm hover:shadow transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                        Reject
                    </button>
                    <button type="button" onclick="approveRequest(<?php echo (int) $request['id']; ?>)" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-2.5 bg-gradient-to-r from-slate-900 to-slate-800 text-white text-sm font-bold rounded-xl hover:from-slate-800 hover:to-slate-700 shadow-lg shadow-slate-900/20 transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900">
                        Approve
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Details Section -->
        <div class="bg-white rounded-3xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-8 py-6 border-b border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-address-card text-indigo-400/70 text-base"></i>
                    Application Information
                </h3>
            </div>
            <div class="p-8">
                <?php if (!empty($detail_fields)): ?>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-8">
                        <?php foreach ($detail_fields as $row): ?>
                            <div class="group">
                                <dt class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-2 group-hover:text-indigo-500 transition-colors"><?php echo htmlspecialchars($row['label']); ?></dt>
                                <dd class="text-base font-semibold text-slate-800 bg-slate-50/50 rounded-xl px-4 py-3 border border-slate-100 group-hover:bg-indigo-50/30 group-hover:border-indigo-100 transition-colors"><?php echo htmlspecialchars($row['value']); ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-3 text-slate-200"></i>
                        <p class="text-sm font-medium">No additional details provided.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="bg-white rounded-3xl border border-slate-200/60 overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
            <div class="px-8 py-6 border-b border-slate-100">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-paperclip text-indigo-400/70 text-base"></i>
                    Attached Documents
                </h3>
            </div>
            <div class="p-8 bg-slate-50/30">
                <?php if (!empty($detail_files)): ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <?php foreach ($detail_files as $doc): ?>
                            <div class="flex flex-col p-5 rounded-2xl bg-white border border-slate-200 hover:border-indigo-300 hover:shadow-lg hover:shadow-indigo-500/10 transition-all duration-300 group transform hover:-translate-y-1">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-file-alt text-xl"></i>
                                    </div>
                                    <?php if (!empty($doc['href'])): ?>
                                        <a href="<?php echo htmlspecialchars($doc['href']); ?>" target="_blank" rel="noopener" class="h-8 w-8 rounded-full bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-indigo-500 hover:text-white transition-colors" title="View Document">
                                            <i class="fas fa-external-link-alt text-xs"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-auto">
                                    <p class="text-sm font-bold text-slate-800 line-clamp-2 group-hover:text-indigo-600 transition-colors" title="<?php echo htmlspecialchars($doc['label']); ?>">
                                        <?php echo htmlspecialchars($doc['label']); ?>
                                    </p>
                                    <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wide">Document</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                        <i class="fas fa-folder-open text-4xl mb-3 text-slate-200"></i>
                        <p class="text-sm font-medium">No documents attached.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: Sidebar (Sticky) -->
    <div class="lg:w-[400px] space-y-8">
        
        <!-- Update Status Box -->
        <div class="bg-white rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow duration-300 sticky top-8 overflow-hidden">
            <!-- Decorative header line -->
            <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-blue-500 to-indigo-500"></div>
            
            <div class="p-8">
                <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-bolt text-indigo-500"></i> Action Panel
                </h3>
                
                <form method="POST" action="<?php echo base_url('admin/requests/update'); ?>" class="space-y-6">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars((string) ($request['id'] ?? '')); ?>">
                    <input type="hidden" name="from" value="request">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Change Status</label>
                        <div class="relative group">
                            <select name="status" class="w-full appearance-none rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none cursor-pointer hover:bg-slate-100 transition-all duration-200">
                                <option value="pending" <?php echo ($request['status'] ?? '') === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo ($request['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo ($request['status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                <option value="completed" <?php echo ($request['status'] ?? '') === 'completed' ? 'selected' : ''; ?>>Reviewed</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover:text-indigo-500 transition-colors">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">Internal Note <span class="text-slate-400 font-normal normal-case tracking-normal">(Optional)</span></label>
                        <textarea name="remarks" rows="3" placeholder="Add context for this change..." class="w-full rounded-xl border-2 border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none resize-none hover:bg-slate-100 transition-all duration-200"></textarea>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:from-indigo-500 hover:to-blue-500 transform hover:-translate-y-0.5 transition-all duration-200">
                        Update Application
                    </button>
                </form>
                
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-3 text-xs font-bold text-slate-300 uppercase tracking-widest">History</span>
                    </div>
                </div>

                <!-- Activity Log -->
                <?php if (!empty($logs)): ?>
                    <div class="relative pl-4 border-l-2 border-slate-100 space-y-8 mt-4">
                        <?php foreach ($logs as $index => $log): ?>
                            <div class="relative">
                                <?php
                                    $logStatus = strtolower($log['status_to']);
                                    $dotClasses = 'bg-slate-100 border-slate-300';
                                    $icon = 'fa-circle';
                                    $iconColor = 'text-slate-400';
                                    
                                    if ($logStatus === 'approved') {
                                        $dotClasses = 'bg-emerald-100 border-emerald-200';
                                        $icon = 'fa-check';
                                        $iconColor = 'text-emerald-500';
                                    } elseif ($logStatus === 'rejected') {
                                        $dotClasses = 'bg-rose-100 border-rose-200';
                                        $icon = 'fa-times';
                                        $iconColor = 'text-rose-500';
                                    } elseif ($logStatus === 'completed') {
                                        $dotClasses = 'bg-blue-100 border-blue-200';
                                        $icon = 'fa-flag-checkered';
                                        $iconColor = 'text-blue-500';
                                    } elseif ($logStatus === 'pending') {
                                        $dotClasses = 'bg-amber-100 border-amber-200';
                                        $icon = 'fa-clock';
                                        $iconColor = 'text-amber-500';
                                    }
                                ?>
                                <div class="absolute -left-[27px] top-0.5 h-6 w-6 rounded-full border-2 bg-white flex items-center justify-center shadow-sm <?php echo $dotClasses; ?>">
                                    <i class="fas <?php echo $icon; ?> text-[10px] <?php echo $iconColor; ?>"></i>
                                </div>
                                <div class="<?php echo $index === 0 ? 'bg-slate-50/80 p-4 rounded-2xl border border-slate-100 -mt-3' : ''; ?>">
                                    <p class="text-sm font-medium text-slate-800">
                                        Status changed to <span class="capitalize font-extrabold <?php echo $iconColor; ?>"><?php echo htmlspecialchars($log['status_to']); ?></span>
                                    </p>
                                    <p class="text-[11px] font-bold text-slate-400 mt-1 uppercase tracking-wide">
                                        <?php echo date('M d, g:i A', strtotime($log['created_at'])); ?> &bull; <span class="text-slate-500"><?php echo htmlspecialchars($log['admin_name'] ?? 'System'); ?></span>
                                    </p>
                                    <?php if (!empty($log['remarks'])): ?>
                                        <div class="mt-3 text-sm text-slate-600 bg-white p-3 rounded-xl border border-slate-200 shadow-sm relative">
                                            <div class="absolute -top-1.5 left-4 w-3 h-3 bg-white border-t border-l border-slate-200 transform rotate-45"></div>
                                            <span class="italic font-medium">"<?php echo htmlspecialchars($log['remarks']); ?>"</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-6">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 mb-3 text-slate-300">
                            <i class="fas fa-history text-xl"></i>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">No activity recorded yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
(function () {
    var csrfToken = <?php echo json_encode(generate_csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
    var updateUrl = <?php echo json_encode(base_url('admin/requests/update'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    function appendHidden(form, name, value) {
        var el = document.createElement('input');
        el.type = 'hidden';
        el.name = name;
        el.value = value;
        form.appendChild(el);
    }

    function postDecision(id, status, remarks) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = updateUrl;
        appendHidden(form, 'id', String(id));
        appendHidden(form, 'status', status);
        appendHidden(form, 'remarks', remarks);
        appendHidden(form, 'from', 'request');
        appendHidden(form, 'csrf_token', csrfToken);
        document.body.appendChild(form);
        form.submit();
    }

    window.approveRequest = function (id) {
        if (confirm('Are you sure you want to approve this application?')) {
            postDecision(id, 'approved', 'Approved by administrator');
        }
    };

    window.rejectRequest = function (id) {
        if (confirm('Are you sure you want to reject this application?')) {
            postDecision(id, 'rejected', 'Rejected by administrator');
        }
    };

})();
</script>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
