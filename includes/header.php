<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Christian Marriage Proposals</title>
    
    <!-- Primary Meta Tags -->
    <meta name="description" content="Welcome to Christian Marriage Proposals. Connecting Catholic hearts to build strong, lifelong marriages centered on Christ and shared values.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0a2540',
                        secondary: '#ffffff',
                        accent: '#f3f4f6',
                        'primary-hover': '#0d2f52',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'slide-up': 'slideUp 0.8s ease-out forwards',
                        'slide-down': 'slideDown 0.8s ease-out forwards',
                        'scale-in': 'scaleIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(40px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        slideDown: {
                            '0%': { transform: 'translateY(-40px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.9)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }

        /* Navbar states */
        .nav-scrolled {
            background-color: #0a2540 !important;
            backdrop-filter: blur(8px);
            height: 5rem !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }
        .nav-transparent {
            background-color: transparent !important;
            height: 5.5rem;
        }
        
        /* Logo blend mode to remove black background */
        .logo-blend {
            mix-blend-mode: screen;
            filter: brightness(1.1) contrast(1.1);
            background-color: transparent !important;
        }
        
        .logo-container {
            background: transparent !important;
        }

        /* Scroll Reveal Utility Classes */
        .reveal {
            opacity: 0;
            transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .reveal-up { transform: translateY(40px); }
        .reveal-down { transform: translateY(-40px); }
        .reveal-left { transform: translateX(40px); }
        .reveal-right { transform: translateX(-40px); }
        .reveal-scale { transform: scale(0.95); }

        .reveal.active {
            opacity: 1;
            transform: translate(0) scale(1);
        }

        /* Staggered Delays */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }
        .delay-500 { transition-delay: 500ms; }

        /* Global Themed Background */
        .themed-background {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(10, 37, 64, 0.05) 0, transparent 50%),
                radial-gradient(at 100% 0%, rgba(10, 37, 64, 0.05) 0, transparent 50%),
                url("https://www.transparenttextures.com/patterns/silk.png");
            background-attachment: fixed;
        }
    </style>
</head>
    <?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<body class="font-sans text-gray-800 flex flex-col min-h-screen <?php echo($current_page !== 'index.php') ? 'themed-background' : 'bg-secondary'; ?>">

    <?php
function isActive($page_name, $current_page)
{
    return $current_page === $page_name ? 'text-blue-200 font-bold border-b-2 border-blue-200' : 'text-white/80 hover:text-white transition-colors duration-300';
}
function isActiveMobile($page_name, $current_page)
{
    return $current_page === $page_name ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-primary';
}
?>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-500 <?php echo(isset($hide_spacer) && $hide_spacer) ? 'nav-transparent' : 'bg-[#0a2540] shadow-lg'; ?>" 
         id="navbar" 
         style="<?php echo(!isset($hide_spacer) || !$hide_spacer) ? 'height: 5rem;' : ''; ?>"
         data-transparent="<?php echo(isset($hide_spacer) && $hide_spacer) ? 'true' : 'false'; ?>">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
            <div class="flex justify-between items-center h-full">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center group logo-container">
                        <img src="assets/images/logo.png" alt="Christian Marriage Proposals" class="h-16 md:h-20 w-auto transform group-hover:scale-105 transition-transform duration-300 logo-blend">
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="nav-link <?php echo isActive('index.php', $current_page); ?>">Home</a>
                    <a href="about.php" class="nav-link <?php echo isActive('about.php', $current_page); ?>">About Us</a>
                    <a href="churches.php" class="nav-link <?php echo isActive('churches.php', $current_page); ?>">Churches</a>
                    <a href="contact.php" class="nav-link <?php echo isActive('contact.php', $current_page); ?>">Contact</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="candidates.php" class="<?php echo isActive('candidates.php', $current_page); ?>">Candidates</a>
                    <?php
endif; ?>
                    
                    <div class="h-6 w-px bg-gray-200 mx-2"></div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
    $dashboardLink = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'candidates.php';
?>
                        <div class="relative items-center flex" id="profile-dropdown-container">
                            <button onclick="toggleProfileDropdown()" class="flex items-center gap-3 px-3 py-1.5 rounded-full text-white hover:bg-white/10 transition-all border border-white/10 backdrop-blur-sm group">
                                <div class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold shadow-lg border-2 border-white/20 group-hover:border-blue-300 transition-all">
                                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                                </div>
                                <div class="hidden lg:block text-left">
                                    <p class="text-[10px] text-blue-200 font-bold uppercase tracking-widest leading-none mb-0.5">Welcome back</p>
                                    <p class="text-sm font-bold text-white leading-none"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                </div>
                                <svg class="w-4 h-4 text-white/50 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="profile-dropdown" class="absolute right-0 top-full mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 hidden animate-slide-up origin-top-right overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-50 mb-1">
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Account Settings</p>
                                    <p class="text-sm font-bold text-gray-800 truncate"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                </div>
                                
                                <a href="<?php echo $dashboardLink; ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-primary transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    My Dashboard
                                </a>

                                <?php if ($_SESSION['role'] !== 'admin'): ?>
                                <a href="profile.php?id=<?php echo $_SESSION['user_id']; ?>" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-600 hover:bg-blue-50 hover:text-primary transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-primary">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                    </div>
                                    View Profile
                                </a>
                                <?php
    endif; ?>

                                <div class="h-px bg-gray-50 my-1"></div>
                                
                                <a href="logout.php" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-500 hover:bg-red-50 transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center text-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    </div>
                                    Sign Out
                                </a>
                            </div>
                        </div>
                    <?php
else: ?>
                        <a href="login.php" class="px-6 py-2.5 rounded-full border border-white/30 text-white font-semibold hover:bg-white hover:text-primary transition-all duration-300 transform hover:-translate-y-0.5">
                            Login
                        </a>
                        <a href="registration_type.php" class="px-6 py-2.5 rounded-full bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all duration-300 shadow-lg shadow-blue-600/30 transform hover:-translate-y-0.5">
                            Register
                        </a>
                    <?php
endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="toggleMobileMenu()" class="text-white hover:text-blue-200 focus:outline-none p-2 rounded-md hover:bg-white/10 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu (Hidden by default) -->
        <div class="md:hidden hidden bg-white border-t border-gray-100 absolute w-full" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 shadow-xl">
                <a href="index.php" class="block px-3 py-2 rounded-md text-base font-medium <?php echo isActiveMobile('index.php', $current_page); ?>">Home</a>
                <a href="about.php" class="block px-3 py-2 rounded-md text-base font-medium <?php echo isActiveMobile('about.php', $current_page); ?>">About Us</a>
                <a href="churches.php" class="block px-3 py-2 rounded-md text-base font-medium <?php echo isActiveMobile('churches.php', $current_page); ?>">Churches</a>
                <a href="contact.php" class="block px-3 py-2 rounded-md text-base font-medium <?php echo isActiveMobile('contact.php', $current_page); ?>">Contact Us</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="candidates.php" class="block px-3 py-2 rounded-md text-base font-medium <?php echo isActiveMobile('candidates.php', $current_page); ?>">Candidates</a>
                <?php
endif; ?>
                <div class="border-t border-gray-100 my-2"></div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php $dashboardLink = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'candidates.php'; ?>
                    <a href="<?php echo $dashboardLink; ?>" class="block px-3 py-2 rounded-md text-base font-medium text-primary bg-blue-50">
                        <span class="mr-2">👤</span> My Profile (<?php echo htmlspecialchars($_SESSION['username']); ?>)
                    </a>
                    <a href="logout.php" class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">Logout</a>
                <?php
else: ?>
                    <a href="login.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100 hover:text-primary">Login</a>
                    <a href="registration_type.php" class="block px-3 py-2 rounded-md text-base font-medium text-primary font-bold hover:bg-gray-50">Register</a>
                <?php
endif; ?>
            </div>
        </div>
    </nav>

    <!-- Conditional Spacer -->
    <?php if (!isset($hide_spacer) || !$hide_spacer): ?>
        <div class="h-20 lg:h-24"></div>
    <?php
endif; ?>

    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profile-dropdown');
            dropdown.classList.toggle('hidden');
        }

        window.onclick = function(event) {
            if (!event.target.closest('#profile-dropdown-container')) {
                const dropdown = document.getElementById('profile-dropdown');
                if (dropdown && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            const isTransparentPage = navbar.getAttribute('data-transparent') === 'true';
            
            if (isTransparentPage) {
                if (window.scrollY > 50) {
                    navbar.classList.add('nav-scrolled');
                    navbar.classList.remove('nav-transparent');
                } else {
                    navbar.classList.remove('nav-scrolled');
                    navbar.classList.add('nav-transparent');
                }
            }
        });

        // Initialize Scroll Reveal
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Uncomment if you want it to re-animate when scrolling back up
                        // observer.unobserve(entry.target); 
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>

