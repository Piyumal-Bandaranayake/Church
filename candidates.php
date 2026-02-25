<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Get current user's denomination if role is candidate
$user_denomination = $_SESSION['denomination'] ?? '';
if (empty($user_denomination) && $_SESSION['role'] === 'candidate') {
    $user_stmt = $pdo->prepare("SELECT denomination FROM candidates WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user_denomination = $user_stmt->fetchColumn();
    $_SESSION['denomination'] = $user_denomination;
}

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

// Sort Parameter
$sort = $_GET['sort'] ?? 'latest';

// Build dynamic query
$query = "SELECT * FROM candidates WHERE status = 'approved'";
$params = [];

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $display_denomination = 'All';
} else {
    $query .= " AND denomination = ?";
    $params[] = $user_denomination;
    $display_denomination = $user_denomination;
}

// Search Filter
if ($search) {
    $query .= " AND (fullname LIKE ? OR hometown LIKE ? OR occupation LIKE ? OR church LIKE ? OR district LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Age Filter
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

// District Filter
if ($district) {
    $query .= " AND district = ?";
    $params[] = $district;
}

// Height Filter
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

// Job Filter
if ($job) {
    $query .= " AND occupation LIKE ?";
    $params[] = "%$job%";
}

// Church Filter
if ($church) {
    $query .= " AND church LIKE ?";
    $params[] = "%$church%";
}

// Civil Status Filter
if ($civil_status) {
    $query .= " AND marital_status = ?";
    $params[] = $civil_status;
}

// Education Filter
if ($education) {
    $query .= " AND edu_qual LIKE ?";
    $params[] = "%$education%";
}

// Ordering
if ($sort === 'age_asc') {
    $query .= " ORDER BY age ASC";
} elseif ($sort === 'age_desc') {
    $query .= " ORDER BY age DESC";
} else {
    $query .= " ORDER BY id DESC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Success/Error Message System
$review_success = isset($_GET['success']) && $_GET['success'] == 'review_submitted';
$review_error = isset($_GET['error']);
?>
<?php $hide_spacer = true;
include 'includes/header.php'; ?>

<main class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-primary pt-32 pb-24 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a2540] via-[#1a3a5a] to-[#0a2540] z-0"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] z-10"></div>
        
        <div class="relative z-20 container mx-auto px-4 mt-8 text-white">
            <h1 class="text-5xl md:text-7xl font-black mb-6 animate-fade-in tracking-tight">Find Your Perfect Life Partner</h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto font-medium opacity-90 leading-relaxed mb-8">
                Connect with faithful, like-minded individuals within our community. 
                Rooted in Christ, building lasting foundations for love.
            </p>
            
            <!-- Quick Filter Stats -->
            <div class="flex flex-wrap justify-center gap-4 text-sm font-bold reveal reveal-up delay-300">
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">✨</span> <?php echo count($candidates); ?> <?php echo $user_denomination; ?> Profiles Available
                </span>
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">🛡️</span> Verified Community
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        
        <!-- Advanced Filtering -->
        <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-gray-100 mb-12 reveal reveal-up">
            <form action="candidates.php" method="GET" id="filterForm" class="space-y-8">
                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-50 pb-6">
                    <div class="flex-grow max-w-md">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name, hometown or job..." class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sort By:</label>
                            <select name="sort" onchange="this.form.submit()" class="bg-gray-50 border-none rounded-xl text-xs font-bold px-4 py-2 outline-none focus:ring-2 focus:ring-primary/20 cursor-pointer">
                                <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest Registered</option>
                                <option value="age_asc" <?php echo $sort === 'age_asc' ? 'selected' : ''; ?>>Age: Low to High</option>
                                <option value="age_desc" <?php echo $sort === 'age_desc' ? 'selected' : ''; ?>>Age: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    
                    <!-- Age Filter -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 00-2 2z"/></svg>
                            Age Range
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="age_min" value="<?php echo htmlspecialchars($age_min); ?>" placeholder="Min Age" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <input type="number" name="age_max" value="<?php echo htmlspecialchars($age_max); ?>" placeholder="Max Age" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <!-- District Filter -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            District
                        </label>
                        <select name="district" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">All Districts</option>
                            <?php 
                            $districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
                            foreach($districts as $d): ?>
                                <option value="<?php echo $d; ?>" <?php echo $district === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                            Height (Feet)
                        </label>
                        <div class="flex gap-2">
                            <input type="number" name="height_min" step="0.1" value="<?php echo htmlspecialchars($height_min); ?>" placeholder="Min ft" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <input type="number" name="height_max" step="0.1" value="<?php echo htmlspecialchars($height_max); ?>" placeholder="Max ft" class="w-1/2 bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <!-- Job Filter -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 00-2 2z"/></svg>
                            Job Category
                        </label>
                        <select name="job" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Any Job</option>
                            <option value="Govt" <?php echo $job === 'Govt' ? 'selected' : ''; ?>>Government Agent</option>
                            <option value="Business" <?php echo $job === 'Business' ? 'selected' : ''; ?>>Business</option>
                            <option value="Student" <?php echo $job === 'Student' ? 'selected' : ''; ?>>Student</option>
                            <option value="Unemployed" <?php echo $job === 'Unemployed' ? 'selected' : ''; ?>>Unemployed</option>
                            <option value="Ministry" <?php echo $job === 'Ministry' ? 'selected' : ''; ?>>Full-time Ministry</option>
                        </select>
                    </div>

                    <!-- Church Filter -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            Church / Fellowship
                        </label>
                        <input type="text" name="church" value="<?php echo htmlspecialchars($church); ?>" placeholder="Search church name..." class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                    </div>

                    <!-- Civil Status -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Civil Status
                        </label>
                        <select name="civil_status" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Any</option>
                            <option value="Unmarried" <?php echo $civil_status === 'Unmarried' ? 'selected' : ''; ?>>Unmarried</option>
                            <option value="Divorced" <?php echo $civil_status === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                            <option value="Widowed" <?php echo $civil_status === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                        </select>
                    </div>

                    <!-- Education -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                            <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            Education
                        </label>
                        <select name="education" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">Any Level</option>
                            <option value="O/L" <?php echo $education === 'O/L' ? 'selected' : ''; ?>>O/L Qualified</option>
                            <option value="A/L" <?php echo $education === 'A/L' ? 'selected' : ''; ?>>A/L Qualified</option>
                            <option value="Degree" <?php echo $education === 'Degree' ? 'selected' : ''; ?>>Graduate / Degree</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-end gap-3 lg:col-span-1">
                        <button type="submit" class="flex-grow bg-primary text-white font-bold text-sm py-3.5 rounded-2xl hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Apply Filters
                        </button>
                        <a href="candidates.php" class="p-3.5 bg-gray-50 text-gray-400 rounded-2xl hover:bg-gray-100 transition-colors group" title="Clear Filters">
                            <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($candidates)): ?>
             <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Profiles Found</h2>
                <p class="text-gray-500 max-w-sm mx-auto">We couldn't find any approved candidates at the moment. Please check back later or modify your search.</p>
                <a href="register.php" class="mt-8 inline-block px-8 py-3 bg-primary text-white font-bold rounded-full shadow-lg shadow-primary/20 hover:scale-105 transition-transform">Create Your Profile</a>
             </div>
        <?php
else: ?>

        <!-- Profiles Display -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            
            <?php foreach ($candidates as $candidate): ?>
            <div class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-2 reveal reveal-scale">
                
                <!-- Card Header / Image -->
                <div class="relative h-64 overflow-hidden">
                    <?php
        $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/400x400?text=Profile';
?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Profile">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
                    
                    <!-- Gender Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest text-primary shadow-sm">
                            <?php echo $candidate['sex']; ?>
                        </span>
                    </div>

                    <!-- Name Overlay (Bottom Left) -->
                    <div class="absolute bottom-4 left-5 text-white">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($candidate['fullname']); ?></h3>
                        <p class="text-xs text-blue-100 flex items-center gap-1 opacity-90">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <?php echo htmlspecialchars($candidate['hometown']); ?>
                        </p>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex-grow flex flex-col">
                    <div class="grid grid-cols-1 gap-y-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Age Range</span>
                                <span class="font-bold text-gray-700 text-sm"><?php echo htmlspecialchars($candidate['age']); ?> Years</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Published On</span>
                                <span class="font-bold text-gray-700 text-sm"><?php echo date('M d, Y', strtotime($candidate['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col gap-3">
                        <a href="profile.php?id=<?php echo $candidate['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3.5 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold rounded-2xl transition-all duration-300 text-sm group/btn shadow-sm">
                            View Detailed Profile
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>


                    </div>
                </div>
            </div>
            <?php
    endforeach; ?>

        </div>
        <?php
endif; ?>
    </div>
</main>

<!-- Floating Review Button -->
<button onclick="openReviewModal()" class="fixed bottom-8 right-8 w-16 h-16 bg-blue-600 text-white rounded-full shadow-2xl hover:bg-blue-700 transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[100] group active:scale-95">
    <svg class="w-8 h-8 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
    </svg>
    <div class="absolute right-full mr-4 bg-white text-primary px-4 py-2 rounded-xl text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
        Share Your Story ✨
    </div>
</button>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 z-[110] hidden bg-primary/40 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden animate-slide-up">
        <div class="relative bg-primary p-6 text-white text-center">
            <button onclick="closeReviewModal()" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-black mb-1">Blessed Beginnings</h2>
            <p class="text-[11px] text-blue-100/80">Share your journey with our community.</p>
        </div>

        <form action="submit_review.php" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Couples Names</label>
                <input type="text" name="review_name" required placeholder="e.g. David & Mary" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-bold text-sm">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Our Testimony</label>
                <textarea name="review_description" required rows="3" placeholder="How did you meet?" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-medium text-slate-600 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 text-center">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div class="relative group">
                    <input type="file" name="review_image<?php echo $i; ?>" id="img<?php echo $i; ?>" class="hidden" accept="image/*" onchange="previewImage(this, 'preview<?php echo $i; ?>')">
                    <label for="img<?php echo $i; ?>" class="cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-2 block hover:border-blue-500 hover:bg-blue-50/30 transition-all overflow-hidden h-16 flex flex-col items-center justify-center gap-0.5">
                        <div id="preview<?php echo $i; ?>" class="absolute inset-0 hidden">
                            <img src="" class="w-full h-full object-cover">
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="text-[8px] font-black text-slate-500 uppercase">P<?php echo $i; ?></span>
                    </label>
                </div>
                <?php
endfor; ?>
            </div>

            <button type="submit" class="w-full py-3 bg-primary text-white font-black rounded-xl shadow-lg shadow-blue-900/10 hover:bg-blue-950 transition-all transform hover:-translate-y-0.5 active:scale-95 text-sm uppercase tracking-wider">
                Submit Story ✨
            </button>
        </form>
    </div>
</div>

<!-- Alert Modals -->
<?php if ($review_success): ?>
<div class="fixed top-24 left-1/2 -translate-x-1/2 z-[200] bg-green-500 text-white px-8 py-4 rounded-full shadow-2xl font-bold animate-fade-in flex items-center gap-3">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
    Thank you! Your story has been submitted for review.
</div>
<script>setTimeout(() => { window.location.href = 'candidates.php'; }, 4000);</script>
<?php
endif; ?>

<script>
    function openReviewModal() {
        const modal = document.getElementById('review-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        const modal = document.getElementById('review-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const img = preview.querySelector('img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Close modal on background click
    window.onclick = function(event) {
        const modal = document.getElementById('review-modal');
        if (event.target == modal) closeReviewModal();
    }
</script>

<?php include 'includes/footer.php'; ?>
