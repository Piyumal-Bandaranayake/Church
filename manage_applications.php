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
    header("Location: manage_applications.php?success=approved");
    exit();
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $sql = "UPDATE candidates SET status = 'rejected' WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: manage_applications.php?success=rejected");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT photo_path FROM candidates WHERE id = ?");
    $stmt->execute([$id]);
    $photo = $stmt->fetchColumn();
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }

    $sql = "DELETE FROM candidates WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    header("Location: manage_applications.php?success=deleted");
    exit();
}

// Search Logic
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = "";
$params = [];

if ($search !== '') {
    $search_query = " AND (fullname LIKE ? OR email LIKE ? OR occupation LIKE ? OR my_phone LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%", "%$search%"];
}

// Fetch Pending Candidates
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'pending' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$pending_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Approved Candidates
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'approved' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$approved_candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-[#f8fafc]">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Manage Applications</h1>
                    <p class="text-gray-500 mt-2">Approve, reject, or manage marriage candidate profiles.</p>
                </div>
                
                <!-- Search Bar -->
                <div class="w-full md:w-96">
                    <form action="" method="GET" class="relative group">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, email, or phone..." 
                               class="w-full pl-12 pr-4 py-3.5 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all shadow-sm group-hover:shadow-md">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                        <?php if ($search): ?>
                            <a href="manage_applications.php" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
                        <?php
endif; ?>
                    </form>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-6 p-4 rounded-xl <?php
    echo(strpos($_GET['success'], 'approved') !== false ? 'bg-green-50 text-green-700 border border-green-200' :
        (strpos($_GET['success'], 'deleted') !== false ? 'bg-red-50 text-red-700 border border-red-200' :
        'bg-orange-50 text-orange-700 border border-orange-200'));
?> flex items-center gap-3 animate-fade-in text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    <span>Action successful: <strong><?php echo str_replace('_', ' ', $_GET['success']); ?></strong>.</span>
                </div>
            <?php
endif; ?>

            <!-- Pending Applications List -->
            <div id="pending-applications" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        Pending Applications
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary text-white"><?php echo count($pending_candidates); ?></span>
                    </h2>
                </div>

                <?php if (empty($pending_candidates)): ?>
                     <div class="p-12 text-center text-gray-500 italic">
                        <p>No pending applications to review.</p>
                     </div>
                <?php
else: ?>
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
                                <?php
    endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
endif; ?>
            </div>

            <!-- Approved Candidates Directory -->
            <div id="approved-directory" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
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
                <?php
else: ?>
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
                                <?php
    endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php
endif; ?>
            </div>
        </div>
    </main>
</div>
</body>
</html>
