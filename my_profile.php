<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Ensure interests table exists (Auto-migration)
try {
    $pdo->query("SELECT 1 FROM interests LIMIT 1");
} catch (Exception $e) {
    include_once 'setup_db.php';
}

$user_id = $_SESSION['user_id'];

// Fetch all candidate details
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->execute([$user_id]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    header("Location: logout.php");
    exit();
}

// Map package names to readable labels
$package_labels = [
    '3_months' => '3 Months (Basic)',
    '6_months' => '6 Months (Popular)',
    'unlimited' => 'Unlimited (Lifetime)'
];
$current_package = $package_labels[$candidate['package']] ?? $candidate['package'];

?>
<?php include 'includes/header.php'; ?>

<main class="min-h-screen py-10 px-4 sm:px-6 lg:px-8 themed-background">
    <div class="max-w-5xl mx-auto">
        
        <!-- Notification Message -->
        <?php if (isset($_GET['action_success']) && isset($_GET['message'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3 reveal reveal-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold text-sm"><?php echo htmlspecialchars($_GET['message']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Special Redirect Handle (from report_partner.php) -->
        <?php if (isset($_GET['notified'])): ?>
            <div class="mb-6 p-4 bg-pink-100 border border-pink-200 text-pink-700 rounded-2xl flex items-center gap-3 reveal reveal-up">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                <span class="font-bold text-sm">Thank you for notifying us! Your profile has been updated.</span>
            </div>
        <?php endif; ?>

        <!-- Header Section -->
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6 reveal reveal-up">
            <div>
                <h1 class="text-4xl font-black text-primary mb-2">My Profile Dashboard</h1>
                <p class="text-gray-500 font-medium">View your full registration details in one place</p>
            </div>
            
            <div class="flex flex-wrap gap-3">
                <div class="px-6 py-3 bg-white rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</span>
                    <?php 
                        $status_colors = [
                            'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                            'approved' => 'bg-green-100 text-green-700 border-green-200',
                            'rejected' => 'bg-red-100 text-red-700 border-red-200'
                        ];
                        $status_class = $status_colors[$candidate['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?php echo $status_class; ?>">
                        <?php echo ucfirst($candidate['status']); ?>
                    </span>
                </div>
                
                <div class="px-6 py-3 bg-primary text-white rounded-2xl shadow-xl shadow-primary/20 flex items-center gap-3">
                    <span class="text-[10px] font-black text-blue-200 uppercase tracking-widest">Reg No</span>
                    <span class="text-lg font-black tracking-wider"><?php echo $candidate['reg_number'] ?: 'N/A'; ?></span>
                </div>
            </div>
        </div>

        <!-- Unified Profile Form -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden reveal reveal-up delay-200">
            <!-- Header Section with Photo & Identity -->
            <div class="p-8 md:p-10 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
                <div class="w-40 h-52 shrink-0 rounded-2xl overflow-hidden shadow-2xl border-4 border-white rotate-2 hover:rotate-0 transition-transform duration-500">
                    <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/400x600?text=Profile'; ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover">
                </div>
                
                <div class="flex-grow pt-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary text-white rounded-full text-[10px] font-bold uppercase tracking-widest mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Active Membership: <?php echo $current_package; ?>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-2"><?php echo htmlspecialchars($candidate['fullname']); ?></h2>
                    <p class="text-xl text-gray-500 font-medium mb-6"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-8">
                        <div>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Age</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['age']; ?> Years</span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Location</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($candidate['hometown']); ?></span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Denomination</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['denomination']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Information Sections -->
            <div class="p-8 md:p-12 space-y-12">
                
                <!-- Personal & Contact -->
                <section>
                    <h3 class="flex items-center gap-3 text-lg font-black text-primary mb-8">
                        <span class="w-2.5 h-6 bg-primary rounded-full"></span>
                        Personal & Contact Details
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">NIC Number</span>
                            <p class="text-gray-800 font-bold"><?php echo htmlspecialchars($candidate['nic_number']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Date of Birth</span>
                            <p class="text-gray-800 font-bold"><?php echo date('M d, Y', strtotime($candidate['dob'])); ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Gender</span>
                            <p class="text-gray-800 font-bold"><?php echo $candidate['sex']; ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Marital Status</span>
                            <p class="text-gray-800 font-bold"><?php echo $candidate['marital_status']; ?> <?php echo ($candidate['children'] === 'Yes') ? '(Has Children)' : '(No Children)'; ?></p>
                            <?php if ($candidate['children'] === 'Yes' && !empty($candidate['children_details'])): ?>
                                <p class="text-xs text-gray-500 mt-1 font-medium"><?php echo htmlspecialchars($candidate['children_details']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="lg:col-span-2 space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Permanent Address</span>
                            <p class="text-gray-800 font-bold leading-relaxed"><?php echo htmlspecialchars($candidate['address']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">District & Province</span>
                            <p class="text-gray-800 font-bold"><?php echo $candidate['district']; ?>, <?php echo $candidate['province']; ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Hometown</span>
                            <p class="text-gray-800 font-bold"><?php echo htmlspecialchars($candidate['hometown']); ?></p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Registered On</span>
                            <p class="text-gray-800 font-bold"><?php echo date('M d, Y', strtotime($candidate['created_at'])); ?></p>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100">

                <!-- Academic & Career -->
                <section>
                    <h3 class="flex items-center gap-3 text-lg font-black text-primary mb-8">
                        <span class="w-2.5 h-6 bg-blue-500 rounded-full"></span>
                        Academic & Career History
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /></svg>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">General Education</span>
                                <p class="text-gray-800 font-bold text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($candidate['edu_qual'])); ?></p>
                            </div>
                        </div>
                        <?php if (!empty($candidate['add_qual'])): ?>
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                            </div>
                            <div>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Additional Qualifications</span>
                                <p class="text-gray-800 font-bold text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($candidate['add_qual'])); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <hr class="border-gray-100">

                <!-- Faith & Family -->
                <section class="bg-primary rounded-3xl p-8 md:p-10 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    <h3 class="flex items-center gap-3 text-lg font-black mb-8 relative z-10">
                        <span class="w-2.5 h-6 bg-blue-400 rounded-full"></span>
                        Religious & Family Connections
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 relative z-10">
                        <div class="space-y-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-1">Denomination</span>
                                    <p class="text-sm font-bold"><?php echo $candidate['denomination']; ?></p>
                                </div>
                                <div>
                                    <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-1">Catholic by Birth</span>
                                    <p class="text-sm font-bold"><?php echo $candidate['catholic_by_birth']; ?></p>
                                </div>
                            </div>
                            <div>
                                <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-1">Church / Fellowship Name</span>
                                <p class="text-lg font-bold"><?php echo htmlspecialchars($candidate['church']); ?></p>
                            </div>
                            <div>
                                <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-1">Pastor / Father Name</span>
                                <p class="text-lg font-bold"><?php echo htmlspecialchars($candidate['pastor_name']); ?></p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-2">Pastor's Phone</span>
                                <p class="text-sm font-black"><?php echo htmlspecialchars($candidate['pastor_phone']); ?></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-2">Parent's Phone</span>
                                <p class="text-sm font-black text-green-400"><?php echo htmlspecialchars($candidate['parent_phone']); ?></p>
                            </div>
                            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <span class="text-[9px] text-blue-300 font-black uppercase tracking-widest block mb-2">My Phone</span>
                                <p class="text-sm font-black"><?php echo htmlspecialchars($candidate['my_phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Habits & Health -->
                <section>
                    <h3 class="flex items-center gap-3 text-lg font-black text-primary mb-8">
                        <span class="w-2.5 h-6 bg-red-400 rounded-full"></span>
                        Habits & Health Status
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-3">Reported Habits</span>
                            <div class="flex flex-wrap gap-2">
                                <?php 
                                    $habits = explode(',', $candidate['habits']);
                                    foreach($habits as $habit): 
                                        if(empty(trim($habit))) continue;
                                ?>
                                    <span class="px-4 py-2 bg-gray-50 text-gray-700 rounded-xl text-xs font-bold border border-gray-100">
                                        <?php echo trim($habit); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-3">Chronic Diseases / Illness</span>
                            <p class="text-gray-800 font-bold"><?php echo $candidate['illness'] ?: 'No chronic diseases reported.'; ?></p>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100">

                <!-- Account & Profile Settings -->
                <section>
                    <h3 class="flex items-center gap-3 text-lg font-black text-primary mb-8">
                        <span class="w-2.5 h-6 bg-gray-600 rounded-full"></span>
                        Account & Profile Settings
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Found My Partner Section -->
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 relative overflow-hidden text-left shadow-sm">
                            <h4 class="text-md font-black text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-pink-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /></svg>
                                Found My Partner
                            </h4>
                            
                            <?php if ($candidate['partner_found']): ?>
                                <div class="p-4 bg-pink-50 border border-pink-100 rounded-2xl">
                                    <p class="text-pink-700 text-sm font-bold">Congratulations! You have marked your profile as "Partner Found".</p>
                                    <p class="text-pink-600 text-xs mt-1">Our team has been notified. We wish you a blessed journey ahead!</p>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-xs mb-6 font-medium leading-relaxed">If you have found your life partner through our service or elsewhere, please let us know. This will help us maintain an updated database.</p>
                                <button onclick="document.getElementById('partnerModal').classList.remove('hidden')" class="w-full py-4 bg-white border-2 border-pink-200 text-pink-600 rounded-2xl font-bold hover:bg-pink-50 hover:border-pink-300 transition-all duration-300 flex items-center justify-center gap-2 group">
                                    Notify: I Found My Partner
                                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Visibility Control Section -->
                        <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 text-left shadow-sm">
                            <h4 class="text-md font-black text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" /></svg>
                                Profile Visibility
                            </h4>
                            
                            <?php if ($candidate['disable_requested'] && !$candidate['is_disabled']): ?>
                                <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl mb-4">
                                    <p class="text-amber-700 text-sm font-bold leading-tight">Request Pending Review</p>
                                    <p class="text-amber-600 text-xs mt-1">Your request to disable your profile is currently being reviewed by our administrator.</p>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 text-xs mb-6 font-medium leading-relaxed">
                                    <?php echo $candidate['is_disabled'] ? 'Your profile has been disabled by the administrator. Contact support to re-enable.' : 'You can request to hide your profile from other members. This request will be reviewed by the administrator.'; ?>
                                </p>
                                
                                <?php if (!$candidate['is_disabled']): ?>
                                    <a href="disable_profile.php" onclick="return confirm('Are you sure you want to request your profile to be disabled? This will hide you from other members and prevent further logins once approved.')" class="w-full py-4 bg-white border-2 border-amber-200 text-amber-600 hover:bg-amber-50 hover:border-amber-300 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center gap-2 group">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.046m4.596-4.596A9.964 9.964 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.059 10.059 0 01-2.27 4.013M15.549 15.549A3 3 0 1111.45 11.451m4.099 4.099L3 3m18 18l-18-18" /></svg>
                                        Request Profile Disable
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <hr class="border-gray-100">

            </div>
            
            <!-- Footer Verification -->
            <div class="bg-gray-50 p-6 md:p-8 border-t border-gray-100 text-center">
                 <p class="text-gray-400 text-xs font-medium">This is a system-generated profile preview of your registration details. All information is secure and handled with confidentiality.</p>
            </div>
        </div>
    </div>
</main>

<!-- Partner Found Modal -->
<div id="partnerModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden reveal reveal-up">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center text-left">
            <h3 class="text-xl font-black text-gray-900">I Found My Partner</h3>
            <button onclick="document.getElementById('partnerModal').classList.add('hidden')" class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        
        <form action="report_partner.php" method="POST" class="p-8 space-y-6 text-left">
            <input type="hidden" name="his_name" value="<?php echo htmlspecialchars($candidate['fullname']); ?>">
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">My Partner's Name</label>
                <input type="text" name="partner_name" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-800" placeholder="Enter partner's name">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">My Partner's Register Number</label>
                <input type="text" name="partner_reg_number" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-800" placeholder="e.g. REG/2026/001">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">My Mobile Number</label>
                <input type="text" name="mobile_number" value="<?php echo htmlspecialchars($candidate['my_phone']); ?>" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-800">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">A Short Message (Optional)</label>
                <textarea name="message" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all outline-none font-bold text-gray-800" placeholder="Share your joy with us..."></textarea>
            </div>
            
            <button type="submit" class="w-full py-5 bg-pink-500 text-white rounded-2xl font-black shadow-xl shadow-pink-200 hover:bg-pink-600 transition-all duration-300 transform active:scale-[0.98]">
                Submit Notification
            </button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
