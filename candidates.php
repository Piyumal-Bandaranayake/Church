<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Ensure interests table exists (Auto-migration)
try {
    $pdo->query("SELECT 1 FROM interests LIMIT 1");
} catch (Exception $e) {
    include_once 'setup_db.php';
}

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

// District List
$districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];

// Sort Parameter
$sort = $_GET['sort'] ?? 'latest';

// Build dynamic query
$query = "SELECT * FROM candidates WHERE status = 'approved' AND is_disabled = 0 AND partner_found = 0";
$params = [];

// Don't show the logged-in user's own profile in the list
if (isset($_SESSION['role']) && $_SESSION['role'] === 'candidate') {
    $query .= " AND id != ?";
    $params[] = $_SESSION['user_id'];
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $denom_filter = $_GET['denomination_filter'] ?? '';
    if ($denom_filter) {
        $query .= " AND denomination = ?";
        $params[] = $denom_filter;
        $display_denomination = $denom_filter;
    } else {
        $display_denomination = 'All';
    }
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

// Fetch candidate's interests and own details if logged in as candidate
$my_interests = [];
$my_details = null;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'candidate') {
    $int_stmt = $pdo->prepare("SELECT receiver_id FROM interests WHERE sender_id = ?");
    $int_stmt->execute([$_SESSION['user_id']]);
    $my_interests = $int_stmt->fetchAll(PDO::FETCH_COLUMN);

    $me_stmt = $pdo->prepare("SELECT reg_number, fullname FROM candidates WHERE id = ?");
    $me_stmt->execute([$_SESSION['user_id']]);
    $my_details = $me_stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<?php $hide_spacer = true;
include 'includes/header.php'; ?>

<main class="min-h-screen">
    <!-- Hero Section -->
    <div class="bg-primary pt-32 pb-24 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a2540] via-[#1a3a5a] to-[#0a2540] z-0"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] z-10"></div>
        
        <div class="relative z-20 container mx-auto px-4 mt-8 text-white">
            <div class="max-w-4xl mx-auto text-center">
                <div id="hero-text-container" class="transition-opacity duration-500 opacity-100 min-h-[160px] flex flex-col items-center justify-center">
                    <h1 id="hero-title" class="text-4xl md:text-7xl font-black mb-4 tracking-tight">
                        Find Your Perfect Life Partner
                    </h1>
                    <p id="hero-subtitle" class="text-lg md:text-xl text-blue-100 font-medium max-w-2xl mx-auto leading-relaxed">
                        Connect with faithful, like-minded individuals within our community. Rooted in Christ, building lasting foundations for love.
                    </p>
                </div>
            </div>
            
            <!-- Quick Filter Stats -->
            <div class="flex flex-wrap justify-center gap-4 text-sm font-bold reveal reveal-up delay-300 mt-12 active">
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">✨</span> <?php echo count($candidates); ?> <?php echo $user_denomination; ?> Profiles Available
                </span>
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">🛡️</span> Verified Community
                </span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const heroContainer = document.getElementById('hero-text-container');
            const heroTitle = document.getElementById('hero-title');
            const heroSubtitle = document.getElementById('hero-subtitle');
            
            const texts = [
                {
                    title: 'Find Your Perfect Life Partner',
                    subtitle: 'Connect with faithful, like-minded individuals within our community. Rooted in Christ, building lasting foundations for love.'
                },
                {
                    title: 'ඔබට ගැළපෙනම සහකරු හෝ සහකාරිය සොයාගන්න',
                    subtitle: 'අපගේ ප්‍රජාව තුළ සිටින විශ්වාසවන්ත, සමාන සිතිවිලි ඇති පුද්ගලයින් සමඟ සම්බන්ධ වන්න. ක්‍රිස්තුස් වහන්සේ තුළ මුල් බැසගත්, ආදරය සඳහා ස්ථාවර අඩිතාලමක් ගොඩනඟන්න.'
                }
            ];
            
            let currentTextIndex = 0;
            
            setInterval(() => {
                // Fade out
                heroContainer.classList.replace('opacity-100', 'opacity-0');
                
                setTimeout(() => {
                    currentTextIndex = (currentTextIndex + 1) % texts.length;
                    heroTitle.innerText = texts[currentTextIndex].title;
                    heroSubtitle.innerText = texts[currentTextIndex].subtitle;
                    
                    // Change font for Sinhala if needed (matches index.php style)
                    if (currentTextIndex === 1) {
                        heroContainer.style.fontFamily = "'Sinhala-UN-Gurulugomi', sans-serif";
                    } else {
                        heroContainer.style.fontFamily = "";
                    }
                    
                    // Fade in
                    heroContainer.classList.replace('opacity-0', 'opacity-100');
                }, 500); 
                
            }, 5000); 
        });
    </script>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        
        <!-- Premium Search & Filter Bar -->
        <div class="mb-12 reveal reveal-up">
            <form action="candidates.php" method="GET" id="filterForm" class="space-y-6">
                <?php 
                // Calculate active filters
                $active_filters = 0;
                if(!empty($age_min) || !empty($age_max)) $active_filters++;
                if(!empty($district)) $active_filters++;
                if(!empty($height_min) || !empty($height_max)) $active_filters++;
                if(!empty($job)) $active_filters++;
                if(!empty($church)) $active_filters++;
                if(!empty($civil_status)) $active_filters++;
                if(!empty($education)) $active_filters++;
                ?>
                <!-- Main Control Bar -->
                <div class="bg-white/80 backdrop-blur-xl p-5 rounded-[2.5rem] shadow-2xl shadow-blue-900/5 border border-white flex flex-col lg:flex-row items-center gap-5">
                    <!-- Search Input -->
                    <div class="flex-grow w-full lg:w-auto relative group">
                        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400 group-focus-within:text-primary transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Search name, hometown, job... (නම, නගරය හෝ රැකියාව අනුව සොයන්න)" 
                               class="w-full pl-14 pr-6 py-4 bg-slate-50/50 border border-slate-100 rounded-3xl text-sm font-bold text-slate-700 placeholder:text-slate-400 focus:ring-4 focus:ring-primary/5 focus:bg-white focus:border-primary/20 outline-none transition-all duration-300">
                    </div>

                    <!-- Denomination Filter (Admin Only) -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="w-full lg:w-48 relative group">
                        <select name="denomination_filter" onchange="this.form.submit()" 
                                class="w-full appearance-none bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-xs font-black text-slate-600 uppercase tracking-wider focus:ring-4 focus:ring-primary/5 outline-none cursor-pointer group-hover:bg-white transition-all">
                            <option value="">All Denominations (සියලුම නිකායන්)</option>
                            <option value="Catholic" <?php echo ($_GET['denomination_filter'] ?? '') === 'Catholic' ? 'selected' : ''; ?>>Catholic (කතෝලික)</option>
                            <option value="Christian" <?php echo ($_GET['denomination_filter'] ?? '') === 'Christian' ? 'selected' : ''; ?>>Christian (ක්‍රිස්තියානි)</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover:text-primary transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                        <!-- Quick Sort -->
                        <div class="relative w-full sm:w-48 group">
                            <select name="sort" onchange="this.form.submit()" 
                                    class="w-full appearance-none bg-slate-50 border border-slate-100 rounded-2xl px-5 py-4 text-xs font-black text-slate-600 uppercase tracking-wider focus:ring-4 focus:ring-primary/5 outline-none cursor-pointer group-hover:bg-white transition-all">
                                <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest (අලුත්ම)</option>
                                <option value="age_asc" <?php echo $sort === 'age_asc' ? 'selected' : ''; ?>>Age ↑ (වයස අඩු සිට)</option>
                                <option value="age_desc" <?php echo $sort === 'age_desc' ? 'selected' : ''; ?>>Age ↓ (වයස වැඩි සිට)</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-hover:text-primary transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Advanced Toggle -->
                        <button type="button" onclick="toggleFilters()" 
                                class="flex-grow sm:flex-grow-0 flex items-center justify-center gap-3 px-8 py-4 bg-primary text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-blue-900 transition-all duration-300 shadow-xl shadow-primary/20 group relative overflow-hidden">
                            <div class="absolute inset-0 bg-white/10 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                            <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                            <span class="relative z-10">Advanced Filters (සහකරු/සහකාරිය තෝරාගන්න)</span>
                            <?php if($active_filters > 0): ?>
                                <span class="relative z-10 flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-white"></span>
                                </span>
                            <?php endif; ?>
                        </button>
                    </div>
                </div>

                <!-- Expanded Advanced Filter Panel -->
                <div id="advancedFilters" class="<?php echo $active_filters > 0 ? '' : 'hidden'; ?> overflow-hidden transition-all duration-500">
                    <div class="bg-white p-10 rounded-[3rem] shadow-2xl border border-slate-100/50">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                            
                            <!-- Group 1: Personal Attributes -->
                            <div class="space-y-6 p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest leading-none">Attributes (විස්තර)</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Age Range (වයස පරාසය)</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="age_min" value="<?php echo htmlspecialchars($age_min); ?>" placeholder="Min" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                            <input type="number" name="age_max" value="<?php echo htmlspecialchars($age_max); ?>" placeholder="Max" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Height (Ft) (උස)</label>
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="number" name="height_min" step="0.1" value="<?php echo htmlspecialchars($height_min); ?>" placeholder="Min" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                            <input type="number" name="height_max" step="0.1" value="<?php echo htmlspecialchars($height_max); ?>" placeholder="Max" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Location & Faith -->
                            <div class="space-y-6 p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest leading-none">Spirituality (ආගමික පසුබිම)</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">District (දිස්ත්‍රික්කය)</label>
                                        <select name="district" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 cursor-pointer text-slate-700">
                                            <option value="">All Districts (සියලුම)</option>
                                            <?php 
                                            foreach($districts as $d): ?>
                                                <option value="<?php echo $d; ?>" <?php echo $district === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Church Name (දේවස්ථානයේ නම)</label>
                                        <input type="text" name="church" value="<?php echo htmlspecialchars($church); ?>" placeholder="Search church..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Education & Status -->
                            <div class="space-y-6 p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest leading-none">Background (පසුබිම)</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Education (අධ්‍යාපනය)</label>
                                        <select name="education" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                            <option value="">Any Level (සියලුම අධ්‍යාපන මට්ටම්)</option>
                                            <option value="O/L" <?php echo $education === 'O/L' ? 'selected' : ''; ?>>O/L Qualified (සා.පෙළ)</option>
                                            <option value="A/L" <?php echo $education === 'A/L' ? 'selected' : ''; ?>>A/L Qualified (උ.පෙළ)</option>
                                            <option value="Degree" <?php echo $education === 'Degree' ? 'selected' : ''; ?>>Graduate / Degree (උපාධිධාරී)</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Civil Status (විවාහක/අවිවාහක බව)</label>
                                        <select name="civil_status" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                            <option value="">Any Status (සියල්ල)</option>
                                            <option value="Unmarried" <?php echo $civil_status === 'Unmarried' ? 'selected' : ''; ?>>Unmarried (අවිවාහක)</option>
                                            <option value="Divorced" <?php echo $civil_status === 'Divorced' ? 'selected' : ''; ?>>Divorced (දික්කසාද)</option>
                                            <option value="Widowed" <?php echo $civil_status === 'Widowed' ? 'selected' : ''; ?>>Widowed (වැන්දඹු)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Professional -->
                            <div class="space-y-6 p-6 rounded-[2rem] bg-slate-50/50 border border-slate-100">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-700 uppercase tracking-widest leading-none">Career (වෘත්තීය විස්තර)</h4>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-1">Occupation (රැකියාව)</label>
                                        <select name="job" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold outline-none focus:ring-4 focus:ring-primary/5 text-slate-700">
                                            <option value="">All Occupations (සියලුම රැකියා)</option>
                                            <optgroup label="Common (පොදු)">
                                                <option value="Teacher" <?php echo $job === 'Teacher' ? 'selected' : ''; ?>>Teacher (ගුරු)</option>
                                                <option value="Doctor" <?php echo $job === 'Doctor' ? 'selected' : ''; ?>>Doctor (වෛද්‍ය)</option>
                                                <option value="Nurse" <?php echo $job === 'Nurse' ? 'selected' : ''; ?>>Nurse (හෙද)</option>
                                                <option value="Engineer" <?php echo $job === 'Engineer' ? 'selected' : ''; ?>>Engineer (ඉංජිනේරු)</option>
                                                <option value="Software Developer" <?php echo $job === 'Software Developer' ? 'selected' : ''; ?>>Software Developer (මෘදුකාංග ඉංජිනේරු)</option>
                                                <option value="Businessman / Businesswoman" <?php echo $job === 'Businessman / Businesswoman' ? 'selected' : ''; ?>>Businessman (ව්‍යාපාරික)</option>
                                            </optgroup>
                                            <option value="Other">Other (වෙනත්)</option>
                                        </select>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-medium italic pl-1 leading-relaxed">
                                        Tip: You can also search for jobs in the main search bar above. (ඔබට ප්‍රධාන සෙවුම් කොටුව හරහාද රැකියාවන් සෙවිය හැක.)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Actions -->
                        <div class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6 pt-10 border-t border-slate-100">
                             <div class="flex items-center gap-4">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Active Filters (සක්‍රිය කර ඇති පෙරහන්): <?php echo $active_filters; ?></span>
                                <?php if($active_filters > 0): ?>
                                <a href="candidates.php" class="text-[10px] font-black text-red-500 uppercase tracking-widest hover:text-red-700 transition-colors flex items-center gap-2">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reset All (සියල්ල ඉවත් කරන්න)
                                </a>
                                <?php endif; ?>
                             </div>
                             
                             <div class="flex items-center gap-3 w-full sm:w-auto">
                                <button type="button" onclick="toggleFilters()" class="flex-grow sm:flex-grow-0 px-8 py-4 bg-slate-100 text-slate-600 font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-slate-200 transition-all">
                                    Cancel (අවලංගු කරන්න)
                                </button>
                                <button type="submit" class="flex-grow sm:flex-grow-0 px-12 py-4 bg-primary text-white font-black text-[10px] uppercase tracking-widest rounded-2xl hover:bg-blue-900 shadow-xl shadow-primary/20 transition-all transform hover:-translate-y-1 active:translate-y-0">
                                    Filter Now (සෙවීමට භාවිතා කරන්න)
                                </button>
                             </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <script>
            function toggleFilters() {
                const advanced = document.getElementById('advancedFilters');
                advanced.classList.toggle('hidden');
            }
        </script>

        <?php if (empty($candidates)): ?>
             <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Profiles Found (ප්‍රතිඵල හමු නොවීය)</h2>
                <p class="text-gray-500 max-w-sm mx-auto">We couldn't find any approved candidates at the moment. Please check back later or modify your search. (කරුණාකර ඔබගේ පෙරහන් වෙනස් කර නැවත උත්සාහ කරන්න.)</p>
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
                        <a href="profile.php?id=<?php echo $candidate['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3.5 bg-gray-50 hover:bg-primary-hover hover:text-white text-gray-700 font-bold rounded-2xl transition-all duration-300 text-sm group/btn shadow-sm">
                            View Detailed Profile
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>

                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'candidate'): ?>
                            <?php $isInterested = in_array($candidate['id'], $my_interests); ?>
                            <button 
                                onclick="expressInterest(this, <?php echo $candidate['id']; ?>)" 
                                data-parent="<?php echo htmlspecialchars($candidate['parent_phone'] ?? ''); ?>"
                                data-myphone="<?php echo htmlspecialchars($candidate['my_phone'] ?? ''); ?>"
                                data-name="<?php echo htmlspecialchars($candidate['fullname'] ?? ''); ?>"
                                data-reg="<?php echo htmlspecialchars($candidate['reg_number'] ?? ''); ?>"
                                class="w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold transition-all duration-300 text-sm shadow-sm <?php echo $isInterested ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white'; ?>">
                                <svg class="w-4 h-4 <?php echo $isInterested ? 'fill-current' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span class="btn-text"><?php echo $isInterested ? 'Interested' : 'Express Interest'; ?></span>
                            </button>
                        <?php endif; ?>
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


<script>
    // Current user's registration details
    const myRegNo = '<?php echo $my_details['reg_number'] ?? ''; ?>';
    const myName = '<?php echo addslashes($my_details['fullname'] ?? ''); ?>';

    function expressInterest(btn, receiverId) {
        const span = btn.querySelector('.btn-text');
        const icon = btn.querySelector('svg');
        const isCurrentlyInterested = btn.classList.contains('bg-red-500');
        
        const parentPhone = btn.getAttribute('data-parent');
        const receiverName = btn.getAttribute('data-name');
        const receiverReg = btn.getAttribute('data-reg');

        if (!isCurrentlyInterested) {
            // Show Contact Modal First
            const modal = document.getElementById('contactModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            // Set up the confirm button
            document.getElementById('modalConfirmBtn').onclick = function() {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                performInterestAction(btn, receiverId, span, icon, parentPhone, receiverName, receiverReg);
            };
            return;
        }

        // If removing interest, just do it directly
        performInterestAction(btn, receiverId, span, icon);
    }

    function performInterestAction(btn, receiverId, span, icon, parentPhone = '', receiverName = '', receiverReg = '') {
        const originalText = span.textContent;
        btn.disabled = true;
        span.textContent = 'Processing...';

        const formData = new FormData();
        formData.append('receiver_id', receiverId);

        fetch('express_interest.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.action === 'added') {
                    btn.className = 'w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold transition-all duration-300 text-sm shadow-sm bg-red-500 text-white hover:bg-red-600';
                    span.textContent = 'Interested';
                    icon.classList.add('fill-current');

                    // AUTOMATIC WHATSAPP REDIRECT
                    if (parentPhone) {
                        const cleanPhone = parentPhone.replace(/\D/g, '');
                        const message = encodeURIComponent(`Hello, I am interested in profile #${receiverReg} (${receiverName}). My registration number is #${myRegNo} (${myName}).`);
                        window.open(`https://wa.me/${cleanPhone}?text=${message}`, '_blank');
                    }
                } else {
                    btn.className = 'w-full flex items-center justify-center gap-2 py-3.5 rounded-2xl font-bold transition-all duration-300 text-sm shadow-sm bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white';
                    span.textContent = 'Express Interest';
                    icon.classList.remove('fill-current');
                }
            } else {
                alert(data.message);
                span.textContent = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
            span.textContent = originalText;
        })
        .finally(() => {
            btn.disabled = false;
        });
    }

    function closeContactModal() {
        document.getElementById('contactModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<!-- Contact Information Modal -->
<div id="contactModal" class="fixed inset-0 z-[120] hidden">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeContactModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 animate-zoom-in relative">
            <button onclick="closeContactModal()" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-all z-10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="p-8 md:p-10">
                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 text-center mb-2">Contact Information</h3>
                <p class="text-xs text-gray-500 font-bold text-center mb-8 uppercase tracking-widest">සම්බන්ධතා තොරතුරු</p>
                
                <div class="space-y-6">
                    <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100">
                        <p class="text-[13px] text-primary font-bold leading-relaxed text-center">
                            Note: All the contact details regarding via the parent phone number.
                            <br>
                            <span class="text-[11px] opacity-80 mt-2 block">මෙම සියලු සම්බන්ධතා තොරතුරු දෙමාපියන්ගේ දුරකථන අංකය හරහා සිදු වේ.</span>
                        </p>
                    </div>
                </div>

                <div class="mt-8">
                    <button id="modalConfirmBtn" class="w-full py-4 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] transition-transform flex items-center justify-center gap-2">
                        OK, Express Interest
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
