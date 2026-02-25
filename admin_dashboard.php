<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Direct Deletions from Dashboard
if (isset($_GET['delete_found'])) {
    $id = $_GET['delete_found'];
    
    // Get photo path to delete file
    $stmt = $pdo->prepare("SELECT photo_path FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }

    $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: admin_dashboard.php?success=profile_removed_after_partner_found");
    exit();
}

// Fetch Stats
try {
    $total_stmt = $pdo->query("SELECT COUNT(*) FROM candidates");
    $total_count = $total_stmt->fetchColumn();

    $approved_stmt = $pdo->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'");
    $approved_count = $approved_stmt->fetchColumn();

    $pending_stmt = $pdo->query("SELECT COUNT(*) FROM candidates WHERE status = 'pending'");
    $pending_count = $pending_stmt->fetchColumn();

    // Fetch Recent Pending Candidates (Top 5)
    $stmt = $pdo->query("SELECT * FROM candidates WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
    $pending_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Review Stats
    $review_total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
    $review_total_count = $review_total_stmt->fetchColumn();

    $review_pending_stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'");
    $review_pending_count = $review_pending_stmt->fetchColumn();

    // Fetch Recent Pending Reviews (Top 5)
    $stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'pending' ORDER BY created_at DESC LIMIT 5");
    $pending_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Partner Found Notifications
    $partner_stmt = $pdo->query("SELECT * FROM candidates WHERE partner_found = 1 ORDER BY created_at DESC");
    $partner_found_candidates = $partner_stmt->fetchAll(PDO::FETCH_ASSOC);
    $partner_found_count = count($partner_found_candidates);

    // Fetch Total Success Stories
    $success_stmt = $pdo->query("SELECT COUNT(*) FROM candidates WHERE partner_found = 1");
    $success_count = $success_stmt->fetchColumn();
}
catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen">
        <!-- Dashboard Header -->
        <div class="bg-primary relative overflow-hidden text-white pt-16 pb-28 px-4 sm:px-6 lg:px-8 shadow-inner">
            <!-- Decorative Gradient Blobs -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-600/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-80 h-80 bg-indigo-600/10 blur-[80px] rounded-full"></div>
            
            <div class="max-w-7xl mx-auto relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <h1 class="text-4xl font-black tracking-tight leading-none mb-3">Admin Control Center</h1>
                        <p class="text-blue-200 text-lg font-medium max-w-2xl">Welcome back, Admin  Here's what's happening today.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20 pb-20">
            <!-- Stats Cards Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-12">
                <!-- Success Stories Card -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-green-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Success</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$success_count; ?></h3>
                </div>

                <!-- Total Users -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Users</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$total_count; ?></h3>
                </div>

                <!-- Approved -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-emerald-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Approved</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$approved_count; ?></h3>
                </div>

                <!-- Pending -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300 relative">
                    <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <?php if ($pending_count > 0): ?>
                        <span class="absolute top-4 right-4 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                    <?php endif; ?>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$pending_count; ?></h3>
                </div>

                <!-- Testimonies -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Stories</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$review_total_count; ?></h3>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-8 p-4 rounded-2xl flex items-center gap-3 animate-fade-in text-xs font-bold uppercase tracking-tight <?php echo(strpos($_GET['success'], 'approved') !== false ? 'bg-green-50 text-green-700 border border-green-200' : (strpos($_GET['success'], 'deleted') !== false ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-orange-50 text-orange-700 border border-orange-200')); ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Action successful</span>
                </div>
            <?php endif; ?>

            <!-- Partner Found Notifications Section -->
            <div class="mt-12">
                <div class="flex items-center gap-3 mb-8 px-2">
                    <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
                    <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter">Partner Found Notifications</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black rounded-full"><?php echo $partner_found_count; ?> NEW</span>
                </div>

                <?php if (empty($partner_found_candidates)): ?>
                    <div class="bg-white rounded-[2rem] p-12 border border-dashed border-gray-200 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">No new partner success reports</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($partner_found_candidates as $p_candidate): ?>
                        <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-200/30 border border-gray-100 flex flex-col group animate-fade-in hover:-translate-y-1 transition-all">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="relative shrink-0">
                                    <?php $p_img = !empty($p_candidate['photo_path']) ? $p_candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                                    <img src="<?php echo htmlspecialchars($p_img); ?>" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-gray-50 group-hover:ring-green-100 transition-all">
                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-white rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-black text-gray-900 leading-tight mb-1"><?php echo htmlspecialchars($p_candidate['fullname']); ?></h4>
                                    <span class="text-[9px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded uppercase"><?php echo $p_candidate['denomination']; ?></span>
                                </div>
                            </div>
                            
                            <div class="bg-[#fcfdfd] rounded-[1.5rem] p-5 mb-8 flex-grow relative overflow-hidden ring-1 ring-gray-50">
                                <span class="absolute top-2 left-3 text-4xl text-gray-100 font-serif">"</span>
                                <p class="text-[13px] text-gray-600 font-medium italic leading-relaxed relative z-10">
                                    <?php echo nl2br(htmlspecialchars($p_candidate['partner_message'])); ?>
                                </p>
                            </div>

                            <div class="flex gap-3">
                                <a href="view_candidate.php?id=<?php echo $p_candidate['id']; ?>" class="flex-grow py-3.5 bg-gray-50 text-gray-400 text-[10px] font-black uppercase text-center rounded-2xl hover:bg-primary/5 hover:text-primary transition-all tracking-widest border border-transparent hover:border-primary/10">View Profile</a>
                                <a href="?delete_found=<?php echo $p_candidate['id']; ?>" onclick="return confirm('Found partner confirmed. Remove this profile permanently?')" class="w-12 py-3.5 bg-red-50 text-red-600 rounded-2xl hover:bg-red-600 hover:text-white transition-all flex items-center justify-center shadow-sm shadow-red-100/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Recent Activity Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-12">
                <!-- Recent Pending Applications Section -->
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden flex flex-col min-h-[400px]">
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 leading-none">Recent Applications</h2>
                            <p class="text-xs text-gray-400 mt-2 font-medium tracking-wide uppercase">Awaiting your review</p>
                        </div>
                        <a href="manage_applications.php" class="p-2 bg-white border border-gray-200 rounded-xl text-primary hover:bg-primary hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>

                    <div class="flex-grow">
                        <?php if (empty($pending_candidates)): ?>
                             <div class="p-16 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                </div>
                                <p class="text-gray-400 font-medium italic">All caught up!</p>
                             </div>
                        <?php
else: ?>
                            <div class="divide-y divide-gray-50">
                                <?php foreach ($pending_candidates as $candidate): ?>
                                <div class="px-8 py-5 hover:bg-gray-50/50 transition-colors flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-white shadow-md">
                                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-orange-500 border-2 border-white rounded-full"></div>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                            <p class="text-[11px] text-gray-500 font-medium italic mt-0.5"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                                        </div>
                                    </div>
                                    <a href="manage_applications.php" class="text-[11px] font-black text-primary uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                        Review Detail →
                                    </a>
                                </div>
                                <?php
    endforeach; ?>
                            </div>
                        <?php
endif; ?>
                    </div>
                    <?php if (!empty($pending_candidates)): ?>
                    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center text-xs font-bold">
                        <a href="manage_applications.php" class="text-blue-600 hover:text-blue-700">View all <?php echo (int)$pending_count; ?> pending applications</a>
                    </div>
                    <?php
endif; ?>
                </div>

                <!-- Recent Pending Testimonies Section -->
                <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden flex flex-col min-h-[400px]">
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 leading-none">New Testimonies</h2>
                            <p class="text-xs text-gray-400 mt-2 font-medium tracking-wide uppercase">Share the Joy</p>
                        </div>
                        <a href="manage_testimonies.php" class="p-2 bg-white border border-gray-200 rounded-xl text-primary hover:bg-primary hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </a>
                    </div>

                    <div class="flex-grow">
                        <?php if (empty($pending_reviews)): ?>
                             <div class="p-16 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                </div>
                                <p class="text-gray-400 font-medium italic">No new stories yet.</p>
                             </div>
                        <?php
else: ?>
                            <div class="divide-y divide-gray-50">
                                <?php foreach ($pending_reviews as $review): ?>
                                <div class="px-8 py-5 hover:bg-gray-50/50 transition-colors flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <?php $img = !empty($review['image1']) ? $review['image1'] : 'https://via.placeholder.com/100?text=None'; ?>
                                            <img src="<?php echo htmlspecialchars($img); ?>" class="w-16 h-10 rounded-xl object-cover ring-2 ring-white shadow-md">
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($review['name']); ?></h4>
                                            <p class="text-[11px] text-gray-400 font-medium mt-0.5 line-clamp-1 max-w-[150px]">New story submission</p>
                                        </div>
                                    </div>
                                    <a href="manage_testimonies.php" class="text-[11px] font-black text-primary uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-all transform translate-x-2 group-hover:translate-x-0">
                                        Moderate →
                                    </a>
                                </div>
                                <?php
    endforeach; ?>
                            </div>
                        <?php
endif; ?>
                    </div>
                    <?php if (!empty($pending_reviews)): ?>
                    <div class="p-4 bg-gray-50 border-t border-gray-100 text-center text-xs font-bold">
                        <a href="manage_testimonies.php" class="text-blue-600 hover:text-blue-700">View all <?php echo (int)$review_pending_count; ?> pending stories</a>
                    </div>
                    <?php
endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
