<?php $hide_spacer = true;
include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="bg-primary pt-32 pb-24 text-center relative overflow-hidden">
     <div class="absolute inset-0 bg-gradient-to-br from-[#0a2540] via-[#1a3a5a] to-[#0a2540] z-0"></div>
     <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] z-10"></div>
    <div class="relative z-20 container mx-auto px-4 mt-8">
        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 animate-fade-in tracking-tight">About Us</h1>
        <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto font-medium opacity-90 leading-relaxed">Get to know the heart and soul of Grace Community Church.</p>
    </div>
</div>

<!-- Our Story Section -->
<div class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-center">
            <div class="mb-12 lg:mb-0 relative reveal reveal-left">
                 <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <img src="assets/images/god.jpg" alt="Church History" class="rounded-lg shadow-2xl relative z-10 w-full h-auto object-cover transform hover:scale-[1.02] transition-transform duration-500">
            </div>
            <div class="reveal reveal-right delay-200">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Story</h2>
                <div class="prose prose-lg text-gray-600">
                    <p class="mb-4">
                        Grace Community Church began as a small Bible study in a living room, born out of a simple dream—to create a church where everyone is welcome and where the message of Jesus is clear, meaningful, and accessible to all.
                    </p>
                    <p class="mb-4">
                       Over time, we have witnessed God do incredible things. What started with just a few families has grown into a vibrant and diverse community with multiple campuses, while our heart and purpose have remained unchanged.
                    </p>
                    <p>
                        Today, we continue to be committed to serving our city, loving our neighbors, and being a guiding light that helps people find their way back to God.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mission & Vision -->
<div class="py-20 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent to-white/50 pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid md:grid-cols-2 gap-8 text-center md:text-left">
            <!-- Mission -->
            <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-b-4 border-blue-500 reveal reveal-up">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6 mx-auto md:mx-0">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                <p class="text-gray-600 leading-relaxed">
                    To lead people into a growing relationship with Jesus Christ by creating environments where people are encouraged and equipped to pursue intimacy with God, community with insiders, and influence with outsiders.
                </p>
            </div>

            <!-- Vision -->
            <div class="bg-white p-10 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-b-4 border-indigo-500 reveal reveal-up delay-200">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6 mx-auto md:mx-0">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                <p class="text-gray-600 leading-relaxed">
                    To be a church that is irresistible to those who are turned off by religion, focusing on the grace, truth, and love found in Jesus to transform our city and the world.
                </p>
            </div>
        </div>
    </div>
</div>




<?php include 'includes/footer.php'; ?>
