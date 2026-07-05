<?php require_once APP_PATH . '/views/layouts/admin_layout_open.php'; ?>

<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight font-outfit">Admin Profile</h1>
    <p class="mt-1 text-gray-500 text-sm">Manage your personal information, role details, and security settings.</p>
</div>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-start gap-3 animate-fade-in-up">
        <i class="fas fa-check-circle mt-0.5 text-emerald-500"></i>
        <div>
            <h4 class="text-sm font-bold">Success</h4>
            <p class="text-xs mt-0.5"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-700 flex items-start gap-3 animate-fade-in-up">
        <i class="fas fa-exclamation-circle mt-0.5 text-rose-500"></i>
        <div>
            <h4 class="text-sm font-bold">Error</h4>
            <p class="text-xs mt-0.5"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
        </div>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Profile Card (Left Column) -->
    <div class="lg:col-span-1 space-y-8">
        <div class="bg-white/80 backdrop-blur-md rounded-2xl p-8 border border-gray-100 shadow-sm text-center relative overflow-hidden group">
            <div class="absolute top-0 inset-x-0 h-24 bg-gradient-to-r from-rose-500 to-indigo-600 opacity-90"></div>
            
            <div class="relative z-10 mt-6 mb-4">
                <?php $avatar_chars = strtoupper(mb_substr($admin['fullname'] ?? 'A', 0, 1)); ?>
                <div class="w-24 h-24 mx-auto rounded-full bg-white p-1 shadow-lg shadow-gray-200/50">
                    <div class="w-full h-full rounded-full bg-gradient-to-br from-rose-100 to-indigo-100 flex items-center justify-center text-rose-600 font-bold text-3xl border-2 border-white">
                        <?php echo htmlspecialchars($avatar_chars); ?>
                    </div>
                </div>
            </div>
            
            <h2 class="text-xl font-bold text-gray-900 tracking-tight"><?php echo htmlspecialchars($admin['fullname'] ?? ''); ?></h2>
            <p class="text-sm font-semibold text-rose-600 uppercase tracking-widest mt-1 mb-6"><?php echo htmlspecialchars($adminInfo['position'] ?? 'Administrator'); ?></p>
            
            <div class="flex items-center justify-center gap-4 text-gray-500 text-sm border-t border-gray-100 pt-6">
                <div class="flex items-center gap-2">
                    <i class="fas fa-envelope text-gray-400"></i>
                    <span class="truncate max-w-[150px]"><?php echo htmlspecialchars($admin['email'] ?? ''); ?></span>
                </div>
            </div>
            <?php if (!empty($adminInfo['department'])): ?>
            <div class="flex items-center justify-center gap-2 text-gray-500 text-sm mt-3">
                <i class="fas fa-building text-gray-400"></i>
                <span class="truncate max-w-[150px]"><?php echo htmlspecialchars($adminInfo['department']); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <!-- Optional Stats or Info -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-6 shadow-sm border border-slate-700 relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/20 rounded-full blur-2xl"></div>
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-indigo-500/20 rounded-full blur-2xl"></div>
            <h3 class="text-slate-300 text-[10px] font-bold uppercase tracking-widest mb-4">System Access</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-slate-400 text-xs mb-1">Role Type</p>
                    <p class="text-white text-sm font-medium capitalize"><?php echo htmlspecialchars($admin['role'] ?? 'Admin'); ?></p>
                </div>
                <div>
                    <p class="text-slate-400 text-xs mb-1">Account Created</p>
                    <p class="text-white text-sm font-medium"><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form (Right Column) -->
    <div class="lg:col-span-2">
        <form action="<?php echo base_url('admin/profile/update'); ?>" method="POST" class="bg-white/80 backdrop-blur-md rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generate_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-1">Personal Details</h3>
                <p class="text-xs text-gray-500">Update your core personal information.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Full Name</label>
                    <input type="text" name="fullname" value="<?php echo htmlspecialchars($admin['fullname'] ?? ''); ?>" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email'] ?? ''); ?>" required class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($adminInfo['phone'] ?? $admin['phone'] ?? ''); ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                </div>
            </div>

            <div class="border-t border-gray-100 pt-8 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-1">Professional Details</h3>
                <p class="text-xs text-gray-500">Your role and department within the organization.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Department</label>
                    <input type="text" name="department" value="<?php echo htmlspecialchars($adminInfo['department'] ?? ''); ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Position</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($adminInfo['position'] ?? ''); ?>" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all">
                </div>

                <div class="space-y-2 md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">Bio / Description</label>
                    <textarea name="bio" rows="3" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-indigo-300 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 outline-none transition-all resize-none"><?php echo htmlspecialchars($adminInfo['bio'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-8 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-1">Security</h3>
                <p class="text-xs text-gray-500">Update your password. Leave blank if you don't want to change it.</p>
            </div>

            <div class="space-y-2 mb-8 md:w-1/2">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-widest">New Password</label>
                <div class="relative group">
                    <input type="password" name="password" placeholder="••••••••" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 text-sm text-gray-900 focus:border-rose-300 focus:bg-white focus:ring-4 focus:ring-rose-500/10 outline-none transition-all">
                    <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-rose-400 transition-colors"></i>
                </div>
            </div>

            <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                <button type="submit" class="bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-8 py-3 rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

</div>

<?php require_once APP_PATH . '/views/layouts/admin_layout_close.php'; ?>
