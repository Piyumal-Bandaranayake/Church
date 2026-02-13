<?php
session_start();
include 'includes/db.php';

$error = '';
$success = '';
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_email'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (empty($email)) {
        $error = "Please enter your email.";
    } else {
        $stmt_cand = $pdo->prepare("SELECT id, 'candidate' as type FROM candidates WHERE email = ?");
        $stmt_cand->execute([$email]);
        $user = $stmt_cand->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $stmt_admin = $pdo->prepare("SELECT id, 'admin' as type FROM admins WHERE email = ?");
            $stmt_admin->execute([$email]);
            $user = $stmt_admin->fetch(PDO::FETCH_ASSOC);
        }

        if ($user) {
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_user_type'] = $user['type'];
            $_SESSION['reset_user_id'] = $user['id'];
            $step = 2;
            $success = "Account verified successfully! Please enter your new password.";
        } else {
            $error = "Email not found in our records.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $step = 2;
    $new_password = $_POST['new_password'];
    $re_password = $_POST['re_password'];
    $email = $_SESSION['reset_email'] ?? '';
    $type = $_SESSION['reset_user_type'] ?? '';
    $id = $_SESSION['reset_user_id'] ?? '';

    if (empty($new_password) || strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($new_password !== $re_password) {
        $error = "Passwords do not match.";
    } elseif (empty($email)) {
        $error = "Session expired. Try again.";
        $step = 1;
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $table = ($type === 'admin') ? 'admins' : 'candidates';
        try {
            $stmt = $pdo->prepare("UPDATE $table SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $id]);
            header("Location: login.php?reset=success");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating password.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                <?php echo ($step === 1) ? 'Reset Password' : 'Create New Password'; ?>
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                <?php echo ($step === 1) ? 'Enter your email to verify your account' : 'Enter your new password below'; ?>
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-10 px-8 shadow-md rounded-2xl border border-gray-100">
            
            <?php if ($error): ?>
                <div class="mb-6 p-3 bg-red-50 border border-red-100 text-red-600 rounded-lg text-sm font-medium">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 p-3 bg-green-50 border border-green-100 text-green-700 rounded-lg text-sm font-medium">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="" method="POST">
                <?php if ($step === 1): ?>
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                        <input id="email" name="email" type="email" required placeholder="example@mail.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    <button type="submit" name="check_email" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-900 shadow-lg shadow-blue-900/10 transition-all">
                        Verify Account
                    </button>
                <?php else: ?>
                    <div>
                        <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <input id="new_password" name="new_password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label for="re_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <input id="re_password" name="re_password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    <button type="submit" name="reset_password" class="w-full py-3.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-900/10 transition-all">
                        Update Password
                    </button>
                <?php endif; ?>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-100 text-center">
                <a href="login.php" class="text-sm font-medium text-primary hover:underline">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
