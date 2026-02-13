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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and Get Input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password_raw = $_POST['password'];
    $re_password = $_POST['re_password'];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password_raw) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password_raw !== $re_password) {
        $error = "Passwords do not match!";
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);
    $fullname = $_POST['fullname'];
    $sex = $_POST['sex'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $nationality = $_POST['nationality'];
    $language = $_POST['language'];
    $address = $_POST['address'];
    $hometown = $_POST['hometown'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $height = $_POST['height'];
    $occupation = $_POST['occupation'];
    $edu_qual = $_POST['edu_qual'];
    $add_qual = $_POST['add_qual'];
    $marital_status = $_POST['marital_status'];
    $children = isset($_POST['children']) ? $_POST['children'] : 'No';
    $illness = $_POST['illness'];
    $habits = isset($_POST['habit']) ? implode(',', $_POST['habit']) : 'None';
    $church = $_POST['church'];
    $pastor_name = $_POST['pastor_name'];
    $pastor_phone = $_POST['pastor_phone'];
    $parent_phone = $_POST['parent_phone'];
    $my_phone = $_POST['my_phone'];
    
    // File Upload
    $photo_path = null;
    if (isset($_FILES['file-upload']) && $_FILES['file-upload']['error'] == 0) {
        $target_dir = "uploads/";
        $file_name = time() . '_' . basename($_FILES["file-upload"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["file-upload"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            $error = "Error uploading photo.";
        }
    }

    if (empty($error)) {
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
                                <input id="password" type="password" name="password" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent pr-10">
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="fullname" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                            <select name="sex" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="dob" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                            <input type="number" name="age" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                            <input type="text" name="nationality" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mother Tongue</label>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                            <textarea name="address" rows="2" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hometown</label>
                            <input type="text" name="hometown" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                            <input type="text" name="district" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                            <input type="text" name="province" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                 <!-- Professional & Education -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Education & Profession</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                            <input type="text" name="occupation" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Educational Qualifications</label>
                            <textarea name="edu_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Degrees, Diplomas, etc."></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Qualifications</label>
                            <textarea name="add_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Skills, Certifications, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Marital Status & Habits -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Background</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                            <select name="marital_status" onchange="toggleChildren(this.value)" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="Unmarried">Unmarried</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>
                        <div id="children_field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Do you have children?</label>
                            <select name="children" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="No">No</option>
                                <option value="Yes">Yes</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Long-term Illness (requiring continuous treatment)</label>
                            <textarea name="illness" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Describe if any, otherwise leave blank or type 'None'"></textarea>
                        </div>
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Habits (Betel chewing / Smoking / Alcohol / Drugs)</label>
                            <div class="flex gap-4 flex-wrap">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="betel" class="rounded text-primary focus:ring-primary">
                                    <span>Betel Chewing</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="smoking" class="rounded text-primary focus:ring-primary">
                                    <span>Smoking</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="alcohol" class="rounded text-primary focus:ring-primary">
                                    <span>Alcohol</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="drugs" class="rounded text-primary focus:ring-primary">
                                    <span>Drugs</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="none" class="rounded text-primary focus:ring-primary">
                                    <span>None</span>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Denomination / Church Name</label>
                            <input type="text" name="church" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name of Pastor</label>
                            <input type="text" name="pastor_name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pastor's WhatsApp</label>
                            <input type="tel" name="pastor_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent's WhatsApp</label>
                            <input type="tel" name="parent_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your WhatsApp</label>
                            <input type="tel" name="my_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Verification</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recent Photograph (Face clearly visible)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors cursor-pointer bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only">
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
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:-translate-y-1">
                        Submit Application
                    </button>
                    <p class="mt-4 text-center text-sm text-gray-500">
                        By submitting this form, you certify that all information provided is true and accurate.
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
</script>

<?php include 'includes/footer.php'; ?>
