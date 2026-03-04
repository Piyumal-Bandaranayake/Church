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
        
        <!-- Compact Filtering Bar -->
        <div class="mb-8 reveal reveal-up">
            <form action="candidates.php" method="GET" id="filterForm" class="space-y-4">
                <!-- Main Search & Sort Row -->
                <div class="bg-white p-4 rounded-3xl shadow-xl border border-gray-100 flex flex-col md:flex-row items-center gap-4">
                    <!-- Search -->
                    <div class="flex-grow w-full md:w-auto relative group">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-primary transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name, hometown, job... (නම, නගරය හෝ රැකියාව අනුව සොයන්න)" class="w-full pl-11 pr-4 py-3 bg-gray-50 border-none rounded-2xl text-sm font-semibold focus:ring-2 focus:ring-primary/10 outline-none transition-all">
                    </div>

                    <!-- Denomination Filter -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <div class="w-full md:w-56">
                        <select name="denomination_filter" class="w-full bg-gray-50 border-none rounded-2xl text-xs font-bold px-4 py-3 outline-none focus:ring-2 focus:ring-primary/10 cursor-pointer">
                            <option value="">All Denominations (සියලුම නිකායන්)</option>
                            <option value="Catholic" <?php echo ($_GET['denomination_filter'] ?? '') === 'Catholic' ? 'selected' : ''; ?>>Catholic (කතෝලික)</option>
                            <option value="Christian" <?php echo ($_GET['denomination_filter'] ?? '') === 'Christian' ? 'selected' : ''; ?>>Christian (ක්‍රිස්තියානි)</option>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="flex items-center gap-2 w-full md:w-auto flex-col sm:flex-row">
                        <!-- Advanced Filter Toggle -->
                        <button type="button" onclick="toggleFilters()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 font-bold text-xs md:text-sm rounded-2xl transition-all border border-transparent hover:border-gray-200">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            සහකරු හෝ සහකාරිය තෝරාගන්න
                            <?php 
                            $active_filters = 0;
                            if($age_min || $age_max) $active_filters++;
                            if($district) $active_filters++;
                            if($height_min || $height_max) $active_filters++;
                            if($job) $active_filters++;
                            if($church) $active_filters++;
                            if($civil_status) $active_filters++;
                            if($education) $active_filters++;
                            if($active_filters > 0): ?>
                                <span class="bg-primary text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center"><?php echo $active_filters; ?></span>
                            <?php endif; ?>
                        </button>

                        <!-- Sort Dropdown -->
                        <select name="sort" onchange="this.form.submit()" class="w-full sm:w-auto bg-primary text-white border-none rounded-2xl text-xs md:text-sm font-bold px-5 py-3 outline-none focus:ring-4 focus:ring-primary/20 cursor-pointer shadow-lg shadow-primary/20">
                            <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest (අලුත්ම)</option>
                            <option value="age_asc" <?php echo $sort === 'age_asc' ? 'selected' : ''; ?>>Age ↑ (වයස අඩු සිට)</option>
                            <option value="age_desc" <?php echo $sort === 'age_desc' ? 'selected' : ''; ?>>Age ↓ (වයස වැඩි සිට)</option>
                        </select>
                    </div>
                </div>

                <!-- Expanded Filters (Hidden by Default) -->
                <div id="advancedFilters" class="<?php echo $active_filters > 0 ? '' : 'hidden'; ?> bg-white p-8 rounded-[2.5rem] shadow-2xl border border-gray-100 animate-slide-down">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Age Filter -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Age Range (වයස පරාසය)</label>
                            <div class="flex gap-2">
                                <input type="number" name="age_min" value="<?php echo htmlspecialchars($age_min); ?>" placeholder="Min (අවම)" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <input type="number" name="age_max" value="<?php echo htmlspecialchars($age_max); ?>" placeholder="Max (උපරිම)" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                            </div>
                        </div>

                        <!-- District Filter -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">District (දිස්ත්‍රික්කය)</label>
                            <select name="district" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <option value="">All Districts (සියලුම දිස්ත්‍රික්ක)</option>
                                <?php 
                                $districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
                                foreach($districts as $d): ?>
                                    <option value="<?php echo $d; ?>" <?php echo $district === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Height Filter -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Height (Ft) (උස)</label>
                            <div class="flex gap-2">
                                <input type="number" name="height_min" step="0.1" value="<?php echo htmlspecialchars($height_min); ?>" placeholder="Min (අවම)" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <input type="number" name="height_max" step="0.1" value="<?php echo htmlspecialchars($height_max); ?>" placeholder="Max (උපරිම)" class="w-1/2 bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                            </div>
                        </div>

                        <!-- Job Filter -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Occupation (රැකියාව)</label>
                            <select name="job" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <option value="">All Occupations (සියලුම රැකියා)</option>

                                <optgroup label="Government & Public Service (රාජ්‍ය සේවය)">
                                    <option value="Government Officer" <?php echo $job === 'Government Officer' ? 'selected' : ''; ?>>Government Officer (රාජ්‍ය නිලධාරී)</option>
                                    <option value="Teacher" <?php echo $job === 'Teacher' ? 'selected' : ''; ?>>Teacher (ගුරුවරයා / ගුරුවරිය)</option>
                                    <option value="Principal" <?php echo $job === 'Principal' ? 'selected' : ''; ?>>Principal (විදුහල්පති)</option>
                                    <option value="Lecturer" <?php echo $job === 'Lecturer' ? 'selected' : ''; ?>>Lecturer (කථිකාචාර්ය)</option>
                                    <option value="Police Officer" <?php echo $job === 'Police Officer' ? 'selected' : ''; ?>>Police Officer (පොලිස් නිලධාරී)</option>
                                    <option value="Military Officer" <?php echo $job === 'Military Officer' ? 'selected' : ''; ?>>Military / Armed Forces (හමුදා නිලධාරී)</option>
                                    <option value="Public Health Inspector" <?php echo $job === 'Public Health Inspector' ? 'selected' : ''; ?>>Public Health Inspector (PHI)</option>
                                    <option value="Local Government Officer" <?php echo $job === 'Local Government Officer' ? 'selected' : ''; ?>>Local Government Officer (ප්‍රාදේශීය සභා නිලධාරී)</option>
                                    <option value="Postal Officer" <?php echo $job === 'Postal Officer' ? 'selected' : ''; ?>>Postal Officer (තැපෑල් නිලධාරී)</option>
                                </optgroup>

                                <optgroup label="Healthcare (සෞඛ්‍ය සේවය)">
                                    <option value="Doctor" <?php echo $job === 'Doctor' ? 'selected' : ''; ?>>Doctor / Physician (වෛද්‍යවරයා)</option>
                                    <option value="Nurse" <?php echo $job === 'Nurse' ? 'selected' : ''; ?>>Nurse (හෙද / හෙදිය)</option>
                                    <option value="Pharmacist" <?php echo $job === 'Pharmacist' ? 'selected' : ''; ?>>Pharmacist (ඖෂධවේදී)</option>
                                    <option value="Dentist" <?php echo $job === 'Dentist' ? 'selected' : ''; ?>>Dentist (දන්ත වෛද්‍යවරයා)</option>
                                    <option value="Medical Lab Technician" <?php echo $job === 'Medical Lab Technician' ? 'selected' : ''; ?>>Medical Lab Technician (වෛද්‍ය රසායනාගාර තාක්ෂණවේදී)</option>
                                </optgroup>

                                <optgroup label="Engineering & Technology (ඉංජිනේරු & තාක්ෂණ)">
                                    <option value="Engineer" <?php echo $job === 'Engineer' ? 'selected' : ''; ?>>Engineer (ඉංජිනේරු)</option>
                                    <option value="Software Developer" <?php echo $job === 'Software Developer' ? 'selected' : ''; ?>>Software Developer / IT (මෘදුකාංග නිර්මාතෘ)</option>
                                    <option value="Electrician" <?php echo $job === 'Electrician' ? 'selected' : ''; ?>>Electrician (විදුලි කාර්මික)</option>
                                    <option value="Mechanic" <?php echo $job === 'Mechanic' ? 'selected' : ''; ?>>Mechanic (යන්ත්‍ර කාර්මික)</option>
                                    <option value="Civil Technician" <?php echo $job === 'Civil Technician' ? 'selected' : ''; ?>>Civil Technician (සිවිල් තාක්ෂණවේදී)</option>
                                    <option value="Architect" <?php echo $job === 'Architect' ? 'selected' : ''; ?>>Architect (ගෘහ නිර්මාණ ශිල්පී)</option>
                                </optgroup>

                                <optgroup label="Business & Finance (ව්‍යාපාර & මූල්‍ය)">
                                    <option value="Accountant" <?php echo $job === 'Accountant' ? 'selected' : ''; ?>>Accountant (ගණකාධිකාරී)</option>
                                    <option value="Bank Officer" <?php echo $job === 'Bank Officer' ? 'selected' : ''; ?>>Bank Officer (බැංකු නිලධාරී)</option>
                                    <option value="Businessman / Businesswoman" <?php echo $job === 'Businessman / Businesswoman' ? 'selected' : ''; ?>>Businessman / Businesswoman (ව්‍යාපාරික)</option>
                                    <option value="Manager" <?php echo $job === 'Manager' ? 'selected' : ''; ?>>Manager (කළමනාකාර)</option>
                                    <option value="Sales Representative" <?php echo $job === 'Sales Representative' ? 'selected' : ''; ?>>Sales Representative (විකුණුම් නියෝජිත)</option>
                                    <option value="Clerk" <?php echo $job === 'Clerk' ? 'selected' : ''; ?>>Clerk / Office Staff (කාර්යාල ශ්‍රමිකයා)</option>
                                    <option value="Lawyer" <?php echo $job === 'Lawyer' ? 'selected' : ''; ?>>Lawyer (නීතිඥ)</option>
                                </optgroup>

                                <optgroup label="Agriculture & Manual Work (කෘෂිකර්ම & ශ්‍රමය)">
                                    <option value="Farmer" <?php echo $job === 'Farmer' ? 'selected' : ''; ?>>Farmer (ගොවිතැන)</option>
                                    <option value="Fisher" <?php echo $job === 'Fisher' ? 'selected' : ''; ?>>Fisher (ධීවර)</option>
                                    <option value="Builder / Labourer" <?php echo $job === 'Builder / Labourer' ? 'selected' : ''; ?>>Builder / Construction Labourer (ඉදිකිරීම් කාර්මික)</option>
                                    <option value="Driver" <?php echo $job === 'Driver' ? 'selected' : ''; ?>>Driver (රියදුරු)</option>
                                    <option value="Tailor" <?php echo $job === 'Tailor' ? 'selected' : ''; ?>>Tailor (ටේලර්)</option>
                                    <option value="Cook" <?php echo $job === 'Cook' ? 'selected' : ''; ?>>Cook / Chef (සූපවේදී)</option>
                                    <option value="Plumber" <?php echo $job === 'Plumber' ? 'selected' : ''; ?>>Plumber (පයිප් කාර්මික)</option>
                                </optgroup>

                                <optgroup label="Religious & Social Service (ආගමික & සමාජ සේවය)">
                                    <option value="Clergy / Religious Worker" <?php echo $job === 'Clergy / Religious Worker' ? 'selected' : ''; ?>>Clergy / Religious Worker (ආගමික සේවය)</option>
                                    <option value="Social Worker" <?php echo $job === 'Social Worker' ? 'selected' : ''; ?>>Social Worker (සමාජ සේවක)</option>
                                    <option value="NGO Worker" <?php echo $job === 'NGO Worker' ? 'selected' : ''; ?>>NGO Worker (රාජ්‍ය නොවන සංවිධාන)</option>
                                </optgroup>

                                <optgroup label="Other (වෙනත්)">
                                    <option value="Student" <?php echo $job === 'Student' ? 'selected' : ''; ?>>Student (ශිෂ්‍ය)</option>
                                    <option value="Self-Employed" <?php echo $job === 'Self-Employed' ? 'selected' : ''; ?>>Self-Employed (ස්වයං රැකියා)</option>
                                    <option value="Housewife / Homemaker" <?php echo $job === 'Housewife / Homemaker' ? 'selected' : ''; ?>>Housewife / Homemaker (ගෘහිණිය)</option>
                                    <option value="Retired" <?php echo $job === 'Retired' ? 'selected' : ''; ?>>Retired (විශ්‍රාමිකයා)</option>
                                    <option value="Unemployed" <?php echo $job === 'Unemployed' ? 'selected' : ''; ?>>Unemployed (රැකියා රහිත)</option>
                                    <option value="Other" <?php echo $job === 'Other' ? 'selected' : ''; ?>>Other (වෙනත්)</option>
                                </optgroup>
                            </select>
                        </div>

                        <!-- Church Filter -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Church (දේවස්ථානය)</label>
                            <input type="text" name="church" value="<?php echo htmlspecialchars($church); ?>" placeholder="Church name... (දේවස්ථානයේ නම)" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                        </div>

                        <!-- Civil Status -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Civil Status (විවාහක/අවිවාහක බව)</label>
                            <select name="civil_status" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <option value="">Any (සියල්ල)</option>
                                <option value="Unmarried" <?php echo $civil_status === 'Unmarried' ? 'selected' : ''; ?>>Unmarried (අවිවාහක)</option>
                                <option value="Divorced" <?php echo $civil_status === 'Divorced' ? 'selected' : ''; ?>>Divorced (දික්කසාද)</option>
                                <option value="Widowed" <?php echo $civil_status === 'Widowed' ? 'selected' : ''; ?>>Widowed (වැන්දඹු)</option>
                            </select>
                        </div>

                        <!-- Education -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Education (අධ්‍යාපනය)</label>
                            <select name="education" class="w-full bg-gray-50 border-none rounded-xl text-xs font-bold p-3 outline-none focus:ring-2 focus:ring-primary/10">
                                <option value="">Any Level (සියලුම)</option>
                                <option value="O/L" <?php echo $education === 'O/L' ? 'selected' : ''; ?>>O/L Qualified (සා.පෙළ)</option>
                                <option value="A/L" <?php echo $education === 'A/L' ? 'selected' : ''; ?>>A/L Qualified (උ.පෙළ)</option>
                                <option value="Degree" <?php echo $education === 'Degree' ? 'selected' : ''; ?>>Graduate / Degree (උපාධිධාරී)</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-grow bg-primary text-white font-bold text-xs py-3 rounded-xl hover:shadow-lg transition-all animate-pulse-slow">
                                Apply (භාවිතා කරන්න)
                            </button>
                            <a href="candidates.php" class="p-3 bg-red-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all group" title="Clear (ඉවත් කරන්න)">
                                <svg class="w-4 h-4 group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </a>
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
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Profiles Found</h2>
                <p class="text-gray-500 max-w-sm mx-auto">We couldn't find any approved candidates at the moment. Please check back later or modify your search.</p>
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
