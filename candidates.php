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

// Handle Self-Deletion (If candidate found a partner)
if (isset($_POST['delete_my_profile'])) {
    $target_id = $_POST['profile_id'];
    
    // Security check: Only allow deleting own profile
    if ($_SESSION['user_id'] == $target_id || (isset($_SESSION['role']) && $_SESSION['role'] === 'admin')) {
        try {
            // Delete photo if exists
            $stmt = $pdo->prepare("SELECT photo_path FROM candidates WHERE id = ?");
            $stmt->execute([$target_id]);
            $photo = $stmt->fetchColumn();
            if ($photo && file_exists($photo)) {
                unlink($photo);
            }

            // Delete record
            $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
            $stmt->execute([$target_id]);
            
            // If deleting own profile, log out
            if ($_SESSION['user_id'] == $target_id) {
                session_destroy();
                header("Location: index.php?status=profile_deleted");
            } else {
                header("Location: candidates.php?status=deleted");
            }
            exit();
        } catch (PDOException $e) {
            $error = "Deletion failed: " . $e->getMessage();
        }
    }
}
// Success/Error Message System
$review_success = isset($_GET['success']) && $_GET['success'] == 'review_submitted';
$review_error = isset($_GET['error']);
?>
<?php $hide_spacer = true; include 'includes/header.php'; ?>

<main class="bg-[#fafbff] min-h-screen">
    <!-- Hero Section -->
    <div class="bg-primary pt-32 pb-24 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a2540] via-[#1a3a5a] to-[#0a2540] z-0"></div>
        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] z-10"></div>
        
        <div class="relative z-20 container mx-auto px-4 mt-8 text-white">
            <h1 class="text-5xl md:text-7xl font-black mb-6 animate-fade-in tracking-tight">Find Your Perfect Life Partner</h1>
            <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto font-medium opacity-90 leading-relaxed mb-8">
                Connect with faithful, like-minded individuals within our community. 
                Rooted in Christ, building lasting foundations for love.
            </p>
            
            <!-- Quick Filter Stats -->
            <div class="flex flex-wrap justify-center gap-4 text-sm font-bold">
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">✨</span> <?php echo count($candidates); ?> Available Profiles
                </span>
                <span class="px-6 py-2.5 bg-white/10 rounded-full border border-white/20 backdrop-blur-md shadow-xl flex items-center gap-2">
                    <span class="text-xl">🛡️</span> Verified Community
                </span>
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
                    <div class="grid grid-cols-1 gap-y-4 text-sm">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Age Range</span>
                                <span class="font-bold text-gray-700 text-sm"><?php echo htmlspecialchars($candidate['age']); ?> Years</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="block text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-0.5">Published On</span>
                                <span class="font-bold text-gray-700 text-sm"><?php echo date('M d, Y', strtotime($candidate['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col gap-3">
                        <a href="profile.php?id=<?php echo $candidate['id']; ?>" class="w-full flex items-center justify-center gap-2 py-3.5 bg-gray-50 hover:bg-primary hover:text-white text-gray-700 font-bold rounded-2xl transition-all duration-300 text-sm group/btn shadow-sm">
                            View Detailed Profile
                            <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </a>

                        <?php if($_SESSION['user_id'] == $candidate['id']): ?>
                        <form method="POST" onsubmit="return confirm('Congratulations on finding your partner! Are you sure you want to delete your profile permanently?');">
                            <input type="hidden" name="profile_id" value="<?php echo $candidate['id']; ?>">
                            <button type="submit" name="delete_my_profile" class="w-full flex items-center justify-center gap-2 py-3 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold rounded-2xl transition-all duration-300 text-xs uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Found my partner (Delete)
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Floating Review Button -->
<button onclick="openReviewModal()" class="fixed bottom-8 right-8 w-16 h-16 bg-blue-600 text-white rounded-full shadow-2xl hover:bg-blue-700 transition-all duration-300 transform hover:scale-110 flex items-center justify-center z-[100] group active:scale-95">
    <svg class="w-8 h-8 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
    </svg>
    <div class="absolute right-full mr-4 bg-white text-primary px-4 py-2 rounded-xl text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
        Share Your Story ✨
    </div>
</button>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 z-[110] hidden bg-primary/40 backdrop-blur-md flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden animate-slide-up">
        <div class="relative bg-primary p-6 text-white text-center">
            <button onclick="closeReviewModal()" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <h2 class="text-xl font-black mb-1">Blessed Beginnings</h2>
            <p class="text-[11px] text-blue-100/80">Share your journey with our community.</p>
        </div>

        <form action="submit_review.php" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Couples Names</label>
                <input type="text" name="review_name" required placeholder="e.g. David & Mary" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-bold text-sm">
            </div>
            
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Our Testimony</label>
                <textarea name="review_description" required rows="3" placeholder="How did you meet?" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-medium text-slate-600 text-sm"></textarea>
            </div>

            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2 text-center">
                <?php for($i=1; $i<=5; $i++): ?>
                <div class="relative group">
                    <input type="file" name="review_image<?php echo $i; ?>" id="img<?php echo $i; ?>" class="hidden" accept="image/*" onchange="previewImage(this, 'preview<?php echo $i; ?>')">
                    <label for="img<?php echo $i; ?>" class="cursor-pointer border-2 border-dashed border-slate-200 rounded-2xl p-2 block hover:border-blue-500 hover:bg-blue-50/30 transition-all overflow-hidden h-16 flex flex-col items-center justify-center gap-0.5">
                        <div id="preview<?php echo $i; ?>" class="absolute inset-0 hidden">
                            <img src="" class="w-full h-full object-cover">
                        </div>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <span class="text-[8px] font-black text-slate-500 uppercase">P<?php echo $i; ?></span>
                    </label>
                </div>
                <?php endfor; ?>
            </div>

            <button type="submit" class="w-full py-3 bg-primary text-white font-black rounded-xl shadow-lg shadow-blue-900/10 hover:bg-blue-950 transition-all transform hover:-translate-y-0.5 active:scale-95 text-sm uppercase tracking-wider">
                Submit Story ✨
            </button>
        </form>
    </div>
</div>

<!-- Alert Modals -->
<?php if($review_success): ?>
<div class="fixed top-24 left-1/2 -translate-x-1/2 z-[200] bg-green-500 text-white px-8 py-4 rounded-full shadow-2xl font-bold animate-fade-in flex items-center gap-3">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
    Thank you! Your story has been submitted for review.
</div>
<script>setTimeout(() => { window.location.href = 'candidates.php'; }, 4000);</script>
<?php endif; ?>

<script>
    function openReviewModal() {
        const modal = document.getElementById('review-modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        const modal = document.getElementById('review-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const img = preview.querySelector('img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Close modal on background click
    window.onclick = function(event) {
        const modal = document.getElementById('review-modal');
        if (event.target == modal) closeReviewModal();
    }
</script>

<?php include 'includes/footer.php'; ?>
