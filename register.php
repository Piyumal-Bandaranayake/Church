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

// Get type from URL or POST
$denomination = isset($_GET['type']) ? ucfirst($_GET['type']) : (isset($_POST['denomination']) ? $_POST['denomination'] : 'Christian');
if (!in_array($denomination, ['Catholic', 'Christian'])) {
    $denomination = 'Christian';
}

// Fetch Churches for dropdown
$church_stmt = $pdo->query("SELECT name FROM churches ORDER BY name ASC");
$churches_list = $church_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize all inputs
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $re_password = $_POST['re_password'];
    $nic_number = strtoupper(trim($_POST['nic_number']));
    $denomination = $_POST['denomination']; // Get from hidden field

    // Catholic Specifics
    $catholic_by_birth = $_POST['catholic_by_birth'] ?? null;
    $christianization_year = !empty($_POST['christianization_year']) ? intval($_POST['christianization_year']) : null;
    $sacraments = trim($_POST['sacraments_received'] ?? '');

    $fullname = trim($_POST['fullname']);
    $sex = $_POST['sex'];
    $dob = $_POST['dob'];
    $age = intval($_POST['age']);
    $nationality = trim($_POST['nationality']);
    if ($nationality === 'Other' && !empty($_POST['other_nationality'])) {
        $nationality = trim($_POST['other_nationality']);
    }
    $language = trim($_POST['language']);
    if ($language === 'Other' && !empty($_POST['other_language'])) {
        $language = trim($_POST['other_language']);
    }
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
    $children_details = trim($_POST['children_details'] ?? '');
    $illness = trim($_POST['illness']);
    if ($illness === 'Other' && !empty($_POST['other_illness'])) {
        $illness = trim($_POST['other_illness']);
    }
    $habits = isset($_POST['habit']) ? implode(',', $_POST['habit']) : 'None';
    $pastor_name = trim($_POST['pastor_name']);
    $pastor_phone = trim($_POST['pastor_phone']);
    $parent_phone = trim($_POST['parent_phone']);
    $my_phone = trim($_POST['my_phone']);
    $church = $_POST['church'];
    $package = $_POST['package'] ?? '3_months';

    // Validation logic
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password_raw !== $re_password) {
        $error = "Passwords do not match!";
    } elseif (empty($fullname) || strlen($fullname) < 3) {
        $error = "Please enter your full name (at least 3 characters).";
    } elseif (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic_number)) {
        $error = "Please enter a valid NIC Number (9 digits + V/X or 12 digits).";
    }
    // NIC vs DOB & Gender cross-validation
    elseif (!empty($dob) && !empty($sex)) {
        $nic_year = 0;
        $nic_days = 0;

        if (strlen($nic_number) == 12) {
            // New NIC: YYYYDDD#####
            $nic_year = intval(substr($nic_number, 0, 4));
            $nic_days = intval(substr($nic_number, 4, 3));
        } else {
            // Old NIC: YYDDD####V/X
            $nic_year = 1900 + intval(substr($nic_number, 0, 2));
            $nic_days = intval(substr($nic_number, 2, 3));
        }

        // Determine gender from day count
        $nic_gender = 'Male';
        if ($nic_days > 500) {
            $nic_gender = 'Female';
            $nic_days -= 500;
        }

        // Convert day-of-year to month-day
        $nic_date = DateTime::createFromFormat('Y-z', $nic_year . '-' . ($nic_days - 1));
        $entered_dob = new DateTime($dob);

        if (!$nic_date) {
            $error = "NIC number contains an invalid date. Please check your NIC.";
        } elseif ($nic_date->format('Y-m-d') !== $entered_dob->format('Y-m-d')) {
            $error = "Your Date of Birth does not match your NIC Number. NIC indicates: " . $nic_date->format('Y-m-d') . " (ඔබගේ උපන්දිනය NIC අංකයට නොගැලපේ)";
        } elseif ($nic_gender !== $sex) {
            $error = "Your Gender does not match your NIC Number. NIC indicates: " . $nic_gender . " (ඔබගේ ස්ත්‍රී පුරුෂ භාවය NIC අංකයට නොගැලපේ)";
        }
    } elseif ($age < 18 || $age > 80) {
        $error = "Age must be between 18 and 80.";
    } elseif (empty($address)) {
        $error = "Permanent address is required.";
    } elseif (empty($hometown) || empty($district)) {
        $error = "Hometown and District are required.";
    } elseif (strlen($my_phone) < 9 || strlen($my_phone) > 15 || !preg_match('/^[0-9+]+$/', $my_phone)) {
        $error = "Please enter a valid WhatsApp number.";
    } elseif (empty($church)) {
        $error = "Please select or enter your church/ministry.";
    } elseif (empty($pastor_name)) {
        $error = "Pastor/Father's name is required.";
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
            $church_ins = $pdo->prepare("INSERT IGNORE INTO churches (name, pastor_name, location) VALUES (?, ?, ?)");
            $church_ins->execute([$custom_church_name, $custom_pastor, $custom_location]);
            $church = $custom_church_name;
        }
    }

    // Photo Upload Validation
    $photo_path = null;
    $payment_slip_path = null;

    if (empty($error)) {
        // --- Photo Upload ---
        if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($_FILES['file-upload']['name'], PATHINFO_EXTENSION));
            $size = $_FILES['file-upload']['size'];

            if (!in_array($ext, $allowed)) {
                $error = "Only JPG, PNG and GIF images are allowed.";
            } elseif ($size > 5 * 1024 * 1024) {
                $error = "Photo size must be less than 5MB.";
            } else {
                $target_dir = "uploads/";
                if (!is_dir($target_dir))
                    mkdir($target_dir, 0777, true);
                $file_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $target_file = $target_dir . $file_name;
                if (move_uploaded_file($_FILES["file-upload"]["tmp_name"], $target_file)) {
                    $photo_path = $target_file;
                } else {
                    $error = "Failed to save uploaded photo.";
                }
            }
        } else {
            $error = "Please upload a clear photograph of yourself.";
        }

        // --- Payment Slip Upload ---
        if (empty($error)) {
            if (isset($_FILES['payment-slip']) && $_FILES['payment-slip']['error'] == 0) {
                $allowed_slip = ['jpg', 'jpeg', 'png', 'pdf'];
                $ext_slip = strtolower(pathinfo($_FILES['payment-slip']['name'], PATHINFO_EXTENSION));
                $size_slip = $_FILES['payment-slip']['size'];

                if (!in_array($ext_slip, $allowed_slip)) {
                    $error = "Invalid format for payment slip. Only JPG, PNG and PDF are allowed.";
                } elseif ($size_slip > 5 * 1024 * 1024) {
                    $error = "Payment slip size must be less than 5MB.";
                } else {
                    $target_dir_slip = "uploads/payment_slips/";
                    if (!is_dir($target_dir_slip))
                        mkdir($target_dir_slip, 0777, true);
                    $file_name_slip = 'slip_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext_slip;
                    $target_file_slip = $target_dir_slip . $file_name_slip;
                    if (move_uploaded_file($_FILES["payment-slip"]["tmp_name"], $target_file_slip)) {
                        $payment_slip_path = $target_file_slip;
                    } else {
                        $error = "Failed to save payment slip.";
                    }
                }
            } else {
                $error = "Please upload the payment slip to proceed with registration.";
            }
        }
    }

    if (empty($error)) {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);
        try {
            $sql = "INSERT INTO candidates (email, password, denomination, catholic_by_birth, nic_number, christianization_year, sacraments_received, fullname, sex, dob, age, nationality, language, address, hometown, district, province, height, occupation, edu_qual, add_qual, marital_status, children, children_details, illness, habits, church, pastor_name, pastor_phone, parent_phone, my_phone, photo_path, payment_slip_path, package, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$email, $password, $denomination, $catholic_by_birth, $nic_number, $christianization_year, $sacraments, $fullname, $sex, $dob, $age, $nationality, $language, $address, $hometown, $district, $province, $height, $occupation, $edu_qual, $add_qual, $marital_status, $children, $children_details, $illness, $habits, $church, $pastor_name, $pastor_phone, $parent_phone, $my_phone, $photo_path, $payment_slip_path, $package]);

            // Generate Registration Number: YYMMDDC
            $new_id = $pdo->lastInsertId();
            $date_prefix = date('ymd'); // e.g., 260306
            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM candidates WHERE DATE(created_at) = CURDATE() AND id <= ?");
            $count_stmt->execute([$new_id]);
            $daily_count = $count_stmt->fetchColumn();
            $reg_number = $date_prefix . $daily_count; // e.g., 2603061

            // Update the record with the registration number
            $update_stmt = $pdo->prepare("UPDATE candidates SET reg_number = ? WHERE id = ?");
            $update_stmt->execute([$reg_number, $new_id]);

            header("Location: login.php?registered=true&reg=" . urlencode($reg_number));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "This email or NIC Number is already registered.";
            } else {
                $error = "Registration failed. Please try again later.";
            }
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="text-center mb-10 reveal reveal-up">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo $denomination; ?> Registration</h1>
            <p class="mt-2 text-gray-600">Please fill in your details accurately. Your profile will be reviewed by our
                team before approval.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
            <?php
        endif; ?>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden reveal reveal-scale delay-200">
            <!-- Form -->
            <form class="p-8 space-y-8" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="denomination" value="<?php echo $denomination; ?>">

                <!-- Account Information -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Account Setup (ගිණුම පිහිටුවීම)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address (විද්‍යුත්
                                තැපෑල)</label>
                            <input type="email" name="email" required placeholder="example@mail.com"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password (මුරපදය)</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required minlength="6"
                                    oninput="checkStrength(this.value)"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                                <button type="button" onclick="togglePassword('password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eye-icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <!-- Strength Indicator -->
                            <div class="mt-2 flex items-center gap-2">
                                <div class="flex-grow h-1 bg-gray-200 rounded-full overflow-hidden flex">
                                    <div id="strength-bar" class="h-full w-0 transition-all duration-500"></div>
                                </div>
                                <span id="strength-text"
                                    class="text-[9px] font-black uppercase tracking-widest text-gray-400">Weak</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password (මුරපදය තහවුරු
                                කරන්න)</label>
                            <div class="relative">
                                <input id="re_password" type="password" name="re_password" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
                                <button type="button" onclick="togglePassword('re_password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    <svg id="eye-icon-re_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catholic Specific Section -->
                <?php if ($denomination === 'Catholic'): ?>
                    <div class="reveal reveal-up bg-blue-50/30 p-6 rounded-2xl border border-blue-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 2L12 22M7 7L17 7" />
                            </svg>
                            Catholic Faith Life (කතෝලික ජීවිතය)
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catholic by birth? (උපතින්
                                    කතෝලිකද?)</label>
                                <select name="catholic_by_birth" onchange="toggleChristianization(this.value)"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div id="christianization_field" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year of Christianization (කිතුනු
                                    වූ වර්ෂය)</label>
                                <input type="number" name="christianization_year" placeholder="YYYY" min="1950"
                                    max="<?php echo date('Y'); ?>"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div id="sacraments_field" class="md:col-span-2 hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-1">The bonuses you have currently
                                    received (ලබාගෙන ඇති ආශිර්වාද / සක්‍රමේන්තු)</label>
                                <input type="text" name="sacraments_received"
                                    placeholder="Baptism, Holy Communion, Confirmation, etc."
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Personal Details -->
                <div class="reveal reveal-up">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name (නම)</label>
                            <input type="text" name="fullname" required minlength="3"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIC Number (හැඳුනුම්පත්
                                අංකය)</label>
                            <input type="text" name="nic_number" id="nic_input" required
                                placeholder="Ex: 199012345678 or 901234567V" oninput="validateNIC()"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent uppercase">
                            <div id="nic_feedback" class="mt-2 text-xs font-bold hidden"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex (ස්ත්‍රී පුරුෂ භාවය)</label>
                            <select name="sex"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth (උපන්දිනය)</label>
                            <input type="date" name="dob" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Age (වයස)</label>
                            <input type="number" name="age" required min="18" max="80"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality (ජාතිය)</label>
                            <select name="nationality" id="nationality"
                                onchange="toggleOptionField('nationality', 'other_nationality_div', 'other_nationality')"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select Nationality</option>
                                <option value="Sri Lankan">Sri Lankan (ශ්‍රී ලාංකික)</option>
                                <option value="Sinhalese">Sinhalese (සිංහල)</option>
                                <option value="Tamil">Tamil (දෙමළ)</option>
                                <option value="Burgher">Burgher (බර්ගර්)</option>
                                <option value="Other">Other (වෙනත්)</option>
                            </select>
                            <div id="other_nationality_div" class="hidden mt-3 animate-fade-in">
                                <input type="text" name="other_nationality" id="other_nationality"
                                    placeholder="Please specify your nationality"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mother Tongue (මව්බස)</label>
                            <select name="language" id="language"
                                onchange="toggleOptionField('language', 'other_language_div', 'other_language')"
                                required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select Mother Tongue</option>
                                <option value="Sinhala">Sinhala (සිංහල)</option>
                                <option value="Tamil">Tamil (දෙමළ)</option>
                                <option value="English">English (ඉංග්‍රීසි)</option>
                                <option value="Other">Other (වෙනත්)</option>
                            </select>
                            <div id="other_language_div" class="hidden mt-3 animate-fade-in">
                                <input type="text" name="other_language" id="other_language"
                                    placeholder="Please specify your mother tongue"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Height in Feet (උස -
                                අඩි)</label>
                            <input type="number" name="height" step="0.1" min="3" max="8" required placeholder="Ex: 5.6"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="reveal reveal-up">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Location</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address (ස්ථිර
                                පදිංචි)</label>
                            <textarea name="address" rows="2" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hometown (ගම)</label>
                            <input type="text" name="hometown" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District (දිස්ත්‍රික්කය)</label>
                            <select name="district" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select District</option>
                                <option value="Ampara">Ampara (අම්පාර)</option>
                                <option value="Anuradhapura">Anuradhapura (අනුරාධපුර)</option>
                                <option value="Badulla">Badulla (බදුල්ල)</option>
                                <option value="Batticaloa">Batticaloa (මඩකලපුව)</option>
                                <option value="Colombo">Colombo (කොළඹ)</option>
                                <option value="Galle">Galle (ගාල්ල)</option>
                                <option value="Gampaha">Gampaha (ගම්පහ)</option>
                                <option value="Hambantota">Hambantota (හම්බන්තොට)</option>
                                <option value="Jaffna">Jaffna (යාපනය)</option>
                                <option value="Kalutara">Kalutara (කළුතර)</option>
                                <option value="Kandy">Kandy (මහනුවර)</option>
                                <option value="Kegalle">Kegalle (කෑගල්ල)</option>
                                <option value="Kilinochchi">Kilinochchi (කිලිනොච්චිය)</option>
                                <option value="Kurunegala">Kurunegala (කුරුණෑගල)</option>
                                <option value="Mannar">Mannar (මන්නාරම)</option>
                                <option value="Matale">Matale (මාතලේ)</option>
                                <option value="Matara">Matara (මාතර)</option>
                                <option value="Moneragala">Moneragala (මොණරාගල)</option>
                                <option value="Mullaitivu">Mullaitivu (මුලතිව්)</option>
                                <option value="Nuwara Eliya">Nuwara Eliya (නුවර එළිය)</option>
                                <option value="Polonnaruwa">Polonnaruwa (පොළොන්නරුව)</option>
                                <option value="Puttalam">Puttalam (පුත්තලම)</option>
                                <option value="Ratnapura">Ratnapura (රත්නපුර)</option>
                                <option value="Trincomalee">Trincomalee (ත්‍රිකුණාමලය)</option>
                                <option value="Vavuniya">Vavuniya (වවුනියාව)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province (පළාත)</label>
                            <select name="province" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select Province</option>
                                <option value="Western">Western (බස්නාහිර)</option>
                                <option value="Central">Central (මධ්‍යම)</option>
                                <option value="Southern">Southern (දකුණු)</option>
                                <option value="Northern">Northern (උතුරු)</option>
                                <option value="Eastern">Eastern (නැගෙනහිර)</option>
                                <option value="North Western">North Western (වයඹ)</option>
                                <option value="North Central">North Central (උතුරු මැද)</option>
                                <option value="Uva">Uva (ඌව)</option>
                                <option value="Sabaragamuwa">Sabaragamuwa (සබරගමුව)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Professional & Education -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Education & Profession (අධ්‍යාපනය හා
                        වෘත්තිය)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation (වෘත්තිය)</label>
                            <select name="occupation" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select Occupation (රැකියාව තෝරන්න)</option>

                                <optgroup label="Government &amp; Public Service (රාජ්‍ය සේවය)">
                                    <option value="Government Officer">Government Officer (රාජ්‍ය නිලධාරී)</option>
                                    <option value="Teacher">Teacher (ගුරුවරයා / ගුරුවරිය)</option>
                                    <option value="Principal">Principal (විදුහල්පති)</option>
                                    <option value="Lecturer">Lecturer (කථිකාචාර්ය)</option>
                                    <option value="Police Officer">Police Officer (පොලිස් නිලධාරී)</option>
                                    <option value="Military Officer">Military / Armed Forces (හමුදා නිලධාරී)</option>
                                    <option value="Public Health Inspector">Public Health Inspector (PHI)</option>
                                    <option value="Local Government Officer">Local Government Officer (ප්‍රාදේශීය සභා
                                        නිලධාරී)</option>
                                    <option value="Postal Officer">Postal Officer (තැපෑල් නිලධාරී)</option>
                                </optgroup>

                                <optgroup label="Healthcare (සෞඛ්‍ය සේවය)">
                                    <option value="Doctor">Doctor / Physician (වෛද්‍යවරයා)</option>
                                    <option value="Nurse">Nurse (හෙද / හෙදිය)</option>
                                    <option value="Pharmacist">Pharmacist (ඖෂධවේදී)</option>
                                    <option value="Dentist">Dentist (දන්ත වෛද්‍යවරයා)</option>
                                    <option value="Medical Lab Technician">Medical Lab Technician (වෛද්‍ය රසායනාගාර
                                        තාක්ෂණවේදී)</option>
                                </optgroup>

                                <optgroup label="Engineering &amp; Technology (ඉංජිනේරු &amp; තාක්ෂණ)">
                                    <option value="Engineer">Engineer (ඉංජිනේරු)</option>
                                    <option value="Software Developer">Software Developer / IT (මෘදුකාංග නිර්මාතෘ)
                                    </option>
                                    <option value="Electrician">Electrician (විදුලි කාර්මික)</option>
                                    <option value="Mechanic">Mechanic (යන්ත්‍ර කාර්මික)</option>
                                    <option value="Civil Technician">Civil Technician (සිවිල් තාක්ෂණවේදී)</option>
                                    <option value="Architect">Architect (ගෘහ නිර්මාණ ශිල්පී)</option>
                                </optgroup>

                                <optgroup label="Business &amp; Finance (ව්‍යාපාර &amp; මූල්‍ය)">
                                    <option value="Accountant">Accountant (ගණකාධිකාරී)</option>
                                    <option value="Bank Officer">Bank Officer (බැංකු නිලධාරී)</option>
                                    <option value="Businessman / Businesswoman">Businessman / Businesswoman (ව්‍යාපාරික)
                                    </option>
                                    <option value="Manager">Manager (කළමනාකාර)</option>
                                    <option value="Sales Representative">Sales Representative (විකුණුම් නියෝජිත)
                                    </option>
                                    <option value="Clerk">Clerk / Office Staff (කාර්යාල ශ්‍රමිකයා)</option>
                                    <option value="Lawyer">Lawyer (නීතිඥ)</option>
                                </optgroup>

                                <optgroup label="Agriculture &amp; Manual Work (කෘෂිකර්ම &amp; ශ්‍රමය)">
                                    <option value="Farmer">Farmer (ගොවිතැන)</option>
                                    <option value="Fisher">Fisher (ධීවර)</option>
                                    <option value="Builder / Labourer">Builder / Construction Labourer (ඉදිකිරීම්
                                        කාර්මික)</option>
                                    <option value="Driver">Driver (රියදුරු)</option>
                                    <option value="Tailor">Tailor (ටේලර්)</option>
                                    <option value="Cook">Cook / Chef (සූපවේදී)</option>
                                    <option value="Plumber">Plumber (පයිප් කාර්මික)</option>
                                </optgroup>

                                <optgroup label="Religious &amp; Social Service (ආගමික &amp; සමාජ සේවය)">
                                    <option value="Clergy / Religious Worker">Clergy / Religious Worker (ආගමික සේවය)
                                    </option>
                                    <option value="Social Worker">Social Worker (සමාජ සේවක)</option>
                                    <option value="NGO Worker">NGO Worker (රාජ්‍ය නොවන සංවිධාන)</option>
                                </optgroup>

                                <optgroup label="Other (වෙනත්)">
                                    <option value="Student">Student (ශිෂ්‍ය)</option>
                                    <option value="Self-Employed">Self-Employed (ස්වයං රැකියා)</option>
                                    <option value="Housewife / Homemaker">Housewife / Homemaker (ගෘහිණිය)</option>
                                    <option value="Retired">Retired (විශ්‍රාමිකයා)</option>
                                    <option value="Unemployed">Unemployed (රැකියා රහිත)</option>
                                    <option value="Other">Other (වෙනත්)</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Educational Qualifications
                                (අධ්‍යාපන සුදුසුකම්)</label>
                            <select name="edu_qual" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="" disabled selected>Select Qualification</option>
                                <option value="upto O/L">upto O/L (අපොස සාමාන්‍ය පෙළ දක්වා)</option>
                                <option value="upto A/L">upto A/L (අපොස උසස් පෙළ දක්වා)</option>
                                <option value="Degree">Degree (උපාධිය)</option>
                                <option value="Other">Other (වෙනත්)</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Qualifications
                                (අතිරේක සුදුසුකම්)</label>
                            <textarea name="add_qual" rows="2"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Skills, Certifications, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Marital Status & Habits -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Background (පෞද්ගලික පසුබිම)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status (විවාහක
                                තත්ත්වය)</label>
                            <select name="marital_status" onchange="toggleChildren(this.value)"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="Unmarried">Unmarried (අවිවාහක)</option>
                                <option value="Divorced">Divorced (දික්කසාද)</option>
                                <option value="Widowed">Widowed (වැන්දඹු)</option>
                            </select>
                        </div>
                        <div id="children_field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Do you have children? (ඔබට
                                දරුවන් සිටීද?)</label>
                            <select name="children" onchange="toggleChildrenDetails(this.value)"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div id="children_details_field" class="md:col-span-2 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Children Details (Number of
                                children, ages, etc.) (දරුවන් පිළිබඳ විස්තර)</label>
                            <textarea name="children_details" rows="2"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="e.g., 2 children (Ages 5 and 8)"></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Long-term Illness / Chronic
                                Diseases (දීර්ඝ කාලීනව ප්‍රතිකාර ගන්නා වූ රෝගයකින් පෙළෙන්නේද)</label>
                            <select name="illness" id="illness_select" onchange="toggleOtherIllness(this.value)"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="None">None (නැත)</option>
                                <option value="Diabetes">Diabetes (දියවැඩියාව)</option>
                                <option value="High Blood Pressure">High Blood Pressure (අධි රුධිර පීඩනය)</option>
                                <option value="Heart Disease">Heart Disease (හෘද රෝග)</option>
                                <option value="Asthma">Asthma (ඇදුම)</option>
                                <option value="Kidney Disease">Kidney Disease (වකුගඩු රෝග)</option>
                                <option value="Cancer">Cancer (පිළිකා)</option>
                                <option value="Thyroid Disorder">Thyroid Disorder (තයිරොයිඩ් ආබාධ)</option>
                                <option value="Epilepsy">Epilepsy (අපස්මාරය)</option>
                                <option value="Arthritis">Arthritis (ආතරයිටිස් / සන්ධි වේදනාව)</option>
                                <option value="Mental Health Condition">Mental Health Condition (මානසික සෞඛ්‍ය තත්ත්වය)
                                </option>
                                <option value="Liver Disease">Liver Disease (අක්මා රෝග)</option>
                                <option value="Cholesterol">High Cholesterol (අධික කොලෙස්ටරෝල්)</option>
                                <option value="Anemia">Anemia (රක්තහීනතාවය)</option>
                                <option value="Skin Disease">Skin Disease (සම රෝග)</option>
                                <option value="Other">Other (වෙනත්)</option>
                            </select>
                            <div id="other_illness_div" class="hidden mt-3 animate-fade-in">
                                <input type="text" name="other_illness" id="other_illness_input"
                                    placeholder="Please describe your condition (රෝගය විස්තර කරන්න)"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Habits (Betel chewing / Smoking
                                / Alcohol / Drugs)(බුලත්විට/ දුම්පානය /මත්පැන්/ මත්ද්‍රව්‍ය භාවිතය)</label>
                            <div class="flex gap-4 flex-wrap">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="betel"
                                        class="rounded text-primary focus:ring-primary habit-item"
                                        onchange="toggleHabits(this)">
                                    <span>Betel Chewing(බුලත්විට)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="smoking"
                                        class="rounded text-primary focus:ring-primary habit-item"
                                        onchange="toggleHabits(this)">
                                    <span>Smoking(දුම්පානය)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="alcohol"
                                        class="rounded text-primary focus:ring-primary habit-item"
                                        onchange="toggleHabits(this)">
                                    <span>Alcohol(මත්පැන්)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="drugs"
                                        class="rounded text-primary focus:ring-primary habit-item"
                                        onchange="toggleHabits(this)">
                                    <span>Drugs(මත්ද්‍රව්‍ය)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="none" id="habit_none"
                                        class="rounded text-primary focus:ring-primary" onchange="toggleHabits(this)">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <?php echo $denomination === 'Christian' ? 'Denomination (නිකාය)' : 'Church Name (දේවස්ථානය)'; ?>
                            </label>
                            <?php if ($denomination === 'Christian'): ?>
                                <input type="text" name="church" required
                                    placeholder="Enter your Ministry / Denomination Name"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                            <?php else: ?>
                                <select name="church" id="church_select" onchange="toggleOtherChurch(this.value)" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="" disabled selected>Select your church</option>
                                    <?php foreach ($churches_list as $c_name): ?>
                                        <option value="<?php echo htmlspecialchars($c_name); ?>">
                                            <?php echo htmlspecialchars($c_name); ?></option>
                                    <?php endforeach; ?>
                                    <option value="Other">Other (Not in list)</option>
                                </select>
                            <?php endif; ?>
                        </div>

                        <!-- Manual Church Details (Hidden by default) -->
                        <?php if ($denomination !== 'Christian'): ?>
                            <div id="other_church_section"
                                class="md:col-span-2 hidden bg-gray-50 p-6 rounded-xl border border-gray-200 mt-2 space-y-4">
                                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">
                                    Manual Church Details</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Name of Church
                                            (දේවස්ථානය)</label>
                                        <input type="text" name="other_church_name" id="other_church_name"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-500 uppercase mb-1"><?php echo $denomination === 'Christian' ? 'Chief Pastor (ප්‍රධාන දේවගැතිතුමාගේ නම)' : 'Chief Father (ප්‍රධාන පියතුමාගේ නම)'; ?></label>
                                        <input type="text" name="other_church_pastor" id="other_church_pastor"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Location City
                                            (නගරය)</label>
                                        <input type="text" name="other_church_location" id="other_church_location"
                                            class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                    </div>
                                    <div class="md:col-span-2 flex flex-col items-start gap-3">
                                        <button type="button" id="save_church_btn" onclick="saveNewChurch()"
                                            class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition-all shadow-md flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                            </svg>
                                            Save Church Details
                                        </button>
                                        <div id="church_save_message" class="text-sm font-bold hidden"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <?php echo $denomination === 'Christian' ? 'Name of Pastor (ප්‍රධාන දේවගැතිතුමාගේ නම)' : 'Father Name (පියතුමාගේ නම)'; ?>
                            </label>
                            <input type="text" name="pastor_name" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <?php echo $denomination === 'Christian' ? "Pastor's WhatsApp (දේවගැතිතුමාගේ වට්ස්ඇප් අංකය)" : "Father's WhatsApp (පියතුමාගේ වට්ස්ඇප් අංකය)"; ?>
                            </label>
                            <input type="tel" name="pastor_phone" required pattern="[0-9+]{9,15}"
                                title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent's WhatsApp (දෙමාපියන්ගේ
                                වට්ස්ඇප් අංකය)</label>
                            <input type="tel" name="parent_phone" required pattern="[0-9+]{9,15}"
                                title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your WhatsApp (ඔබගේ වට්ස්ඇප්
                                අංකය)</label>
                            <input type="tel" name="my_phone" required pattern="[0-9+]{9,15}"
                                title="Please enter a valid phone number (9-15 digits)" placeholder="07XXXXXXXX"
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <!-- Important Note -->
                        <div class="md:col-span-2 mt-2">
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
                                <div
                                    class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-amber-800">Important Notice</p>
                                    <p class="text-xs text-amber-700 mt-1 leading-relaxed">All communication regarding
                                        your registration — including approvals, updates, and match notifications — will
                                        be conducted through the <strong>Parent's WhatsApp number</strong> provided
                                        above. Please ensure it is correct and active.</p>
                                    <p class="text-xs text-amber-600 mt-2 leading-relaxed font-medium">ඔබගේ ලියාපදිංචිය
                                        සම්බන්ධ සියලුම සන්නිවේදනයන් — අනුමැතිය, යාවත්කාලීන කිරීම් සහ ගැලපීම් දැනුම්දීම්
                                        ඇතුළුව — ඉහත සපයා ඇති <strong>දෙමාපියන්ගේ වට්ස්ඇප් අංකය</strong> හරහා සිදු කෙරේ.
                                        කරුණාකර එය නිවැරදි සහ ක්‍රියාකාරී බව තහවුරු කරන්න.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Verification</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recent Photograph (Face clearly
                                visible) (මෑතකදී ගත් ඡායාරූපයක් (මුහුණ පැහැදිලිව පෙනෙන))</label>
                            <div
                                class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors cursor-pointer bg-gray-50 relative group">
                                <div id="preview-container"
                                    class="hidden w-48 h-64 mb-4 rounded-xl overflow-hidden shadow-lg border-4 border-white">
                                    <img id="image-preview" src="#" alt="Preview" class="w-full h-full object-cover">
                                    <button type="button" onclick="removeImage(event)"
                                        class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors shadow-lg">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div id="upload-placeholder" class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only"
                                                onchange="previewFile(this)" accept="image/*">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Package Selection -->
                        <div class="md:col-span-2 mt-8 space-y-6">
                            <div
                                class="bg-gradient-to-br from-blue-50 to-indigo-50/50 p-6 rounded-2xl border border-blue-200">
                                <h3
                                    class="text-md font-black text-primary uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Select Package (පැකේජය තෝරන්න)
                                </h3>
                                <p class="text-xs text-gray-500 mb-5">Choose how long your profile stays visible. (ඔබගේ
                                    පෝරමය දිස්වන කාලසීමාව තෝරන්න.)</p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <!-- 1 Month (First Visit Offer) -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="package" value="first_visit" class="sr-only peer"
                                            checked onchange="updateFee()">
                                        <div
                                            class="p-5 rounded-2xl border-2 border-gray-200 bg-white text-center transition-all duration-300 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-lg peer-checked:shadow-red-500/10 hover:border-red-300 hover:shadow-md relative overflow-hidden">
                                            <div
                                                class="absolute -top-0 -right-0 bg-red-500 text-white text-[8px] font-black uppercase px-2 py-0.5 rounded-bl-lg tracking-wider">
                                                First Visit Offer</div>
                                            <div class="text-3xl font-black text-gray-800 peer-checked:text-red-600">1st
                                            </div>
                                            <div
                                                class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Visit (පළමු)</div>
                                            <div class="mt-3 text-xl font-black text-red-600">Rs. 500</div>
                                            <div class="text-[10px] text-gray-400 font-bold mt-1">රු. 500</div>
                                        </div>
                                    </label>
                                    <!-- 3 Months -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="package" value="3_months" class="sr-only peer"
                                            onchange="updateFee()">
                                        <div
                                            class="p-5 rounded-2xl border-2 border-gray-200 bg-white text-center transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:shadow-lg peer-checked:shadow-blue-500/10 hover:border-blue-300 hover:shadow-md">
                                            <div class="text-3xl font-black text-gray-800 peer-checked:text-blue-600">3
                                            </div>
                                            <div
                                                class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Months (මාස)</div>
                                            <div class="mt-3 text-xl font-black text-blue-600">Rs. 1,000</div>
                                            <div class="text-[10px] text-gray-400 font-bold mt-1">රු. 1,000</div>
                                        </div>
                                    </label>
                                    <!-- 6 Months -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="package" value="6_months" class="sr-only peer"
                                            onchange="updateFee()">
                                        <div
                                            class="p-5 rounded-2xl border-2 border-gray-200 bg-white text-center transition-all duration-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 peer-checked:shadow-lg peer-checked:shadow-indigo-500/10 hover:border-indigo-300 hover:shadow-md relative overflow-hidden">
                                            <div
                                                class="absolute -top-0 -right-0 bg-indigo-500 text-white text-[8px] font-black uppercase px-3 py-0.5 rounded-bl-lg tracking-wider">
                                                Popular</div>
                                            <div class="text-3xl font-black text-gray-800">6</div>
                                            <div
                                                class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Months (මාස)</div>
                                            <div class="mt-3 text-xl font-black text-indigo-600">Rs. 1,500</div>
                                            <div class="text-[10px] text-gray-400 font-bold mt-1">රු. 1,500</div>
                                        </div>
                                    </label>
                                    <!-- Unlimited -->
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="package" value="unlimited" class="sr-only peer"
                                            onchange="updateFee()">
                                        <div
                                            class="p-5 rounded-2xl border-2 border-gray-200 bg-white text-center transition-all duration-300 peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:shadow-lg peer-checked:shadow-amber-500/10 hover:border-amber-300 hover:shadow-md">
                                            <div class="text-3xl font-black text-gray-800">∞</div>
                                            <div
                                                class="text-xs font-black uppercase tracking-widest text-gray-400 mt-1">
                                                Unlimited (අසීමිත)</div>
                                            <div class="mt-3 text-xl font-black text-amber-600">Rs. 2,500</div>
                                            <div class="text-[10px] text-gray-400 font-bold mt-1">රු. 2,500</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Payment details -->
                            <div class="bg-blue-50/50 p-6 rounded-2xl border-2 border-dashed border-blue-200">
                                <h3
                                    class="text-md font-black text-primary uppercase tracking-widest mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Payment Instructions (ගෙවීම් උපදෙස්)
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="space-y-1">
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Bank Name (බැංකුව)</p>
                                        <p class="text-gray-900 font-black">Commercial Bank</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Branch (ශාඛාව)</p>
                                        <p class="text-gray-900 font-black">katukurunda</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Account Number (ගිණුම්
                                            අංකය)</p>
                                        <p class="text-gray-900 font-black">8027586422</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-gray-500 font-bold uppercase text-[10px]">Account Holder (ගිණුමේ
                                            නම)</p>
                                        <p class="text-gray-900 font-black">DSNJ GALLAGE</p>
                                    </div>
                                    <div class="space-y-1 bg-amber-50 p-2 rounded-lg border border-amber-100">
                                        <p class="text-amber-600 font-black uppercase text-[10px]">Registration Fee
                                            (ලියාපදිංචි ගාස්තුව)</p>
                                        <p id="fee-display" class="text-primary font-black text-base">Registration Fee:
                                            Rs. 1000.00</p>
                                        <p id="fee-sinhala" class="text-gray-500 font-bold text-[11px]">ලියාපදිංචි
                                            ගාස්තුව: රු. 1000.00</p>
                                    </div>
                                </div>
                                <p
                                    class="mt-4 text-[11px] text-gray-500 font-bold uppercase tracking-widest leading-relaxed">
                                    Note: This payment is for office verification purposes only. (සටහන: මෙම ගෙවීම
                                    කාර්යාලීය ප්‍රයෝජනය සඳහා පමණි.)
                                </p>
                            </div>

                            <!-- File input for slip -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Payment Slip (ගෙවීම්
                                    පත මෙහි ඇතුළත් කරන්න)</label>
                                <div
                                    class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors cursor-pointer bg-white relative group">
                                    <div id="slip-preview-container"
                                        class="hidden w-48 h-64 mb-4 rounded-xl overflow-hidden shadow-lg border-4 border-white">
                                        <img id="slip-preview" src="#" alt="Slip Preview"
                                            class="w-full h-full object-cover">
                                        <button type="button" onclick="removeSlip(event)"
                                            class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors shadow-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div id="slip-placeholder" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="payment-slip"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                                <span>Upload Slip</span>
                                                <input id="payment-slip" name="payment-slip" type="file" class="sr-only"
                                                    required onchange="previewSlip(this)"
                                                    accept="image/*,application/pdf">
                                            </label>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG or PDF up to 5MB</p>
                                    </div>
                                </div>
                                <p class="mt-2 text-[10px] text-orange-600 font-bold uppercase tracking-widest">
                                    Mandatory to register * (ලියාපදිංචි වීමට අනිවාර්ය වේ)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <!-- Terms and Conditions Agreement -->
                    <div class="mb-4 bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50">
                        <label class="flex items-start gap-4 cursor-pointer group">
                            <div class="mt-1">
                                <input type="checkbox" name="terms_agreement" required
                                    class="w-6 h-6 rounded-lg border-gray-300 text-primary focus:ring-primary transition-all">
                            </div>
                            <div class="text-sm text-gray-600 leading-relaxed font-medium">
                                <span class="block text-gray-900 font-bold mb-1">Agreement (ගිවිසුම)</span>
                                I have read, understood, and agree to the <a href="terms.php" target="_blank"
                                    class="text-primary font-black hover:underline decoration-2 underline-offset-4">Terms
                                    and Conditions, Privacy Policy</a>, and other guidelines of this platform.
                                <p class="mt-1 text-[11px] text-gray-400 font-bold uppercase tracking-widest">මම මෙහි
                                    ඇති නියමයන් සහ කොන්දේසි කියවා ඒවාට එකඟ වෙමි.</p>
                            </div>
                        </label>
                    </div>

                    <!-- Truthfulness Declaration -->
                    <div class="mb-6 bg-red-50/50 p-6 rounded-2xl border border-red-100/50">
                        <label class="flex items-start gap-4 cursor-pointer group">
                            <div class="mt-1">
                                <input type="checkbox" name="truth_declaration" required
                                    class="w-6 h-6 rounded-lg border-red-300 text-red-600 focus:ring-red-500 transition-all">
                            </div>
                            <div class="text-sm text-gray-600 leading-relaxed font-medium">
                                <span class="block text-gray-900 font-bold mb-1">Declaration of Truthfulness (සත්‍යතා
                                    ප්‍රකාශය)</span>
                                I hereby declare that all information provided in this registration form is
                                <strong>true, accurate, and complete</strong> to the best of my knowledge. I take
                                <strong>full responsibility</strong> for the accuracy of these details and understand
                                that providing false information may result in the rejection or removal of my profile.
                                <p
                                    class="mt-2 text-[11px] text-gray-400 font-bold uppercase tracking-widest leading-relaxed">
                                    මෙම ලියාපදිංචි පෝරමයේ සපයා ඇති සියලු තොරතුරු මාගේ දැනුමට අනුව <strong>සත්‍ය, නිවැරදි
                                        සහ සම්පූර්ණ</strong> බව මම මින් ප්‍රකාශ කරමි. මෙම විස්තරවල නිරවද්‍යතාවය සඳහා
                                    <strong>මම පූර්ණ වගකීම භාර ගනිමි</strong>. අසත්‍ය තොරතුරු සැපයීම මාගේ පෝරමය
                                    ප්‍රතික්ෂේප කිරීමට හෝ ඉවත් කිරීමට හේතු විය හැකි බව මම අවබෝධ කරගනිමි.</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-xl text-xl font-black text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:-translate-y-1">
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
    function validateNIC() {
        const nic = document.getElementById('nic_input').value.trim().toUpperCase();
        const feedback = document.getElementById('nic_feedback');
        const dobField = document.querySelector('input[name="dob"]');
        const ageField = document.querySelector('input[name="age"]');
        const sexField = document.querySelector('select[name="sex"]');

        // Reset
        feedback.classList.add('hidden');
        feedback.className = 'mt-2 text-xs font-bold hidden';

        let nicYear, nicDays;

        // Check format
        if (/^[0-9]{12}$/.test(nic)) {
            // New NIC: YYYYDDD#####
            nicYear = parseInt(nic.substring(0, 4));
            nicDays = parseInt(nic.substring(4, 7));
        } else if (/^[0-9]{9}[VX]$/i.test(nic)) {
            // Old NIC: YYDDD####V/X
            nicYear = 1900 + parseInt(nic.substring(0, 2));
            nicDays = parseInt(nic.substring(2, 5));
        } else {
            if (nic.length > 0) {
                feedback.textContent = '⚠ Invalid NIC format';
                feedback.className = 'mt-2 text-xs font-bold text-orange-500';
                feedback.classList.remove('hidden');
            }
            return;
        }

        // Determine gender
        let gender = 'Male';
        if (nicDays > 500) {
            gender = 'Female';
            nicDays -= 500;
        }

        // Validate day range
        if (nicDays < 1 || nicDays > 366) {
            feedback.textContent = '❌ NIC contains an invalid date (අවලංගු දිනයක්)';
            feedback.className = 'mt-2 text-xs font-bold text-red-500';
            feedback.classList.remove('hidden');
            return;
        }

        // Convert day-of-year to date
        const dateObj = new Date(nicYear, 0); // Jan 1 of that year
        dateObj.setDate(nicDays);

        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        const nicDOB = nicYear + '-' + month + '-' + day;

        // Auto-fill DOB, Age, and Sex
        if (dobField) dobField.value = nicDOB;
        if (sexField) sexField.value = gender;
        if (ageField) {
            const today = new Date();
            let calcAge = today.getFullYear() - nicYear;
            const monthDiff = today.getMonth() - dateObj.getMonth();
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dateObj.getDate())) {
                calcAge--;
            }
            ageField.value = calcAge;
        }

        // Show success feedback
        const genderSi = gender === 'Male' ? 'පුරුෂ' : 'ස්ත්‍රී';
        feedback.innerHTML = '✅ NIC verified — DOB: <strong>' + nicDOB + '</strong> | Gender: <strong>' + gender + ' (' + genderSi + ')</strong>';
        feedback.className = 'mt-2 text-xs font-bold text-green-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200';
        feedback.classList.remove('hidden');
    }
    function updateFee() {
        const selected = document.querySelector('input[name="package"]:checked');
        const feeDisplay = document.getElementById('fee-display');
        const feeSinhala = document.getElementById('fee-sinhala');
        if (!selected || !feeDisplay) return;

        const fees = {
            'first_visit': { en: 'Registration Fee: Rs. 500.00', si: 'ලියාපදිංචි ගාස්තුව: රු. 500.00' },
            '3_months': { en: 'Registration Fee: Rs. 1,000.00', si: 'ලියාපදිංචි ගාස්තුව: රු. 1,000.00' },
            '6_months': { en: 'Registration Fee: Rs. 1,500.00', si: 'ලියාපදිංචි ගාස්තුව: රු. 1,500.00' },
            'unlimited': { en: 'Registration Fee: Rs. 2,500.00', si: 'ලියාපදිංචි ගාස්තුව: රු. 2,500.00' }
        };

        const fee = fees[selected.value] || fees['first_visit'];
        feeDisplay.textContent = fee.en;
        if (feeSinhala) feeSinhala.textContent = fee.si;
    }

    function toggleOtherIllness(val) {
        const div = document.getElementById('other_illness_div');
        if (val === 'Other') {
            div.classList.remove('hidden');
        } else {
            div.classList.add('hidden');
        }
    }

    function toggleHabits(el) {
        const noneBox = document.getElementById('habit_none');
        const habitBoxes = document.querySelectorAll('.habit-item');

        if (el === noneBox && noneBox.checked) {
            // "None" was checked → uncheck & disable all others
            habitBoxes.forEach(cb => { cb.checked = false; cb.disabled = true; cb.parentElement.classList.add('opacity-40'); });
        } else if (el !== noneBox && el.checked) {
            // A habit was checked → uncheck & disable "None"
            noneBox.checked = false;
            noneBox.disabled = true;
            noneBox.parentElement.classList.add('opacity-40');
        } else {
            // Something was unchecked — check if any habit is still selected
            const anyHabitChecked = [...habitBoxes].some(cb => cb.checked);
            if (!anyHabitChecked) {
                // No habits selected → re-enable "None"
                noneBox.disabled = false;
                noneBox.parentElement.classList.remove('opacity-40');
            }
            if (!noneBox.checked) {
                // "None" not checked → re-enable all habits
                habitBoxes.forEach(cb => { cb.disabled = false; cb.parentElement.classList.remove('opacity-40'); });
            }
        }
    }
    function toggleChildren(status) {
        const childrenField = document.getElementById('children_field');
        const detailsField = document.getElementById('children_details_field');
        const childrenSelect = document.getElementsByName('children')[0];

        if (status === 'Divorced' || status === 'Widowed') {
            childrenField.classList.remove('hidden');
        } else {
            childrenField.classList.add('hidden');
            detailsField.classList.add('hidden');
            childrenSelect.value = 'No';
        }
    }

    function toggleChristianization(val) {
        const yearField = document.getElementById('christianization_field');
        const sacramentsField = document.getElementById('sacraments_field');

        if (val === 'No') {
            yearField.classList.remove('hidden');
            sacramentsField.classList.remove('hidden');
        } else {
            yearField.classList.add('hidden');
            sacramentsField.classList.add('hidden');
        }
    }

    function toggleChildrenDetails(hasChildren) {
        const detailsField = document.getElementById('children_details_field');
        if (hasChildren === 'Yes') {
            detailsField.classList.remove('hidden');
        } else {
            detailsField.classList.add('hidden');
        }
    }

    function toggleOptionField(selectId, divId, inputId) {
        const select = document.getElementById(selectId);
        const div = document.getElementById(divId);
        const input = document.getElementById(inputId);
        if (select.value === 'Other') {
            div.classList.remove('hidden');
            input.setAttribute('required', 'true');
        } else {
            div.classList.add('hidden');
            input.removeAttribute('required');
            input.value = '';
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

        if (file) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                alert('Only JPG, PNG and GIF images are allowed.');
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Photo size must be less than 5MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onloadend = function () {
                preview.src = reader.result;
                container.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
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

                const select = document.getElementById('church_select');
                const option = new Option(name, name);
                select.add(option, select.options[select.options.length - 1]);
                select.value = name;

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

    function previewSlip(input) {
        const preview = document.getElementById('slip-preview');
        const container = document.getElementById('slip-preview-container');
        const placeholder = document.getElementById('slip-placeholder');
        const file = input.files[0];

        if (file) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (!['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
                alert('Only JPG, PNG and PDF files are allowed.');
                input.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Slip size must be less than 5MB.');
                input.value = '';
                return;
            }

            if (ext === 'pdf') {
                preview.src = "https://cdn-icons-png.flaticon.com/512/337/337946.png";
                container.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                const reader = new FileReader();
                reader.onloadend = function () {
                    preview.src = reader.result;
                    container.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
    }

    function removeSlip(event) {
        event.stopPropagation();
        const input = document.getElementById('payment-slip');
        const preview = document.getElementById('slip-preview');
        const container = document.getElementById('slip-preview-container');
        const placeholder = document.getElementById('slip-placeholder');

        input.value = "";
        preview.src = "";
        container.classList.add('hidden');
        placeholder.classList.remove('hidden');
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

    // --- ENHANCED FORM VALIDATION ---
    const validateRules = {
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: "Please enter a valid email address."
        },
        password: {
            minLength: 6,
            message: "Password must be at least 6 characters."
        },
        re_password: {
            match: 'password',
            message: "Passwords do not match."
        },
        fullname: {
            minLength: 3,
            message: "Name must be at least 3 characters."
        },
        nic_number: {
            pattern: /^([0-9]{9}[vVxX]|[0-9]{12})$/,
            message: "Enter a valid NIC (9 digits + V/X or 12 digits)."
        },
        dob: {
            required: true,
            message: "Date of birth is required."
        },
        age: {
            min: 18,
            max: 80,
            message: "Age must be between 18 and 80."
        },
        nationality: { minLength: 2, message: "Nationality is required." },
        language: { minLength: 2, message: "Mother tongue is required." },
        address: { minLength: 10, message: "Full address is required." },
        hometown: { minLength: 2, message: "Hometown is required." },
        district: { minLength: 2, message: "District is required." },
        occupation: { required: true, message: "Please select your occupation." },
        church: { required: true, message: "Please select or enter your church." },
        pastor_name: { minLength: 3, message: "Pastor/Father's name is required." },
        pastor_phone: { pattern: /^[0-9+]{9,15}$/, message: "Valid phone required." },
        my_phone: { pattern: /^[0-9+]{9,15}$/, message: "Valid WhatsApp required." }
    };

    function showError(field, message) {
        clearError(field);
        field.classList.add('!border-red-400', '!bg-red-50/50', 'ring-2', 'ring-red-100');

        const wrapper = field.closest('div');
        const msg = document.createElement('div');
        msg.className = 'validation-error flex items-center gap-1.5 text-red-500 mt-1.5 animate-bounce-in';
        msg.innerHTML = `
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
        <span class="text-[10px] font-black uppercase tracking-tight">${message}</span>
    `;
        wrapper.appendChild(msg);
    }

    function clearError(field) {
        if (!field) return;
        field.classList.remove('!border-red-400', '!bg-red-50/50', 'ring-2', 'ring-red-100');
        const wrapper = field.closest('div');
        const existing = wrapper.querySelector('.validation-error');
        if (existing) existing.remove();
    }

    // Real-time validation
    document.querySelectorAll('input, select, textarea').forEach(field => {
        ['blur', 'change'].forEach(event => {
            field.addEventListener(event, function () {
                const name = this.getAttribute('name') || this.id;
                const rule = validateRules[name];
                if (!rule) return;

                const val = this.value.trim();
                let hasError = false;

                if (rule.required && !val) hasError = true;
                if (rule.pattern && !rule.pattern.test(val)) hasError = true;
                if (rule.minLength && val.length < rule.minLength) hasError = true;
                if (rule.min !== undefined && parseInt(val) < rule.min) hasError = true;
                if (rule.max !== undefined && parseInt(val) > rule.max) hasError = true;
                if (rule.match && val !== document.getElementById(rule.match).value) hasError = true;

                if (hasError) showError(this, rule.message);
                else clearError(this);
            });
        });

        field.addEventListener('input', function () {
            clearError(this);
        });
    });

    document.querySelector('form').addEventListener('submit', function (e) {
        let firstInvalid = null;

        for (const [name, rule] of Object.entries(validateRules)) {
            const field = (name === 'password' || name === 're_password') ? document.getElementById(name) : document.getElementsByName(name)[0];
            if (!field) continue;

            const val = field.value.trim();
            let hasError = false;

            if (rule.required && !val) hasError = true;
            if (rule.pattern && !rule.pattern.test(val)) hasError = true;
            if (rule.minLength && val.length < rule.minLength) hasError = true;
            if (rule.min !== undefined && parseInt(val) < rule.min) hasError = true;
            if (rule.max !== undefined && parseInt(val) > rule.max) hasError = true;
            if (rule.match && val !== document.getElementById(rule.match).value) hasError = true;

            if (hasError) {
                showError(field, rule.message);
                if (!firstInvalid) firstInvalid = field;
            }
        }

        // Photo check
        const photo = document.getElementById('file-upload');
        if (photo && photo.files.length === 0) {
            alert("Please upload a photograph (ඡායාරූපයක් එක් කරන්න).");
            if (!firstInvalid) firstInvalid = photo.closest('div');
        }

        // Terms check
        const terms = document.getElementsByName('terms_agreement')[0];
        if (terms && !terms.checked) {
            alert("You must agree to the Terms and Conditions to proceed.");
            if (!firstInvalid) firstInvalid = terms;
        }

        if (firstInvalid) {
            e.preventDefault();
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // DOB Auto-calc
    document.getElementsByName('dob')[0].addEventListener('change', function () {
        const dob = new Date(this.value);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const m = today.getMonth() - dob.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
        document.getElementsByName('age')[0].value = age;
        clearError(document.getElementsByName('age')[0]);
    });

    function checkStrength(password) {
        const bar = document.getElementById('strength-bar');
        const text = document.getElementById('strength-text');
        if (!bar || !text) return;

        let strength = 0;
        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;

        const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'];
        const labels = ['Weak', 'Fair', 'Good', 'Strong'];
        const widths = ['25%', '50%', '75%', '100%'];
        const idx = Math.max(0, Math.min(strength, labels.length) - 1);

        if (password.length === 0) {
            bar.style.width = '0';
            text.textContent = 'None';
            text.className = 'text-[9px] font-black uppercase tracking-widest text-gray-400';
        } else {
            bar.style.width = widths[idx];
            bar.className = `h-full transition-all duration-500 ${colors[idx]}`;
            text.textContent = labels[idx];
            text.className = `text-[9px] font-black uppercase tracking-widest ${colors[idx].replace('bg-', 'text-')}`;
        }
    }
</script>


<?php include 'includes/footer.php'; ?>