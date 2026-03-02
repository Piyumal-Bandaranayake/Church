<?php

$hide_spacer = true;

include 'includes/header.php';

include 'includes/db.php';

// Fetch approved reviews
$reviews_stmt = $pdo->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY id DESC LIMIT 6");
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
// Get current user's denomination if role is candidate
$user_denomination = $_SESSION['denomination'] ?? '';
if (empty($user_denomination) && isset($_SESSION['role']) && $_SESSION['role'] === 'candidate') {
    $user_stmt = $pdo->prepare("SELECT denomination FROM candidates WHERE id = ?");
    $user_stmt->execute([$_SESSION['user_id']]);
    $user_denomination = $user_stmt->fetchColumn();
    $_SESSION['denomination'] = $user_denomination;
}
$display_denomination = !empty($user_denomination) ? strtoupper($user_denomination) : 'CATHOLIC';
?>


<!-- Hero Section -->
<div class="relative h-screen min-h-[600px] flex items-center justify-center overflow-hidden">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'profile_deleted'): ?>
        <div class="fixed top-24 left-1/2 -translate-x-1/2 z-[100] bg-green-500 text-white px-8 py-4 rounded-full shadow-2xl font-bold animate-fade-in flex items-center gap-3">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            Congratulations! Your profile has been removed as you found your partner.
        </div>
    <?php
endif; ?>
    <!-- Background Slideshow -->
    <div class="absolute inset-0 z-0" id="hero-slideshow">
        <img src="assets/images/wedding1.jpg" alt="Grace Church" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-100 slide">
        <img src="assets/images/wedding2.jpg" alt="Grace Church Gathering" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0 slide">
        <img src="assets/images/wedding3.jpg" alt="St. Mary's Negombo" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 opacity-0 slide">
        
        <div class="absolute inset-0 bg-gradient-to-b from-primary/80 via-primary/60 to-primary/80 z-10"></div>
    </div>

    <!-- Content -->
    <div class="relative z-20 text-center px-4 max-w-5xl mx-auto">
        <span class="inline-block py-1 px-3 rounded-full bg-blue-500/20 text-blue-200 border border-blue-400/30 text-sm font-semibold tracking-wide mb-6 animate-fade-in opacity-0" style="animation-delay: 0.1s;"><?php echo $display_denomination; ?> MARRIAGE CONNECTION</span>
        
        <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight tracking-tight animate-slide-up opacity-0" style="animation-delay: 0.2s;">
            Find Your  Christian Soulmate,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-white">Blessed by Faith.</span>
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-200 mb-10 max-w-2xl mx-auto animate-slide-up opacity-0" style="animation-delay: 0.4s;">
            Connecting Christian hearts to build strong, lifelong marriages centered on Christ and shared values.
        </p>
        
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up opacity-0" style="animation-delay: 0.6s;">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="candidates.php" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-primary font-bold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl">
                    Browse Candidates
                </a>
            <?php
else: ?>
                <a href="registration_type.php" class="w-full sm:w-auto px-8 py-4 rounded-full bg-white text-primary font-bold hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-xl">
                    Find Your Match
                </a>
            <?php
endif; ?>
            <a href="about.php" class="w-full sm:w-auto px-8 py-4 rounded-full bg-transparent border-2 border-white text-white font-bold hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                About Our Community
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce z-20">
        <svg class="w-8 h-8 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('#hero-slideshow .slide');
        let currentSlide = 0;
        const totalSlides = slides.length;

        function nextSlide() {
            // Fade out current slide
            slides[currentSlide].classList.replace('opacity-100', 'opacity-0');
            
            // Increment slide index
            currentSlide = (currentSlide + 1) % totalSlides;
            
            // Fade in next slide
            slides[currentSlide].classList.replace('opacity-0', 'opacity-100');
        }

        // Change image every 5 seconds
        if (totalSlides > 1) {
            setInterval(nextSlide, 5000);
        }
    });
</script>

<!-- Reviews Section -->
<?php if (!empty($reviews)): ?>
<section class="py-24 bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 left-0 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute bottom-0 right-0 w-64 h-64 bg-indigo-50 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 reveal reveal-up">
            <h2 class="text-blue-600 font-bold text-sm tracking-uppercase uppercase mb-2">Beautiful Testimonies</h2>
            <h3 class="text-4xl font-bold text-gray-900 mb-4">Blessed Success Stories</h3>
            <p class="text-gray-500 max-w-2xl mx-auto">Discover how God has brought hearts together in our community. These are the stories of faith, love, and new beginnings.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($reviews as $review): ?>
            <div onclick="openTestimonyModal(<?php echo htmlspecialchars(json_encode($review)); ?>)" class="group bg-gray-50 rounded-[2.5rem] p-8 border border-gray-100 hover:bg-white hover:shadow-2xl hover:shadow-blue-900/5 transition-all duration-500 relative flex flex-col h-full cursor-pointer">
                <!-- Quote Icon -->
                <div class="absolute top-8 right-8 text-blue-100 group-hover:text-blue-200 transition-colors">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                </div>
                <div class="flex items-center gap-4 mb-8 reveal reveal-up">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-lg transform group-hover:scale-110 transition-transform duration-500">
                        <?php
        $review_img = !empty($review['image1']) ? $review['image1'] : 'https://via.placeholder.com/150?text=Couple';
?>
                        <img src="<?php echo htmlspecialchars($review_img); ?>" alt="Couple" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($review['name']); ?></h4>
                        <div class="flex gap-0.5 mt-1 text-yellow-400">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <?php
        endfor; ?>
                        </div>
                    </div>
                </div>

                <div class="flex-grow reveal reveal-up delay-100">
                    <p class="text-gray-600 leading-relaxed italic relative z-10 line-clamp-4 pb-4">
                        "<?php echo nl2br(htmlspecialchars($review['description'])); ?>"
                    </p>
                </div>
            </div>

            <?php
    endforeach; ?>
        </div>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-16">
            <a href="all_testimonies.php" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-primary border-2 border-primary/20 font-bold rounded-full hover:bg-gray-50 hover:border-primary/40 transition-all shadow-lg group">
                View More
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
            <a href="candidates.php" class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-primary text-white font-bold rounded-full hover:bg-primary-hover transition-all shadow-xl shadow-primary/20 group">
                Share Your Success Story
                <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- Service Times Section -->
<div id="services" class="py-24 bg-accent relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute -bottom-8 -left-8 w-64 h-24 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16 reveal reveal-up">
            <h2 class="text-primary font-bold text-sm tracking-uppercase uppercase mb-2">Join Us For Worship</h2>
            <h3 class="text-4xl font-bold text-gray-900">Service Times</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Card 1 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-blue-500 group reveal reveal-up">
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Sunday Morning</h4>
                <p class="text-3xl font-extrabold text-blue-600 mb-4">8:30 AM</p>
                <p class="text-gray-500 text-sm">Main Worship Service<br>Kids Ministry Available</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-indigo-500 group reveal reveal-up delay-100">
                <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-indigo-100 transition-colors">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Prayer Group</h4>
                <p class="text-2xl font-extrabold text-indigo-600 mb-4">8:30 AM | 11:30 PM</p>
                <p class="text-gray-500 text-sm">Daily Prayer & Worship Sessions<br>Everyone Welcome</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-purple-500 group reveal reveal-up delay-200">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center mb-6 group-hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2">Wednesday fasting prayer </h4>
                <p class="text-2xl font-extrabold text-purple-600 mb-4">8.30 AM - 11.30 AM</p>
                <p class="text-gray-500 text-sm">Bible Study Groups 11.30 AM-2.30 PM<br> House visiting 3.30</p>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Section -->
<div class="py-24 bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="lg:w-1/2 relative reveal reveal-left">
                <div class="absolute -top-4 -left-4 w-24 h-24 bg-blue-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-yellow-100 rounded-full mix-blend-multiply filter blur-xl opacity-70"></div>
                <img src="assets/images/church.jpg" alt="Church Gathering" class="rounded-2xl shadow-2xl w-full object-cover h-[500px] relative z-10 hover:scale-[1.02] transition-transform duration-500">
            </div>
            
            <div class="lg:w-1/2 reveal reveal-right delay-200">
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
    
    <div class="max-w-4xl mx-auto px-4 relative z-10 text-center reveal reveal-scale">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Ready to Visit?</h2>
        <p class="text-blue-100 text-lg mb-8 max-w-2xl mx-auto">
            We'd love to host you this weekend. Let us know you're coming, and we'll help plan your visit.
        </p>
        <a href="contact.php" class="inline-block px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-100 transition-colors shadow-lg transform hover:-translate-y-1">
            Get Directions
        </a>
    </div>
</div>


<?php include 'includes/testimony_modal.php'; ?>


<?php include 'includes/footer.php'; ?>
