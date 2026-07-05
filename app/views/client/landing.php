<?php

$title = "SSFO eLog - Better Support for Every Community";
$body_class = "font-sans selection:bg-brand-red-500/10 min-h-screen flex flex-col relative";
require_once APP_PATH . '/views/layouts/header.php';

?>
    <?php require_once APP_PATH . '/views/layouts/client_navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative bg-optimum-gray overflow-hidden min-h-[80vh] flex items-center">
            <!-- Red Swoosh Shape (SVG) -->
            <div class="absolute top-0 right-0 h-full w-1/2 z-0 hidden lg:block">
                <svg viewBox="0 0 500 800" class="h-full w-full object-cover text-optimum-red fill-current">
                    <path d="M500 0 C 200 150, 0 400, 100 800 L 500 800 Z" />
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-8 w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10 py-20">
                <!-- Left Column: Text Content -->
                <div class="space-y-10">
                    <div class="w-16 h-1 bg-optimum-red"></div>
                    <div class="space-y-2">
                        <h1 class="text-6xl md:text-8xl font-black text-optimum-dark leading-[0.85] tracking-tighter uppercase">
                            <span class="block text-transparent mb-2" style="-webkit-text-stroke: 1px #111111;">Better</span>
                            <span class="block">Support</span>
                            <span class="block text-optimum-red">Every Community</span>
                        </h1>
                    </div>
                    <p class="text-lg font-bold opacity-60 max-w-md leading-relaxed">
                        Access financial assistance and government programs with transparency and ease. We're here to help you move forward.
                    </p>
                    <div class="pt-4">
                        <a href="#programs" class="inline-flex items-center px-10 py-5 bg-optimum-red text-white font-black uppercase tracking-[0.2em] relative group transition-transform hover:-translate-y-1">
                            EXPLORE PROGRAMS
                            <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover:w-3 transition-all"></div>
                        </a>
                    </div>
                    
                    <!-- Vertical Scroll Indicator (Simplified) -->
                    <div class="hidden md:flex flex-col items-center space-y-4 absolute -left-12 top-1/2 -translate-y-1/2">
                        <div class="w-2 h-2 rounded-full border border-optimum-dark opacity-20"></div>
                        <div class="w-px h-12 bg-optimum-dark opacity-10"></div>
                        <div class="w-2 h-2 rounded-full bg-optimum-red"></div>
                        <div class="w-px h-12 bg-optimum-dark opacity-10"></div>
                        <div class="w-2 h-2 rounded-full border border-optimum-dark opacity-20"></div>
                    </div>
                </div>

                <!-- Right Column: Hero Image Collage -->
                <div class="relative lg:h-[600px] flex items-center justify-center">
                    <div id="hero-collage" class="relative z-10 w-full max-w-lg h-[400px] md:h-[500px]">
                        <!-- Educational Image -->
                        <img src="<?php echo base_url('assets/images/educational.png'); ?>" alt="Educational Support" 
                             class="hero-collage-item absolute w-4/5 h-auto shadow-2xl rounded-lg transition-all duration-1000 border-4 border-white collage-pos-1">
                        
                        <!-- Education Image -->
                        <img src="<?php echo base_url('assets/images/education.png'); ?>" alt="Education" 
                             class="hero-collage-item absolute w-4/5 h-auto shadow-2xl rounded-lg transition-all duration-1000 border-4 border-white collage-pos-2">
                        
                        <!-- Medical Image -->
                        <img src="<?php echo base_url('assets/images/medical.png'); ?>" alt="Medical Assistance" 
                             class="hero-collage-item absolute w-4/5 h-auto shadow-2xl rounded-lg transition-all duration-1000 border-4 border-white collage-pos-3">
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-32 px-8 bg-white">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-24">
                    <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Our Process</p>
                    <h2 class="text-4xl md:text-5xl font-black text-optimum-dark tracking-tight uppercase">How It Works</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Step 1 -->
                    <div class="group">
                        <div class="relative mb-12">
                            <div class="overflow-hidden rounded-sm">
                                <img src="<?php echo base_url('assets/images/browse_sv.png'); ?>" alt="Browse Programs" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            <div class="absolute left-1/2 -bottom-8 -translate-x-1/2 w-16 h-16 bg-optimum-red rounded-full flex items-center justify-center border-4 border-white shadow-lg text-white text-2xl z-20 font-black">
                                01
                            </div>
                        </div>
                        <div class="bg-white p-8 pt-4 text-center border-b-2 border-transparent group-hover:border-optimum-red transition-all duration-300 shadow-2xl hover:shadow-2xl">
                            <h3 class="text-xl font-black text-optimum-dark uppercase tracking-tight mb-4">Browse Programs</h3>
                            <p class="text-sm font-bold opacity-60 leading-relaxed mb-6">
                                Explore a wide range of financial assistance and community support programs tailored to your needs.
                            </p>
                            <ul class="text-left space-y-2 text-xs font-bold opacity-50">
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Filter by Category</span></li>
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Check Eligibility</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="group">
                        <div class="relative mb-12">
                            <div class="overflow-hidden rounded-sm">
                                <img src="<?php echo base_url('assets/images/application_sv.png'); ?>" alt="Simple Application" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            <div class="absolute left-1/2 -bottom-8 -translate-x-1/2 w-16 h-16 bg-optimum-red rounded-full flex items-center justify-center border-4 border-white shadow-lg text-white text-2xl z-20 font-black">
                                02
                            </div>
                        </div>
                        <div class="bg-white p-8 pt-4 text-center border-b-2 border-transparent group-hover:border-optimum-red transition-all duration-300 shadow-2xl hover:shadow-2xl">
                            <h3 class="text-xl font-black text-optimum-dark uppercase tracking-tight mb-4">Simple Application</h3>
                            <p class="text-sm font-bold opacity-60 leading-relaxed mb-6">
                                Submit your application within minutes through our streamlined and secure digital platform.
                            </p>
                            <ul class="text-left space-y-2 text-xs font-bold opacity-50">
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Secure Document Upload</span></li>
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Instant Confirmation</span></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="group">
                        <div class="relative mb-12">
                            <div class="overflow-hidden rounded-sm">
                                <img src="<?php echo base_url('assets/images/track_sv.png'); ?>" alt="Track Status" class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-700">
                            </div>
                            <div class="absolute left-1/2 -bottom-8 -translate-x-1/2 w-16 h-16 bg-optimum-red rounded-full flex items-center justify-center border-4 border-white shadow-lg text-white text-2xl z-20 font-black">
                                03
                            </div>
                        </div>
                        <div class="bg-white p-8 pt-4 text-center border-b-2 border-transparent group-hover:border-optimum-red transition-all duration-300 shadow-2xl hover:shadow-2xl">
                            <h3 class="text-xl font-black text-optimum-dark uppercase tracking-tight mb-4">Track Status</h3>
                            <p class="text-sm font-bold opacity-60 leading-relaxed mb-6">
                                Monitor your application progress in real-time and receive notifications on every update.
                            </p>
                            <ul class="text-left space-y-2 text-xs font-bold opacity-50">
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Reference Tracking</span></li>
                                <li class="flex items-center space-x-2"><span class="text-optimum-red">›</span><span>Status Notifications</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

        </section>

        <!-- Programs Carousel Section -->
        <section id="programs" class="py-32 bg-black relative overflow-hidden">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-optimum-red/20 via-transparent to-transparent"></div>
            </div>
            
            <div class="max-w-7xl mx-auto px-8 relative z-10">
                <div class="text-center mb-20">
                    <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Available Programs</p>
                    <h2 class="text-4xl md:text-6xl font-black text-white tracking-tight uppercase">Choose Your <span class="text-transparent" style="-webkit-text-stroke: 1px #fff;">Support</span></h2>
                </div>

                <div class="relative">
                    <!-- Navigation Buttons -->
                    <div class="absolute inset-y-0 -left-4 md:-left-12 flex items-center z-30">
                        <button id="prevBtn" class="w-12 h-12 md:w-16 md:h-16 bg-black/40 backdrop-blur-xl rounded-full border border-white/10 flex items-center justify-center text-white hover:bg-optimum-red hover:border-optimum-red transition-all duration-300 group shadow-2xl">
                            <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                        </button>
                    </div>
                    <div class="absolute inset-y-0 -right-4 md:-right-12 flex items-center z-30">
                        <button id="nextBtn" class="w-12 h-12 md:w-16 md:h-16 bg-black/40 backdrop-blur-xl rounded-full border border-white/10 flex items-center justify-center text-white hover:bg-optimum-red hover:border-optimum-red transition-all duration-300 group shadow-2xl">
                            <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </div>

                    <div class="overflow-hidden py-10">
                        <div id="carousel-track" class="flex transition-transform duration-1000 ease-[cubic-bezier(0.23,1,0.32,1)] items-center">
                            
                            <!-- Educational -->
                            <div class="carousel-item flex-shrink-0 w-[300px] md:w-[450px] px-4">
                                <div class="bg-white p-10 md:p-16 border-l-8 border-optimum-red shadow-2xl h-full flex flex-col justify-between group cursor-pointer transition-all duration-500">
                                    <div>
                                        <div class="text-4xl md:text-6xl mb-8">🎓</div>
                                        <h3 class="text-2xl md:text-3xl font-black text-optimum-dark uppercase leading-none mb-6">Educational Assistance</h3>
                                        <p class="text-sm font-bold opacity-60 leading-relaxed mb-8">Empowering future leaders by providing financial grants for tuition, supplies, and academic success.</p>
                                    </div>
                                    <a href="<?php echo base_url('client/educational'); ?>" class="apply-btn inline-flex items-center w-full py-5 bg-optimum-red text-white font-black uppercase tracking-[0.2em] relative group/apply transition-all justify-center hover:scale-[1.02] active:scale-95 text-xs shadow-xl">
                                        Apply Now
                                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover/apply:w-3 transition-all"></div>
                                    </a>
                                </div>
                            </div>

                            <!-- Medical -->
                            <div class="carousel-item flex-shrink-0 w-[300px] md:w-[450px] px-4">
                                <div class="bg-white p-10 md:p-16 border-l-8 border-blue-600 shadow-2xl h-full flex flex-col justify-between group cursor-pointer transition-all duration-500">
                                    <div>
                                        <div class="text-4xl md:text-6xl mb-8">🏥</div>
                                        <h3 class="text-2xl md:text-3xl font-black text-optimum-dark uppercase leading-none mb-6">Medical Assistance</h3>
                                        <p class="text-sm font-bold opacity-60 leading-relaxed mb-8">Providing life-saving support for hospital bills, medicine, and critical healthcare services.</p>
                                    </div>
                                    <a href="<?php echo base_url('client/medical'); ?>" class="apply-btn inline-flex items-center w-full py-5 bg-blue-600 text-white font-black uppercase tracking-[0.2em] relative group/apply transition-all justify-center hover:scale-[1.02] active:scale-95 text-xs shadow-xl">
                                        Apply Now
                                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover/apply:w-3 transition-all"></div>
                                    </a>
                                </div>
                            </div>

                            <!-- Burial -->
                            <div class="carousel-item flex-shrink-0 w-[300px] md:w-[450px] px-4">
                                <div class="bg-white p-10 md:p-16 border-l-8 border-purple-600 shadow-2xl h-full flex flex-col justify-between group cursor-pointer transition-all duration-500">
                                    <div>
                                        <div class="text-4xl md:text-6xl mb-8">🕊️</div>
                                        <h3 class="text-2xl md:text-3xl font-black text-optimum-dark uppercase leading-none mb-6">Burial Assistance</h3>
                                        <p class="text-sm font-bold opacity-60 leading-relaxed mb-8">Extending heartfelt support to help families navigate burial costs and funeral arrangements.</p>
                                    </div>
                                    <a href="<?php echo base_url('client/burial'); ?>" class="apply-btn inline-flex items-center w-full py-5 bg-purple-600 text-white font-black uppercase tracking-[0.2em] relative group/apply transition-all justify-center hover:scale-[1.02] active:scale-95 text-xs shadow-xl">
                                        Apply Now
                                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover/apply:w-3 transition-all"></div>
                                    </a>
                                </div>
                            </div>

                            <!-- Employment -->
                            <div class="carousel-item flex-shrink-0 w-[300px] md:w-[450px] px-4">
                                <div class="bg-white p-10 md:p-16 border-l-8 border-green-600 shadow-2xl h-full flex flex-col justify-between group cursor-pointer transition-all duration-500">
                                    <div>
                                        <div class="text-4xl md:text-6xl mb-8">💼</div>
                                        <h3 class="text-2xl md:text-3xl font-black text-optimum-dark uppercase leading-none mb-6">Employment Support</h3>
                                        <p class="text-sm font-bold opacity-60 leading-relaxed mb-8">Connecting you with job opportunities, training, and resources to build a brighter career path.</p>
                                    </div>
                                    <a href="<?php echo base_url('client/employment'); ?>" class="apply-btn inline-flex items-center w-full py-5 bg-green-600 text-white font-black uppercase tracking-[0.2em] relative group/apply transition-all justify-center hover:scale-[1.02] active:scale-95 text-xs shadow-xl">
                                        Apply Now
                                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover/apply:w-3 transition-all"></div>
                                    </a>
                                </div>
                            </div>

                            <!-- Transit -->
                            <div class="carousel-item flex-shrink-0 w-[300px] md:w-[450px] px-4">
                                <div class="bg-white p-10 md:p-16 border-l-8 border-orange-600 shadow-2xl h-full flex flex-col justify-between group cursor-pointer transition-all duration-500">
                                    <div>
                                        <div class="text-4xl md:text-6xl mb-8">🚌</div>
                                        <h3 class="text-2xl md:text-3xl font-black text-optimum-dark uppercase leading-none mb-6">Transit Assistance</h3>
                                        <p class="text-sm font-bold opacity-60 leading-relaxed mb-8">Fueling progress by providing transportation grants and commuting assistance for those in need.</p>
                                    </div>
                                    <a href="<?php echo base_url('client/transportation'); ?>" class="apply-btn inline-flex items-center w-full py-5 bg-orange-600 text-white font-black uppercase tracking-[0.2em] relative group/apply transition-all justify-center hover:scale-[1.02] active:scale-95 text-xs shadow-xl">
                                        Apply Now
                                        <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-dark group-hover/apply:w-3 transition-all"></div>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Pagination Dots -->
                <div id="carousel-pagination" class="flex justify-center mt-12 space-x-4"></div>
            </div>
        </section>



          <!-- Why Us Section -->
        <section class="py-32 px-8 bg-optimum-gray overflow-hidden">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="space-y-8">
                    <div>
                        <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">About the Fund</p>
                        <h2 class="text-4xl md:text-5xl font-black text-optimum-dark tracking-tight uppercase">Why SSFO <span class="text-xs align-top opacity-40">GOV</span></h2>
                    </div>
                    <p class="text-lg font-bold opacity-60 leading-relaxed">
                        SSFO is dedicated to empowering local communities through direct financial support and accessible government resources.
                    </p>
                    <p class="text-base font-medium opacity-50 leading-relaxed">
                        Our platform ensures that aid reaches those who need it most, with full accountability and real-time tracking at every stage of the application process.
                    </p>
                    <div class="pt-6">
                        <a href="<?php echo base_url('client/track'); ?>" class="inline-flex items-center px-10 py-5 bg-optimum-dark text-white font-black uppercase tracking-[0.2em] relative group transition-transform hover:-translate-y-1">
                            TRACK YOUR STATUS
                            <div class="absolute right-0 top-0 bottom-0 w-1.5 bg-optimum-red group-hover:w-3 transition-all"></div>
                        </a>
                    </div>
                </div>

                <div class="relative">
                    <div class="absolute -top-10 -right-10 w-full h-full border-4 border-optimum-red z-0 translate-x-4 translate-y-4"></div>
                    <div class="relative z-10 overflow-hidden shadow-2xl">
                        <img src="<?php echo base_url('assets/images/meeting.png'); ?>" alt="Community Support Meeting" class="w-full h-auto object-cover">
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section (Restored from design) -->
        <section class="py-20 px-8 bg-white">
            <div class="max-w-7xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div>
                    <div class="text-5xl font-black text-optimum-dark mb-2">20K+</div>
                    <div class="text-[10px] font-black uppercase tracking-widest opacity-40">Members Supported</div>
                </div>
                <div>
                    <div class="text-5xl font-black text-optimum-red mb-2">₱1.2M</div>
                    <div class="text-[10px] font-black uppercase tracking-widest opacity-40">Funds Distributed</div>
                </div>
                <div>
                    <div class="text-5xl font-black text-optimum-dark mb-2">150+</div>
                    <div class="text-[10px] font-black uppercase tracking-widest opacity-40">Local Programs</div>
                </div>
                <div>
                    <div class="text-5xl font-black text-optimum-dark mb-2">98%</div>
                    <div class="text-[10px] font-black uppercase tracking-widest opacity-40">Approval Rate</div>
                </div>
            </div>
      

        <!-- Contact Section -->
        <section id="contact" class="py-20 md:py-32 px-4 md:px-8 bg-optimum-gray">
            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 md:gap-20">
                <div class="space-y-10">
                    <div>
                        <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Contact Us</p>
                        <h2 class="text-4xl md:text-5xl font-black text-optimum-dark tracking-tight uppercase">Get in Touch</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="flex items-start space-x-6">
                            <div class="w-12 h-12 bg-white flex flex-shrink-0 items-center justify-center shadow-lg text-optimum-red transition-transform hover:scale-110"><i class="fas fa-envelope"></i></div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-1">Email Address</p>
                                <p class="text-base md:text-lg font-black text-optimum-dark break-all">support@ssfo.gov</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-6">
                            <div class="w-12 h-12 bg-white flex flex-shrink-0 items-center justify-center shadow-lg text-optimum-red transition-transform hover:scale-110"><i class="fas fa-phone"></i></div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest opacity-40 mb-1">Phone Number</p>
                                <p class="text-base md:text-lg font-black text-optimum-dark">+63 918 743 9096</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 md:p-12 shadow-2xl relative w-[95%] md:w-full mx-auto">
                    <div class="absolute -top-4 -right-4 w-12 h-12 bg-optimum-red flex items-center justify-center text-white text-xl shadow-lg">
                        <i class="fas fa-envelope"></i>
                    </div>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-800 px-4 py-3 rounded-lg flex items-center space-x-3 animate-fade-in">
                            <i class="fas fa-check-circle"></i>
                            <span class="text-sm font-bold"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="mb-6 bg-rose-50 border border-rose-100 text-rose-800 px-4 py-3 rounded-lg flex items-center space-x-3 animate-fade-in">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="text-sm font-bold"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo base_url('client/contact/submit'); ?>" method="POST" class="space-y-6">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <input type="text" name="name" required placeholder="FULL NAME (FIRST NAME/MIDDLE NAME/LAST NAME)" class="w-full bg-optimum-gray px-6 py-4 text-[10px] font-black uppercase tracking-widest focus:outline-none border-l-2 border-transparent focus:border-optimum-red transition-all">
                            <input type="email" name="email" required placeholder="EMAIL ADDRESS" class="w-full bg-optimum-gray px-6 py-4 text-[10px] font-black uppercase tracking-widest focus:outline-none border-l-2 border-transparent focus:border-optimum-red transition-all">
                        </div>
                        <input type="text" name="subject" placeholder="SUBJECT" class="w-full bg-optimum-gray px-6 py-4 text-[10px] font-black uppercase tracking-widest focus:outline-none border-l-2 border-transparent focus:border-optimum-red transition-all">
                        <textarea name="message" required rows="4" placeholder="MESSAGE" class="w-full bg-optimum-gray px-6 py-4 text-[10px] font-black uppercase tracking-widest focus:outline-none border-l-2 border-transparent focus:border-optimum-red transition-all"></textarea>
                        <button type="submit" class="w-full py-5 bg-optimum-dark text-white font-black uppercase tracking-[0.2em] hover:bg-optimum-red transition-colors shadow-lg active:scale-95">SEND MESSAGE</button>
                    </form>

                    <!-- QR Code Post Section -->
                    <div class="mt-12 pt-12 border-t border-slate-100 text-center">
                        <p class="text-[11px] font-black text-optimum-red uppercase tracking-super-wide mb-4">Post & Share</p>
                        <h3 class="text-xl font-black text-optimum-dark tracking-tight uppercase mb-6">Apply via QR Code</h3>
                        <div class="inline-block p-4 bg-white shadow-xl border border-slate-100 rounded-2xl mb-4 group hover:scale-105 transition-transform duration-500">
                            <?php 
                                $current_url = 'https://unshredded-lauretta-nonoxidizing.ngrok-free.dev/';
                                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($current_url);
                            ?>
                            <img src="<?php echo $qr_url; ?>" alt="Application QR Code" class="w-48 h-48 mx-auto">
                        </div>
                        <p class="text-xs font-bold opacity-40 uppercase tracking-widest">Scan to apply on your mobile device</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <style>
        .scroll-reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s cubic-bezier(0.2, 1, 0.3, 1);
        }
        .scroll-reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Carousel specific styles */
        .carousel-item { transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1); }
        .carousel-item > div { transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1); opacity: 0.4; transform: scale(0.85); pointer-events: none; filter: blur(2px); }
        
        .carousel-item.active-card > div { 
            opacity: 1; 
            transform: scale(1.05); 
            pointer-events: auto;
            filter: blur(0);
            box-shadow: 0 45px 80px -25px rgba(0, 0, 0, 0.3);
            border-left-width: 12px;
        }

        #carousel-track::-webkit-scrollbar { display: none; }

        /* Hero Collage Dynamic Styles */
        .collage-pos-1 { z-index: 30; transform: translate(0, 0) rotate(-3deg) scale(1); opacity: 1; }
        .collage-pos-2 { z-index: 20; transform: translate(30px, 40px) rotate(4deg) scale(0.95); opacity: 0.9; }
        .collage-pos-3 { z-index: 10; transform: translate(60px, 80px) rotate(10deg) scale(0.9); opacity: 0.8; }
        
        .hero-collage-item:hover {
            z-index: 50 !important;
            transform: scale(1.1) rotate(0) translate(0, 0) !important;
            opacity: 1 !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Carousel Logic
            const track = document.getElementById('carousel-track');
            const items = document.querySelectorAll('.carousel-item');
            if (track && items.length) {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');
                const pagination = document.getElementById('carousel-pagination');
                
                let currentIndex = 0; 
                items.forEach((_, i) => {
                    const dot = document.createElement('div');
                    dot.className = `w-3 h-3 rounded-full transition-all duration-500 cursor-pointer ${i === currentIndex ? 'bg-optimum-red w-10' : 'bg-white/20'}`;
                    dot.addEventListener('click', () => goTo(i));
                    pagination.appendChild(dot);
                });

                function updateCarousel() {
                    const viewportWidth = track.parentElement.offsetWidth;
                    const cardWidth = items[0].getBoundingClientRect().width;
                    const offset = (viewportWidth / 2) - (cardWidth / 2) - (currentIndex * cardWidth);
                    track.style.transform = `translateX(${offset}px)`;
                    items.forEach((item, index) => { item.classList.toggle('active-card', index === currentIndex); });
                    document.querySelectorAll('#carousel-pagination div').forEach((dot, i) => {
                        if (i === currentIndex) { dot.classList.add('bg-optimum-red', 'w-10'); dot.classList.remove('bg-white/20'); }
                        else { dot.classList.remove('bg-optimum-red', 'w-10'); dot.classList.add('bg-white/20'); }
                    });
                }

                function goTo(index) { currentIndex = index; updateCarousel(); }
                nextBtn.addEventListener('click', () => { currentIndex = (currentIndex + 1) % items.length; updateCarousel(); });
                prevBtn.addEventListener('click', () => { currentIndex = (currentIndex - 1 + items.length) % items.length; updateCarousel(); });
                window.addEventListener('resize', updateCarousel);
                setTimeout(updateCarousel, 100);
            }

            // Scroll Reveal implementation
            const reveals = document.querySelectorAll('.scroll-reveal');
            const observerOptions = { threshold: 0.15 };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) entry.target.classList.add('visible'); });
            }, observerOptions);
            reveals.forEach(reveal => observer.observe(reveal));

            // Simple Tracking Form Interaction
            const trackForm = document.getElementById('track-form');
            const trackResults = document.getElementById('track-results');
            if (trackForm) {
                trackForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    trackResults.classList.remove('hidden');
                    trackResults.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }

            // Hero Collage Rotation
            const collageItems = document.querySelectorAll('.hero-collage-item');
            if (collageItems.length) {
                let positions = ['collage-pos-1', 'collage-pos-2', 'collage-pos-3'];
                
                setInterval(() => {
                    // Shift positions array
                    positions.push(positions.shift());
                    
                    // Apply new classes
                    collageItems.forEach((item, index) => {
                        item.className = 'hero-collage-item absolute w-4/5 h-auto shadow-2xl rounded-lg transition-all duration-1000 border-4 border-white ' + positions[index];
                    });
                }, 5000);
            }
        });
    </script>
    <?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
</body>
</html>
