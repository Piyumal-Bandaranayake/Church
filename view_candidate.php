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

// Handle Status Updates from this page too
if (isset($_POST['action'])) {
    $new_status = $_POST['action'];
    $stmt = $pdo->prepare("UPDATE candidates SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $id]);
    header("Location: view_candidate.php?id=" . $id . "&updated=1");
    exit();
}
?>

<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb & Actions -->
        <div class="flex items-center justify-between mb-8">
            <a href="admin_dashboard.php" class="flex items-center text-primary hover:underline gap-2 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Dashboard
            </a>
            <div class="flex gap-2">
                <form method="POST" class="inline">
                    <?php if ($candidate['status'] == 'pending'): ?>
                        <button name="action" value="approved" class="px-4 py-2 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition shadow-sm">Approve Application</button>
                        <button name="action" value="rejected" class="px-4 py-2 bg-orange-500 text-white rounded-lg font-bold hover:bg-orange-600 transition shadow-sm">Reject Application</button>
                    <?php
endif; ?>
                </form>
                <a href="admin_dashboard.php?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Permanently delete this application?')" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg font-bold hover:bg-red-600 hover:text-white transition">Delete Profile</a>
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
                <div class="w-40 h-40 shrink-0">
                    <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/300?text=No+Photo'; ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover rounded-2xl shadow-2xl ring-4 ring-white/20">
                </div>
                <div class="text-center md:text-left">
                    <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3 <?php echo $candidate['status'] == 'approved' ? 'bg-green-500' : 'bg-orange-500'; ?>">
                        <?php echo $candidate['status']; ?>
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
                                <span class="font-semibold text-gray-900"><?php echo $candidate['height']; ?></span>
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
                                <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? 'Mustache' : 'Church Name'; ?></span>
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
                                    <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? 'Father Name' : 'Pastor Name'; ?></span>
                                    <p class="font-semibold text-gray-900 text-sm"><?php echo htmlspecialchars($candidate['pastor_name']); ?></p>
                                </div>
                                <div>
                                    <span class="text-gray-500 text-xs block uppercase"><?php echo $candidate['denomination'] === 'Christian' ? "Father's WhatsApp" : "Pastor's WhatsApp"; ?></span>
                                    <p class="font-semibold text-gray-900 text-sm"><?php echo htmlspecialchars($candidate['pastor_phone']); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="text-sm font-bold text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span class="w-6 h-px bg-primary/20"></span> Emergency Contact
                        </h3>
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="text-gray-500 text-xs">Parent's WhatsApp</span>
                                <p class="font-bold text-gray-900"><?php echo htmlspecialchars($candidate['parent_phone']); ?></p>
                            </div>
                        </div>
                    </section>

                    <section class="bg-gray-100 p-6 rounded-2xl">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-2">Account Login Info</h3>
                        <p class="text-gray-600 text-sm">Email: <span class="font-mono bg-white px-2 py-0.5 rounded border border-gray-200"><?php echo $candidate['email']; ?></span></p>
                        <p class="text-gray-400 text-[10px] mt-2 italic">* Password is encrypted and cannot be viewed.</p>
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
