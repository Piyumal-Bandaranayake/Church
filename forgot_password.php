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
    }
    else {
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
        }
        else {
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
    }
    elseif ($new_password !== $re_password) {
        $error = "Passwords do not match.";
    }
    elseif (empty($email)) {
        $error = "Session expired. Try again.";
        $step = 1;
    }
    else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $table = ($type === 'admin') ? 'admins' : 'candidates';
        try {
            $stmt = $pdo->prepare("UPDATE $table SET password = ? WHERE id = ?");
            $stmt->execute([$hashed, $id]);
            header("Location: login.php?reset=success");
            exit();
        }
        catch (PDOException $e) {
            $error = "Error updating password.";
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md reveal reveal-up">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                <?php echo($step === 1) ? 'Reset Password' : 'Create New Password'; ?>
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                <?php echo($step === 1) ? 'Enter your email to verify your account' : 'Enter your new password below'; ?>
            </p>
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

            <?php if ($success): ?>
                <div class="mb-6 p-3 bg-green-50 border border-green-100 text-green-700 rounded-lg text-sm font-medium">
                    <?php echo $success; ?>
                </div>
            <?php
endif; ?>

            <form class="space-y-6" action="" method="POST">
                <?php if ($step === 1): ?>
                    <div class="reveal reveal-up delay-300">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                        <input id="email" name="email" type="email" required placeholder="example@mail.com" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                    </div>
                    <button type="submit" name="check_email" class="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-blue-900 shadow-lg shadow-blue-900/10 transition-all transform active:scale-[0.98] reveal reveal-up delay-400">
                        Verify Account
                    </button>
                <?php else: ?>
                    <div class="reveal reveal-up delay-300">
                        <label for="new_password" class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                        <div class="relative">
                            <input id="new_password" name="new_password" type="password" required minlength="6" oninput="checkStrength(this.value)" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all pr-12">
                            <button type="button" onclick="togglePassword('new_password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="eye-icon-new_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <!-- Strength Indicator -->
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-grow h-1 bg-gray-100 rounded-full overflow-hidden flex">
                                <div id="strength-bar" class="h-full w-0 transition-all duration-500"></div>
                            </div>
                            <span id="strength-text" class="text-[9px] font-black uppercase tracking-widest text-gray-400">Weak</span>
                        </div>
                    </div>
                    <div class="reveal reveal-up delay-400">
                        <label for="re_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm New Password</label>
                        <div class="relative">
                            <input id="re_password" name="re_password" type="password" required placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent transition-all pr-12">
                            <button type="button" onclick="togglePassword('re_password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600">
                                <svg id="eye-icon-re_password" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="reset_password" class="w-full py-3.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-900/10 transition-all transform active:scale-[0.98] reveal reveal-up delay-500">
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

// Client-side Validation Highlights
const form = document.querySelector('form');
const validateRules = {
    email: { pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/ },
    new_password: { minLength: 6 },
    re_password: { match: 'new_password' }
};

form.querySelectorAll('input').forEach(input => {
    input.addEventListener('blur', function() {
        const rule = validateRules[this.name];
        if (!rule) return;
        
        const val = this.value.trim();
        let error = false;
        
        if (rule.pattern && !rule.pattern.test(val)) error = true;
        if (rule.minLength && val.length < rule.minLength) error = true;
        if (rule.match && val !== document.getElementById(rule.match).value) error = true;
        
        if (error) {
            this.classList.add('border-red-400', 'bg-red-50');
        } else {
            this.classList.remove('border-red-400', 'bg-red-50');
        }
    });

    input.addEventListener('input', function() {
        this.classList.remove('border-red-400', 'bg-red-50');
    });
});
</script>
<?php include 'includes/footer.php'; ?>
