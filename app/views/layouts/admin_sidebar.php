<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-screen w-72 bg-rose-950 text-rose-200/70 flex flex-col border-r border-rose-900/50 z-50 transition-transform lg:translate-x-0 -translate-x-full duration-300 shadow-[4px_0_24px_-10px_rgba(0,0,0,0.5)]" id="admin-sidebar">
    <!-- Brand Logo -->
    <a href="<?php echo base_url('admin/dashboard'); ?>" class="flex items-center space-x-3 px-6 py-5 border-b border-rose-900/50 group shrink-0">
        <div class="w-9 h-9 bg-gradient-to-tr from-rose-500 to-red-600 rounded-lg flex items-center justify-center text-white text-sm shadow-md shadow-rose-900/20 border border-rose-400/20 group-hover:scale-105 transition-all duration-300">
            <i class="fas fa-hand-holding-heart"></i>
        </div>
        <div class="flex flex-col">
            <span class="text-[17px] font-bold text-white leading-none tracking-tight font-sans">SSFO</span>
            <span class="text-[10px] font-bold text-rose-300 uppercase tracking-widest mt-1 opacity-80">Console</span>
        </div>
    </a>
    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto p-3 sm:p-4 pr-2 custom-scrollbar">
        <?php
            // Fetch unread requests for sidebar badge
            require_once APP_PATH . '/models/Request.php';

            $sidebarRequestModel = new Request();
            $pendingRequestCount = $sidebarRequestModel->getPendingCount();

            $uri = $_SERVER['REQUEST_URI'];
            function nav_class(string $segment, string $uri): string {
                $active = strpos($uri, $segment) !== false;
                return $active
                    ? 'flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-white/10 text-white font-semibold transition-all group'
                    : 'flex items-center space-x-3 px-4 py-2.5 rounded-lg text-rose-200/80 font-medium hover:bg-white/5 hover:text-white transition-all duration-200 group';
            }
        ?>

        <!-- MAIN MENU -->
        <div class="mb-6">
            <h3 class="px-4 text-[10px] font-bold uppercase tracking-wider text-rose-300/50 mb-3">Main Menu</h3>
            <div class="space-y-1.5">
                <a href="<?php echo base_url('admin/dashboard'); ?>" class="<?php echo nav_class('dashboard', $uri); ?>">
                    <i class="fas fa-home text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="<?php echo base_url('admin/requests'); ?>" class="<?php
                    $isAllRequests = strpos($uri, '/admin/requests') !== false && strpos($uri, 'type=') === false && strpos($uri, '/view') === false;
                    echo $isAllRequests
                        ? 'flex items-center space-x-3 px-4 py-2.5 rounded-lg bg-white/10 text-white font-semibold transition-all group'
                        : 'flex items-center space-x-3 px-4 py-2.5 rounded-lg text-rose-200/80 font-medium hover:bg-white/5 hover:text-white transition-all duration-200 group';
                ?>">
                    <i class="fas fa-list-alt text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm flex-1">All Requests</span>
                    <?php if ($pendingRequestCount > 0): ?>
                        <span class="bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full min-w-[18px] text-center shadow-sm">
                            <?php echo $pendingRequestCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo base_url('admin/reports'); ?>" class="<?php echo nav_class('reports', $uri); ?>">
                    <i class="fas fa-chart-bar text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Reports</span>
                </a>
                <a href="<?php echo base_url('admin/programs'); ?>" class="<?php echo nav_class('programs', $uri); ?>">
                    <i class="fas fa-line-chart text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Programs</span>
                </a>
                <a href="<?php echo base_url('admin/announcements'); ?>" class="<?php echo nav_class('announcements', $uri); ?>">
                    <i class="fas fa-bullhorn text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Announcements</span>
                </a>
                <a href="<?php echo base_url('admin/inquiries'); ?>" class="<?php echo nav_class('inquiries', $uri); ?>">
                    <i class="fas fa-inbox text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Inbox</span>
                </a>
            </div>
        </div>

        <!-- SYSTEM -->
        <div class="mb-6">
            <h3 class="px-4 text-[10px] font-bold uppercase tracking-wider text-rose-300/50 mb-3">System</h3>
            <div class="space-y-1.5">
                <a href="<?php echo base_url('admin/system-status'); ?>" class="<?php echo nav_class('system-status', $uri); ?>">
                    <i class="fas fa-server text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">System Status</span>
                </a>
                <a href="<?php echo base_url('logout'); ?>" class="flex items-center space-x-3 px-4 py-2.5 rounded-lg text-rose-200/70 font-medium hover:bg-rose-500/20 hover:text-rose-300 transition-all duration-200 group mt-4 border border-rose-900/50">
                    <i class="fas fa-sign-out-alt text-base opacity-80 group-hover:opacity-100 transition-opacity"></i>
                    <span class="text-sm">Log Out</span>
                </a>
            </div>
        </div>
    </div>
</aside>


<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden backdrop-blur-sm"></div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('mobile-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (toggle && sidebar && overlay) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    });
</script>
