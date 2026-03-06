<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Profile Disable Approval
if (isset($_GET['approve_disable'])) {
    $id = $_GET['approve_disable'];
    $stmt = $pdo->prepare("UPDATE candidates SET is_disabled = 1, disable_requested = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_disable_requests.php?success=profile_disabled");
    exit();
}

// Handle Profile Disable Rejection
if (isset($_GET['reject_disable'])) {
    $id = $_GET['reject_disable'];
    $stmt = $pdo->prepare("UPDATE candidates SET disable_requested = 0 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_disable_requests.php?success=disable_request_rejected");
    exit();
}

// Handle Profile Deletion
if (isset($_GET['delete_profile'])) {
    $id = $_GET['delete_profile'];
    
    // Get paths to delete files if any (optional but good practice)
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
    
    header("Location: admin_disable_requests.php?success=profile_deleted");
    exit();
}

// Search
$search = trim($_GET['search'] ?? '');

// Fetch Disable Requests (with optional search)
try {
    $query = "SELECT * FROM candidates WHERE disable_requested = 1 OR is_disabled = 1";
    $params = [];

    if ($search !== '') {
        $query .= " AND (fullname LIKE ? OR email LIKE ? OR my_phone LIKE ? OR reg_number LIKE ?)";
        $like = "%$search%";
        $params = [$like, $like, $like, $like];
    }

    $query .= " ORDER BY id DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $disable_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $disable_req_count = count($disable_requests);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen pb-12">
        <!-- Header -->
        <div class="bg-primary relative overflow-hidden text-white pt-16 pb-28 px-4 sm:px-6 lg:px-8 shadow-inner">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-600/20 blur-[100px] rounded-full"></div>
            <div class="max-w-7xl mx-auto relative z-10">
                <h1 class="text-4xl font-black tracking-tight leading-none mb-3">Deactivated Profiles</h1>
                <p class="text-blue-200 text-lg font-medium">Manage all profiles that have been disabled or have pending deactivation requests.</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-20">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-50 flex items-center gap-5">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 17c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Deactivated</p>
                        <h3 class="text-2xl font-black text-gray-900"><?php echo $disable_req_count; ?></h3>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <form method="GET" action="" class="flex items-center gap-3">
                    <div class="relative flex-grow">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-gray-400 pointer-events-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search by name, email, phone or reg number..."
                            class="w-full pl-13 pr-5 py-4 bg-white border border-gray-100 rounded-2xl text-sm font-semibold text-gray-700 shadow-xl shadow-gray-200/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary/30 transition-all"
                            style="padding-left: 3rem;"
                        >
                    </div>
                    <button type="submit" class="px-6 py-4 bg-primary text-white font-black text-[11px] uppercase tracking-widest rounded-2xl hover:opacity-90 transition shadow-lg shadow-primary/20 shrink-0">
                        Search
                    </button>
                    <?php if ($search !== ''): ?>
                    <a href="admin_disable_requests.php" class="px-5 py-4 bg-white text-gray-400 font-black text-[11px] uppercase tracking-widest rounded-2xl hover:bg-gray-100 transition border border-gray-100 shadow-xl shadow-gray-200/40 shrink-0" title="Clear Search">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </a>
                    <?php endif; ?>
                </form>
                <?php if ($search !== ''): ?>
                <p class="mt-2 px-2 text-[11px] font-bold text-gray-400">
                    Showing <span class="text-gray-700"><?php echo $disable_req_count; ?></span> result(s) for &ldquo;<span class="text-primary"><?php echo htmlspecialchars($search); ?></span>&rdquo;
                </p>
                <?php endif; ?>
            </div>

            <!-- Toast Notifications -->
            <?php if (isset($_GET['success'])): ?>
                <?php
                $msg = "";
                $bg = "bg-green-50 border-green-100 text-green-700";
                if($_GET['success'] == 'profile_disabled') $msg = "Profile successfully deactivated.";
                if($_GET['success'] == 'profile_deleted') $msg = "Profile permanently deleted.";
                if($_GET['success'] == 'disable_request_rejected') {
                    $msg = "Disable request rejected.";
                    $bg = "bg-amber-50 border-amber-100 text-amber-700";
                }
                ?>
                <div class="mb-6 p-4 rounded-2xl border <?php echo $bg; ?> font-bold text-sm animate-fade-in-up">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <!-- Requests Table -->
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest">
                                <th class="px-8 py-6">Candidate</th>
                                <th class="px-8 py-6">Reg Number</th>
                                <th class="px-8 py-6">Status</th>
                                <th class="px-8 py-6">Requested On</th>
                                <th class="px-8 py-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php if (empty($disable_requests)): ?>
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                            <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        No pending disable requests found.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($disable_requests as $req): ?>
                                    <tr class="hover:bg-gray-50/30 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <img src="<?php echo !empty($req['photo_path']) ? $req['photo_path'] : 'https://via.placeholder.com/100'; ?>" class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-white">
                                                <div>
                                                    <p class="font-black text-gray-900"><?php echo htmlspecialchars($req['fullname']); ?></p>
                                                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest"><?php echo $req['denomination']; ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-[10px] font-black rounded-lg border border-gray-200 uppercase tracking-widest">
                                                <?php echo !empty($req['reg_number']) ? htmlspecialchars($req['reg_number']) : 'N/A'; ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <?php if ($req['is_disabled'] == 1): ?>
                                                <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-black rounded-full border border-red-100 uppercase tracking-widest">Disabled</span>
                                            <?php else: ?>
                                                <span class="px-3 py-1 bg-amber-50 text-amber-600 text-[10px] font-black rounded-full border border-amber-100 uppercase tracking-widest animate-pulse">Request</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-600"><?php echo date('M j, Y', strtotime($req['created_at'])); ?></span>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex items-center justify-end gap-2 text-current">
                                                <form method="POST" action="view_candidate.php?id=<?php echo $req['id']; ?>" class="flex gap-2">
                                                    <!-- View Detail -->
                                                    <a href="view_candidate.php?id=<?php echo $req['id']; ?>" class="p-3 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-2xl transition-all shadow-sm border border-blue-100" title="View Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    </a>

                                                    <?php if ($req['is_disabled'] == 1): ?>
                                                        <!-- Enable Action -->
                                                        <button name="action" value="enable" class="p-3 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-2xl transition-all shadow-sm border border-green-100" title="Reactivate Profile">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                        </button>
                                                    <?php else: ?>
                                                        <!-- Approve Disable Action -->
                                                        <a href="?approve_disable=<?php echo $req['id']; ?>" onclick="return confirm('APPROVE DISABLE: Deactivate this profile?')" class="p-3 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl transition-all shadow-sm border border-red-100" title="Approve Request">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Delete Icon -->
                                                    <a href="?delete_profile=<?php echo $req['id']; ?>" onclick="return confirm('PERMANENT DELETE?')" class="p-3 bg-gray-100 text-gray-400 hover:bg-red-600 hover:text-white rounded-2xl transition-all shadow-sm border border-gray-200" title="Delete Profile">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    </a>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</div>
</body>
</html>
