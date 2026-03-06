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

            </div>
            
            <!-- Footer Verification -->
            <div class="bg-gray-50 p-6 md:p-8 border-t border-gray-100 text-center">
                 <p class="text-gray-400 text-xs font-medium">This is a system-generated profile preview of your registration details. All information is secure and handled with confidentiality.</p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
