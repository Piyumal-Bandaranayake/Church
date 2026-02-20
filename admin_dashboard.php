<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-20 pb-20">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
                <!-- Total Users -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 flex items-center justify-between group hover:-translate-y-2 transition-all duration-500">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-blue-100">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Total Users</p>
                            <h3 class="text-4xl font-black text-gray-900 leading-none"><?php echo (int)$total_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Approved -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 flex items-center justify-between group hover:-translate-y-2 transition-all duration-500">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-green-50 text-green-600 rounded-3xl flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-green-100">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Approved</p>
                            <h3 class="text-4xl font-black text-gray-900 leading-none"><?php echo (int)$approved_count; ?></h3>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 flex items-center justify-between group hover:-translate-y-2 transition-all duration-500">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-3xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-orange-100">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Pending</p>
                            <h3 class="text-4xl font-black text-gray-900 leading-none"><?php echo (int)$pending_count; ?></h3>
                        </div>
                    </div>
                    <?php if ($pending_count > 0): ?>
                        <div class="flex h-4 w-4 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-orange-500"></span>
                        </div>
                    <?php
endif; ?>
                </div>

                <!-- Testimonies -->
                <div class="bg-white p-8 rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 flex items-center justify-between group hover:-translate-y-2 transition-all duration-500">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-3xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-all duration-500 shadow-sm shadow-purple-100">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em] mb-2">Testimonies</p>
                            <h3 class="text-4xl font-black text-gray-900 leading-none"><?php echo (int)$review_total_count; ?></h3>
                        </div>
                    </div>
                    <?php if ($review_pending_count > 0): ?>
                        <span class="px-3 py-1.5 bg-orange-100 text-orange-600 text-[11px] font-black rounded-xl ring-2 ring-white shadow-sm">+<?php echo (int)$review_pending_count; ?> New</span>
                    <?php
endif; ?>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-8 p-4 rounded-xl flex items-center gap-3 animate-fade-in text-sm <?php echo(strpos($_GET['success'], 'approved') !== false ? 'bg-green-50 text-green-700 border border-green-200' : (strpos($_GET['success'], 'deleted') !== false ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-orange-50 text-orange-700 border border-orange-200')); ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Action successful: <strong><?php echo htmlspecialchars(str_replace('_', ' ', $_GET['success'])); ?></strong>.</span>
                </div>
            <?php
endif; ?>

            <!-- Recent Activity Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
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
