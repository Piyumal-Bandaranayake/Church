<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: candidates.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ? AND status = 'approved'");
$stmt->execute([$id]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    header("Location: candidates.php");
    exit();
}

?>
<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-[#fafbff] py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Navigation -->
        <a href="candidates.php" class="inline-flex items-center gap-2 text-primary hover:gap-3 transition-all font-bold mb-8 group">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Directory
        </a>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <!-- Top Section: Photo & Identity -->
            <div class="p-8 md:p-12 flex flex-col md:flex-row items-center md:items-start gap-10 bg-gradient-to-br from-white to-blue-50/30">
                <div class="w-48 h-64 shrink-0 rounded-3xl overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                    <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/400x600?text=Profile'; ?>
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover">
                </div>
                
                <div class="flex-grow text-center md:text-left pt-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 text-primary rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Verified Profile
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-2"><?php echo htmlspecialchars($candidate['fullname']); ?></h1>
                    <p class="text-xl text-gray-500 font-medium"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-8">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Age</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['age']; ?> Years</span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Location</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['hometown']; ?></span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div class="flex flex-col">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Gender</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['sex']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="px-8 pb-12 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- About & Bio -->
                <div class="space-y-10">
                    <section>
                        <h3 class="flex items-center gap-3 text-lg font-black text-gray-900 mb-6">
                            <span class="w-2 h-6 bg-primary rounded-full"></span>
                            Biographical Info
                        </h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Nationality</span>
                                <span class="text-gray-900 font-semibold"><?php echo $candidate['nationality']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Language</span>
                                <span class="text-gray-900 font-semibold"><?php echo $candidate['language']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Height</span>
                                <span class="text-gray-900 font-semibold"><?php echo $candidate['height']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Marital Status</span>
                                <span class="text-gray-900 font-semibold"><?php echo $candidate['marital_status']; ?></span>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h3 class="flex items-center gap-3 text-lg font-black text-gray-900 mb-6">
                            <span class="w-2 h-6 bg-primary rounded-full"></span>
                            Faith & Community
                        </h3>
                        <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100/50">
                            <div class="mb-4">
                                <span class="text-[10px] text-primary/60 font-black uppercase tracking-widest block mb-1">Church / Fellowship</span>
                                <p class="text-lg font-bold text-primary"><?php echo htmlspecialchars($candidate['church']); ?></p>
                            </div>
                            <div class="flex items-center gap-4 text-sm">
                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div>
                                    <span class="text-gray-400 text-xs font-bold uppercase">Pastor Support</span>
                                    <p class="font-bold text-gray-800"><?php echo htmlspecialchars($candidate['pastor_name']); ?></p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Career & Education -->
                <div class="space-y-10">
                    <section>
                        <h3 class="flex items-center gap-3 text-lg font-black text-gray-900 mb-6">
                            <span class="w-2 h-6 bg-primary rounded-full"></span>
                            Academic & Career
                        </h3>
                        <div class="space-y-6">
                            <div class="relative pl-6 border-l-2 border-gray-100">
                                <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-primary"></span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Education</span>
                                <p class="text-gray-800 text-sm leading-relaxed font-medium"><?php echo nl2br(htmlspecialchars($candidate['edu_qual'])); ?></p>
                            </div>
                            
                            <div class="relative pl-6 border-l-2 border-gray-100">
                                <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-gray-200"></span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Current Occupation</span>
                                <p class="text-gray-800 font-bold"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                            </div>

                            <?php if(!empty($candidate['add_qual'])): ?>
                            <div class="relative pl-6 border-l-2 border-gray-100">
                                <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-gray-200"></span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Other Qualifications</span>
                                <p class="text-gray-800 text-sm font-medium"><?php echo nl2br(htmlspecialchars($candidate['add_qual'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <section>
                        <h3 class="flex items-center gap-3 text-lg font-black text-gray-900 mb-6">
                            <span class="w-2 h-6 bg-primary rounded-full"></span>
                            Personal Habits
                        </h3>
                        <div class="flex flex-wrap gap-3">
                            <?php 
                            $habits = explode(',', $candidate['habits']);
                            foreach($habits as $habit):
                                if(empty(trim($habit))) continue;
                            ?>
                            <span class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100">
                                <?php echo trim($habit); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Contact Alert -->
                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <h4 class="text-sm font-black text-gray-900 mb-2">Interested in knowing more?</h4>
                        <p class="text-xs text-gray-500 mb-4 leading-relaxed line-clamp-2">Direct contact details are hidden for privacy. Please contact the church office or express your interest through the official channels.</p>
                        <button class="w-full py-3 bg-white border border-gray-200 text-primary font-bold rounded-2xl hover:bg-primary hover:text-white transition-all shadow-sm">Express Interest</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
