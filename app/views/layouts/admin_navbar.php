<!-- Top Navbar (Enterprise Clean Style) -->
<header class="fixed top-0 left-0 lg:left-72 w-full lg:w-[calc(100%-18rem)] z-[60] h-16 bg-white/95 backdrop-blur-md border-b border-gray-200 flex items-center px-4 sm:px-6 lg:px-8 transition-all duration-300">
    <div class="flex items-center justify-between gap-4 w-full min-w-0">
        <!-- Mobile toggle + Search -->
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <button id="mobile-toggle" type="button" class="lg:hidden shrink-0 p-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-optimum-red transition-all" aria-label="Open menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Right Section: Actions & Profile -->
        <div class="flex items-center space-x-3 sm:space-x-6">
            <!-- Action Icons -->
            <div class="flex items-center space-x-2">
                <div class="relative">
                    <button id="notification-bell" class="w-9 h-9 flex items-center justify-center relative text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-full transition-all group">
                        <i class="fas fa-bell"></i>
                        <span id="notification-badge" class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border-2 border-white group-hover:scale-125 transition-transform hidden"></span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 overflow-hidden hidden animate-in fade-in slide-in-from-top-4 duration-200 z-[100]">
                        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-white">
                            <h3 class="text-xs font-bold text-gray-900">Notifications</h3>
                            <a href="<?php echo base_url('admin/notifications'); ?>" class="text-[11px] font-semibold text-rose-600 hover:text-rose-700">View All</a>
                        </div>
                        <div id="notification-items" class="max-h-[350px] overflow-y-auto divide-y divide-gray-50">
                            <!-- Items populated via JS -->
                            <div class="p-8 text-center">
                                <i class="fas fa-circle-notch fa-spin text-gray-300 text-xl mb-2"></i>
                                <p class="text-xs font-medium text-gray-400">Loading...</p>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50/50 border-t border-gray-50 text-center">
                            <button onclick="refreshNotifications()" class="text-[11px] font-semibold text-gray-500 hover:text-gray-700 transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bell = document.getElementById('notification-bell');
                const dropdown = document.getElementById('notification-dropdown');
                const badge = document.getElementById('notification-badge');
                const itemsContainer = document.getElementById('notification-items');

                function fetchNotifications() {
                    fetch('<?php echo base_url('admin/notifications/ajax'); ?>')
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Update badge
                                if (data.unreadCount > 0) {
                                    badge.classList.remove('hidden');
                                    badge.innerText = ''; // Or data.unreadCount
                                } else {
                                    badge.classList.add('hidden');
                                }

                                // Update items
                                if (data.notifications.length === 0) {
                                    itemsContainer.innerHTML = `
                                        <div class="p-10 text-center">
                                            <p class="text-xs font-bold text-gray-400 italic">No notifications yet</p>
                                        </div>
                                    `;
                                } else {
                                    itemsContainer.innerHTML = data.notifications.map(n => `
                                        <div class="p-4 hover:bg-gray-50 transition-colors cursor-pointer group ${!n.is_read ? 'bg-rose-50/30' : ''}" onclick="markAsRead(${n.id}, '${n.link || ''}')">
                                            <div class="flex items-start space-x-3">
                                                <div class="w-8 h-8 rounded-lg ${n.is_read ? 'bg-gray-100 text-gray-400' : 'bg-optimum-red/10 text-optimum-red'} flex items-center justify-center text-xs shrink-0">
                                                    <i class="fas ${n.message.toLowerCase().includes('new') ? 'fa-file-invoice' : 'fa-bell'}"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-gray-800 leading-tight mb-1 ${!n.is_read ? 'font-black' : ''}">${n.message}</p>
                                                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">${n.time}</p>
                                                </div>
                                                ${!n.is_read ? '<div class="w-1.5 h-1.5 rounded-full bg-optimum-red mt-1"></div>' : ''}
                                            </div>
                                        </div>
                                    `).join('');
                                }
                            }
                        });
                }

                // Initial fetch
                fetchNotifications();

                // Toggle dropdown
                bell.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                    if (!dropdown.classList.contains('hidden')) {
                        fetchNotifications();
                    }
                });

                // Close dropdown on click outside
                document.addEventListener('click', function() {
                    dropdown.classList.add('hidden');
                });

                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                window.markAsRead = function(id, link) {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('csrf_token', '<?php echo htmlspecialchars(generate_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>');
                    fetch('<?php echo base_url('admin/notifications/read'); ?>', {
                        method: 'POST',
                        body: formData
                    }).then(() => {
                        if (link && link !== 'null' && link !== '') {
                            window.location.href = link;
                        } else {
                            fetchNotifications();
                        }
                    });
                };

                window.refreshNotifications = fetchNotifications;
            });
            </script>

            <!-- Vertical Divider -->
            <div class="w-px h-8 bg-gray-100 hidden sm:block"></div>
            
            <!-- Today Date -->
            <div class="hidden xl:flex flex-col items-end pr-4 sm:pr-6 border-r border-gray-200">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1.5">Today</p>
                <p class="text-[13px] font-bold text-slate-800 tracking-tight leading-none"><?php echo date('M d, Y'); ?></p>
            </div>

            <!-- User Profile -->
            <a href="<?php echo base_url('admin/profile'); ?>" class="flex items-center space-x-3 group cursor-pointer pl-2 sm:pl-4 text-right transition-colors">
                <div class="flex flex-col items-end hidden sm:flex min-w-0">
                    <p class="text-[13px] font-bold text-slate-900 group-hover:text-[#e11d48] transition-colors leading-tight truncate max-w-[10rem] lg:max-w-[14rem]">
                        <?php echo htmlspecialchars($_SESSION['user_fullname'] ?? 'Admin User'); ?>
                    </p>
                    <p class="text-[11px] font-semibold text-slate-500 leading-none mt-1">Facilitator</p>
                </div>
                <div class="relative shrink-0">
                    <?php
                        $avatar_chars = strtoupper(mb_substr($_SESSION['user_fullname'] ?? 'A', 0, 1));
                    ?>
                    <div class="flex w-9 h-9 rounded-full bg-slate-100 text-slate-600 group-hover:bg-rose-100 group-hover:text-rose-600 font-bold text-sm items-center justify-center border border-gray-200 transition-all duration-200">
                        <?php echo htmlspecialchars($avatar_chars); ?>
                    </div>
                    <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></div>
                </div>
            </a>

        </div>
    </div>
</header>
