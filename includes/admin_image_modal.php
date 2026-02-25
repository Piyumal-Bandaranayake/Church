<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 z-[150] hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-gray-900/95 backdrop-blur-md" onclick="toggleImageModal()"></div>
    
    <!-- Close Button - Positioned in the top right corner of the screen -->
    <button onclick="toggleImageModal()" class="fixed top-8 right-8 text-white/70 hover:text-white transition-all z-[160] p-3 hover:bg-white/10 rounded-full group">
        <svg class="w-8 h-8 transform group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <!-- Image Container -->
    <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
        <img id="modalFullImage" src="" class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl animate-fade-in pointer-events-auto ring-1 ring-white/10">
    </div>
</div>

<script>
    function openImageModal(imgSrc) {
        const modalImg = document.getElementById('modalFullImage');
        if (modalImg) {
            modalImg.src = imgSrc;
            const modal = document.getElementById('imageModal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function toggleImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
</script>
