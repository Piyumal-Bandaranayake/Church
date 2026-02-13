<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

// Prepare SQL (Order by latest approved)
$sql = "SELECT * FROM candidates WHERE status = 'approved' ORDER BY id DESC";
$stmt = $pdo->query($sql);
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include 'includes/header.php'; ?>

<main class="bg-[#fafbff] min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-primary overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute inset-0 z-0 opacity-20">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" fill-opacity="0.1" />
            </svg>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 text-center text-white">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4 animate-fade-in">Find Your Perfect Life Partner</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto font-light leading-relaxed">
                Connect with faithful, like-minded individuals within our community. 
                Rooted in Christ, building lasting foundations for love.
            </p>
            
            <!-- Quick Filter Stats -->
            <div class="mt-8 flex flex-wrap justify-center gap-4 text-sm font-medium">
                <span class="px-4 py-2 bg-white/10 rounded-full border border-white/20 backdrop-blur-sm">
                    ✨ <?php echo count($candidates); ?> Available Profiles
                </span>
                <span class="px-4 py-2 bg-white/10 rounded-full border border-white/20 backdrop-blur-sm">
                    🛡️ Verified Community
                </span>
                <a href="logout.php" class="px-4 py-2 bg-red-500/20 hover:bg-red-500/40 text-red-100 rounded-full border border-red-400/30 backdrop-blur-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        
        <!-- Search & Filter Bar (UI Only) -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4 flex-grow max-w-xl">
                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </span>
                    <input type="text" placeholder="Search by name, location or church..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:ring-2 focus:ring-primary/20 rounded-xl transition-all outline-none text-sm">
                </div>
                <button class="bg-gray-100 hover:bg-gray-200 p-2.5 rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                </button>
            </div>
            
            <div class="flex items-center gap-4">
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sort By:</label>
                <select class="bg-transparent text-sm font-semibold text-gray-700 outline-none cursor-pointer">
                    <option>Latest Registered</option>
                    <option>Age: Low to High</option>
                    <option>Age: High to Low</option>
                </select>
            </div>
        </div>

        <?php if (empty($candidates)): ?>
             <div class="bg-white p-20 rounded-3xl shadow-sm border border-gray-100 text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">No Profiles Found</h2>
                <p class="text-gray-500 max-w-sm mx-auto">We couldn't find any approved candidates at the moment. Please check back later or modify your search.</p>
                <a href="register.php" class="mt-8 inline-block px-8 py-3 bg-primary text-white font-bold rounded-full shadow-lg shadow-primary/20 hover:scale-105 transition-transform">Create Your Profile</a>
             </div>
        <?php else: ?>

        <!-- Profiles Display -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            
            <?php foreach ($candidates as $candidate): ?>
            <div class="group bg-white rounded-[2rem] shadow-sm hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden flex flex-col h-full transform hover:-translate-y-2">
                
                <!-- Card Header / Image -->
                <div class="relative h-64 overflow-hidden">
                    <?php 
                        $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/400x400?text=Profile';
                    ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700" alt="Profile">
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>
                    
                    <!-- Gender Badge -->
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-[10px] font-bold uppercase tracking-widest text-primary shadow-sm">
                            <?php echo $candidate['sex']; ?>
                        </span>
                    </div>

                    <!-- Name Overlay (Bottom Left) -->
                    <div class="absolute bottom-4 left-5 text-white">
                        <h3 class="text-xl font-bold"><?php echo htmlspecialchars($candidate['fullname']); ?></h3>
                        <p class="text-xs text-blue-100 flex items-center gap-1 opacity-90">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <?php echo htmlspecialchars($candidate['hometown']); ?>
                        </p>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 flex-grow flex flex-col">
                    <div class="grid grid-cols-2 gap-y-4 gap-x-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="truncate">
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-tight">Church</span>
                                <span class="truncate block font-semibold text-gray-700"><?php echo htmlspecialchars($candidate['church']); ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="truncate">
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-tight">Job</span>
                                <span class="truncate block font-semibold text-gray-700"><?php echo htmlspecialchars($candidate['occupation']); ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-tight">Age</span>
                                <span class="font-semibold text-gray-700"><?php echo htmlspecialchars($candidate['age']); ?> Years</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div class="truncate">
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-tight">Status</span>
                                <span class="truncate block font-semibold text-gray-700"><?php echo htmlspecialchars($candidate['marital_status']); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- View Profile Button -->
                    <div class="mt-8 pt-4 border-t border-gray-50">
                        <a href="profile.php?id=<?php echo $candidate['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold rounded-xl transition-all duration-300 text-sm group/btn">
                            Detailed View
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
