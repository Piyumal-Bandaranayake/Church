<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<div class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="assets/images/hero.jpg" alt="Church Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/80"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
        <span class="inline-block py-1 px-3 rounded-full bg-blue-500/20 text-blue-200 border border-blue-400/30 text-sm font-semibold tracking-wide mb-6 animate-fade-in opacity-0" style="animation-delay: 0.1s;">WELCOME TO GRACE COMMUNITY</span>
        
        <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight tracking-tight animate-slide-up opacity-0" style="animation-delay: 0.2s;">
            A Place to Belong,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-white">Believe, & Become.</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-2xl mx-auto animate-slide-up opacity-0" style="animation-delay: 0.4s;">
            Experience a community where faith comes alive, hope is restored, and love knows no bounds.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up opacity-0" style="animation-delay: 0.6s;">
            <a href="#services" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-primary font-bold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl">
                Join Us This Sunday
            </a>
            <a href="contact.php" class="w-full sm:w-auto px-8 py-4 rounded-full bg-transparent border-2 border-white text-white font-bold hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                Plan Your Visit
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</div>

<!-- Service Times Section -->
<div id="services" class="py-24 bg-accent relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute -bottom-8 -left-8 w-64 h-24 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-primary font-bold text-sm tracking-uppercase uppercase mb-2">Join Us For Worship</h2>
            <h3 class="text-4xl font-bold text-gray-900">Service Times</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-blue-500 group">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Sunday Morning</h4>
                <p class="text-4xl font-extrabold text-blue-600 mb-4">9:00 AM</p>
                <p class="text-gray-500 text-sm">Main Worship Service<br>Kids Ministry Available</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-indigo-500 group">
                <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Sunday Evening</h4>
                <p class="text-4xl font-extrabold text-indigo-600 mb-4">6:00 PM</p>
                <p class="text-gray-500 text-sm">Evening Prayer & Worship<br>Youth Group Meeting</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-purple-500 group">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Wednesday Study</h4>
                <p class="text-4xl font-extrabold text-purple-600 mb-4">7:00 PM</p>
                <p class="text-gray-500 text-sm">Bible Study Groups<br>Focus on Discipleship</p>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Section -->
<div class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2 relative">
                <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <img src="assets/images/church.jpg" alt="Church Gathering" class="rounded-2xl shadow-2xl w-full object-cover h-[500px] relative z-10 hover:scale-[1.02] transition-transform duration-500">
            </div>
            
            <div class="lg:w-1/2">
                <h2 class="text-sm font-bold text-blue-600 uppercase tracking-wide mb-3">Who We Are</h2>
                <h3 class="text-4xl font-bold text-gray-900 mb-6 leading-tight">Authentic Community.<br>Real Faith.</h3>
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                    At Grace Community Church, we believe trying to find your way in life is better when we do it together. We are a community of broken people who have found healing in Jesus, and we want to share that hope with you.
                </p>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    Whether you're new to faith, exploring what you believe, or have been walking with Jesus for years, there's a place for you here.
                </p>
                
                <div class="flex items-center gap-4">
                    <a href="about.php" class="text-primary font-bold hover:text-blue-700 transition-colors flex items-center gap-2 group">
                        Learn More About Us
                        <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="py-20 bg-primary relative overflow-hidden">
    <div class="absolute inset-0 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    
    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Visit?</h2>
        <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
            We'd love to host you this weekend. Let us know you're coming, and we'll help plan your visit.
        </p>
        <a href="contact.php" class="inline-block px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-100 transition-colors shadow-lg transform hover:-translate-y-1">
            Get Directions
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
