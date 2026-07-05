<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight font-outfit">Exported Report</h1>
        <p class="mt-1 text-gray-500 text-sm">Showing <?php echo count($data); ?> application(s) and their attached files.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="<?php echo base_url('admin/reports'); ?>" class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors shadow-sm">
            <i class="fas fa-arrow-left mr-2"></i> Back
        </a>
        <button onclick="window.print()" class="bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-800 transition-colors shadow-sm">
            <i class="fas fa-print mr-2"></i> Print View
        </button>
    </div>
</div>

<div class="space-y-12">
    <?php if (empty($data)): ?>
        <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center shadow-sm">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-400">
                <i class="fas fa-folder-open text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No Data Found</h3>
            <p class="text-gray-500 text-sm">There are no applications matching your export criteria.</p>
        </div>
    <?php endif; ?>

    <?php foreach ($data as $req): ?>
        <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm print:shadow-none print:border-gray-300 mb-8 page-break-after">
            
            <div class="border-b border-gray-100 pb-6 mb-6">
                <div class="flex items-center justify-between gap-4 flex-wrap mb-4">
                    <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($req['fullname'] ?? 'Unknown'); ?></h2>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold uppercase tracking-wider">
                        <?php echo htmlspecialchars($req['status'] ?? ''); ?>
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Program</span>
                        <span class="font-semibold text-gray-900 capitalize"><?php echo htmlspecialchars($req['request_type'] ?? ''); ?></span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Date Submitted</span>
                        <span class="font-semibold text-gray-900"><?php echo date('M d, Y g:i A', strtotime($req['created_at'])); ?></span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Email</span>
                        <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($req['email'] ?? 'N/A'); ?></span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-1">Contact</span>
                        <span class="font-semibold text-gray-900"><?php echo htmlspecialchars($req['contact'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Application Form Details -->
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-list text-gray-400"></i> Form Details
                    </h3>
                    <div class="bg-gray-50/80 rounded-2xl p-6 border border-gray-100">
                        <dl class="space-y-4">
                            <?php 
                            $details = json_decode($req['details'] ?? '{}', true) ?: [];
                            $hasTextDetails = false;
                            
                            foreach ($details as $key => $val):
                                if (strpos($key, '_path') === false && is_scalar($val) && strval($val) !== ''):
                                    $hasTextDetails = true;
                                    // Convert camelCase or snake_case to Title Case
                                    $cleanKey = ucwords(str_replace('_', ' ', preg_replace('/(?<!^)[A-Z]/', ' $0', $key)));
                            ?>
                                <div>
                                    <dt class="text-[11px] font-bold text-gray-500 uppercase tracking-widest mb-1"><?php echo htmlspecialchars($cleanKey); ?></dt> 
                                    <dd class="text-sm font-semibold text-gray-900 bg-white px-3 py-2 rounded-lg border border-gray-100 shadow-sm"><?php echo htmlspecialchars($val); ?></dd>
                                </div>
                            <?php 
                                endif;
                            endforeach; 
                            
                            if (!$hasTextDetails):
                            ?>
                                <p class="text-sm text-gray-500 italic">No additional form details provided.</p>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>

                <!-- Attached Documents -->
                <div>
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-paperclip text-gray-400"></i> Attached Documents
                    </h3>
                    
                    <div class="space-y-6">
                        <?php 
                        $hasFiles = false;
                        foreach ($details as $key => $val):
                            if (strpos($key, '_path') !== false && !empty($val)):
                                $filePath = ROOT_PATH . '/public/' . ltrim($val, '/');
                                if (file_exists($filePath)):
                                    $hasFiles = true;
                                    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                                    $docName = str_replace('_path', '', $key);
                                    $cleanDocName = ucwords(str_replace('_', ' ', preg_replace('/(?<!^)[A-Z]/', ' $0', $docName)));
                        ?>
                                    <div class="border border-gray-200 rounded-2xl overflow-hidden bg-white shadow-sm print:break-inside-avoid">
                                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                                            <span class="font-bold text-xs text-gray-700 uppercase tracking-wider">
                                                <?php echo htmlspecialchars($cleanDocName); ?>
                                            </span>
                                            <a href="<?php echo base_url(ltrim($val, '/')); ?>" target="_blank" class="text-blue-600 hover:text-blue-800 text-[10px] uppercase font-bold tracking-widest print:hidden">
                                                Open Original
                                            </a>
                                        </div>
                                        <div class="p-4 flex items-center justify-center min-h-[150px] bg-gray-50/30">
                                            <?php if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                                <img src="<?php echo base_url(ltrim($val, '/')); ?>" class="max-w-full h-auto max-h-96 object-contain rounded-lg border border-gray-100 shadow-sm" alt="<?php echo htmlspecialchars($cleanDocName); ?>">
                                            <?php else: ?>
                                                <div class="text-center p-6">
                                                    <i class="fas fa-file-pdf text-5xl text-rose-400 mb-3 drop-shadow-sm"></i>
                                                    <p class="text-sm font-semibold text-gray-700">PDF Document</p>
                                                    <p class="text-xs text-gray-500 mt-1">Cannot preview directly in print view.</p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                        <?php 
                                endif;
                            endif;
                        endforeach; 
                        
                        if (!$hasFiles):
                        ?>
                            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 text-center">
                                <p class="text-sm text-gray-500 font-medium">No files attached to this application.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
        </div>
    <?php endforeach; ?>
</div>

<style>
    @media print {
        body { background: white !important; }
        .page-break-after { page-break-after: always; }
        .page-break-after:last-child { page-break-after: auto; }
        nav, aside, header, footer { display: none !important; }
        .lg\:pl-72 { padding-left: 0 !important; }
        main { padding: 0 !important; margin: 0 !important; }
    }
</style>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
