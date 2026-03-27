<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$denomination_filter = 'Catholic';

// Handle Actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $sql = "UPDATE candidates SET status = 'approved' WHERE id = ? AND denomination = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $denomination_filter]);
    header("Location: divorced_catholic.php?success=approved");
    exit();
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $sql = "UPDATE candidates SET status = 'rejected' WHERE id = ? AND denomination = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $denomination_filter]);
    header("Location: divorced_catholic.php?success=rejected");
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
    header("Location: divorced_catholic.php?success=deleted");
    exit();
}

// Advanced Filter Variables
$search = $_GET['search'] ?? '';
$age_min = $_GET['age_min'] ?? '';
$age_max = $_GET['age_max'] ?? '';
$district = $_GET['district'] ?? '';
$height_min = $_GET['height_min'] ?? '';
$height_max = $_GET['height_max'] ?? '';
$job = $_GET['job'] ?? '';
$church = $_GET['church'] ?? '';
$education = $_GET['education'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// Build Dynamic Query
$query = "SELECT * FROM candidates WHERE status = 'pending' AND denomination = ? AND marital_status = 'Divorced'";
$params = [$denomination_filter];

if ($search) {
    $query .= " AND (fullname LIKE ? OR hometown LIKE ? OR occupation LIKE ? OR church LIKE ? OR district LIKE ? OR reg_number LIKE ? OR email LIKE ?)";
    $term = "%$search%";
    $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term; $params[] = $term;
}

if ($age_min !== '' && $age_max !== '') {
    $query .= " AND age BETWEEN ? AND ?";
    $params[] = $age_min;
    $params[] = $age_max;
} elseif ($age_min !== '') {
    $query .= " AND age >= ?";
    $params[] = $age_min;
} elseif ($age_max !== '') {
    $query .= " AND age <= ?";
    $params[] = $age_max;
}

if ($district) {
    $query .= " AND district = ?";
    $params[] = $district;
}

if ($height_min !== '' && $height_max !== '') {
    $query .= " AND CAST(height AS DECIMAL(10,2)) BETWEEN ? AND ?";
    $params[] = $height_min;
    $params[] = $height_max;
} elseif ($height_min !== '') {
    $query .= " AND CAST(height AS DECIMAL(10,2)) >= ?";
    $params[] = $height_min;
} elseif ($height_max !== '') {
    $query .= " AND CAST(height AS DECIMAL(10,2)) <= ?";
    $params[] = $height_max;
}

if ($job) {
    $query .= " AND occupation LIKE ?";
    $params[] = "%$job%";
}

if ($church) {
    $query .= " AND church LIKE ?";
    $params[] = "%$church%";
}

if ($education) {
    $query .= " AND edu_qual LIKE ?";
    $params[] = "%$education%";
}

if ($sort === 'age_asc') {
    $query .= " ORDER BY age ASC";
} elseif ($sort === 'age_desc') {
    $query .= " ORDER BY age DESC";
} else {
    $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderTable($list) {
    if (empty($list)) {
        echo '<div class="p-20 text-center bg-white rounded-3xl border border-dashed border-gray-200 shadow-sm">';
        echo '<div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>';
        echo '<p class="text-gray-400 font-black uppercase tracking-widest text-[10px]">No divorced Catholic applications found</p>';
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
                                <img src="<?php echo htmlspecialchars($img); ?>" class="w-14 h-14 rounded-2xl object-cover ring-4 ring-gray-50 group-hover:ring-primary/10 transition-all shadow-sm cursor-pointer" onclick="openImageModal('<?php echo htmlspecialchars($img); ?>')">
                                <div>
                                    <h4 class="text-base font-black text-gray-900 line-clamp-1"><?php echo htmlspecialchars($candidate['fullname']); ?></h4>
                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                        <?php if (!empty($candidate['reg_number'])): ?>
                                            <span class="text-[9px] font-black text-primary uppercase tracking-tight bg-primary/5 px-2 py-0.5 rounded border border-primary/10">#<?php echo htmlspecialchars($candidate['reg_number']); ?></span>
                                        <?php endif; ?>
                                        <span class="text-[9px] font-black text-orange-600 uppercase tracking-tight bg-orange-50 px-2 py-0.5 rounded"><?php echo htmlspecialchars($candidate['district']); ?></span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tight bg-gray-100 px-2 py-0.5 rounded"><?php echo $candidate['age']; ?> Yrs</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tight bg-gray-100 px-2 py-0.5 rounded"><?php echo $candidate['height']; ?> Ft</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight italic"><?php echo htmlspecialchars($candidate['occupation']); ?></span>
                                        <span class="text-[9px] font-black text-red-600 uppercase tracking-tight bg-red-50 px-2 py-0.5 rounded">Divorced</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-end gap-2 text-white">
                                <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-3 bg-white text-gray-400 hover:text-primary hover:bg-primary/5 border border-gray-100 rounded-2xl transition-all shadow-sm" title="View Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="admin_edit_profile.php?id=<?php echo $candidate['id']; ?>" class="p-3 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Edit Profile">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </a>
                                <a href="?approve=<?php echo $candidate['id']; ?>" class="p-3 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Approve">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </a>
                                <a href="?reject=<?php echo $candidate['id']; ?>" class="p-3 bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Reject">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </a>
                                <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Delete this application?')" class="p-3 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Delete">
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
$active_page = 'divorced_catholic';
include 'includes/admin_sidebar.php'; 
?>

<div class="sm:ml-64">
    <main class="min-h-screen py-10 px-8 bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-black uppercase tracking-widest rounded-full">Divorced Section</span>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Catholic Divorced</h1>
                    </div>
                    <p class="text-gray-500 font-medium italic">Review applications from divorced Catholic members.</p>
                </div>
            </div>

            <!-- Compact Filtering Bar -->
            <div class="mb-8 reveal reveal-up">
                <form action="" method="GET" id="filterForm" class="space-y-4">
                    <!-- Main Search & Sort Row -->
                    <div class="bg-white p-4 rounded-3xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-center gap-4">
                        <!-- Search -->
                        <div class="flex-grow w-full md:w-auto relative group">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-primary transition-colors">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, hometown, job..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-primary/10 outline-none transition-all">
                        </div>

                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <!-- Advanced Filter Toggle -->
                            <button type="button" onclick="toggleFilters()" class="flex-grow md:flex-none flex items-center justify-center gap-2 px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold text-xs rounded-2xl transition-all border border-transparent hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                Filters
                                <?php 
                                $active_filters = 0;
                                if($age_min || $age_max) $active_filters++;
                                if($district) $active_filters++;
                                if($height_min || $height_max) $active_filters++;
                                if($church) $active_filters++;
                                if($education) $active_filters++;
                                if($active_filters > 0): ?>
                                    <span class="bg-primary text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center"><?php echo $active_filters; ?></span>
                                <?php endif; ?>
                            </button>

                            <!-- Sort Dropdown -->
                            <select name="sort" onchange="this.form.submit()" class="flex-grow md:flex-none bg-primary text-white border-none rounded-2xl text-xs font-bold px-5 py-3 outline-none focus:ring-4 focus:ring-primary/20 cursor-pointer appearance-none shadow-lg shadow-primary/20">
                                <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest</option>
                                <option value="age_asc" <?php echo $sort === 'age_asc' ? 'selected' : ''; ?>>Age ↑</option>
                                <option value="age_desc" <?php echo $sort === 'age_desc' ? 'selected' : ''; ?>>Age ↓</option>
                            </select>
                        </div>
                    </div>

                    <!-- Expanded Filters (Hidden by Default) -->
                    <div id="advancedFilters" class="<?php echo $active_filters > 0 ? '' : 'hidden'; ?> bg-white p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 animate-slide-down">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Age Filter -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Age Range</label>
                                <div class="flex gap-2">
                                    <input type="number" name="age_min" value="<?php echo htmlspecialchars($age_min); ?>" placeholder="Min" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                    <input type="number" name="age_max" value="<?php echo htmlspecialchars($age_max); ?>" placeholder="Max" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                </div>
                            </div>

                            <!-- District Filter -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">District</label>
                                <select name="district" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                    <option value="">All Districts</option>
                                    <?php 
                                    $districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
                                    foreach($districts as $d): ?>
                                        <option value="<?php echo $d; ?>" <?php echo $district === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Height Filter -->
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Height (Ft)</label>
                                <div class="flex gap-2">
                                    <input type="number" name="height_min" step="0.1" value="<?php echo htmlspecialchars($height_min); ?>" placeholder="Min" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                    <input type="number" name="height_max" step="0.1" value="<?php echo htmlspecialchars($height_max); ?>" placeholder="Max" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-end gap-2 lg:col-span-1">
                                <button type="submit" class="flex-grow bg-primary text-white font-bold text-xs py-3 rounded-xl hover:shadow-lg transition-all">
                                    Apply Filters
                                </button>
                                <a href="divorced_catholic.php" class="p-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all group" title="Clear Filters">
                                    <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <script>
                function toggleFilters() {
                    const panel = document.getElementById('advancedFilters');
                    panel.classList.toggle('hidden');
                }
            </script>

            <?php if (isset($_GET['success'])): ?>
                <div class="mb-10 p-5 rounded-3xl bg-green-50 border border-green-100 text-green-700 flex items-center gap-4 animate-fade-in shadow-sm font-bold text-xs uppercase tracking-tight">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Action complete successfully.
                </div>
            <?php endif; ?>

            <div class="space-y-16">
                <section>
                    <div class="flex items-center justify-between mb-8 px-2">
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-tighter flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                            Divorced Catholic Waitlist
                        </h2>
                        <span class="px-4 py-1.5 bg-orange-500 text-white rounded-full text-[10px] font-black shadow-lg shadow-orange-200"><?php echo count($pending); ?> MATCHES</span>
                    </div>
                    <?php renderTable($pending); ?>
                </section>
            </div>

        </div>
    </main>
</div>
<?php include 'includes/admin_image_modal.php'; ?>
</body>
</html>
