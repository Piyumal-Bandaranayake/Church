<?php
session_start();
include 'includes/db.php';

// Security: Only existing Admins can create new Admins
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        
        $success = "Admin user <strong>$username</strong> created successfully!";
    } catch(PDOException $e) {
        if ($e->getCode() == 23000) {
            $error = "Username <strong>$username</strong> already exists.";
        } else {
            $error = "Database Error: " . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-[#fafbff] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <!-- Breadcrumb -->
        <a href="admin_dashboard.php" class="flex items-center text-primary hover:underline gap-2 font-medium mb-8">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Dashboard
        </a>

        <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-gray-100 p-10">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-primary">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                </div>
                <h1 class="text-2xl font-black text-gray-900">Add New Admin</h1>
                <p class="text-gray-500 text-sm mt-1">Grant administrative access to another user.</p>
            </div>
            
            <?php if($error): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl text-sm border border-red-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl text-sm border border-green-100 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">New Admin Username</label>
                    <input type="text" name="username" required placeholder="e.g., manager_grace" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-gray-100 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Access Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-gray-100 focus:bg-white focus:ring-2 focus:ring-primary/20 outline-none transition-all pr-12">
                        <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye-icon-password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-primary-hover transition-all shadow-xl shadow-primary/20">
                        Create Admin Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

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
</script>

</body>
</html>
