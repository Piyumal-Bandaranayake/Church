<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grace Community Church</title>
    
    <!-- Primary Meta Tags -->
    <meta name="description" content="Welcome to Grace Community Church. Join us for worship, community, and spiritual growth.">
    
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
                        'fade-in': 'fadeIn 0.5s ease-out forwards',
                        'slide-up': 'slideUp 0.5s ease-out forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
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
    </style>
</head>
<body class="font-sans text-gray-800 bg-secondary flex flex-col min-h-screen">

    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    function isActive($page_name, $current_page) {
        return $current_page === $page_name ? 'text-blue-500 font-semibold' : 'text-gray-600 hover:text-primary transition-colors duration-300';
    }
    function isActiveMobile($page_name, $current_page) {
        return $current_page === $page_name ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100 hover:text-primary';
    }
    ?>

    <!-- Navigation -->
    <nav class="bg-white/90 backdrop-blur-md shadow-sm fixed w-full z-50 transition-all duration-300" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex-shrink-0 flex items-center gap-2 group">
                        <svg class="w-8 h-8 text-primary group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="font-bold text-xl tracking-tight text-primary">Grace Church</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="index.php" class="<?php echo isActive('index.php', $current_page); ?>">Home</a>
                    <a href="about.php" class="<?php echo isActive('about.php', $current_page); ?>">About Us</a>
                    <a href="churches.php" class="<?php echo isActive('churches.php', $current_page); ?>">Churches</a>
                    <a href="contact.php" class="<?php echo isActive('contact.php', $current_page); ?>">Contact</a>
                    
                    <div class="h-6 w-px bg-gray-200 mx-2"></div>

                    <a href="login.php" class="px-4 py-2 rounded-full text-gray-600 hover:text-primary hover:bg-gray-50 font-medium transition-all">Login</a>
                    <a href="register.php" class="px-5 py-2.5 rounded-full bg-primary text-white font-medium hover:bg-primary-hover transition-all duration-300 shadow-lg shadow-primary/20 transform hover:-translate-y-0.5">
                        Register
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button type="button" onclick="toggleMobileMenu()" class="text-gray-600 hover:text-primary focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors">
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
                <div class="border-t border-gray-100 my-2"></div>
                <a href="login.php" class="block px-3 py-2 rounded-md text-base font-medium text-gray-600 hover:bg-gray-100 hover:text-primary">Login</a>
                <a href="register.php" class="block px-3 py-2 rounded-md text-base font-medium text-primary font-bold hover:bg-gray-50">Register</a>
            </div>
        </div>
    </nav>

    <!-- Spacer for fixed navbar -->
    <div class="h-20"></div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
