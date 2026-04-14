<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: login.php");
    exit();
}

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM partner_found_reports WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_partner_found.php?success=deleted");
    exit();
}

// Fetch Reports
try {
    $stmt = $pdo->query("SELECT * FROM partner_found_reports ORDER BY created_at DESC");
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen bg-gray-50/50 pb-20">
        <!-- Header -->
        <div class="bg-white border-b border-gray-100 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between py-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Partner Found Records</h1>
                        <p class="text-sm font-medium text-gray-500 mt-1">Manage partner found submissions.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <!-- Feedback Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 'deleted'): ?>
            <div class="mb-8 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 flex items-center gap-3 animate-fade-in text-sm font-bold shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                <span>Report successfully deleted.</span>
            </div>
            <?php endif; ?>

            <?php if (empty($reports)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-[2rem] border border-dashed border-gray-200 p-16 text-center max-w-2xl mx-auto mt-12 shadow-sm">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">No Reports Yet</h3>
                <p class="text-gray-500 font-medium pb-6">There are currently no partner found records to manage.</p>
            </div>

            <?php else: ?>
            <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="p-5 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">His/Her Name</th>
                                <th class="p-5 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Partner's Name</th>
                                <th class="p-5 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Mobile Number</th>
                                <th class="p-5 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Message</th>
                                <th class="p-5 px-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <?php foreach ($reports as $report): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">

                                <td class="p-5 px-6">
                                    <h4 class="text-sm font-black text-gray-900 whitespace-nowrap"><?php echo htmlspecialchars($report['his_name']); ?></h4>
                                </td>
                                <td class="p-5 px-6">
                                    <h4 class="text-sm font-black text-primary whitespace-nowrap"><?php echo htmlspecialchars($report['partner_name']); ?></h4>
                                </td>
                                <td class="p-5 px-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center text-green-600 shrink-0 border border-green-100/50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <span class="text-sm font-bold text-gray-800 whitespace-nowrap"><?php echo htmlspecialchars($report['mobile_number']); ?></span>
                                    </div>
                                </td>
                                <td class="p-5 px-6 max-w-[300px]">
                                    <div class="bg-red-50/50 px-4 py-2.5 rounded-xl border border-red-50 relative group/msg">
                                        <span class="absolute top-1 left-2 text-2xl text-red-100 font-serif leading-none">"</span>
                                        <p class="text-[13px] font-medium text-gray-700 italic line-clamp-2 relative z-10" title="<?php echo htmlspecialchars($report['message']); ?>">
                                            <?php echo nl2br(htmlspecialchars($report['message'])); ?>
                                        </p>
                                    </div>
                                </td>
                                <td class="p-5 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="view_candidate.php?id=<?php echo $report['user_id']; ?>" class="inline-flex items-center justify-center h-10 px-4 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all border border-blue-100 shadow-sm shadow-blue-100/50 text-[10px] font-black uppercase tracking-widest" title="View Profile">
                                            View Profile
                                        </a>
                                        <a href="?delete=<?php echo $report['id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-600 hover:text-white transition-all border border-red-100 shadow-sm shadow-red-100/50" title="Delete Record">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
