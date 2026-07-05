<?php 
$body_class = "bg-optimum-gray font-sans relative text-slate-950";
require_once APP_PATH . '/views/layouts/header.php'; 

// Calculate PHP post_max_size in bytes for client-side validation
$max_size_str = ini_get('post_max_size');
$val = trim($max_size_str);
$last = strtolower($val[strlen($val)-1]);
$val = (int)$val;
switch($last) {
    case 'g': $val *= 1024;
    case 'm': $val *= 1024;
    case 'k': $val *= 1024;
}
$post_max_size_bytes = $val;
?>
    <?php require_once APP_PATH . '/views/layouts/client_navbar.php'; ?>
    
    <!-- Fixed Back Button -->
    <a href="<?php echo base_url('client'); ?>" 
       class="fixed top-24 left-6 z-50 w-12 h-12 md:w-14 md:h-14 bg-white/80 backdrop-blur-md shadow-2xl border border-white flex items-center justify-center text-slate-800 hover:text-optimum-red rounded-full transition-all duration-300 hover:scale-110 active:scale-95 group">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
    </a>

    <main class="min-h-screen py-20 relative z-10">
        <div class="container mx-auto px-6 max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="inline-block px-5 py-2 mb-6 bg-optimum-red/5 text-optimum-red text-sm font-bold rounded-full border border-optimum-red/10">
                    New Application
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight tracking-tight mb-4">
                    <?php echo $title ?? 'Submit Application'; ?>
                </h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    Please provide accurate information and upload the required documents to process your request.
                </p>
            </div>

            <?php if (!empty($_SESSION['error_message'])): ?>
                <div class="mb-12 p-6 rounded-2xl bg-red-50 border border-red-100 flex items-start space-x-4 animate-fade-in shadow-sm">
                    <div class="w-10 h-10 bg-red-100 text-red-600 flex items-center justify-center rounded-full flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-red-900 mb-1">Submission Error</h4>
                        <p class="text-red-700 text-sm leading-relaxed"><?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success_message'])): ?>
                <div class="mb-12 p-6 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-start space-x-4 animate-fade-in shadow-sm">
                    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 flex items-center justify-center rounded-full flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-emerald-900 mb-1">Success</h4>
                        <p class="text-emerald-700 text-sm leading-relaxed"><?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form id="request-form" method="post" enctype="multipart/form-data" action="<?php echo base_url('client/submit'); ?>" 
                  class="bg-white p-6 sm:p-10 md:p-14 rounded-2xl shadow-xl border border-slate-100">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="request_type" value="<?php echo $request_type ?? 'educational'; ?>">

                <!-- Personal Information -->
                <div class="mb-12">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">1</div>
                        <h2 class="text-2xl font-bold text-slate-900">Personal Details</h2>
                    </div>
                    
                    <div class="space-y-8">
                        <!-- Name Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">First Name <span class="text-xs font-normal text-slate-400 ml-1">(e.g. Juan)</span></label>
                                <input type="text" name="firstname" required placeholder="" 
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Middle Name <span class="text-xs font-normal text-slate-400 ml-1">(e.g. Santos)</span></label>
                                <input type="text" name="middlename" placeholder="" 
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Last Name <span class="text-xs font-normal text-slate-400 ml-1">(e.g. Dela Cruz)</span></label>
                                <input type="text" name="lastname" required placeholder="" 
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                            </div>
                        </div>

                        <!-- DOB, Age, Sex Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Birth (DOB)</label>
                                <input type="date" name="dob" id="dob" required 
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Age</label>
                                <input type="number" name="age" id="age" required readonly placeholder="Calculated from DOB" 
                                       class="w-full px-6 py-4 bg-slate-100 border border-slate-200 rounded-xl outline-none text-slate-500 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Sex</label>
                                <select name="sex" required 
                                        class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white appearance-none cursor-pointer">
                                    <option value="">Select</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contact Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-xs font-normal text-slate-400 ml-1">(e.g. name@email.com)</span></label>
                                <input type="email" name="email" required placeholder="" 
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Contact Number <span class="text-xs font-normal text-slate-400 ml-1">(e.g. 09171234567)</span></label>
                                <input type="tel" name="contact" id="contact" required placeholder="" pattern="09[0-9]{9}" maxlength="11" minlength="11"
                                       class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                                <p id="contact-error" class="text-xs text-optimum-red mt-1 hidden">Must be a valid Philippine number starting with 09 (11 digits).</p>
                            </div>
                        </div>

                        <!-- Address Row -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Current Address <span class="text-xs font-normal text-slate-400 ml-1">(e.g. 123 Rizal St., Brgy. San Jose, Manila)</span></label>
                            <input type="text" name="address" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Academic / Program Information -->
                <?php if (($request_type ?? 'educational') == 'educational'): ?>
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">2</div>
                        <h2 class="text-2xl font-bold text-slate-900">Academic Details</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Name of School <span class="text-xs font-normal text-slate-400 ml-1">(e.g. National University)</span></label>
                            <input type="text" name="school" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">School Type</label>
                            <select name="schoolType" id="schoolType" required 
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white appearance-none cursor-pointer">
                                <option value="">Select</option>
                                <option value="Private">Private</option>
                                <option value="Public">Public</option>
                            </select>
                        </div>
                        <div id="statement-container" class="hidden animate-fade-in">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Statement of Account (Private Only)</label>
                            <div class="relative group">
                                <input type="file" name="statement" id="statement"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                                <div class="px-5 py-3 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all group-hover:bg-slate-100 group-hover:border-optimum-red">
                                    <span class="text-lg mr-3">🖼️</span>
                                    <span class="text-xs font-bold text-slate-500">Upload Statement</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Deceased Information -->
                <?php if (($request_type ?? 'educational') == 'burial'): ?>
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">2</div>
                        <h2 class="text-2xl font-bold text-slate-900">Information of the Deceased</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name of Deceased <span class="text-xs font-normal text-slate-400 ml-1">(First name/Middle name/Last name)</span></label>
                            <input type="text" name="deceasedName" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Age at Death <span class="text-xs font-normal text-slate-400 ml-1">(e.g. 65)</span></label>
                            <input type="number" name="deceasedAge" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Sex</label>
                            <select name="deceasedSex" required 
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white appearance-none cursor-pointer">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Death</label>
                            <input type="date" name="dateOfDeath" required 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Place of Death <span class="text-xs font-normal text-slate-400 ml-1">(e.g. PGH)</span></label>
                            <input type="text" name="placeOfDeath" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cause of Death <span class="text-xs font-normal text-slate-400 ml-1">(e.g. Cardiac Arrest)</span></label>
                            <input type="text" name="causeOfDeath" required placeholder="" 
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Patient Information (Medical) -->
                <?php if (($request_type ?? 'educational') == 'medical'): ?>
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">2</div>
                        <h2 class="text-2xl font-bold text-slate-900">Personal Information of the Patient</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name of Patient <span class="text-xs font-normal text-slate-400 ml-1">(First name/Middle name/Last name)</span></label>
                            <input type="text" name="patientName" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Age <span class="text-xs font-normal text-slate-400 ml-1">(e.g. 45)</span></label>
                            <input type="number" name="patientAge" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Sex</label>
                            <select name="patientSex" required
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white appearance-none cursor-pointer">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (($request_type ?? 'educational') == 'employment'): ?>
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">2</div>
                        <h2 class="text-2xl font-bold text-slate-900">Employment Information</h2>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Employment Type</label>
                            <select name="employmentType" id="employmentType" required
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white appearance-none cursor-pointer">
                                <option value="">Select</option>
                                <option value="New">New Employment</option>
                                <option value="Renew">Renew Employment</option>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Transportation Information -->
                <?php if (($request_type ?? 'educational') == 'transportation'): ?>
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">2</div>
                        <h2 class="text-2xl font-bold text-slate-900">Transportation Information</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Purpose of Travel <span class="text-xs font-normal text-slate-400 ml-1">(e.g. Medical Checkup)</span></label>
                            <input type="text" name="purpose" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Location / Destination <span class="text-xs font-normal text-slate-400 ml-1">(e.g. PGH)</span></label>
                            <input type="text" name="destination" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Travel Date</label>
                            <input type="date" name="travelDate" required
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white">
                        </div>
                    </div>
                </div>

                <!-- Driver's Information -->
                <div class="mb-12 animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">3</div>
                        <h2 class="text-2xl font-bold text-slate-900">Driver's Information</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Driver's Name <span class="text-xs font-normal text-slate-400 ml-1">(First name/Middle name/Last name)</span></label>
                            <input type="text" name="driverName" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Driver's Contact Number <span class="text-xs font-normal text-slate-400 ml-1">(e.g. 09181234567)</span></label>
                            <input type="tel" name="driverContact" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Driver's License Number <span class="text-xs font-normal text-slate-400 ml-1">(e.g. N01-12-123456)</span></label>
                            <input type="text" name="driverLicense" required placeholder=""
                                   class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-900 transition-all focus:border-optimum-red focus:bg-white placeholder:text-slate-400">
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Document Upload -->
                <?php if (($request_type ?? 'educational') != 'transportation'): ?>
                <div class="mb-12">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg"><?php echo (in_array($request_type ?? 'educational', ['educational', 'burial', 'employment', 'medical'])) ? '3' : '2'; ?></div>
                        <h2 class="text-2xl font-bold text-slate-900">Required Documents</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <?php 
                        $req_type = $request_type ?? 'educational';
                        $docs = [];
                        if ($req_type == 'educational') {
                            $docs = [
                                'enrollment' => 'Certificate of Enrollment',
                                'registration' => 'Certificate of Registration',
                                'indigency' => 'Barangay Indigency',
                                'schoolid' => 'School ID',
                                'validid' => '1 Valid ID (National / Postal ID)',
                                'grades' => 'Grade (2nd Sem / Incoming 1st Year)'
                            ];
                        } elseif ($req_type == 'medical') {
                            $docs = [
                                'medicalCertificate' => 'Medical Certificate',
                                'barangayIndigency'  => 'Barangay Indigency',
                                'validId1'           => '1 Valid ID (of the Applicant)',
                                'validId2'           => '1 Valid ID (of the Patient)',
                                'hospitalBill'       => 'Hospital Bill',
                                'authorization'      => 'Authorization of Patient',
                                'letterRequest'      => 'Letter Request of the Provincial Governor',
                                'socialCaseStudy'    => 'Social Case Study Form (MSWDO)'
                            ];
                        } elseif ($req_type == 'burial') {
                            $docs = [
                                'deathCertificate' => 'Death Certificate',
                                'barangayIndigency' => 'Barangay Indigency',
                                'validId' => '1 Valid ID (of the Applicant)',
                                'letterRequest' => 'Letter Request of the Provincial Governor',
                                'socialCaseStudy' => 'Social Case Study Form (MSWDO)'
                            ];
                        } elseif ($req_type == 'employment') {
                            $docs = [
                                'pds' => 'PDS (Personal Data Sheet)'
                            ];
                        } else {
                            $docs = [
                                'document1' => 'Supporting Document 1',
                                'indigency' => 'Barangay Indigency',
                                'validid' => '1 Valid Government ID'
                            ];
                        }
                        foreach ($docs as $key => $label): ?>
                        <div class="relative">
                            <label class="block text-sm font-semibold text-slate-700 mb-2"><?php echo $label; ?></label>
                            <div class="relative group">
                                <input type="file" name="<?php echo $key; ?>" required 
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                       accept="image/*">
                                <div class="px-5 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all group-hover:bg-slate-100 group-hover:border-optimum-red">
                                    <span class="text-lg mr-3">🖼️</span>
                                    <span class="text-xs font-bold text-slate-500">Click or drop to upload</span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Additional Documents for New Employment -->
                <?php if (($request_type ?? 'educational') == 'employment'): ?>
                <div id="new-employment-docs" class="mb-12 hidden animate-fade-in">
                    <div class="flex items-center space-x-4 mb-10 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-optimum-red text-white flex items-center justify-center text-lg font-bold rounded-full shadow-lg">4</div>
                        <h2 class="text-2xl font-bold text-slate-900">Additional Documents for New Employment</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="relative">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Resume</label>
                            <div class="relative group">
                                <input type="file" name="resume" id="resume" accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="px-5 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all group-hover:bg-slate-100 group-hover:border-optimum-red">
                                    <span class="text-lg mr-3">🖼️</span>
                                    <span class="text-xs font-bold text-slate-500">Click or drop to upload</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Recommendation</label>
                            <div class="relative group">
                                <input type="file" name="recommendation" id="recommendation" accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="px-5 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all group-hover:bg-slate-100 group-hover:border-optimum-red">
                                    <span class="text-lg mr-3">🖼️</span>
                                    <span class="text-xs font-bold text-slate-500">Click or drop to upload</span>
                                </div>
                            </div>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Endorsement</label>
                            <div class="relative group">
                                <input type="file" name="endorsement" id="endorsement" accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="px-5 py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center text-slate-400 transition-all group-hover:bg-slate-100 group-hover:border-optimum-red">
                                    <span class="text-lg mr-3">🖼️</span>
                                    <span class="text-xs font-bold text-slate-500">Click or drop to upload</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php endif; ?>

                <!-- Submit -->
                <div class="pt-10 text-center border-t border-slate-100">
                    <button type="submit" 
                            class="inline-flex items-center px-12 py-5 bg-optimum-red text-white font-bold rounded-xl shadow-lg hover:bg-slate-900 transition-all hover:scale-105 active:scale-95">
                        Submit Application
                    </button>
                    <p class="mt-6 text-sm font-medium text-slate-400">Processing time: 3-5 Working Days</p>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Age Calculation from DOB
            const dobInput = document.getElementById('dob');
            const ageInput = document.getElementById('age');

            if (dobInput && ageInput) {
                dobInput.addEventListener('change', function() {
                    if (!this.value) return;
                    const birthDate = new Date(this.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const m = today.getMonth() - birthDate.getMonth();
                    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    ageInput.value = age >= 0 ? age : 0;
                });
            }

            // Contact Validation
            const contactInput = document.getElementById('contact');
            const contactError = document.getElementById('contact-error');
            if (contactInput) {
                contactInput.addEventListener('input', function() {
                    const val = this.value.replace(/[^0-9]/g, '');
                    this.value = val;
                    if (val.length > 0 && !/^09[0-9]{9}$/.test(val)) {
                        contactError.classList.remove('hidden');
                    } else {
                        contactError.classList.add('hidden');
                    }
                });
            }

            // Check file upload sizes before submitting
            const form = document.getElementById('request-form');
            const maxPayloadSize = <?php echo (int) $post_max_size_bytes; ?>; // in bytes
            
            form.addEventListener('submit', function(e) {
                let totalSize = 0;
                const fileInputs = form.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.files && input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            totalSize += input.files[i].size;
                        }
                    }
                });
                
                // Maintain a small buffer (approx 500KB) for other text/form fields
                if (totalSize > (maxPayloadSize - 500000)) {
                    e.preventDefault();
                    alert("Upload error: The total size of your files (" + (totalSize / 1048576).toFixed(2) + " MB) is too large. Please reduce the file sizes so the total form is under " + (maxPayloadSize / 1048576).toFixed(2) + " MB.");
                }
            });

            // School type toggle (educational)
            const schoolType = document.getElementById('schoolType');
            const statementContainer = document.getElementById('statement-container');
            const statementInput = document.getElementById('statement');

            if (schoolType) {
                schoolType.addEventListener('change', function() {
                    if (this.value === 'Private') {
                        statementContainer.classList.remove('hidden');
                        statementInput.setAttribute('required', 'required');
                    } else {
                        statementContainer.classList.add('hidden');
                        statementInput.removeAttribute('required');
                        statementInput.value = '';
                    }
                });
                
                // Trigger once on load if already selected
                if (schoolType.value === 'Private') {
                    statementContainer.classList.remove('hidden');
                    statementInput.setAttribute('required', 'required');
                }
            }

            // Employment type toggle
            const employmentType = document.getElementById('employmentType');
            const newEmploymentDocs = document.getElementById('new-employment-docs');
            const newEmploymentInputs = newEmploymentDocs ? newEmploymentDocs.querySelectorAll('input[type="file"]') : [];

            if (employmentType && newEmploymentDocs) {
                employmentType.addEventListener('change', function() {
                    if (this.value === 'New') {
                        newEmploymentDocs.classList.remove('hidden');
                        newEmploymentInputs.forEach(function(input) {
                            input.setAttribute('required', 'required');
                        });
                    } else {
                        newEmploymentDocs.classList.add('hidden');
                        newEmploymentInputs.forEach(function(input) {
                            input.removeAttribute('required');
                            input.value = '';
                        });
                    }
                });

                // Trigger once on load if already selected
                if (employmentType.value === 'New') {
                    newEmploymentDocs.classList.remove('hidden');
                    newEmploymentInputs.forEach(function(input) {
                        input.setAttribute('required', 'required');
                    });
                }
            }

            // File Upload Preview
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const file = this.files[0];
                    const displayDiv = this.nextElementSibling;
                    if (file) {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                displayDiv.innerHTML = `
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <img src="${e.target.result}" class="max-h-24 object-contain rounded-lg">
                                        <span class="text-xs font-bold text-slate-500">${file.name}</span>
                                    </div>
                                `;
                            }
                            reader.readAsDataURL(file);
                        } else {
                            displayDiv.innerHTML = `
                                <div class="flex items-center space-x-2">
                                    <span class="text-lg">📄</span>
                                    <span class="text-xs font-bold text-slate-500">${file.name}</span>
                                </div>
                            `;
                        }
                    }
                });
            });
        });
    </script>
    <?php require_once APP_PATH . '/views/layouts/footer.php'; ?>
</body>
