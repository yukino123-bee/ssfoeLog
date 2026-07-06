<?php 
$title = "Track Your Request";
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
                <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Real-time Updates</p>
                <h1 class="text-4xl md:text-7xl font-black text-optimum-dark tracking-tight uppercase mb-6">
                    Track Your <span class="text-transparent" style="-webkit-text-stroke: 1px #111111;">Request</span>
                </h1>
                <p class="text-lg font-bold opacity-60">
                    Enter your Full Name or Phone Number to check your status and view your history.
                </p>
            </div>

            <!-- Search Form -->
            <div class="max-w-4xl mx-auto mb-20 px-4">
                <div class="bg-optimum-gray p-2 md:p-3 shadow-2xl relative border border-gray-100 group/track-container">
                    <div class="absolute -top-3 -left-3 md:-top-4 md:-left-4 w-10 h-10 md:w-12 md:h-12 bg-optimum-red flex items-center justify-center text-white text-base md:text-xl shadow-lg">
                        <i class="fas fa-search"></i>
                    </div>
                    <form id="track-form" method="POST" action="" class="flex flex-col md:flex-row items-stretch gap-4">
                        <input type="text" name="identifier" required 
                               placeholder="FULL NAME OR PHONE NUMBER" 
                               class="flex-1 bg-white px-6 py-4 md:px-8 md:py-6 text-[11px] font-black uppercase tracking-widest focus:outline-none border-l-4 border-transparent focus:border-optimum-red transition-all text-optimum-dark placeholder:text-slate-400">
                        <button type="submit" 
                                class="px-8 py-4 md:px-12 md:py-6 bg-optimum-dark text-white font-black uppercase tracking-[0.2em] hover:bg-optimum-red transition-colors whitespace-nowrap shadow-lg">
                            CHECK STATUS
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div class="max-w-5xl mx-auto">
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-slate-100">
                    <h2 class="text-2xl font-bold text-slate-900">Your Applications</h2>
                    <span class="px-5 py-2 bg-slate-100 text-slate-600 text-sm font-bold rounded-full">
                        <?php echo count($requests ?? []); ?> Found
                    </span>
                </div>

                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-hidden bg-white rounded-2xl border border-slate-100 shadow-xl">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="px-8 py-6 text-sm font-bold text-slate-700">ID / Category</th>
                                <th class="px-8 py-6 text-sm font-bold text-slate-700">Assistance Program</th>
                                <th class="px-8 py-6 text-sm font-bold text-slate-700 text-center">Applied Date</th>
                                <th class="px-8 py-6 text-sm font-bold text-slate-700 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            <?php if (empty($requests) && !empty($identifier)): ?>
                                <tr><td colspan="4" class="px-8 py-6 text-center text-slate-500">No applications found for this name or phone number.</td></tr>
                            <?php elseif (empty($requests)): ?>
                                <tr><td colspan="4" class="px-8 py-6 text-center text-slate-500">Enter your full name or phone number above to track your requests.</td></tr>
                            <?php else: ?>
                            <?php foreach ($requests as $req): 
                                $icon = '📄';
                                if($req['request_type'] == 'educational') $icon = '🎓';
                                elseif($req['request_type'] == 'medical') $icon = '🏥';
                                elseif($req['request_type'] == 'burial') $icon = '🕊️';
                                elseif($req['request_type'] == 'employment') $icon = '💼';
                                elseif($req['request_type'] == 'transportation') $icon = '🚌';
                                elseif($req['request_type'] == 'access') $icon = '🔑';
                            ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-xl"><?php echo $icon; ?></div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                            <p class="text-xs text-slate-500"><?php echo $req['request_type'] == 'access' ? 'System Access' : ucfirst($req['request_type']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-base font-semibold text-slate-900"><?php echo $req['request_type'] == 'access' ? 'Program Access' : ucfirst($req['request_type']) . ' Assistance'; ?></p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <p class="text-sm text-slate-600"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="status <?php echo strtolower($req['status']); ?>">
                                            <?php echo ucfirst($req['status']); ?>
                                        </span>
                                        <button onclick="showStatusNotice(<?php echo htmlspecialchars(json_encode($req)); ?>)"
                                           class="text-[10px] font-black text-optimum-red uppercase tracking-widest hover:underline">
                                            <i class="fas fa-info-circle mr-1"></i> View Notice
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-4">
                    <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $req): 
                        $icon = '📄';
                        if($req['request_type'] == 'educational') $icon = '🎓';
                        elseif($req['request_type'] == 'medical') $icon = '🏥';
                        elseif($req['request_type'] == 'burial') $icon = '🕊️';
                        elseif($req['request_type'] == 'employment') $icon = '💼';
                        elseif($req['request_type'] == 'transportation') $icon = '🚌';
                        elseif($req['request_type'] == 'access') $icon = '🔑';
                    ?>
                    <div class="bg-white border border-slate-200 rounded-sm p-6 shadow-lg scroll-reveal">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-slate-50 text-slate-900 rounded-sm flex items-center justify-center text-2xl border border-slate-200"><?php echo $icon; ?></div>
                                <div>
                                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Application ID</p>
                                    <p class="text-lg font-black text-slate-900">#<?php echo str_pad($req['id'], 4, '0', STR_PAD_LEFT); ?></p>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <span class="status <?php echo strtolower($req['status']); ?> scale-90 origin-right"><?php echo ucfirst($req['status']); ?></span>
                                <button onclick="showStatusNotice(<?php echo htmlspecialchars(json_encode($req)); ?>)"
                                   class="text-[9px] font-black text-optimum-red uppercase tracking-widest hover:underline">
                                    View Notice
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Program</p>
                                <p class="text-base font-bold text-slate-900"><?php echo $req['request_type'] == 'access' ? 'Program Access' : ucfirst($req['request_type']) . ' Assistance'; ?></p>
                            </div>
                            <div class="flex justify-between items-end pt-4 border-t border-slate-100">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Category</p>
                                    <p class="text-xs font-bold text-slate-500"><?php echo $req['request_type'] == 'access' ? 'System Access' : ucfirst($req['request_type']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Applied Date</p>
                                    <p class="text-xs font-bold text-slate-500"><?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Status Notice Modal -->
    <div id="status-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div id="status-modal-overlay" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
            
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            
            <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100">
                <div id="printable-notice" class="bg-white px-8 pt-10 pb-8">
                    <!-- Print Header (Only visible on print) -->
                    <div class="hidden print:block text-center border-b-2 border-optimum-red pb-6 mb-8">
                        <h2 class="text-2xl font-black text-slate-900 uppercase tracking-tighter">SSFO eLog Program</h2>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">Social Services & Financial Office (SSFO)</p>
                    </div>

                    <div class="sm:flex sm:items-start">
                        <div id="status-icon-container" class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full sm:mx-0 sm:h-14 sm:w-14 print:hidden">
                            <i id="status-icon" class="fas text-2xl"></i>
                        </div>
                        <div class="mt-4 text-center sm:mt-0 sm:ml-6 sm:text-left w-full">
                            <h3 class="text-2xl font-black text-slate-900 leading-tight uppercase tracking-tight" id="status-modal-title">
                                Status Update
                            </h3>
                            <div class="mt-4 space-y-6">
                                <p class="text-slate-600 font-medium leading-relaxed" id="status-modal-message"></p>
                                
                                <!-- Detailed Info for Printing -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-6 border-y border-slate-100">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Applicant Name</p>
                                        <p class="text-sm font-bold text-slate-900" id="notice-name">-</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Applied</p>
                                        <p class="text-sm font-bold text-slate-900" id="notice-program">-</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Date Applied</p>
                                        <p class="text-sm font-bold text-slate-900" id="notice-date-applied">-</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Status Date</p>
                                        <p class="text-sm font-bold text-slate-900" id="notice-date-updated">-</p>
                                    </div>
                                </div>

                                <div class="p-5 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Office Instruction / Remarks</p>
                                    <p class="text-sm font-bold text-slate-900 leading-relaxed" id="status-modal-remarks">Please wait for the next update. Our office in-charge will send you an SMS notification with further instructions on your registered contact number.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Footer -->
                    <div class="hidden print:block mt-12 pt-8 border-t border-slate-100 text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                        This is an electronically generated notice from the SSFO eLog.
                    </div>
                </div>
                <div class="bg-slate-50 px-8 py-6 flex flex-col sm:flex-row-reverse gap-3">
                    <button type="button" id="close-status-modal" 
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-lg px-8 py-3 bg-slate-900 text-base font-bold text-white hover:bg-optimum-red focus:outline-none transition-all sm:ml-3 sm:w-auto">
                        GOT IT, THANKS!
                    </button>

                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { margin: 0; size: auto; }
            body { margin: 0; padding: 0; background: white; }
            body * { visibility: hidden; }
            #printable-notice, #printable-notice * { visibility: visible; }
            #printable-notice { 
                position: fixed; 
                left: 0; 
                top: 0; 
                width: 100vw; 
                height: 100vh;
                padding: 40px !important; 
                margin: 0 !important;
                background: white !important;
                display: block !important;
            }
            #status-modal { position: absolute !important; }
            #status-modal-overlay { display: none !important; }
        }
        /* Previous styles... */

    <style>
        /* Specific handling for Track Status error message */
        #track-form .error-message {
            position: absolute;
            left: 2rem;
            bottom: -1.75rem;
            color: var(--optimum-red, #d32f2f);
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Container error state */
        .group\/track-container:has(input.error) {
            border-color: var(--optimum-red, #d32f2f) !important;
            background-color: rgba(211, 47, 47, 0.05) !important;
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1) !important;
        }

        /* Reset input error style to not conflict with container */
        #track-form input.error {
            box-shadow: none !important;
            border: none !important;
        }

        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        #proof-frame {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.scroll-reveal');
            const observerOptions = {
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            reveals.forEach(reveal => observer.observe(reveal));

            // Status Notice Modal Logic
            const statusModal = document.getElementById('status-modal');
            const statusModalTitle = document.getElementById('status-modal-title');
            const statusModalMessage = document.getElementById('status-modal-message');
            const statusIcon = document.getElementById('status-icon');
            const statusIconContainer = document.getElementById('status-icon-container');

            const closeStatusModal = document.getElementById('close-status-modal');
            const statusModalOverlay = document.getElementById('status-modal-overlay');

            window.showStatusNotice = (req) => {
                const status = req.status.toLowerCase();
                const type = req.request_type.charAt(0).toUpperCase() + req.request_type.slice(1);
                
                // Populate Details
                document.getElementById('notice-name').innerText = req.fullname || 'N/A';
                document.getElementById('notice-program').innerText = (type === 'Access' ? 'Program Access' : type + ' Assistance');
                document.getElementById('notice-date-applied').innerText = new Date(req.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                document.getElementById('notice-date-updated').innerText = new Date(req.updated_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                
                const defaultRemarks = "Please wait for the next update. Our office in-charge will send you an SMS notification with further instructions on your registered contact number.";
                document.getElementById('status-modal-remarks').innerText = req.latest_remark || defaultRemarks;

                // Reset styles
                statusIconContainer.className = "mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full sm:mx-0 sm:h-14 sm:w-14 print:hidden";

                if (status === 'approved') {
                    statusModalTitle.innerText = "Notice of Approval";
                    statusModalMessage.innerHTML = `Your <strong>${type} Assistance</strong> application (#${String(req.id).padStart(4, '0')}) has been <strong>APPROVED</strong> by the SSFO administrator.`;
                    statusIcon.className = "fas fa-check-circle text-emerald-600 text-2xl";
                    statusIconContainer.classList.add("bg-emerald-50");
                } else if (status === 'rejected') {
                    statusModalTitle.innerText = "Application Status";
                    statusModalMessage.innerHTML = `Your <strong>${type} Assistance</strong> application has been <strong>REJECTED</strong>. Please visit the SSFO office for more details.`;
                    statusIcon.className = "fas fa-times-circle text-red-600 text-2xl";
                    statusIconContainer.classList.add("bg-red-50");
                } else {
                    statusModalTitle.innerText = "Application Pending";
                    statusModalMessage.innerHTML = `Your <strong>${type} Assistance</strong> application is currently <strong>UNDER REVIEW</strong>.`;
                    statusIcon.className = "fas fa-clock text-amber-600 text-2xl";
                    statusIconContainer.classList.add("bg-amber-50");
                }

                statusModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            const hideStatusModal = () => {
                statusModal.classList.add('hidden');
                document.body.style.overflow = '';
            };

            if (closeStatusModal) closeStatusModal.addEventListener('click', hideStatusModal);
            if (statusModalOverlay) statusModalOverlay.addEventListener('click', hideStatusModal);

            // Escape key to close
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !statusModal.classList.contains('hidden')) {
                    hideStatusModal();
                }
            });
        });
    </script>

    <?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
</body>
