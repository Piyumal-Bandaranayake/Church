<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$denomination_filter = 'Christian';

// Handle Actions
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
    header("Location: approved_christian.php?success=deleted");
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
$civil_status = $_GET['civil_status'] ?? '';
$education = $_GET['education'] ?? '';
$sort = $_GET['sort'] ?? 'latest';

// Build Dynamic Query
$query = "SELECT * FROM candidates WHERE status = 'approved' AND denomination = ?";
$params = [$denomination_filter];

if ($search) {
    $query .= " AND (fullname LIKE ? OR hometown LIKE ? OR occupation LIKE ? OR church LIKE ? OR district LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
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

if ($civil_status) {
    $query .= " AND marital_status = ?";
    $params[] = $civil_status;
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
$approved = $stmt->fetchAll(PDO::FETCH_ASSOC);

function renderTable($list) {
    if (empty($list)) {
        echo '<div class="p-20 text-center bg-white rounded-3xl border border-dashed border-gray-200 shadow-sm">';
        echo '<div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>';
        echo '<p class="text-gray-400 font-black uppercase tracking-widest text-[10px]">No approved Christian members found</p>';
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
                                        <span class="text-[9px] font-black text-green-600 uppercase tracking-tight bg-green-50 px-2 py-0.5 rounded"><?php echo htmlspecialchars($candidate['district']); ?></span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tight bg-gray-100 px-2 py-0.5 rounded"><?php echo $candidate['age']; ?> Yrs</span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase tracking-tight bg-gray-100 px-2 py-0.5 rounded"><?php echo $candidate['height']; ?> Ft</span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight italic"><?php echo htmlspecialchars($candidate['occupation']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-end gap-2 text-white">
                                <a href="view_candidate.php?id=<?php echo $candidate['id']; ?>" class="p-3 bg-white text-gray-400 hover:text-primary hover:bg-primary/5 border border-gray-100 rounded-2xl transition-all shadow-sm" title="View Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                </a>
                                <a href="?delete=<?php echo $candidate['id']; ?>" onclick="return confirm('Permanently delete this approved profile?')" class="p-3 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl transition-all shadow-sm" title="Delete">
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
$active_page = 'approved_christian';
include 'includes/admin_sidebar.php'; 
?>

<div class="sm:ml-64">
    <main class="min-h-screen py-10 px-8 bg-[#f8fafc]">
        <div class="max-w-6xl mx-auto">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest rounded-full">Directory</span>
                        <h1 class="text-4xl font-black text-gray-900 tracking-tighter">Christian Members</h1>
                    </div>
                    <p class="text-gray-500 font-medium italic">Advanced filtering for active Christian members.</p>
                </div>
            </div>

            <!-- Advanced Filtering Section -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-12">
                <form action="" method="GET" class="space-y-8">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-50 pb-6">
                        <div class="flex-grow max-w-md">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </span>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, hometown or job..." class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                            </div>
                        </div>
                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sort:</label>
                                <select name="sort" onchange="this.form.submit()" class="bg-gray-50 border-none rounded-xl text-xs font-bold px-4 py-2 outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                                    <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest</option>
                                    <option value="age_asc" <?php echo $sort === 'age_asc' ? 'selected' : ''; ?>>Age ↑</option>
                                    <option value="age_desc" <?php echo $sort === 'age_desc' ? 'selected' : ''; ?>>Age ↓</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Age Filter -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Age Range</label>
                            <div class="flex gap-2">
                                <input type="number" name="age_min" value="<?php echo htmlspecialchars($age_min); ?>" placeholder="Min" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                                <input type="number" name="age_max" value="<?php echo htmlspecialchars($age_max); ?>" placeholder="Max" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>

                        <!-- District Filter -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">District</label>
                            <select name="district" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="">All Districts</option>
                                <?php 
                                $districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
                                foreach($districts as $d): ?>
                                    <option value="<?php echo $d; ?>" <?php echo $district === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Height -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Height (Ft)</label>
                            <div class="flex gap-2">
                                <input type="number" name="height_min" step="0.1" value="<?php echo htmlspecialchars($height_min); ?>" placeholder="Min" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                                <input type="number" name="height_max" step="0.1" value="<?php echo htmlspecialchars($height_max); ?>" placeholder="Max" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            </div>
                        </div>

                        <!-- Job Category -->
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Job Category</label>
                            <select name="job" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="">Any Job</option>
                                <option value="Govt" <?php echo $job === 'Govt' ? 'selected' : ''; ?>>Government</option>
                                <option value="Business" <?php echo $job === 'Business' ? 'selected' : ''; ?>>Business</option>
                                <option value="Student" <?php echo $job === 'Student' ? 'selected' : ''; ?>>Student</option>
                                <option value="Unemployed" <?php echo $job === 'Unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                                <option value="Ministry" <?php echo $job === 'Ministry' ? 'selected' : ''; ?>>Ministry</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <a href="approved_christian.php" class="px-6 py-3 bg-gray-100 text-gray-500 rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-gray-200 transition-all">Clear</a>
                        <button type="submit" class="px-8 py-3 bg-primary text-white rounded-2xl text-xs font-bold uppercase tracking-widest hover:bg-primary-hover shadow-lg shadow-primary/20 transition-all">Apply Filters</button>
                    </div>
                </form>
            </div>

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
                            <span class="w-1.5 h-6 bg-green-500 rounded-full"></span>
                            Filtered Directory
                        </h2>
                        <span class="px-4 py-1.5 bg-green-500 text-white rounded-full text-[10px] font-black shadow-lg shadow-green-200"><?php echo count($approved); ?> ACTIVE</span>
                    </div>
                    <?php renderTable($approved); ?>
                </section>
            </div>

        </div>
    </main>
</div>
<?php include 'includes/admin_image_modal.php'; ?>
</body>
</html>
