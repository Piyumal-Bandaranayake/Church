<?php
session_start();
include 'includes/db.php';

// Security: Only Admin can view full details
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->execute([$id]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    die("Candidate not found.");
}

// Handle Status & Disable Updates
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'approved' || $action === 'rejected') {
        $stmt = $pdo->prepare("UPDATE candidates SET status = ? WHERE id = ?");
        $stmt->execute([$action, $id]);
    } elseif ($action === 'enable') {
        $stmt = $pdo->prepare("UPDATE candidates SET is_disabled = 0, disable_requested = 0 WHERE id = ?");
        $stmt->execute([$id]);
    } elseif ($action === 'disable') {
        $stmt = $pdo->prepare("UPDATE candidates SET is_disabled = 1, disable_requested = 0 WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: view_candidate.php?id=" . $id . "&updated=1");
    exit();
}
?>

<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen pt-20 pb-12 sm:py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-8 mt-8 sm:mt-0">
            <a href="admin_dashboard.php" class="flex items-center text-primary hover:underline gap-2 font-bold text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
            
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" class="flex flex-wrap gap-2">
                    <?php if ($candidate['status'] == 'pending'): ?>
                        <button name="action" value="approved" class="flex-1 sm:flex-none px-4 py-2.5 bg-green-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-green-700 transition shadow-lg shadow-green-600/20 active:scale-95">Approve</button>
                        <button name="action" value="rejected" class="flex-1 sm:flex-none px-4 py-2.5 bg-orange-500 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-orange-600 transition shadow-lg shadow-orange-500/20 active:scale-95">Reject</button>
                    <?php endif; ?>
                    
                    <?php if ($candidate['is_disabled'] == 1): ?>
                        <button name="action" value="enable" class="flex-1 sm:flex-none px-4 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 active:scale-95">Enable Profile</button>
                    <?php else: ?>
                        <button name="action" value="disable" onclick="return confirm('Disable this profile? It will be hidden from all users.')" class="flex-1 sm:flex-none px-4 py-2.5 bg-gray-600 text-white rounded-xl text-xs font-black uppercase tracking-wider hover:bg-gray-700 transition shadow-lg active:scale-95">Disable Profile</button>
                    <?php endif; ?>
                </form>

                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="admin_edit_profile.php?id=<?php echo $candidate['id']; ?>" class="flex-1 sm:flex-none px-4 py-2.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white transition flex items-center justify-center gap-2 border border-blue-100 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Edit
                    </a>
                    <a href="admin_dashboard.php?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Permanently delete this application?')" class="flex-1 sm:flex-none px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-black uppercase tracking-wider hover:bg-red-600 hover:text-white transition text-center border border-red-100 active:scale-95">Delete</a>
                </div>
            </div>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Status updated successfully!
            </div>
        <?php
endif; ?>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <!-- Header Section -->
            <div class="bg-primary p-8 text-white flex flex-col md:flex-row items-center gap-8">
                <div class="w-40 h-40 shrink-0 cursor-pointer group relative" onclick="openImageModal('<?php echo htmlspecialchars($img); ?>')">
                    <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/300?text=No+Photo'; ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover rounded-2xl shadow-2xl ring-4 ring-white/20 group-hover:ring-white/40 transition-all">
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-2xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <div class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest <?php echo $candidate['status'] == 'approved' ? 'bg-green-500' : 'bg-orange-500'; ?>">
                            <?php echo strtoupper($candidate['status']); ?>
                        </div>
                        <?php if ($candidate['is_disabled'] == 1): ?>
                            <div class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-red-600 animate-pulse">
                                Account Disabled
                            </div>
                        <?php endif; ?>
                        
                        <!-- Profile Validity Section -->
                        <?php
                            $created_at = new DateTime($candidate['created_at']);
                            $pkg = !empty($candidate['package']) ? $candidate['package'] : '3_months';
                            $expiry = null;
                            $days_left = null;
                            $is_expired = false;

                            if ($pkg === 'first_visit') {
                                $expiry = (clone $created_at)->modify('+1 month');
                            } elseif ($pkg === '3_months') {
                                $expiry = (clone $created_at)->modify('+3 months');
                            } elseif ($pkg === '6_months') {
                                $expiry = (clone $created_at)->modify('+6 months');
                            }

                            if ($expiry) {
                                $today = new DateTime();
                                if ($today > $expiry) {
                                    $is_expired = true;
                                    $days_left = 0;
                                } else {
                                    $diff = $today->diff($expiry);
                                    $days_left = $diff->days;
                                }
                            }
                        ?>
                        
                        <div class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-[10px] font-black uppercase tracking-widest flex items-center gap-2 border border-white/10">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <?php 
                                if ($pkg === 'unlimited') {
                                    echo "Unlimited Access";
                                } elseif ($is_expired) {
                                    echo "Expired (" . $expiry->format('Y-m-d') . ")";
                                } else {
                                    echo ($days_left + 1) . " Days Remaining (" . strtoupper(str_replace('_', ' ', $pkg)) . ")";
                                }
                            ?>
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold"><?php echo htmlspecialchars($candidate['fullname']); ?></h1>
                    <p class="text-blue-200 text-lg mt-1"><?php echo htmlspecialchars($candidate['occupation']); ?> &middot; <?php echo htmlspecialchars($candidate['age']); ?> Years Old</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-4 mt-4 text-sm">
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg> <?php echo htmlspecialchars($candidate['my_phone']); ?></span>
                        <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> <?php echo htmlspecialchars($candidate['hometown']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Details Content -->
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- Column 1: Personal & Bio -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Personal Identity
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">NIC Number</span>
                                <span class="font-semibold text-gray-900 uppercase"><?php echo $candidate['nic_number']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Gender</span>
                                <span class="font-semibold text-gray-900"><?php echo $candidate['sex']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Date of Birth</span>
                                <span class="font-semibold text-gray-900"><?php echo date('M d, Y', strtotime($candidate['dob'])); ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Nationality</span>
                                <span class="font-semibold text-gray-900"><?php echo $candidate['nationality']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Mother Tongue</span>
                                <span class="font-semibold text-gray-900"><?php echo $candidate['language']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Height</span>
                                <span class="font-semibold text-gray-900"><?php echo $candidate['height']; ?> ft</span>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Location & Address
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-xl text-sm leading-relaxed text-gray-700">
                            <strong>Full Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($candidate['address'])); ?><br><br>
                            <strong>Hometown:</strong> <?php echo $candidate['hometown']; ?><br>
                            <strong>District:</strong> <?php echo $candidate['district']; ?><br>
                            <strong>Province:</strong> <?php echo $candidate['province']; ?>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Marital Status & Health
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Marital Status</span>
                                <span class="font-semibold text-gray-900 text-right"><?php echo $candidate['marital_status']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Children</span>
                                <span class="font-semibold text-gray-900"><?php echo $candidate['children']; ?></span>
                            </div>
                            <?php if ($candidate['children'] === 'Yes' && !empty($candidate['children_details'])): ?>
                            <div class="pt-2">
                                <span class="text-gray-500 text-sm block mb-1">Children Details</span>
                                <p class="text-gray-700 bg-blue-50 p-3 rounded-lg text-sm"><?php echo nl2br(htmlspecialchars($candidate['children_details'])); ?></p>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span class="text-gray-500 text-sm">Habits</span>
                                <span class="font-semibold text-red-600"><?php echo $candidate['habits']; ?></span>
                            </div>
                            <div class="pt-2">
                                <span class="text-gray-500 text-sm block mb-1">Long-term Illness</span>
                                <p class="text-gray-700 bg-orange-50 p-3 rounded-lg text-sm"><?php echo !empty($candidate['illness']) ? htmlspecialchars($candidate['illness']) : 'None reported'; ?></p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Column 2: Prof, Edu, Religion -->
                <div class="space-y-8">
                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Professional Background
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <span class="text-gray-500 text-xs block uppercase">Occupation</span>
                                <p class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs block uppercase">Education</span>
                                <p class="text-gray-700 text-sm italic"><?php echo nl2br(htmlspecialchars($candidate['edu_qual'])); ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs block uppercase">Additional Skills</span>
                                <p class="text-gray-700 text-sm"><?php echo !empty($candidate['add_qual']) ? nl2br(htmlspecialchars($candidate['add_qual'])) : 'N/A'; ?></p>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Religious Affiliation
                        </h3>
                        <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100 space-y-4">
                            <div>
                                <span class="text-gray-500 text-xs block uppercase">Denomination</span>
                                <p class="font-bold text-blue-600"><?php echo htmlspecialchars($candidate['denomination'] ?? 'Not set'); ?></p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? 'Denomination' : 'Church Name'; ?></span>
                                <p class="font-bold text-primary"><?php echo htmlspecialchars($candidate['church']); ?></p>
                            </div>
                            <?php if ($candidate['denomination'] === 'Catholic'): ?>
                            <div class="pt-4 mt-4 border-t border-blue-100">
                                <h4 class="text-xs font-bold text-blue-400 uppercase tracking-tighter mb-3">Catholic Faith Details</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-gray-500 text-xs block uppercase">By Birth</span>
                                        <p class="font-semibold text-gray-900 text-sm"><?php echo $candidate['catholic_by_birth'] ?? 'N/A'; ?></p>
                                    </div>
                                    <?php if ($candidate['catholic_by_birth'] === 'No'): ?>
                                    <div>
                                        <span class="text-gray-500 text-xs block uppercase">Y.O.C</span>
                                        <p class="font-semibold text-gray-900 text-sm"><?php echo $candidate['christianization_year'] ?? 'N/A'; ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="col-span-2">
                                        <span class="text-gray-500 text-xs block uppercase">Sacraments / Bonuses</span>
                                        <p class="font-semibold text-gray-900 text-sm mt-1"><?php echo !empty($candidate['sacraments_received']) ? htmlspecialchars($candidate['sacraments_received']) : 'None reported'; ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? 'Pastor Name' : 'Father Name'; ?></span>
                                    <p class="font-semibold text-gray-900 text-sm"><?php echo htmlspecialchars($candidate['pastor_name']); ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? "Pastor's WhatsApp" : "Father's WhatsApp"; ?></span>
                                    <p class="font-semibold text-gray-900 text-sm"><?php echo htmlspecialchars($candidate['pastor_phone']); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Emergency Contacts
                        </h3>
                        <div class="space-y-3">
                            <!-- Account Owner -->
                            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Candidate's WhatsApp</span>
                                    <p class="font-bold text-primary"><?php echo htmlspecialchars($candidate['my_phone']); ?></p>
                                </div>
                            </div>
                            <!-- Parent -->
                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest">Parent's WhatsApp</span>
                                    <p class="font-bold text-gray-900"><?php echo htmlspecialchars($candidate['parent_phone']); ?></p>
                                </div>
                            </div>
                            <!-- Pastor -->
                            <div class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl border border-purple-100">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H2v2h9v9h2v-9h9v-2h-9V2h-2z"/></svg>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-[10px] font-black uppercase tracking-widest"><?php echo $candidate['denomination'] === 'Christian' ? 'Pastor' : 'Father'; ?> WhatsApp</span>
                                    <p class="font-bold text-purple-700"><?php echo htmlspecialchars($candidate['pastor_phone']); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="bg-gray-100 p-6 rounded-2xl">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Account Login Info</h3>
                        <p class="text-gray-600 text-sm">Email: <span class="font-mono bg-white px-2 py-0.5 rounded border border-gray-200"><?php echo $candidate['email']; ?></span></p>
                        <p class="text-gray-400 text-[10px] mt-2 italic">* Password is encrypted and cannot be viewed.</p>
                    </section>

                    <!-- Payment Verification -->
                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Payment Verification
                        </h3>
                        <?php if (!empty($candidate['payment_slip_path'])): ?>
                            <div class="relative group cursor-pointer" onclick="openImageModal('<?php echo htmlspecialchars($candidate['payment_slip_path']); ?>')">
                                <?php 
                                $is_pdf = strtolower(pathinfo($candidate['payment_slip_path'], PATHINFO_EXTENSION)) === 'pdf';
                                if ($is_pdf): 
                                ?>
                                    <div class="w-full p-6 bg-red-50 rounded-2xl border-2 border-dashed border-red-200 flex flex-col items-center justify-center gap-3">
                                        <svg class="w-12 h-12 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414A2 2 0 0016.414 5L14 2.586A2 2 0 0012.586 2H9z" /><path d="M3 8a2 2 0 012-2v10h8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" /></svg>
                                        <span class="text-red-700 font-bold">PDF Payment Slip</span>
                                        <a href="<?php echo htmlspecialchars($candidate['payment_slip_path']); ?>" target="_blank" class="px-4 py-2 bg-red-500 text-white rounded-lg text-xs font-bold hover:bg-red-600 transition">View PDF</a>
                                    </div>
                                <?php else: ?>
                                    <img src="<?php echo htmlspecialchars($candidate['payment_slip_path']); ?>" class="w-full rounded-2xl shadow-lg border-4 border-white group-hover:opacity-90 transition-opacity">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/20 rounded-2xl">
                                        <span class="bg-white text-primary px-4 py-2 rounded-full font-bold text-sm shadow-xl">View Full Slip</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-400 font-bold uppercase text-xs">
                                No payment slip uploaded
                            </div>
                        <?php endif; ?>
                    </section>
                </div>

            </div>
        </div>
    </div>
    </main>
</div>
</body>
</html>


</body>
</html>
