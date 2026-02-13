<?php
session_start();
include 'includes/db.php';
// Ensure table exists
try {
    $pdo->query('select 1 from candidates LIMIT 1');
} catch (Exception $e) {
    include 'setup_db.php';
}

$error = '';
$success = '';

// Fetch Churches for dropdown
$church_stmt = $pdo->query("SELECT name FROM churches ORDER BY name ASC");
$churches_list = $church_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all inputs
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password_raw = $_POST['password'];
    $re_password = $_POST['re_password'];
    $fullname = trim($_POST['fullname']);
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
    $children = isset($_POST['children']) ? $_POST['children'] : 'No';
    $illness = trim($_POST['illness']);
    $habits = isset($_POST['habit']) ? implode(',', $_POST['habit']) : 'None';
    $pastor_name = trim($_POST['pastor_name']);
    $pastor_phone = trim($_POST['pastor_phone']);
    $parent_phone = trim($_POST['parent_phone']);
    $my_phone = trim($_POST['my_phone']);
    $church = $_POST['church'];

    // Validation logic
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password_raw !== $re_password) {
        $error = "Passwords do not match!";
    } elseif (empty($fullname) || strlen($fullname) < 3) {
        $error = "Please enter a valid full name.";
    } elseif ($age < 18 || $age > 80) {
        $error = "Age must be between 18 and 80.";
    } elseif (strlen($my_phone) < 9 || strlen($my_phone) > 15) {
        $error = "Please enter a valid WhatsApp number.";
    } elseif (!isset($_POST['terms_agreement'])) {
        $error = "You must agree to the Terms and Conditions to register.";
    }

    // Handle Custom Church if 'Other' is selected
    if ($church === 'Other' && empty($error)) {
        $custom_church_name = trim($_POST['other_church_name']);
        $custom_pastor = trim($_POST['other_church_pastor']);
        $custom_location = trim($_POST['other_church_location']);
        
        if (empty($custom_church_name)) {
            $error = "Please provide the name of your church.";
        } else {
            // Save to master churches table for future use
            $church_ins = $pdo->prepare("INSERT IGNORE INTO churches (name, pastor_name, location) VALUES (?, ?, ?)");
            $church_ins->execute([$custom_church_name, $custom_pastor, $custom_location]);
            $church = $custom_church_name;
        }
    }
    
    // Photo Upload Validation
    $photo_path = null;
    if (empty($error) && isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['file-upload']['name'], PATHINFO_EXTENSION));
        $size = $_FILES['file-upload']['size'];

        if (!in_array($ext, $allowed)) {
            $error = "Only JPG, PNG and GIF images are allowed.";
        } elseif ($size > 5 * 1024 * 1024) { // 5MB limit
            $error = "Photo size must be less than 5MB.";
        } else {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES["file-upload"]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES["file-upload"]["tmp_name"], $target_file)) {
                $photo_path = $target_file;
            } else {
                $error = "Error uploading photo.";
            }
        }
    } elseif (empty($error) && !isset($_FILES['file-upload'])) {
        $error = "Please upload a photograph.";
    }

    if (empty($error)) {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO candidates (email, password, fullname, sex, dob, age, nationality, language, address, hometown, district, province, height, occupation, edu_qual, add_qual, marital_status, children, illness, habits, church, pastor_name, pastor_phone, parent_phone, my_phone, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email, $password, $fullname, $sex, $dob, $age, $nationality, $language, $address, $hometown, $district, $province, $height, $occupation, $edu_qual, $add_qual, $marital_status, $children, $illness, $habits, $church, $pastor_name, $pastor_phone, $parent_phone, $my_phone, $photo_path]);
            
            header("Location: login.php?registered=true");
            exit();
            
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "This email is already registered. Please login or use another email.";
            } else {
                $error = "Database Error: " . $e->getMessage();
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Marriage Candidate Registration</h1>
            <p class="mt-2 text-gray-600">Please fill in your details accurately. Your profile will be reviewed by the admin before approval.</p>
        </div>

        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Form -->
            <form class="p-8 space-y-8" action="" method="POST" enctype="multipart/form-data">
                
                <!-- Account Information -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Account Setup
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" required placeholder="example@mail.com" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required minlength="6" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eye-icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <input id="re_password" type="password" name="re_password" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                                <button type="button" onclick="togglePassword('re_password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eye-icon-re_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name( නම)</label>
                            <input type="text" name="fullname" required minlength="3" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex( ස්ත්‍රී පුරුෂ භාවය)</label>
                            <select name="sex" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth(උපන්දිනය)</label>
                            <input type="date" name="dob" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Age( වයස)</label>
                            <input type="number" name="age" required min="18" max="80" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality( ජාතිය)</label>
                            <input type="text" name="nationality" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mother Tongue(මව්බස)</label>
                            <input type="text" name="language" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Height (ft/cm)</label>
                            <input type="text" name="height" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Location</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address(ස්ථිර පදිංචි)</label>
                            <textarea name="address" rows="2" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hometown(ගම)</label>
                            <input type="text" name="hometown" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District(දිස්ත්‍රික්කය)</label>
                            <input type="text" name="district" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province(පළාත)</label>
                            <input type="text" name="province" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                 <!-- Professional & Education -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Education & Profession(අධ්‍යාපනය හා වෘත්තිය)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation(වෘත්තිය)</label>
                            <input type="text" name="occupation" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Educational Qualifications(අධ්‍යාපන සුදුසුකම්)</label>
                            <textarea name="edu_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Degrees, Diplomas, etc."></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Qualifications(අතිරේක සුදුසුකම්)</label>
                            <textarea name="add_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Skills, Certifications, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Marital Status & Habits -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Background(පෞද්ගලික පසුබිම)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status(විවාහක තත්ත්වය)</label>
                            <select name="marital_status" onchange="toggleChildren(this.value)" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="Unmarried">Unmarried</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div id="children_field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Do you have children?(ඔබට දරුවන් සිටීද?)</label>
                            <select name="children" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Long-term Illness (requiring continuous treatment)(දීර්ඝ කාලීනව ප්‍රතිකාර ගන්නා වූ රෝගයකින් පෙළෙන්නේද)</label>
                            <textarea name="illness" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Describe if any, otherwise leave blank or type 'None'"></textarea>
                        </div>
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Habits (Betel chewing / Smoking / Alcohol / Drugs)(බුලත්විට/ දුම්පානය /මත්පැන්/ මත්ද්‍රව්‍ය භාවිතය)</label>
                            <div class="flex gap-4 flex-wrap">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="betel" class="rounded text-primary focus:ring-primary">
                                    <span>Betel Chewing(බුලත්විට)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="smoking" class="rounded text-primary focus:ring-primary">
                                    <span>Smoking(දුම්පානය)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="alcohol" class="rounded text-primary focus:ring-primary">
                                    <span>Alcohol(මත්පැන්)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="drugs" class="rounded text-primary focus:ring-primary">
                                    <span>Drugs(මත්ද්‍රව්‍ය)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="none" class="rounded text-primary focus:ring-primary">
                                    <span>None(නැත)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Religious & Family -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Religious & Family Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Denomination / Church Name(නිකාය හෝ දේවස්ථානය)</label>
                            <select name="church" id="church_select" onchange="toggleOtherChurch(this.value)" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select your church</option>
                                <?php foreach($churches_list as $c_name): ?>
                                    <option value="<?php echo htmlspecialchars($c_name); ?>"><?php echo htmlspecialchars($c_name); ?></option>
                                <?php endforeach; ?>
                                <option value="Other">Other (Not in list)</option>
                            </select>
                        </div>

                        <!-- Manual Church Details (Hidden by default) -->
                        <div id="other_church_section" class="md:col-span-2 hidden bg-gray-50 p-6 rounded-xl border border-gray-200 mt-2 space-y-4">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Manual Church Details</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name of Church(දේවස්ථානය)</label>
                                    <input type="text" name="other_church_name" id="other_church_name" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Chief Pastor(ප්‍රධාන දේවගැතිතුමාගේ නම)</label>
                                    <input type="text" name="other_church_pastor" id="other_church_pastor" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location City(නගරය)</label>
                                    <input type="text" name="other_church_location" id="other_church_location" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div class="md:col-span-2 flex flex-col items-start gap-3">
                                    <button type="button" id="save_church_btn" onclick="saveNewChurch()" class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-all shadow-md flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                                        Save Church Details
                                    </button>
                                    <div id="church_save_message" class="text-sm font-bold hidden"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name of Pastor(ප්‍රධාන දේවගැතිතුමාගේ නම)</label>
                            <input type="text" name="pastor_name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pastor's WhatsApp(දේවගැතිතුමාගේ වට්ස්ඇප් අංකය)</label>
                            <input type="tel" name="pastor_phone" required pattern="[0-9+]{9,15}" title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent's WhatsApp(දෙමාපියන්ගේ වට්ස්ඇප් අංකය)</label>
                            <input type="tel" name="parent_phone" required pattern="[0-9+]{9,15}" title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your WhatsApp(ඔබගේ වට්ස්ඇප් අංකය)</label>
                            <input type="tel" name="my_phone" required pattern="[0-9+]{9,15}" title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Verification</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recent Photograph (Face clearly visible)(මෑතකදී ගත් ඡායාරූපයක් (මුහුණ පැහැදිලිව පෙනෙන))</label>
                             <div class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors cursor-pointer bg-gray-50 relative group">
                                <div id="preview-container" class="hidden w-48 h-64 mb-4 rounded-xl overflow-hidden shadow-lg border-4 border-white">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                    <button type="button" onclick="removeImage(event)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                                
                                <div id="upload-placeholder" class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only" onchange="previewFile(this)" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <!-- Terms and Conditions Agreement -->
                    <div class="mb-6 bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50">
                        <label class="flex items-start gap-4 cursor-pointer group">
                            <div class="mt-1">
                                <input type="checkbox" name="terms_agreement" required class="w-6 h-6 rounded-lg border-gray-300 text-primary focus:ring-primary transition-all">
                            </div>
                            <div class="text-sm text-gray-600 leading-relaxed font-medium">
                                <span class="block text-gray-900 font-bold mb-1">Agreement (ගිවිසුම)</span>
                                I have read, understood, and agree to the <a href="terms.php" target="_blank" class="text-primary font-black hover:underline decoration-2 underline-offset-4">Terms and Conditions, Privacy Policy</a>, and other guidelines of this platform.
                                <p class="mt-1 text-[11px] text-gray-400 font-bold uppercase tracking-widest">මම මෙහි ඇති නියමයන් සහ කොන්දේසි කියවා ඒවාට එකඟ වෙමි.</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-xl text-xl font-black text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:-translate-y-1">
                        Complete Registration
                    </button>
                    <p class="mt-6 text-center text-xs text-gray-400 font-bold uppercase tracking-widest">
                        Solomon's Porch &middot; Established in Faith
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function toggleChildren(status) {
    const childrenField = document.getElementById('children_field');
    if (status === 'Divorced' || status === 'Widowed') {
        childrenField.classList.remove('hidden');
    } else {
        childrenField.classList.add('hidden');
    }
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('eye-icon-' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.057 10.057 0 012.183-4.403M9.616 9.616L11 11m4 4l1.384 1.384M15.404 15.404l1.384 1.384M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />';
    } else {
        input.type = 'password';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
    }
}

function toggleOtherChurch(val) {
    const section = document.getElementById('other_church_section');
    const inputs = section.querySelectorAll('input');
    
    if (val === 'Other') {
        section.classList.remove('hidden');
        inputs.forEach(i => i.setAttribute('required', 'true'));
    } else {
        section.classList.add('hidden');
        inputs.forEach(i => i.removeAttribute('required'));
    }
}

function previewFile(input) {
    const preview = document.getElementById('image-preview');
    const container = document.getElementById('preview-container');
    const placeholder = document.getElementById('upload-placeholder');
    const file = input.files[0];
    const reader = new FileReader();

    reader.onloadend = function () {
        preview.src = reader.result;
        container.classList.remove('hidden');
        placeholder.classList.add('hidden');
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
        container.classList.add('hidden');
        placeholder.classList.remove('hidden');
    }
}

async function saveNewChurch() {
    const name = document.getElementById('other_church_name').value;
    const pastor = document.getElementById('other_church_pastor').value;
    const location = document.getElementById('other_church_location').value;
    const btn = document.getElementById('save_church_btn');
    const msg = document.getElementById('church_save_message');

    if (!name) {
        alert('Please enter a church name.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Saving...';
    msg.classList.add('hidden');

    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('pastor', pastor);
        formData.append('location', location);

        const response = await fetch('save_church.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        msg.classList.remove('hidden');
        if (data.success) {
            msg.textContent = data.message;
            msg.classList.remove('text-red-500');
            msg.classList.add('text-green-600');
            
            // Optionally add to dropdown and select it
            const select = document.getElementById('church_select');
            const option = new Option(name, name);
            select.add(option, select.options[select.options.length - 1]);
            select.value = name;
            
            // Hide the manual section after saving
            setTimeout(() => {
                toggleOtherChurch(name);
            }, 2000);
        } else {
            msg.textContent = data.message;
            msg.classList.remove('text-green-600');
            msg.classList.add('text-red-500');
        }
    } catch (error) {
        msg.classList.remove('hidden');
        msg.textContent = 'An error occurred. Please try again.';
        msg.classList.add('text-red-500');
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg> Save Church Details`;
    }
}

function removeImage(event) {
    event.stopPropagation();
    const input = document.getElementById('file-upload');
    const preview = document.getElementById('image-preview');
    const container = document.getElementById('preview-container');
    const placeholder = document.getElementById('upload-placeholder');
    
    input.value = "";
    preview.src = "";
    container.classList.add('hidden');
    placeholder.classList.remove('hidden');
}

document.querySelector('form').addEventListener('submit', function(e) {
    const pass = document.getElementById('password').value;
    const rePass = document.getElementById('re_password').value;
    
    if (pass !== rePass) {
        e.preventDefault();
        alert('Passwords do not match!');
    }
});
</script>

<?php include 'includes/footer.php'; ?>
