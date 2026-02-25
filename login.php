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
                    $error = "Your account is pending admin approval.";
                }
                else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['fullname'];
                    $_SESSION['role'] = 'candidate';
                    $_SESSION['denomination'] = $user['denomination'];
                    header("Location: profile.php");
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
            <?php
endif; ?>

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
                    <input id="password" name="password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                </div>

                <div class="flex items-center reveal reveal-up delay-500">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded cursor-pointer">
                    <label for="remember-me" class="ml-2 block text-sm text-gray-700 cursor-pointer">Remember me</label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-900 shadow-lg shadow-blue-900/10 transition-all transform active:scale-[0.98] reveal reveal-up delay-600">
                    Sign in
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 text-center reveal reveal-up delay-700">
                <p class="text-sm text-gray-600">
                    Don't have an account? 
                    <a href="registration_type.php" class="font-bold text-primary hover:underline">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
