<?php
session_start();
include 'includes/header.php';
?>

<div class="min-h-[80vh] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-12">
        <!-- Header -->
        <div class="text-center reveal reveal-up">
            <h2 class="text-4xl md:text-5xl font-extrabold text-primary tracking-tight">
                Choose Your Denomination
            </h2>
            <p class="mt-4 text-xl text-gray-600 font-medium">
                To provide you with the best matches, please select your faith community.
            </p>
        </div>

        <!-- Selection Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
            <!-- Christian Card -->
            <a href="register.php?type=christian" class="group relative overflow-hidden bg-white rounded-3xl shadow-2xl hover:shadow-primary/20 transition-all duration-500 transform hover:-translate-y-2 reveal reveal-left delay-100">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="p-10 flex flex-col items-center text-center space-y-6 relative z-10">
                    <div class="w-24 h-24 bg-primary/5 rounded-2xl flex items-center justify-center group-hover:bg-primary transition-colors duration-500 shadow-inner">
                        <svg class="w-12 h-12 text-primary group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v18M8 7h8" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 group-hover:text-primary transition-colors duration-300">Pentecostal Christian</h3>
                        <p class="mt-3 text-gray-500 leading-relaxed font-medium">
                            Connect with the Pentecostal Christian community and build long-lasting faith-centered relationships.
                        </p>
                    </div>
                    <div class="flex items-center text-primary font-bold uppercase tracking-widest text-sm group-hover:translate-x-2 transition-transform duration-300">
                        Select Pentecostal Christian
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                </div>
            </a>

            <!-- Catholic Card -->
            <a href="register.php?type=catholic" class="group relative overflow-hidden bg-white rounded-3xl shadow-2xl hover:shadow-primary/20 transition-all duration-500 transform hover:-translate-y-2 reveal reveal-right delay-200">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                <div class="p-10 flex flex-col items-center text-center space-y-6 relative z-10">
                    <div class="w-24 h-24 bg-blue-50 rounded-2xl flex items-center justify-center group-hover:bg-blue-600 transition-colors duration-500 shadow-inner">
                        <svg class="w-12 h-12 text-blue-600 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2L12 22M7 7L17 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors duration-300">Catholic</h3>
                        <p class="mt-3 text-gray-500 leading-relaxed font-medium">
                            Join our Catholic community to connect with others who share your values and traditions.
                        </p>
                    </div>
                    <div class="flex items-center text-blue-600 font-bold uppercase tracking-widest text-sm group-hover:translate-x-2 transition-transform duration-300">
                        Select Catholic
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </div>
                </div>
            </a>
        </div>

        <!-- Back Link -->
        <div class="text-center mt-12 reveal reveal-up delay-300">
            <a href="index.php" class="text-gray-500 hover:text-primary font-bold flex items-center justify-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Home
            </a>
        </div>
    </div>
</div>

<style>
    /* Additional micro-animations */
    .group:hover .w-24 {
        transform: rotate(5deg) scale(1.05);
    }
</style>

<?php include 'includes/footer.php'; ?>
