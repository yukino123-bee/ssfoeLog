    <footer class="bg-optimum-dark text-white pt-20 pb-10 px-8">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <!-- Logo & About -->
            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                    <div class="w-11 h-11 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-500 text-xl shadow-sm">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <div class="leading-none text-left">
                        <span class="text-xl font-black tracking-tighter text-white block">SSFO</span>
                        <span class="text-[13px] font-bold text-gray-400 tracking-tight -mt-1 block">Assistance</span>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-300 leading-relaxed max-w-xs">
                    SSFO is dedicated to empowering local communities through direct financial support and accessible government resources.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-optimum-red transition-all duration-300">
                        <i class="fab fa-facebook-f text-xs"></i>
                        <span class="sr-only">Facebook</span>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-optimum-red transition-all duration-300">
                        <i class="fab fa-twitter text-xs"></i>
                        <span class="sr-only">Twitter</span>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-optimum-red transition-all duration-300">
                        <i class="fab fa-instagram text-xs"></i>
                        <span class="sr-only">Instagram</span>
                    </a>
                </div>
            </div>

            <!-- Links -->
            <div class="md:pl-10">
                <h3 class="text-lg font-black tracking-tight mb-8 text-white">Links</h3>
                <ul class="space-y-4 text-sm font-bold text-slate-400">
                    <li><a href="<?php echo base_url('client/dashboard'); ?>" class="hover:text-optimum-red transition-colors">Programs</a></li>
                    <li><a href="#services" class="hover:text-optimum-red transition-colors">Process</a></li>

                    <li><a href="#contact" class="hover:text-optimum-red transition-colors">Contact us</a></li>
                </ul>
            </div>

            <!-- Get in Touch -->
            <div>
                <h3 class="text-lg font-black tracking-tight mb-8 text-white">Get in Touch</h3>
                <ul class="space-y-6 text-sm font-bold text-slate-300">
                    <li class="flex items-start space-x-4">
                        <span class="text-optimum-red mt-1 flex-shrink-0"><i class="fas fa-map-marker-alt"></i></span>
                        <span>Support Services Facilitators Office<br>San Miguel, Zamboanga del Sur, Philippines</span>
                    </li>
                    <li class="flex items-center space-x-4">
                        <span class="text-optimum-red flex-shrink-0"><i class="fas fa-envelope"></i></span>
                        <a href="mailto:support@ssfo.gov" class="hover:text-white transition-colors">support@ssfo.gov</a>
                    </li>
                    <li class="flex items-center space-x-4">
                        <span class="text-optimum-red flex-shrink-0"><i class="fas fa-phone"></i></span>
                        <span>+63 918 743 9096</span>
                    </li>
                </ul>
            </div>

            <!-- Location Meta -->
            <div>
                <h3 class="text-lg font-black tracking-tight mb-8 text-white">Resources</h3>
                <div class="rounded-lg overflow-hidden border border-white/10 group grayscale hover:grayscale-0 transition-all duration-700">
                    <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=400&h=250&q=80" alt="Support Hub" class="w-full h-auto object-cover group-hover:scale-110 transition-transform duration-700">
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="max-w-7xl mx-auto pt-10 border-t border-white/5 flex flex-col md:flex-row justify-between items-center text-[10px] font-bold uppercase tracking-widest text-slate-500 space-y-4 md:space-y-0 text-center">
            <div>&copy; <?php echo date('Y'); ?> SSFO eLog. All rights reserved.</div>
        </div>
    </footer>
    <script src="<?php echo asset_url('js/main.js'); ?>"></script>
    

</body>
</html>
