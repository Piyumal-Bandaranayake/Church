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
    // Hardcoded Super Admin check
    elseif ($login_id === 'masteradmin' && $password === 'master@2024') {
        $_SESSION['user_id'] = 0; // Fixed ID for super admin
        $_SESSION['username'] = 'Super Admin';
        $_SESSION['role'] = 'superadmin';
        header("Location: admin_dashboard.php");
        exit();
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
    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md reveal reveal-scale delay-200">
        <div class="bg-white py-12 px-10 shadow-2xl rounded-[3rem] border border-gray-100 flex flex-col">
            
            <!-- Information Section (Moved from bottom to top) -->
            <div class="bg-blue-50/50 p-8 rounded-[2.5rem] border border-blue-100/50 text-center mb-10">
                <p class="text-sm text-gray-600 mb-4 font-medium tracking-tight">Don't have an account yet?</p>
                <a href="registration_type.php" class="inline-flex items-center justify-center gap-3 w-full py-4 bg-[#0a2540] text-white font-black rounded-2xl transition-all duration-300 shadow-xl hover:bg-slate-900 group">
                    Register Your Profile
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <p class="mt-4 text-[10px] text-blue-400 font-black uppercase tracking-[0.2em]">Join our faithful community today</p>
            </div>

            <?php if ($error): ?>
                <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-600 rounded-2xl text-sm font-bold flex items-center gap-3 animate-shake">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['reset']) && $_GET['reset'] == 'success'): ?>
                <div class="mb-8 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl text-sm font-bold flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Password updated successfully.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
                <div class="mb-8 p-6 bg-green-50 border-2 border-green-200 rounded-[2.5rem] text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                    <p class="text-green-700 font-black text-sm uppercase tracking-tight">Registration Successful!</p>
                    <p class="text-xs text-green-600 font-bold mb-4">ලියාපදිංචිය සාර්ථකයි!</p>
                    
                    <?php if (isset($_GET['reg'])): ?>
                        <div class="bg-white rounded-3xl p-5 border border-green-100 shadow-sm mb-4">
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest mb-1.5 line-clamp-1">Your Registration Number</p>
                            <p class="text-4xl font-black text-[#0a2540] tracking-tighter"><?php echo htmlspecialchars($_GET['reg']); ?></p>
                        </div>
                    <?php endif; ?>
                    <p class="text-[10px] text-gray-500 font-bold leading-relaxed">Your profile is under review. You can login after approval.<br>(ඔබගේ පෝරමය සමාලෝචනය යටතේ පවතී. අනුමැතියෙන් පසු පුරනය වන්න.)</p>
                </div>

                <!-- Registration Success Popup Script -->
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Registration Successful! 🎉<br><span class="text-base font-bold text-gray-500 block mt-2">ලියාපදිංචිය සාර්ථකයි!</span>',
                            html: `
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left text-sm mt-8">
                                    <div class="bg-blue-50/80 p-6 rounded-[2rem] border border-blue-100 flex flex-col h-full">
                                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center mb-4 text-blue-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        </div>
                                        <h4 class="font-black text-blue-900 mb-3 text-lg leading-tight tracking-tight">Next Steps<br><span class="text-[10px] block text-blue-700/60 mt-1 uppercase tracking-[0.15em] font-black">අනුමැතිය සඳහා වූ පියවර</span></h4>
                                        <p class="text-blue-800 leading-relaxed font-bold flex-grow text-sm opacity-80">Our team will begin reviewing your submitted profile and details. Once everything is verified, we will notify you immediately via WhatsApp.<br><span class="block mt-4 text-[11px] font-black opacity-50 uppercase tracking-wide leading-relaxed border-t border-blue-200/50 pt-3">අපගේ කණ්ඩායම ඔබගේ තොරතුරු පරීක්ෂා කිරීමෙන් පසු පණිවිඩයක් එවනු ඇත.</span></p>
                                    </div>
                                    
                                    <div class="bg-amber-50/80 p-6 rounded-[2rem] border border-amber-100 flex flex-col h-full">
                                        <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center mb-4 text-amber-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <h4 class="font-black text-amber-900 mb-3 text-lg leading-tight tracking-tight">Active Status<br><span class="text-[10px] block text-amber-700/60 mt-1 uppercase tracking-[0.15em] font-black">සක්‍රියව සිටීම</span></h4>
                                        <p class="text-amber-800 leading-relaxed font-bold flex-grow text-sm opacity-80">Please ensure that the WhatsApp number you provided remains active so you don't miss any important updates for your profile approval.<br><span class="block mt-4 text-[11px] font-black opacity-50 uppercase tracking-wide leading-relaxed border-t border-amber-200/50 pt-3">ලබාදුන් වට්ස්ඇප් අංකය සැමවිටම සක්‍රියව තබා ගන්න.</span></p>
                                    </div>
                                    
                                    <div class="bg-emerald-50/80 p-6 rounded-[2rem] border border-emerald-100 flex flex-col h-full">
                                        <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center mb-4 text-emerald-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <h4 class="font-black text-emerald-900 mb-3 text-lg leading-tight tracking-tight">Access Control<br><span class="text-[10px] block text-emerald-700/60 mt-1 uppercase tracking-[0.15em] font-black">ප්‍රවේශ වීම</span></h4>
                                        <p class="text-emerald-800 leading-relaxed font-bold flex-grow text-sm opacity-80">Upon approval, you'll gain full access to view compatible profiles and securely express interest and connect with potential life partners.<br><span class="block mt-4 text-[11px] font-black opacity-50 uppercase tracking-wide leading-relaxed border-t border-emerald-200/50 pt-3">අනුමත වූ පසු සියලු තොරතුරු බැලීමට අවස්ථාව හිමිවනු ඇත.</span></p>
                                    </div>
                                </div>
                            `,
                            icon: 'success',
                            confirmButtonText: 'Understood & Continue (ඉදිරියට යන්න)',
                            confirmButtonColor: '#0a2540',
                            customClass: {
                                popup: 'rounded-[3rem]',
                                confirmButton: 'rounded-2xl px-10 py-4 font-black shadow-2xl hover:shadow-primary/30 transition-all active:scale-95 text-base',
                                title: 'text-3xl font-black text-gray-900',
                            },
                            width: '64rem',
                            padding: '3rem',
                            backdrop: `
                                rgba(10, 37, 64, 0.6)
                                backdrop-filter: blur(12px)
                            `
                        });
                    });
                </script>
            <?php endif; ?>

            <form class="space-y-8" action="" method="POST">
                <div class="reveal reveal-up delay-300">
                    <label for="login_id" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.15em] mb-3 ml-2">Email or Username</label>
                    <input id="login_id" name="login_id" type="text" required placeholder="Enter your email or username" class="w-full px-5 py-4 bg-gray-50 rounded-2xl border border-gray-100 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-sm font-bold text-gray-700">
                </div>

                <div class="reveal reveal-up delay-400">
                    <div class="flex items-center justify-between mb-3 ml-2 pr-2">
                        <label for="password" class="block text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">Password</label>
                        <a href="forgot_password.php" class="text-[11px] font-black text-primary uppercase tracking-[0.1em] hover:opacity-70 transition-opacity">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <input id="password" name="password" type="password" required placeholder="••••••••" class="w-full px-5 py-4 bg-gray-50 rounded-2xl border border-gray-100 focus:bg-white focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none text-sm font-bold text-gray-700 pr-14">
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 flex items-center px-5 text-gray-400 hover:text-primary transition-colors focus:outline-none">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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
                            eyeIcon.innerHTML = `
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            `;
                        } else {
                            passwordInput.type = 'password';
                            eyeIcon.innerHTML = `
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            `;
                        }
                    }
                </script>

                <div class="flex items-center reveal reveal-up delay-500 ml-2">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-5 w-5 text-primary focus:ring-primary/20 border-gray-200 rounded-lg cursor-pointer transition-all">
                    <label for="remember-me" class="ml-3 block text-sm font-bold text-gray-600 cursor-pointer select-none">Remember me</label>
                </div>

                <div class="reveal reveal-up delay-600 pt-2">
                    <button type="submit" class="w-full py-5 bg-[#0a2540] text-white font-black text-lg rounded-2xl shadow-2xl shadow-blue-900/20 hover:bg-slate-900 hover:-translate-y-0.5 transition-all active:scale-[0.98]">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
