<?php
session_start();
include 'includes/db.php';

// Security: Only logged-in Admins
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match!";
    }
    else {
        // Fetch current admin password
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($current_password, $admin['password'])) {
            // Update password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $update_stmt->execute([$hashed_password, $_SESSION['user_id']]);

            // End session and redirect to login
            session_destroy();
            header("Location: login.php?reset=success");
            exit();
        }
        else {
            $error = "Current password is incorrect!";
        }
    }
}
?>

<?php include 'includes/admin_head.php'; ?>
<?php include 'includes/admin_sidebar.php'; ?>

<div class="sm:ml-64">
    <main class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Breadcrumb -->
        <a href="admin_dashboard.php" class="flex items-center text-primary hover:underline gap-2 font-medium mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">Security Settings</h1>
            
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl text-sm border border-red-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo $error; ?>
                </div>
            <?php
endif; ?>

            <?php if ($success): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl text-sm border border-green-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Current Password</label>
                    <div class="relative">
                        <input id="current_password" type="password" name="current_password" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all pr-12">
                        <button type="button" onclick="togglePassword('current_password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-current_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <hr class="border-gray-50">

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">New Password</label>
                    <div class="relative">
                        <input id="new_password" type="password" name="new_password" required onkeyup="checkStrength(this.value)" class="w-full px-4 py-3 rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all pr-12">
                        <button type="button" onclick="togglePassword('new_password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-new_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <!-- Password Strength Indicator -->
                    <div class="mt-2 px-1">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div id="strength-bar" class="h-full w-0 transition-all duration-500"></div>
                        </div>
                        <p id="strength-text" class="text-[10px] font-bold uppercase tracking-wider mt-1.5 text-gray-400">Strength: <span class="text-gray-300">Enter password</span></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input id="confirm_password" type="password" name="confirm_password" required class="w-full px-4 py-3 rounded-xl bg-gray-50 border-gray-100 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all pr-12">
                        <button type="button" onclick="togglePassword('confirm_password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-confirm_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-primary-hover transition-all shadow-lg shadow-primary/20">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    </main>
</div>
</body>
</html>


<script>
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

function checkStrength(password) {
    const bar = document.getElementById('strength-bar');
    const text = document.getElementById('strength-text').querySelector('span');
    let strength = 0;
    
    if (password.length > 5) strength++;
    if (password.length > 8) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-500'];
    const labels = ['Very Weak', 'Weak', 'Good', 'Strong', 'Very Strong'];
    
    bar.className = 'h-full transition-all duration-500 ' + (strength > 0 ? colors[strength-1] : 'bg-gray-100');
    bar.style.width = (strength * 20) + '%';
    text.innerText = strength > 0 ? labels[strength-1] : 'Enter password';
    text.className = strength > 0 ? colors[strength-1].replace('bg-', 'text-') : 'text-gray-300';
}
</script>

</body>
</html>
