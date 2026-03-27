    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <!-- About Column -->
                <div class="col-span-1 md:col-span-1">
                    <div class="mb-4">
                        <img src="assets/images/logo.png" alt="Christian Marriage Proposals" class="h-14 w-auto logo-blend">
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">
                        A community of faith, hope, and love. Join us as we journey together in faith and serve our community.
                    </p>
                </div>

                <!-- Quick Links -->
                <div class="col-span-1 md:text-left text-center">
                    <h3 class="text-sm font-bold mb-4 text-gray-100 uppercase tracking-wider">Quick Links</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="index.php" class="hover:text-blue-400 transition-colors flex items-center gap-2 justify-center md:justify-start"><span class="w-1.5 h-1.5 rounded-full bg-blue-500/50"></span> Home</a></li>
                        <li><a href="about.php" class="hover:text-blue-400 transition-colors flex items-center gap-2 justify-center md:justify-start"><span class="w-1.5 h-1.5 rounded-full bg-blue-500/50"></span> About Us</a></li>
                        <li><a href="churches.php" class="hover:text-blue-400 transition-colors flex items-center gap-2 justify-center md:justify-start"><span class="w-1.5 h-1.5 rounded-full bg-blue-500/50"></span> Churches</a></li>
                        <li><a href="contact.php" class="hover:text-blue-400 transition-colors flex items-center gap-2 justify-center md:justify-start"><span class="w-1.5 h-1.5 rounded-full bg-blue-500/50"></span> Contact Us</a></li>
                    </ul>
                </div>

                <!-- Service Times -->
                <div class="col-span-1 md:text-left text-center">
                    <h3 class="text-sm font-bold mb-4 text-gray-100 uppercase tracking-wider">Service Times</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex flex-col md:flex-row md:justify-between gap-1">
                            <span class="text-xs uppercase tracking-widest opacity-60">Sunday Morning</span>
                            <span class="text-white font-bold tracking-wider">8:30 AM</span>
                        </li>
                        <li class="flex flex-col md:flex-row md:justify-between gap-1">
                            <span class="text-xs uppercase tracking-widest opacity-60">Prayer Group</span>
                            <span class="text-white font-bold tracking-wider">8:30 AM | 11:30 PM</span>
                        </li>
                        <li class="flex flex-col md:flex-row md:justify-between gap-1">
                            <span class="text-xs uppercase tracking-widest opacity-60">Wednesday Fasting</span>
                            <span class="text-white font-bold tracking-wider">8:30 AM - 11:30 AM</span>
                        </li>
                        <li class="flex flex-col md:flex-row md:justify-between gap-1">
                            <span class="text-xs uppercase tracking-widest opacity-60">Bible Study</span>
                            <span class="text-white font-bold tracking-wider">11:30 AM - 2:30 PM</span>
                        </li>
                        <li class="flex flex-col md:flex-row md:justify-between gap-1">
                            <span class="text-xs uppercase tracking-widest opacity-60">House Visiting</span>
                            <span class="text-white font-bold tracking-wider">3:30 PM Onwards</span>
                        </li>
                    </ul>
                </div>

                <!-- Connect -->
                <div class="col-span-1 md:text-left text-center">
                    <h3 class="text-sm font-bold mb-4 text-gray-100 uppercase tracking-wider">Connect With Us</h3>
                    <div class="flex justify-center md:justify-start space-x-3">
                        <a href="https://web.facebook.com/profile.php?id=61588245032764" target="_blank" class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-all duration-300 group border border-gray-700 hover:border-blue-400">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-500">
                <p>&copy; <?php echo date("Y"); ?> Christian Marriage Proposals. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Success/Error Message System for Reviews -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'review_submitted'): ?>
    <div class="fixed top-28 left-0 right-0 flex justify-center z-[10001] px-4 pointer-events-none">
        <div class="pointer-events-auto w-full max-w-lg bg-green-600/95 text-white px-6 py-4 rounded-[2rem] shadow-2xl font-bold animate-slide-up backdrop-blur-md flex items-center gap-4 text-sm md:text-base border border-white/10">
            <div class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div class="flex-grow text-center md:text-left">
                <p class="leading-tight mb-1 text-md">Thank you! Your story has been submitted.</p>
                <p class="text-[11px] md:text-xs opacity-90 font-medium leading-normal">ස්තූතියි! ඔබගේ සාර්ථක කතාව සමාලෝචනය සඳහා අප වෙත ලැබුණි.</p>
            </div>
        </div>
    </div>
    <script>setTimeout(() => { 
        const url = new URL(window.location.href);
        url.searchParams.delete('success');
        window.history.replaceState({}, document.title, url.pathname + url.search);
    }, 4000);</script>
    <?php endif; ?>

    <!-- Floating Action Buttons (WhatsApp & Review) -->
    <div class="fixed bottom-8 right-8 flex flex-col items-center gap-4 z-[9999]">
        <!-- WhatsApp Button -->
        <a id="whatsapp-float"
           href="https://wa.me/94775842820"
           target="_blank"
           rel="noopener noreferrer"
           aria-label="Chat with us on WhatsApp"
           title="Chat on WhatsApp"
           class="relative flex items-center justify-center w-16 h-16 bg-[#25D366] color-[#fff] rounded-full shadow-[0_6px_24px_rgba(37,211,102,0.45)] transition-all duration-300 hover:scale-110 group">
            <span class="wa-pulse absolute inset-0 rounded-full bg-[#25D366]/45 animate-wa-pulse-anim"></span>
            <svg class="w-8 h-8 relative z-[1]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
            <span class="absolute right-full mr-4 bg-[#1a1a1a] text-white text-[13px] font-semibold whitespace-nowrap px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-all pointer-events-none translate-x-2 group-hover:translate-x-0">
                Chat with us!
            </span>
        </a>

        <!-- Review Button (Only for Logged-in Users) -->
        <?php if (isset($_SESSION['user_id'])): ?>
        <button onclick="openReviewModal()" class="w-16 h-16 bg-blue-600 text-white rounded-full shadow-2xl hover:bg-blue-700 transition-all duration-300 transform hover:scale-110 flex items-center justify-center group active:scale-95 relative">
            <svg class="w-8 h-8 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.175 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <div class="absolute right-full mr-4 bg-white text-primary px-4 py-2 rounded-xl text-sm font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none translate-x-2 group-hover:translate-x-0">
                Share Your Success Story ✨ (ඔබේ කතාව අප සමඟ බෙදාගන්න)
            </div>
        </button>
        <?php endif; ?>
    </div>

    <!-- Review Modal (Only if Logged-in) -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div id="review-modal" class="fixed inset-0 z-[11000] hidden bg-primary/40 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden animate-slide-up relative">
            <div class="relative bg-primary p-6 text-white text-center">
                <button onclick="closeReviewModal()" class="absolute top-4 right-4 text-white/60 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-xl font-black mb-1">Blessed Beginnings</h2>
                <p class="text-[11px] text-blue-100/80">Share your journey with our community. (ඔබේ සාර්ථක කතාව අප වෙත එවන්න)</p>
            </div>

            <form action="submit_review.php" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Couples Names (දෙදෙනාගේම නම්)</label>
                    <input type="text" name="review_name" required placeholder="e.g. David & Mary" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-bold text-sm">
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Our Testimony (කතාව)</label>
                    <textarea name="review_description" required rows="3" placeholder="How did you meet?" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-blue-500/20 transition-all outline-none font-medium text-slate-600 text-sm"></textarea>
                </div>

                <div class="grid grid-cols-5 gap-2 text-center">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
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
                    Submit Story ✨ (ඉදිරිපත් කරන්න)
                </button>
            </form>
        </div>
    </div>

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

        // Close review modal on background click
        window.onclick = function(event) {
            const modal = document.getElementById('review-modal');
            if (event.target == modal) closeReviewModal();
        }
    </script>
    <?php endif; ?>

    <style>
        @keyframes wa-pulse-anim {
            0%   { transform: scale(1);   opacity: 0.7; }
            70%  { transform: scale(1.55); opacity: 0; }
            100% { transform: scale(1.55); opacity: 0; }
        }
        .animate-wa-pulse-anim {
            animation: wa-pulse-anim 2s infinite;
        }
    </style>
</body>
</html>
