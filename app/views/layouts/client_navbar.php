<nav class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 flex items-center justify-between sticky top-0 z-[100] transition-all duration-300">
    <!-- Logo -->
    <div class="flex items-center space-x-2">
        <a href="<?php echo base_url('client'); ?>" class="flex items-center space-x-4 group">
            <div class="w-11 h-11 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 text-xl shadow-sm group-hover:scale-105 transition-transform">
                <i class="fas fa-hand-holding-heart"></i>
            </div>
            <div class="leading-none">
                <span class="text-xl font-black tracking-tighter text-gray-900 block">SSFO</span>
                <span class="text-[13px] font-bold text-gray-800 tracking-tight -mt-1 block">eLog</span>
            </div>
        </a>
    </div>

    <!-- Spacer -->
    <div class="flex-1"></div>

    <!-- Actions -->
    <div class="flex items-center space-x-12 text-optimum-dark">
        <div class="hidden lg:flex items-center space-x-6 mr-6 flex-wrap justify-end">
            <a href="<?php echo base_url('client'); ?>" class="text-[13px] font-black text-optimum-dark hover:text-optimum-red transition-all tracking-tight uppercase relative group">
                Home
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-optimum-red transition-all group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('client/track'); ?>" class="text-[13px] font-black text-optimum-dark hover:text-optimum-red transition-all tracking-tight uppercase relative group">
                Track Status
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-optimum-red transition-all group-hover:w-full"></span>
            </a>
            <a href="<?php echo base_url('client'); ?>#contact" class="text-[13px] font-black text-optimum-dark hover:text-optimum-red transition-all tracking-tight uppercase relative group">
                Contact Us
                <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-optimum-red transition-all group-hover:w-full"></span>
            </a>
        </div>
    </div>
</nav>

<!-- Bottom Navigation for Mobile -->
<div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-100 z-[100] flex justify-around items-center h-16 pb-safe shadow-[0_-1px_10px_rgba(0,0,0,0.05)]">
    <!-- Home -->
    <a href="<?php echo base_url('client'); ?>" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-optimum-red transition-colors">
        <i class="fas fa-home text-lg"></i>
        <span class="text-[10px] mt-0.5 font-bold">Home</span>
    </a>
    
    <!-- Track -->
    <a href="<?php echo base_url('client/track'); ?>" class="flex flex-col items-center justify-center w-full h-full relative">
        <div class="w-14 h-14 bg-optimum-red rounded-full flex items-center justify-center text-white shadow-lg border-4 border-white -mt-6 hover:scale-105 transition-transform">
            <i class="fas fa-search text-lg"></i>
        </div>
        <span class="text-[10px] mt-0.5 font-bold text-gray-400">Track</span>
    </a>
    
    <!-- Menu -->
    <button id="client-mobile-toggle" class="flex flex-col items-center justify-center w-full h-full text-gray-400 hover:text-optimum-red transition-colors">
        <i class="fas fa-bars text-lg"></i>
        <span class="text-[10px] mt-0.5 font-bold">Menu</span>
    </button>
</div>

<!-- Mobile Menu Overlay -->
<div id="mobile-menu-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] hidden transition-opacity duration-300 opacity-0"></div>

<!-- Mobile Menu Drawer -->
<div id="mobile-menu" class="fixed top-0 right-0 h-full w-72 bg-white border-l border-gray-100 z-[70] translate-x-full transition-transform duration-500 ease-in-out shadow-2xl">
    <div class="p-6 flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Menu</h2>
                <p class="text-xs text-slate-500 mt-0.5">SSFO eLog</p>
            </div>
            <button id="close-mobile-menu" class="text-slate-400 hover:text-slate-900 text-2xl transition-colors">&times;</button>
        </div>
        
        <!-- Links -->
        <nav class="flex flex-col space-y-2 flex-1 overflow-y-auto">
            <a href="<?php echo base_url('client'); ?>#services" class="mobile-nav-link flex items-center justify-between py-4 px-4 rounded-xl hover:bg-slate-50 transition-colors group">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-rose-50 group-hover:text-rose-500 transition-colors">
                        <i class="fas fa-layer-group text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">How It Works</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs group-hover:text-slate-600 transition-colors"></i>
            </a>
            
            <a href="<?php echo base_url('client'); ?>#programs" class="mobile-nav-link flex items-center justify-between py-4 px-4 rounded-xl hover:bg-slate-50 transition-colors group">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-rose-50 group-hover:text-rose-500 transition-colors">
                        <i class="fas fa-th-large text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Programs</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs group-hover:text-slate-600 transition-colors"></i>
            </a>
            
            <a href="<?php echo base_url('client/track'); ?>" class="mobile-nav-link flex items-center justify-between py-4 px-4 rounded-xl hover:bg-slate-50 transition-colors group">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-rose-50 group-hover:text-rose-500 transition-colors">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Track Status</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs group-hover:text-slate-600 transition-colors"></i>
            </a>

            <a href="<?php echo base_url('client'); ?>#contact" class="mobile-nav-link flex items-center justify-between py-4 px-4 rounded-xl hover:bg-slate-50 transition-colors group">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 group-hover:bg-rose-50 group-hover:text-rose-500 transition-colors">
                        <i class="fas fa-phone-alt text-sm"></i>
                    </div>
                    <span class="text-sm font-semibold text-slate-700">Contact Us</span>
                </div>
                <i class="fas fa-chevron-right text-slate-300 text-xs group-hover:text-slate-600 transition-colors"></i>
            </a>
        </nav>

        <!-- Footer in Drawer -->
        <div class="pt-6 border-t border-gray-100 mt-auto">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider text-center">Powered by SSFO</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('client-mobile-toggle');
    const close = document.getElementById('close-mobile-menu');
    const menu = document.getElementById('mobile-menu');
    const overlay = document.getElementById('mobile-menu-overlay');
    const links = document.querySelectorAll('.mobile-nav-link');

    function openMenu() {
        menu.classList.remove('translate-x-full');
        overlay.classList.remove('hidden');
        setTimeout(() => overlay.classList.add('opacity-100'), 10);
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        menu.classList.add('translate-x-full');
        overlay.classList.remove('opacity-100');
        setTimeout(() => {
            if (menu.classList.contains('translate-x-full')) {
                overlay.classList.add('hidden');
            }
        }, 500);
        document.body.style.overflow = '';
    }

    if (toggle) toggle.addEventListener('click', openMenu);
    if (close) close.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);
    
    links.forEach(link => {
        link.addEventListener('click', closeMenu);
    });
});
</script>
