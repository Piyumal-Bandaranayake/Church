<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Admin User Deletion
if (isset($_GET['delete_admin'])) {
    $id = $_GET['delete_admin'];
    
    // Security check: Admins cannot delete their own account
    if ($id != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        
        header("Location: admin_dashboard.php?success=admin_deleted");
        exit();
    }
}

// Handle Direct Deletions from Dashboard
if (isset($_GET['delete_found'])) {
    $id = $_GET['delete_found'];
    
    // Get paths to delete files
    $stmt = $pdo->prepare("SELECT photo_path, payment_slip_path FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($res) {
        if ($res['photo_path'] && file_exists($res['photo_path'])) {
            unlink($res['photo_path']);
        }
        if ($res['payment_slip_path'] && file_exists($res['payment_slip_path'])) {
            unlink($res['payment_slip_path']);
        }
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

    // Fetch All Administrators
    $admin_list_stmt = $pdo->query("SELECT id, username, email, created_at FROM admins ORDER BY id ASC");
    $all_admins = $admin_list_stmt->fetchAll(PDO::FETCH_ASSOC);
    $admin_total_count = count($all_admins);
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
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 mb-12">
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



                <!-- Testimonies -->
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex flex-col items-center text-center group hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Stories</p>
                    <h3 class="text-2xl font-black text-gray-900"><?php echo (int)$review_total_count; ?></h3>
                </div>
            </div>

            <!-- Feedback Messages Popup -->
            <?php if (isset($_GET['success'])): ?>
                <?php
                // Determine message and styling
                $is_deleted = strpos($_GET['success'], 'deleted') !== false || $_GET['success'] == 'profile_removed_after_partner_found';
                $is_approved = strpos($_GET['success'], 'approved') !== false || $_GET['success'] == 'admin_created';
                
                $msg_text = "Action successful";
                if ($_GET['success'] == 'admin_deleted') {
                    $msg_text = "Administrator account securely deleted.";
                } elseif ($_GET['success'] == 'admin_created') {
                    $msg_text = "New administrator account created successfully.";
                } elseif ($_GET['success'] == 'profile_removed_after_partner_found') {
                    $msg_text = "Candidate profile successfully removed.";
                }

                $bg_class = $is_approved ? 'bg-green-50 border-green-200 text-green-700 shadow-green-100/50' : 
                            ($is_deleted ? 'bg-red-50 border-red-200 text-red-700 shadow-red-100/50' : 
                            'bg-orange-50 border-orange-200 text-orange-700 shadow-orange-100/50');
                ?>
                <div id="toast-success" class="fixed bottom-8 right-8 z-[100] flex items-center p-4 mb-4 text-sm font-bold border rounded-2xl shadow-xl <?php echo $bg_class; ?> animate-fade-in-up" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span><?php echo $msg_text; ?></span>
                    <button type="button" class="ml-auto -mx-1.5 -my-1.5 ml-4 rounded-xl focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-black/5 inline-flex items-center justify-center h-8 w-8 text-current transition-colors" data-dismiss-target="#toast-success" aria-label="Close" onclick="document.getElementById('toast-success').remove()">
                        <span class="sr-only">Close</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <!-- Auto-hide script -->
                <script>
                    setTimeout(() => {
                        const toast = document.getElementById('toast-success');
                        if (toast) {
                            toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                            setTimeout(() => toast.remove(), 500);
                        }
                    }, 4000);
                </script>
            <?php endif; ?>

            <!-- Partner Found Notifications Section -->

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

            <!-- System Administrators Section -->
            <div class="mt-12">
                <div class="flex items-center gap-3 mb-8 px-2">
                    <span class="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                    <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter">System Administrators</h2>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-[10px] font-black rounded-full"><?php echo $admin_total_count; ?> TOTAL</span>
                    <a href="create_admin.php" class="ml-auto flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-lg shadow-primary/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add New Admin
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($all_admins as $admin): ?>
                    <div class="bg-white p-6 rounded-[2.5rem] shadow-xl shadow-gray-200/30 border border-gray-100 group hover:-translate-y-1 transition-all">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xl border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                <?php echo strtoupper(substr($admin['username'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="font-black text-gray-900 leading-tight"><?php echo htmlspecialchars($admin['username']); ?></h4>
                                <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest">Administrator</span>
                            </div>
                            <?php if ($admin['id'] != $_SESSION['user_id']): ?>
                            <a href="?delete_admin=<?php echo $admin['id']; ?>" onclick="return confirm('WARNING: Are you sure you want to delete this administrator account?')" class="ml-auto w-8 h-8 flex items-center justify-center rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all border border-red-100 shadow-sm shadow-red-100/50" title="Delete Admin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2 text-gray-500">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 01-2 2v10a2 2 0 012 2z"/></svg>
                                <span class="text-xs font-medium truncate"><?php echo htmlspecialchars($admin['email']); ?></span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                                <span class="text-[10px] uppercase font-bold tracking-tight">Joined <?php echo date('M Y', strtotime($admin['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
