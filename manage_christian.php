<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$denomination_filter = 'Christian';

// Handle Actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $sql = "UPDATE candidates SET status = 'approved' WHERE id = ? AND denomination = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $denomination_filter]);
    header("Location: manage_christian.php?success=approved");
    exit();
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $sql = "UPDATE candidates SET status = 'rejected' WHERE id = ? AND denomination = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $denomination_filter]);
    header("Location: manage_christian.php?success=rejected");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT photo_path FROM candidates WHERE id = ? AND denomination = ?");
    $stmt->execute([$id, $denomination_filter]);
    $photo = $stmt->fetchColumn();
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }

    $sql = "DELETE FROM candidates WHERE id = ? AND denomination = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $denomination_filter]);
    header("Location: manage_christian.php?success=deleted");
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

// Fetch Pending
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'pending' AND denomination = ? $search_query ORDER BY created_at DESC");
$stmt->execute(array_merge([$denomination_filter], $params));
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Approved
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE status = 'approved' AND denomination = ? $search_query ORDER BY created_at DESC");
$stmt->execute(array_merge([$denomination_filter], $params));
$approved = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderTable($list, $is_pending) {
    if (empty($list)) {
        echo '<div class="p-20 text-center bg-white rounded-3xl border border-dashed border-gray-200">';
        echo '<p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No records found</p>';
        echo '</div>';
        return;
    }
    ?>
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                        <th class="px-8 py-5">Candidate Details</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($list as $candidate): ?>
                    <tr class="hover:bg-gray-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-5">
                                <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/100?text=None'; ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" class="w-14 h-14 rounded-2xl object-cover ring-4 ring-gray-50 group-hover:ring-primary/10 transition-all shadow-sm">
                                <div>
                                    <h4 class="text-base font-black text-gray-900"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight bg-gray-100 px-2 py-0.5 rounded"><?php echo htmlspecialchars($candidate['district']); ?></span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tight underline italic shrink-0"><?php echo htmlspecialchars($candidate['occupation']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-end gap-2">
                                <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-3 bg-white text-gray-400 hover:text-primary hover:bg-primary/5 border border-gray-100 rounded-2xl transition-all shadow-sm" title="View Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <?php if ($is_pending): ?>
                                    <a href="?approve=<?php echo $candidate['id']; ?>" class="p-3 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Approve">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </a>
                                    <a href="?reject=<?php echo $candidate['id']; ?>" class="p-3 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Reject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </a>
                                <?php endif; ?>
                                <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Permanently delete this Christian profile?')" class="p-3 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
?>

<?php include 'includes/admin_head.php'; ?>
<?php 
$active_sub_page = 'christian';
include 'includes/admin_sidebar.php'; 
?>

<div class="sm:ml-64">
    <main class="min-h-screen py-10 px-8 bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-[10px] font-black uppercase tracking-widest rounded-full">Christian Community</span>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Christian Registrations</h1>
                    </div>
                    <p class="text-gray-500 font-medium">Manage and review all christian marriage candidate applications.</p>
                </div>
                
                <!-- Search Bar -->
                <div class="flex items-center gap-4">
                    <form action="" method="GET" class="relative group">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search Christian names..." 
                               class="w-72 pl-12 pr-4 py-3.5 bg-white border border-gray-100 rounded-[1.5rem] focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all shadow-sm group-hover:shadow-md text-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="mb-10 p-5 rounded-3xl bg-green-50 border border-green-100 text-green-700 flex items-center gap-4 animate-fade-in shadow-sm">
                    <div class="w-10 h-10 bg-green-500 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-green-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <span class="font-bold uppercase tracking-tight text-xs">Action complete: <?php echo str_replace('_', ' ', $_GET['success']); ?> successully.</span>
                </div>
            <?php endif; ?>

            <!-- Main Sections -->
            <div class="space-y-16">
                <!-- Pending -->
                <section>
                    <div class="flex items-center justify-between mb-8 px-2">
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                            Pending Christian Approvals
                        </h2>
                        <span class="text-[10px] font-black bg-purple-500 text-white px-3 py-1 rounded-full"><?php echo count($pending); ?> WAITLIST</span>
                    </div>
                    <?php renderTable($pending, true); ?>
                </section>

                <!-- Approved -->
                <section>
                    <div class="flex items-center justify-between mb-8 px-2">
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
                            Christian Directory (Approved)
                        </h2>
                        <span class="text-[10px] font-black bg-green-500 text-white px-3 py-1 rounded-full"><?php echo count($approved); ?> LIVE</span>
                    </div>
                    <?php renderTable($approved, false); ?>
                </section>
            </div>

        </div>
    </main>
</div>
</body>
</html>
