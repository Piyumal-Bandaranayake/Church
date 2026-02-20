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

<main class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Navigation -->
        <a href="candidates.php" class="inline-flex items-center gap-2 text-primary hover:gap-3 transition-all font-bold mb-8 group reveal reveal-left">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Directory
        </a>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <!-- Top Section: Photo & Identity -->
            <div class="p-8 md:p-12 flex flex-col md:flex-row items-center md:items-start gap-10 bg-gradient-to-br from-white to-blue-50/30">
                <?php $img = !empty($candidate['photo_path']) ? $candidate['photo_path'] : 'https://via.placeholder.com/400x600?text=Profile'; ?>
                <div onclick="openImageModal('<?php echo htmlspecialchars($img); ?>')" class="w-48 h-64 shrink-0 rounded-3xl overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500 cursor-pointer group/img relative reveal reveal-scale">
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover" alt="Profile Photo">
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                    </div>
                </div>

                <style>
                    @keyframes zoom-in {
                        from { opacity: 0; transform: scale(0.95); }
                        to { opacity: 1; transform: scale(1); }
                    }
                    .animate-zoom-in { animation: zoom-in 0.3s ease-out forwards; }
                </style>
                
                <div class="flex-grow text-center md:text-left pt-4 reveal reveal-right delay-200">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/5 text-primary rounded-full text-xs font-bold uppercase tracking-widest mb-4">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        Verified Profile
                    </div>
                    <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-2"><?php echo htmlspecialchars($candidate['fullname']); ?></h1>
                    <p class="text-xl text-gray-500 font-medium"><?php echo htmlspecialchars($candidate['occupation']); ?></p>
                    
                    <div class="flex flex-wrap justify-center md:justify-start gap-6 mt-8">
                        <div class="flex flex-col reveal reveal-up delay-300">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Age</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['age']; ?> Years</span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div class="flex flex-col reveal reveal-up delay-400">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Location</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['hometown']; ?></span>
                        </div>
                        <div class="w-px h-8 bg-gray-200 hidden md:block mt-2"></div>
                        <div class="flex flex-col reveal reveal-up delay-500">
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Gender</span>
                            <span class="text-lg font-bold text-gray-800"><?php echo $candidate['sex']; ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Grid -->
            <div class="px-8 pb-12 md:px-12 grid grid-cols-1 md:grid-cols-2 gap-12">
                
                <!-- About & Bio -->
                <div class="space-y-10 reveal reveal-left delay-300">
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
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Mother Tongue</span>
                                <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($candidate['language']); ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Height</span>
                                <span class="text-gray-900 font-semibold"><?php echo $candidate['height']; ?></span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-50">
                                <span class="text-gray-400 font-bold uppercase text-[10px]">Email Address</span>
                                <span class="text-gray-900 font-semibold lowercase italic text-xs"><?php echo htmlspecialchars($candidate['email']); ?></span>
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
                <div class="space-y-10 reveal reveal-right delay-400">
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

                            <?php if (!empty($candidate['add_qual'])): ?>
                            <div class="relative pl-6 border-l-2 border-gray-100">
                                <span class="absolute -left-[9px] top-0 w-4 h-4 rounded-full bg-white border-4 border-gray-200"></span>
                                <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest block mb-1">Other Qualifications</span>
                                <p class="text-gray-800 text-sm font-medium"><?php echo nl2br(htmlspecialchars($candidate['add_qual'])); ?></p>
                            </div>
                            <?php
endif; ?>
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
foreach ($habits as $habit):
    if (empty(trim($habit)))
        continue;
?>
                            <span class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100">
                                <?php echo trim($habit); ?>
                            </span>
                            <?php
endforeach; ?>
                        </div>
                    </section>

                    <!-- Express Interest Action -->
                    <div class="mt-8">
                        <button onclick="toggleModal('interestModal')" class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-primary-hover hover:scale-[1.02] transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Express Interest
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- Interest Popup Modal -->
<div id="interestModal" class="fixed inset-0 z-[100] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="toggleModal('interestModal')"></div>
    
    <!-- Modal Content -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md px-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-gray-100 animate-fade-in-up">
            <div class="p-8 md:p-10 text-center">
                <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-gray-900 mb-4">Interested in knowing more?</h3>
                <p class="text-gray-500 leading-relaxed font-medium mb-8">
                    Direct contact details are hidden to protect candidate privacy. Please contact the church office or our administrator to express your interest and receive more information.
                </p>
                <div class="space-y-3">
                    <a href="contact.php" class="block w-full py-4 bg-primary text-white font-bold rounded-2xl shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform">
                        Contact Office
                    </a>
                    <button onclick="toggleModal('interestModal')" class="block w-full py-4 bg-gray-50 text-gray-500 font-bold rounded-2xl hover:bg-gray-100 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 z-[150] hidden">
    <div class="absolute inset-0 bg-gray-900/95 backdrop-blur-md" onclick="toggleModal('imageModal')"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl p-4 flex flex-col items-center">
        <button onclick="toggleModal('imageModal')" class="absolute -top-12 right-4 text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img id="modalFullImage" src="" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl animate-zoom-in">
    </div>
</div>

<script>
function openImageModal(imgSrc) {
    document.getElementById('modalFullImage').src = imgSrc;
    toggleModal('imageModal');
}

function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
