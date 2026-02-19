<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $sql = "UPDATE candidates SET status = 'approved' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=approved");
    exit();
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $sql = "UPDATE candidates SET status = 'rejected' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=rejected");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Delete file if exists
    $stmt = $pdo->prepare("SELECT photo_path FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }
    
    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=deleted");
    exit();
}

// Handle Review Actions
if (isset($_GET['approve_review'])) {
    $id = $_GET['approve_review'];
    $sql = "UPDATE reviews SET status = 'approved' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=review_approved");
    exit();
}

if (isset($_GET['reject_review'])) {
    $id = $_GET['reject_review'];
    $sql = "UPDATE reviews SET status = 'rejected' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=review_rejected");
    exit();
}

if (isset($_GET['delete_review'])) {
    $id = $_GET['delete_review'];
    // Delete files if exists
    $stmt = $pdo->prepare("SELECT image1, image2, image3, image4, image5 FROM reviews WHERE id = ?");
    $stmt->execute([$id]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($review) {
        for ($i = 1; $i <= 5; $i++) {
            $img = $review['image' . $i];
            if ($img && file_exists($img)) {
                unlink($img);
            }
        }
    }
    
    $sql = "DELETE FROM reviews WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: admin_dashboard.php?success=review_deleted");
    exit();
}

// Fetch Stats
$total_stmt = $pdo->query("SELECT COUNT(*) FROM candidates");
$total_count = $total_stmt->fetchColumn();

$approved_stmt = $pdo->query("SELECT COUNT(*) FROM candidates WHERE status = 'approved'");
$approved_count = $approved_stmt->fetchColumn();

$pending_stmt = $pdo->query("SELECT COUNT(*) FROM candidates WHERE status = 'pending'");
$pending_count = $pending_stmt->fetchColumn();

// Fetch Pending Candidates
$stmt = $pdo->query("SELECT * FROM candidates WHERE status = 'pending' ORDER BY created_at DESC");
$pending_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Approved Candidates
$stmt = $pdo->query("SELECT * FROM candidates WHERE status = 'approved' ORDER BY created_at DESC");
$approved_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Review Stats
$review_total_stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
$review_total_count = $review_total_stmt->fetchColumn();

$review_pending_stmt = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'");
$review_pending_count = $review_pending_stmt->fetchColumn();

// Fetch Pending Reviews
$stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'pending' ORDER BY created_at DESC");
$pending_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Approved Reviews
$stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC");
$approved_reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php $hide_spacer = true; include 'includes/header.php'; ?>

<main class="min-h-screen bg-[#f8fafc]">
    <!-- Dashboard Header -->
    <div class="bg-primary text-white pt-32 pb-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Admin Control Center</h1>
                    <p class="mt-2 text-blue-200">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>. Monitor and manage marriage candidate applications.</p>
                </div>
                <div class="flex gap-3">
                    <a href="create_admin.php" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-sm font-medium transition-all backdrop-blur-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                        Add Admin
                    </a>
                    <a href="admin_profile.php" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-sm font-medium transition-all backdrop-blur-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2m16-11V7a2 2 0 00-2-2H6a2 2 0 00-2 2v11a2 2 0 002 2h10a2 2 0 002-2v-3m-1 4l3-3m0 0l-3-3m3 3H9" /></svg>
                        Security Settings
                    </a>
                    <a href="logout.php" class="px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-lg text-sm font-medium transition-all backdrop-blur-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 pb-20">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Total Users</p>
                    <h3 class="text-2xl font-black text-gray-900 leading-none mt-1"><?php echo $total_count; ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Approved</p>
                    <h3 class="text-2xl font-black text-gray-900 leading-none mt-1"><?php echo $approved_count; ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Pending</p>
                    <h3 class="text-2xl font-black text-gray-900 leading-none mt-1"><?php echo $pending_count; ?></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-tight">Testimonies</p>
                    <h3 class="text-2xl font-black text-gray-900 leading-none mt-1"><?php echo $review_total_count; ?> <span class="text-xs text-orange-500">(<?php echo $review_pending_count; ?>)</span></h3>
                </div>
            </div>
        </div>

        <!-- Feedback Messages -->
        <?php if(isset($_GET['success'])): ?>
            <div class="mb-6 p-4 rounded-xl <?php 
                echo (strpos($_GET['success'], 'approved') !== false ? 'bg-green-50 text-green-700 border border-green-200' : 
                     (strpos($_GET['success'], 'deleted') !== false ? 'bg-red-50 text-red-700 border border-red-200' : 
                     'bg-orange-50 text-orange-700 border border-orange-200')); 
                     ?> flex items-center gap-3 animate-fade-in text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>Action successful: <strong><?php echo str_replace('_', ' ', $_GET['success']); ?></strong>.</span>
            </div>
        <?php endif; ?>

        <!-- Pending Applications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    Pending Applications
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-white"><?php echo count($pending_candidates); ?></span>
                </h2>
            </div>

            <?php if (empty($pending_candidates)): ?>
                 <div class="p-12 text-center">
                    <p class="text-gray-500 italic">No pending applications to review.</p>
                 </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b">
                                <th class="px-6 py-4">Candidate</th>
                                <th class="px-6 py-4">Contact</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($pending_candidates as $candidate): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <?php echo htmlspecialchars($candidate['my_phone']); ?>
                                </td>
                                <td class="px-6 py-5 text-right flex justify-end gap-2 text-white">
                                    <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-2 bg-primary text-white hover:bg-primary-hover rounded-lg transition-all shadow-sm" title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    <a href="?approve=<?php echo $candidate['id']; ?>" class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition-all" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </a>
                                    <a href="?reject=<?php echo $candidate['id']; ?>" class="p-2 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white rounded-lg transition-all" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </a>
                                    <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?')" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Approved Candidates Directory -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    Approved Directory (Current Candidates)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700"><?php echo count($approved_candidates); ?></span>
                </h2>
            </div>

            <?php if (empty($approved_candidates)): ?>
                 <div class="p-12 text-center text-gray-500">
                    <p>No approved candidates yet.</p>
                 </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b">
                                <th class="px-6 py-4">Candidate</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($approved_candidates as $candidate): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                            <p class="text-xs text-gray-400">Email: <?php echo htmlspecialchars($candidate['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-100 uppercase">Live</span>
                                </td>
                                <td class="px-6 py-5 text-right flex items-center justify-end gap-2">
                                    <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-1.5 bg-primary text-white hover:bg-primary-hover rounded-lg transition-all shadow-sm inline-flex items-center" title="View Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        <span class="ml-1 text-[10px] font-bold uppercase tracking-tighter">View</span>
                                    </a>
                                    <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Are you sure you want to delete this profile from the directory?')" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all text-xs font-bold flex items-center gap-2 inline-flex">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete Profile
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Testimonies Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    Pending Testimonies
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-white"><?php echo count($pending_reviews); ?></span>
                </h2>
            </div>

            <?php if (empty($pending_reviews)): ?>
                 <div class="p-12 text-center text-gray-500 italic">
                    <p>No pending testimonies to review.</p>
                 </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b">
                                <th class="px-6 py-4">Couple Name</th>
                                <th class="px-6 py-4">Testimony</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($pending_reviews as $review): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <?php $img = !empty($review['image1']) ? $review['image1'] : 'https://via.placeholder.com/100?text=None'; ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-10 h-10 rounded-lg object-cover">
                                        <span class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($review['name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600">
                                    <p class="line-clamp-2 max-w-md"><?php echo htmlspecialchars($review['description']); ?></p>
                                </td>
                                <td class="px-6 py-5 text-right flex justify-end gap-2">
                                    <a href="?approve_review=<?php echo $review['id']; ?>" class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg transition-all" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </a>
                                    <a href="?reject_review=<?php echo $review['id']; ?>" class="p-2 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white rounded-lg transition-all" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </a>
                                    <a href="?delete_review=<?php echo $review['id']; ?>" onclick="return confirm('Are you sure you want to delete this testimony?')" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Approved Testimonies Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    Live Testimonies
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700"><?php echo count($approved_reviews); ?></span>
                </h2>
            </div>

            <?php if (empty($approved_reviews)): ?>
                 <div class="p-12 text-center text-gray-500">
                    <p>No approved testimonies yet.</p>
                 </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500 font-semibold border-b">
                                <th class="px-6 py-4">Couple</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($approved_reviews as $review): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <?php $img = !empty($review['image1']) ? $review['image1'] : 'https://via.placeholder.com/100?text=None'; ?>
                                        <img src="<?php echo htmlspecialchars($img); ?>" class="w-10 h-10 rounded-lg object-cover">
                                        <span class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($review['name']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="px-2 py-1 bg-green-50 text-green-600 text-xs font-bold rounded-full border border-green-100 uppercase">Live</span>
                                </td>
                                <td class="px-6 py-5 text-right flex items-center justify-end gap-2">
                                    <a href="?delete_review=<?php echo $review['id']; ?>" onclick="return confirm('Delete this live testimony?')" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all text-xs font-bold flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        Delete
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
