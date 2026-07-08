<?php 
$title = "View Request Details";
$body_class = "bg-optimum-gray font-sans relative text-slate-950";
require_once APP_PATH . '/views/layouts/header.php'; 
?>
    <?php require_once APP_PATH . '/views/layouts/client_navbar.php'; ?>

    <!-- Fixed Back Button -->
    <a href="<?php echo base_url('client'); ?>" 
       class="fixed top-24 left-6 z-50 w-12 h-12 md:w-14 md:h-14 bg-white/80 backdrop-blur-md shadow-2xl border border-white flex items-center justify-center text-slate-800 hover:text-optimum-red rounded-full transition-all duration-300 hover:scale-110 active:scale-95 group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
    </a>

    <main class="min-h-screen py-20 relative z-10">
        <div class="container mx-auto px-6">
            <!-- Header -->
            <div class="max-w-3xl mx-auto text-center mb-16">
                <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Comprehensive View</p>
                <h1 class="text-4xl md:text-7xl font-black text-optimum-dark tracking-tight uppercase mb-6">
                    Request <span class="text-transparent" style="-webkit-text-stroke: 1px #111111;">Details</span>
                </h1>
                <p class="text-lg font-bold opacity-60">
                    Enter your Email and select a Category to view the full details of your application.
                </p>
            </div>

            <!-- Search Form -->
            <div class="max-w-4xl mx-auto mb-20 px-4">
                <div class="bg-optimum-gray p-2 md:p-3 shadow-2xl relative border border-gray-100 group/track-container">
                    <div class="absolute -top-3 -left-3 md:-top-4 md:-left-4 w-10 h-10 md:w-12 md:h-12 bg-optimum-red flex items-center justify-center text-white text-base md:text-xl shadow-lg">
                        <i class="fas fa-search"></i>
                    </div>
                    <form id="track-form" method="POST" action="" class="flex flex-col md:flex-row items-stretch gap-4">
                        <input type="email" name="email" required 
                               placeholder="ENTER YOUR EMAIL" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                               class="flex-1 bg-white px-6 py-4 md:px-8 md:py-6 text-[11px] font-black uppercase tracking-widest focus:outline-none border-l-4 border-transparent focus:border-optimum-red transition-all text-optimum-dark placeholder:text-slate-400">
                        
                        <div class="relative flex-1">
                            <select name="category" required
                                    class="w-full h-full appearance-none bg-white px-6 py-4 md:px-8 md:py-6 text-[11px] font-black uppercase tracking-widest focus:outline-none border-l-4 border-transparent focus:border-optimum-red transition-all text-optimum-dark cursor-pointer">
                                <option value="" disabled <?php echo empty($_POST['category']) ? 'selected' : ''; ?>>SELECT CATEGORY</option>
                                <option value="educational" <?php echo (isset($_POST['category']) && $_POST['category'] == 'educational') ? 'selected' : ''; ?>>Educational Assistance</option>
                                <option value="medical" <?php echo (isset($_POST['category']) && $_POST['category'] == 'medical') ? 'selected' : ''; ?>>Medical Assistance</option>
                                <option value="burial" <?php echo (isset($_POST['category']) && $_POST['category'] == 'burial') ? 'selected' : ''; ?>>Burial Assistance</option>
                                <option value="employment" <?php echo (isset($_POST['category']) && $_POST['category'] == 'employment') ? 'selected' : ''; ?>>Employment Assistance</option>
                                <option value="transportation" <?php echo (isset($_POST['category']) && $_POST['category'] == 'transportation') ? 'selected' : ''; ?>>Transportation Assistance</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>

                        <button type="submit" 
                                class="px-8 py-4 md:px-12 md:py-6 bg-optimum-dark text-white font-black uppercase tracking-[0.2em] hover:bg-optimum-red transition-colors whitespace-nowrap shadow-lg">
                            VIEW DETAILS
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <?php if (isset($searched) && $searched): ?>
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-900">Application Details</h2>
                    <span class="px-5 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-full">
                        <?php echo count($requests ?? []); ?> Found
                    </span>
                </div>

                <?php if (empty($requests)): ?>
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl p-10 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-3xl">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">No Requests Found</h3>
                        <p class="text-slate-500">We couldn't find any applications matching that email and category.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-8">
                        <?php foreach ($requests as $req): 
                            $details = json_decode($req['details'], true) ?: [];
                            $icon = '📄';
                            if($req['request_type'] == 'educational') $icon = '🎓';
                            elseif($req['request_type'] == 'medical') $icon = '🏥';
                            elseif($req['request_type'] == 'burial') $icon = '🕊️';
                            elseif($req['request_type'] == 'employment') $icon = '💼';
                            elseif($req['request_type'] == 'transportation') $icon = '🚌';
                        ?>
                        <div class="bg-white rounded-2xl border border-slate-100 shadow-xl overflow-hidden">
                            <!-- Header Info -->
                            <div class="bg-slate-50 p-6 md:p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-2xl border border-slate-100">
                                        <?php echo $icon; ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3">
                                            <h3 class="text-lg font-bold text-slate-900">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></h3>
                                            <span class="px-3 py-1 rounded-full text-xs font-black tracking-widest uppercase text-white 
                                                <?php echo strtolower($req['status']) == 'approved' ? 'bg-emerald-500' : 
                                                        (strtolower($req['status']) == 'rejected' ? 'bg-rose-500' : 
                                                        (strtolower($req['status']) == 'completed' ? 'bg-blue-500' : 'bg-amber-500')); ?>">
                                                <?php echo $req['status']; ?>
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium text-slate-500 mt-1">Applied on <?php echo date('F j, Y', strtotime($req['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="text-left md:text-right">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Reference Number</p>
                                    <p class="text-base font-black text-slate-800 tracking-wider"><?php echo htmlspecialchars($req['reference_number']); ?></p>
                                </div>
                            </div>
                            
                            <!-- Detailed Information -->
                            <div class="p-6 md:p-8">
                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 pb-2 border-b border-slate-100">Applicant Information</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Full Name</p>
                                        <p class="text-sm font-semibold text-slate-800"><?php echo htmlspecialchars($req['fullname']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Address</p>
                                        <p class="text-sm font-semibold text-slate-800"><?php echo htmlspecialchars($req['email']); ?></p>
                                    </div>
                                </div>

                                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-6 pb-2 border-b border-slate-100">Program Specific Details</h4>
                                <?php if (!empty($details)): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                                        <?php foreach ($details as $key => $value): 
                                            // Skip empty or purely technical fields
                                            if ($value === '' || $value === null || in_array($key, ['csrf_token', 'agree'])) continue;
                                            // Make key readable
                                            $readable_key = ucwords(str_replace(['_', '-'], ' ', preg_replace('/(?<!^)[A-Z]/', ' $0', $key)));
                                        ?>
                                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1"><?php echo htmlspecialchars($readable_key); ?></p>
                                                <?php if (strpos($key, 'path') !== false || strpos($key, 'file') !== false): ?>
                                                    <a href="<?php echo base_url(htmlspecialchars($value)); ?>" target="_blank" class="text-sm font-bold text-optimum-red hover:text-optimum-dark transition-colors flex items-center gap-2">
                                                        <i class="fas fa-file-download"></i> View Document
                                                    </a>
                                                <?php else: ?>
                                                    <p class="text-sm font-semibold text-slate-800 break-words"><?php echo htmlspecialchars($value); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-sm text-slate-500 italic">No specific details recorded for this application.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <style>
        .tracking-super-wide { letter-spacing: 0.3em; }
    </style>

<?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
