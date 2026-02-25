<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/**
 * Helper function to render a simplified application table
 */
function renderApplicationTable($list, $is_pending) {
    if (empty($list)) {
        echo '<div class="p-10 text-center text-gray-400 text-xs font-bold uppercase tracking-widest leading-loose">No profiles found here</div>';
        return;
    }
    ?>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($list as $candidate): ?>
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" class="w-10 h-10 rounded-xl object-cover ring-2 ring-gray-100 group-hover:ring-primary/20 transition-all">
                            <div>
                                <h4 class="text-sm font-black text-gray-900"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase"><?php echo htmlspecialchars($candidate['district']); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-2 bg-gray-100 text-gray-600 hover:bg-primary hover:text-white rounded-xl transition-all" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            <?php if ($is_pending): ?>
                                <a href="?approve=<?php echo $candidate['id']; ?>" class="p-2 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-xl transition-all" title="Approve">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </a>
                                <a href="?reject=<?php echo $candidate['id']; ?>" class="p-2 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white rounded-xl transition-all" title="Reject">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Delete this profile permanently?')" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-xl transition-all" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
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

// Fetch Pending Candidates (Grouped by Denomination)
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'pending' AND denomination = 'Catholic' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$pending_catholic = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'pending' AND denomination = 'Christian' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$pending_christian = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Approved Candidates (Grouped by Denomination)
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'approved' AND denomination = 'Catholic' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$approved_catholic = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'approved' AND denomination = 'Christian' $search_query ORDER BY created_at DESC");
$stmt->execute($params);
$approved_christian = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                        <?php endif; ?>
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
            <?php endif; ?>

            <!-- Tabbed Navigation / Section Headers -->
            <div class="mb-10">
                <div class="flex items-center gap-4 mb-8">
                    <span class="w-2 h-8 bg-primary rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Pending Approval</h2>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Pending Catholic -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-blue-50/30 flex items-center justify-between">
                            <h3 class="font-black text-blue-900 uppercase text-xs tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                                Catholic Applications
                            </h3>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-black"><?php echo count($pending_catholic); ?> PENDING</span>
                        </div>
                        <div class="flex-grow">
                            <?php renderApplicationTable($pending_catholic, true); ?>
                        </div>
                    </div>

                    <!-- Pending Christian -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                        <div class="px-6 py-5 border-b border-gray-100 bg-purple-50/30 flex items-center justify-between">
                            <h3 class="font-black text-purple-900 uppercase text-xs tracking-widest flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></span>
                                Christian Applications
                            </h3>
                            <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-[10px] font-black"><?php echo count($pending_christian); ?> PENDING</span>
                        </div>
                        <div class="flex-grow">
                            <?php renderApplicationTable($pending_christian, true); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-10 pt-10 border-t border-gray-100">
                <div class="flex items-center gap-4 mb-8">
                    <span class="w-2 h-8 bg-green-500 rounded-full"></span>
                    <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">Live Directory</h2>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Approved Catholic -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                            <h3 class="font-black text-gray-600 uppercase text-xs tracking-widest flex items-center gap-2">
                                Catholic Members
                            </h3>
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-black border border-green-100"><?php echo count($approved_catholic); ?> ACTIVE</span>
                        </div>
                        <div class="flex-grow">
                             <?php renderApplicationTable($approved_catholic, false); ?>
                        </div>
                    </div>

                    <!-- Approved Christian -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                            <h3 class="font-black text-gray-600 uppercase text-xs tracking-widest flex items-center gap-2">
                                Christian Members
                            </h3>
                            <span class="px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-black border border-green-100"><?php echo count($approved_christian); ?> ACTIVE</span>
                        </div>
                        <div class="flex-grow">
                            <?php renderApplicationTable($approved_christian, false); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
