<?php
session_start();
include 'includes/db.php';

// Admin access only
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
$success = isset($_GET['updated']) ? "Profile updated successfully!" : '';

// Fetch candidate data
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
$stmt->execute([$id]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    die("Candidate not found.");
}

// Fetch Churches for dropdown
$church_stmt = $pdo->query("SELECT name FROM churches ORDER BY name ASC");
$churches_list = $church_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and update logic
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $nic_number = strtoupper(trim($_POST['nic_number']));
    $denomination = $_POST['denomination'];
    $sex = $_POST['sex'];
    $dob = $_POST['dob'];
    $age = intval($_POST['age']);
    $nationality = trim($_POST['nationality']);
    $language = trim($_POST['language']);
    $address = trim($_POST['address']);
    $hometown = trim($_POST['hometown']);
    $district = trim($_POST['district']);
    $province = trim($_POST['province']);
    $height = trim($_POST['height']);
    $occupation = trim($_POST['occupation']);
    $edu_qual = trim($_POST['edu_qual']);
    $add_qual = trim($_POST['add_qual']);
    $marital_status = $_POST['marital_status'];
    $children = $_POST['children'] ?? 'No';
    $children_details = trim($_POST['children_details'] ?? '');
    $illness = trim($_POST['illness'] ?? '');
    $habits = isset($_POST['habit']) ? implode(',', (array)$_POST['habit']) : ($_POST['habits_text'] ?? '');
    $church = $_POST['church'];
    $pastor_name = trim($_POST['pastor_name']);
    $pastor_phone = trim($_POST['pastor_phone']);
    $parent_phone = trim($_POST['parent_phone']);
    $my_phone = trim($_POST['my_phone']);
    $package = $_POST['package'] ?? '3_months';

    // Catholic Specifics
    $catholic_by_birth = $_POST['catholic_by_birth'] ?? null;
    $christianization_year = !empty($_POST['christianization_year']) ? intval($_POST['christianization_year']) : null;
    $sacraments = trim($_POST['sacraments_received'] ?? '');

    try {
        $sql = "UPDATE candidates SET 
                fullname = ?, email = ?, nic_number = ?, denomination = ?, sex = ?, dob = ?, age = ?, 
                nationality = ?, language = ?, address = ?, hometown = ?, district = ?, 
                province = ?, height = ?, occupation = ?, edu_qual = ?, add_qual = ?, 
                marital_status = ?, children = ?, children_details = ?, illness = ?, 
                habits = ?, church = ?, pastor_name = ?, pastor_phone = ?, 
                parent_phone = ?, my_phone = ?, package = ?,
                catholic_by_birth = ?, christianization_year = ?, sacraments_received = ?
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $fullname, $email, $nic_number, $denomination, $sex, $dob, $age, 
            $nationality, $language, $address, $hometown, $district, 
            $province, $height, $occupation, $edu_qual, $add_qual, 
            $marital_status, $children, $children_details, $illness, 
            $habits, $church, $pastor_name, $pastor_phone, 
            $parent_phone, $my_phone, $package,
            $catholic_by_birth, $christianization_year, $sacraments,
            $id
        ]);

        // Redirect to prevent resubmission
        header("Location: admin_edit_profile.php?id=$id&updated=1");
        exit();

    } catch (PDOException $e) {
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .bg-primary { background-color: #0056b3; }
        .text-primary { color: #0056b3; }
        .form-input { 
            @apply w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-semibold text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0056b3',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <?php include 'includes/preloader.php'; ?>

<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen pb-20">
        <!-- Header -->
        <div class="bg-primary relative overflow-hidden text-white pt-16 pb-32 px-4 sm:px-6 lg:px-8 shadow-inner">
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-blue-600/20 blur-[100px] rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-20 -ml-20 w-72 h-72 bg-white/5 blur-[80px] rounded-full"></div>
            
            <div class="max-w-4xl mx-auto relative z-10">
                <div class="flex items-center gap-6 mb-4">
                    <a href="javascript:history.back()" class="flex items-center justify-center w-12 h-12 bg-white/10 hover:bg-white/20 rounded-2xl transition-all backdrop-blur-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    </a>
                    <div>
                        <h1 class="text-4xl font-black tracking-tight leading-none mb-2">Edit Member Profile</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            
            <?php if ($success): ?>
                <div class="mb-8 p-5 rounded-[2rem] bg-green-500 text-white font-bold text-sm shadow-xl shadow-green-500/20 flex items-center gap-4 animate-bounce">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-8 p-5 rounded-[2rem] bg-red-500 text-white font-bold text-sm shadow-xl shadow-red-500/20 flex items-center gap-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" /></svg>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[3rem] shadow-2xl shadow-gray-200 border border-gray-100 overflow-hidden">
                <form method="POST" class="p-10 space-y-12">
                    
                    <!-- Admin Control Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Administrative Controls</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Registration Number</label>
                                <input type="text" value="<?php echo htmlspecialchars($candidate['reg_number']); ?>" class="w-full px-5 py-4 bg-gray-100 border border-gray-100 rounded-2xl text-sm font-bold text-gray-500 cursor-not-allowed" readonly>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Member Status</label>
                                <div class="w-full px-5 py-4 bg-gray-100 border border-gray-100 rounded-2xl text-sm font-bold text-gray-500 capitalize">
                                    <?php echo htmlspecialchars($candidate['status']); ?>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Subscription Package</label>
                                <select name="package" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    <option value="3_months" <?php echo $candidate['package'] === '3_months' ? 'selected' : ''; ?>>3 Months</option>
                                    <option value="6_months" <?php echo $candidate['package'] === '6_months' ? 'selected' : ''; ?>>6 Months</option>
                                    <option value="12_months" <?php echo $candidate['package'] === '12_months' ? 'selected' : ''; ?>>12 Months</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Denomination</label>
                                <div class="flex gap-4">
                                    <label class="flex-grow">
                                        <input type="radio" name="denomination" value="Catholic" <?php echo $candidate['denomination'] === 'Catholic' ? 'checked' : ''; ?> class="hidden peer">
                                        <div class="text-center py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold peer-checked:bg-blue-600 peer-checked:text-white transition-all cursor-pointer">Catholic</div>
                                    </label>
                                    <label class="flex-grow">
                                        <input type="radio" name="denomination" value="Christian" <?php echo $candidate['denomination'] === 'Christian' ? 'checked' : ''; ?> class="hidden peer">
                                        <div class="text-center py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold peer-checked:bg-primary peer-checked:text-white transition-all cursor-pointer">Christian</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Profile Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Identity Details</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Full Name</label>
                                <input type="text" name="fullname" value="<?php echo htmlspecialchars($candidate['fullname']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Email Address</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($candidate['email']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">NIC Number</label>
                                <input type="text" name="nic_number" value="<?php echo htmlspecialchars($candidate['nic_number']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Date of Birth</label>
                                <input type="date" name="dob" value="<?php echo $candidate['dob']; ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Age</label>
                                    <input type="number" name="age" value="<?php echo $candidate['age']; ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all text-center">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Sex</label>
                                    <select name="sex" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                        <option value="Male" <?php echo $candidate['sex'] === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $candidate['sex'] === 'Female' ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Nationality</label>
                                <input type="text" name="nationality" value="<?php echo htmlspecialchars($candidate['nationality']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Language</label>
                                <input type="text" name="language" value="<?php echo htmlspecialchars($candidate['language']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Height (e.g. 5.8)</label>
                                <input type="text" name="height" value="<?php echo htmlspecialchars($candidate['height']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Marital Status</label>
                                <select name="marital_status" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    <option value="Unmarried" <?php echo $candidate['marital_status'] === 'Unmarried' ? 'selected' : ''; ?>>Unmarried</option>
                                    <option value="Divorced" <?php echo $candidate['marital_status'] === 'Divorced' ? 'selected' : ''; ?>>Divorced</option>
                                    <option value="Widowed" <?php echo $candidate['marital_status'] === 'Widowed' ? 'selected' : ''; ?>>Widowed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Location Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Location & Address</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Full Address</label>
                                <textarea name="address" rows="3" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-3xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all"><?php echo htmlspecialchars($candidate['address']); ?></textarea>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Hometown</label>
                                <input type="text" name="hometown" value="<?php echo htmlspecialchars($candidate['hometown']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">District</label>
                                <select name="district" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    <?php 
                                    $districts = ['Ampara', 'Anuradhapura', 'Badulla', 'Batticaloa', 'Colombo', 'Galle', 'Gampaha', 'Hambantota', 'Jaffna', 'Kalutara', 'Kandy', 'Kegalle', 'Kilinochchi', 'Kurunegala', 'Mannar', 'Matale', 'Matara', 'Moneragala', 'Mullaitivu', 'Nuwara Eliya', 'Polonnaruwa', 'Puttalam', 'Ratnapura', 'Trincomalee', 'Vavuniya'];
                                    foreach($districts as $d): ?>
                                        <option value="<?php echo $d; ?>" <?php echo $candidate['district'] === $d ? 'selected' : ''; ?>><?php echo $d; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Province</label>
                                <input type="text" name="province" value="<?php echo htmlspecialchars($candidate['province'] ?? ''); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Catholic Specific (Toggleable based on Denomination) -->
                    <div id="catholicFields" class="<?php echo $candidate['denomination'] === 'Catholic' ? '' : 'hidden'; ?> space-y-8 p-10 bg-blue-50/30 rounded-[3rem] border border-blue-100/50">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11 2v9H2v2h9v9h2v-9h9v-2h-9V2h-2z"/></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Catholic Information</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Catholic by Birth?</label>
                                <select name="catholic_by_birth" class="w-full px-5 py-4 bg-white border border-blue-200 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    <option value="Yes" <?php echo $candidate['catholic_by_birth'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo $candidate['catholic_by_birth'] === 'No' ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Christianization Year</label>
                                <input type="number" name="christianization_year" value="<?php echo $candidate['christianization_year']; ?>" class="w-full px-5 py-4 bg-white border border-blue-200 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all" placeholder="YYYY">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Sacraments Received</label>
                                <input type="text" name="sacraments_received" value="<?php echo htmlspecialchars($candidate['sacraments_received'] ?? ''); ?>" class="w-full px-5 py-4 bg-white border border-blue-200 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all" placeholder="e.g. Baptism, Confirmation, Marriage">
                            </div>
                        </div>
                    </div>

                    <!-- Work & Background -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Professional & Background</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Current Occupation</label>
                                <input type="text" name="occupation" value="<?php echo htmlspecialchars($candidate['occupation']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Educational Qualification</label>
                                <input type="text" name="edu_qual" value="<?php echo htmlspecialchars($candidate['edu_qual']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Additional Qualifications</label>
                                <textarea name="add_qual" rows="2" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all"><?php echo htmlspecialchars($candidate['add_qual'] ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Children Info -->
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Has Children?</label>
                                <select name="children" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    <option value="No" <?php echo $candidate['children'] === 'No' ? 'selected' : ''; ?>>No</option>
                                    <option value="Yes" <?php echo $candidate['children'] === 'Yes' ? 'selected' : ''; ?>>Yes</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Children Details</label>
                                <input type="text" name="children_details" value="<?php echo htmlspecialchars($candidate['children_details'] ?? ''); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all" placeholder="e.g. 2 sons, 1 daughter">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Health / Chronic Illnesses</label>
                                <input type="text" name="illness" value="<?php echo htmlspecialchars($candidate['illness'] ?? ''); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all" placeholder="None if healthy">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Existing Habits (Saved: <?php echo htmlspecialchars($candidate['habits'] ?? 'None'); ?>)</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-2">
                                    <?php 
                                    $all_habits = ['Smoking', 'Drinking', 'Social Drinking', 'Vegetarian', 'Non-Vegetarian'];
                                    $current_habits = explode(',', $candidate['habits'] ?? '');
                                    foreach($all_habits as $h): ?>
                                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl cursor-pointer hover:bg-gray-100 transition-colors">
                                            <input type="checkbox" name="habit[]" value="<?php echo $h; ?>" <?php echo in_array($h, $current_habits) ? 'checked' : ''; ?> class="w-4 h-4 rounded text-primary focus:ring-primary border-gray-300">
                                            <span class="text-xs font-bold text-gray-600"><?php echo $h; ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <input type="text" name="habits_text" value="<?php echo htmlspecialchars($candidate['habits'] ?? ''); ?>" class="w-full px-5 py-4 mt-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all" placeholder="Or type other habits here...">
                            </div>
                        </div>
                    </div>

                    <!-- Religious & Contact Section -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center shadow-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21l-8.244-8.244a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 tracking-tight">Church & Emergency Contact</h2>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">
                                    <?php echo $candidate['denomination'] === 'Catholic' ? 'Parish / Church Name' : 'Denomination / Ministry'; ?>
                                </label>

                                <?php if ($candidate['denomination'] === 'Catholic'): ?>
                                    <!-- Catholic: free-text input -->
                                    <div id="church_text_wrapper">
                                        <input type="text" name="church" id="church_text_input"
                                            value="<?php echo htmlspecialchars($candidate['church']); ?>"
                                            placeholder="Enter Parish / Church Name"
                                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    </div>
                                    <!-- Christian: dropdown (hidden initially) -->
                                    <div id="church_select_wrapper" class="hidden">
                                        <select name="church_select" id="church_select_input"
                                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                            <option value="">Select Denomination / Ministry</option>
                                            <?php foreach($churches_list as $c): ?>
                                                <option value="<?php echo $c; ?>" <?php echo $candidate['church'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php else: ?>
                                    <!-- Christian: dropdown -->
                                    <div id="church_select_wrapper">
                                        <select name="church" id="church_select_input"
                                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                            <option value="">Select Denomination / Ministry</option>
                                            <?php foreach($churches_list as $c): ?>
                                                <option value="<?php echo $c; ?>" <?php echo $candidate['church'] === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- Catholic: free-text input (hidden initially) -->
                                    <div id="church_text_wrapper" class="hidden">
                                        <input type="text" name="church_text" id="church_text_input"
                                            value="<?php echo htmlspecialchars($candidate['church']); ?>"
                                            placeholder="Enter Parish / Church Name"
                                            class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Pastor / Priest Name</label>
                                <input type="text" name="pastor_name" value="<?php echo htmlspecialchars($candidate['pastor_name']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Pastor Contact</label>
                                <input type="text" name="pastor_phone" value="<?php echo htmlspecialchars($candidate['pastor_phone']); ?>" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                            <div class="border-l-4 border-rose-100 pl-6 bg-rose-50/10 rounded-r-3xl py-2">
                                <label class="block text-[10px] font-black text-rose-400 uppercase tracking-[0.2em] mb-3 ml-2">Father/Mother Phone</label>
                                <input type="text" name="parent_phone" value="<?php echo htmlspecialchars($candidate['parent_phone']); ?>" class="w-full px-5 py-4 bg-white border border-rose-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-rose-500/10 transition-all">
                            </div>
                            <div class="border-l-4 border-primary/20 pl-6 bg-primary/5 rounded-r-3xl py-2">
                                <label class="block text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-3 ml-2">Candidate's Phone (WhatsApp)</label>
                                <input type="text" name="my_phone" value="<?php echo htmlspecialchars($candidate['my_phone']); ?>" class="w-full px-5 py-4 bg-white border border-blue-100 rounded-2xl text-sm font-bold text-gray-700 focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-10 border-t border-gray-100">
                        <button type="submit" class="group relative w-full flex items-center justify-center p-6 bg-primary text-white text-lg font-black rounded-[2rem] shadow-2xl shadow-blue-600/30 hover:scale-[1.03] transition-all active:scale-95">
                            <span class="relative z-10 flex items-center gap-3">
                                <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                Update Profile Information
                            </span>
                        </button>
                        <p class="text-center text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-6">All changes are permanent and live instantly.</p>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
    // Listen for denomination changes
    const radios = document.querySelectorAll('input[name="denomination"]');
    const catholicFields = document.getElementById('catholicFields');
    const churchTextWrapper  = document.getElementById('church_text_wrapper');
    const churchSelectWrapper = document.getElementById('church_select_wrapper');
    const churchTextInput   = document.getElementById('church_text_input');
    const churchSelectInput = document.getElementById('church_select_input');

    radios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'Catholic') {
                catholicFields.classList.remove('hidden');
                // Show text input, hide dropdown
                churchTextWrapper.classList.remove('hidden');
                churchSelectWrapper.classList.add('hidden');
                // Ensure correct name attribute so it posts as 'church'
                churchTextInput.name  = 'church';
                churchSelectInput.name = 'church_select';
            } else {
                catholicFields.classList.add('hidden');
                // Show dropdown, hide text input
                churchSelectWrapper.classList.remove('hidden');
                churchTextWrapper.classList.add('hidden');
                // Ensure correct name attribute so it posts as 'church'
                churchSelectInput.name = 'church';
                churchTextInput.name   = 'church_text';
            }
        });
    });
</script>

</body>
</html>
