<?php
session_start();
include 'includes/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = trim($_POST['login_id'] ?? $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login_id) || empty($password)) {
        $error = "Please enter both credentials and password.";
    }
    else {
        try {
            // 1. Try to find in candidates table using Email
            $stmt = $pdo->prepare("SELECT * FROM candidates WHERE email = ?");
            $stmt->execute([$login_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                if ($user['status'] !== 'approved') {
                    $error = "Your profile is in review.";
                }
                elseif ($user['is_disabled'] == 1) {
                    $error = "Your profile has been disabled. Please contact the administrator for assistance.";
                }
                else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['fullname'];
                    $_SESSION['role'] = 'candidate';
                    $_SESSION['denomination'] = $user['denomination'];
                    header("Location: my_profile.php");
                    exit();
                }
            }
            else {
                // 2. Try to find in admins table using Username
                try {
                    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
                    $stmt->execute([$login_id]);
                    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($admin && password_verify($password, $admin['password'])) {
                        $_SESSION['user_id'] = $admin['id'];
                        $_SESSION['username'] = $admin['username'];
                        $_SESSION['role'] = 'admin';
                        header("Location: admin_dashboard.php");
                        exit();
                    }
                    else {
                        $error = "Invalid login credentials.";
                    }
                }
                catch (PDOException $e) {
                    $error = "Invalid login credentials.";
                }
            }
        }
        catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen flex flex-col justify-center py-12 px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md reveal reveal-up">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Sign in</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your credentials to access your account</p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md reveal reveal-scale delay-200">
        <div class="bg-white py-10 px-8 shadow-md rounded-2xl border border-gray-100">
            
            <?php if ($error): ?>
                <div class="mb-6 p-3 bg-red-50 border border-red-100 text-red-600 rounded-lg text-sm font-medium animate-shake">
                    <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                <div class="mb-6 p-3 bg-green-50 border border-green-100 text-green-700 rounded-lg text-sm font-medium">
                    Password updated successfully.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
                <div class="mb-6 p-5 bg-green-50 border-2 border-green-200 rounded-2xl text-center">
                    <div class="flex justify-center mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                    <p class="text-green-700 font-bold text-sm">Registration Successful! (ලියාපදිංචිය සාර්ථකයි!)</p>
                    <?php if (isset($_GET['reg'])): ?>
                        <div class="mt-3 bg-white rounded-xl p-4 border border-green-100">
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest mb-1">Your Registration Number (ඔබගේ ලියාපදිංචි අංකය)</p>
                            <p class="text-3xl font-black text-primary tracking-wider"><?php echo htmlspecialchars($_GET['reg']); ?></p>
                        </div>
                    <?php endif; ?>
                    <p class="mt-3 text-xs text-gray-500 font-medium">Your profile is under review. You can login after approval.<br>(ඔබගේ පෝරමය සමාලෝචනය යටතේ පවතී. අනුමැතියෙන් පසු පුරනය වන්න.)</p>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="" method="POST">
                <div class="reveal reveal-up delay-300">
                    <label for="login_id" class="block text-sm font-semibold text-gray-700 mb-2">Email or Username</label>
                    <input id="login_id" name="login_id" type="text" required placeholder="Enter your email or username" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div class="reveal reveal-up delay-400">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                        <a href="forgot_password.php" class="text-sm font-medium text-primary hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all pr-12">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-500 hover:text-primary transition-colors focus:outline-none">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <script>
                    function togglePasswordVisibility() {
                        const passwordInput = document.getElementById('password');
                        const eyeIcon = document.getElementById('eye-icon');
                        
                        if (passwordInput.type === 'password') {
                            passwordInput.type = 'text';
                            // Eye slash icon path
                            eyeIcon.innerHTML = `
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            `;
                        } else {
                            passwordInput.type = 'password';
                            // Normal eye icon path
                            eyeIcon.innerHTML = `
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            `;
                        }
                    }
                </script>

                <div class="flex items-center reveal reveal-up delay-500">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700 cursor-pointer">Remember me</label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-900 shadow-lg shadow-blue-900/10 transition-all transform active:scale-[0.98] reveal reveal-up delay-600">
                    Sign in
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 reveal reveal-up delay-700">
                <div class="bg-blue-50/50 p-6 rounded-2xl border border-blue-100/50 text-center">
                    <p class="text-sm text-gray-600 mb-3">Don't have an account yet?</p>
                    <a href="registration_type.php" class="inline-flex items-center justify-center gap-2 w-full py-3 bg-white text-primary border-2 border-primary hover:bg-primary hover:text-white font-black rounded-xl transition-all duration-300 shadow-sm group">
                        Register Your Profile
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <p class="mt-3 text-[10px] text-blue-400 font-bold uppercase tracking-widest">Join our faithful community today</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
