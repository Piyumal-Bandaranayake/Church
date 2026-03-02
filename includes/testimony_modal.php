<!-- Testimony Popup Modal -->
<div id="testimony-modal" class="fixed inset-0 z-[150] hidden flex items-center justify-center p-4 bg-primary/40 backdrop-blur-xl animate-fade-in">
    <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl overflow-hidden relative flex flex-col md:flex-row max-h-[90vh] animate-slide-up">
        <!-- Close Button -->
        <button onclick="closeTestimonyModal()" class="absolute top-6 right-6 z-30 p-2.5 bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-500 rounded-full transition-all duration-300 shadow-sm border border-gray-100 group">
            <svg class="w-5 h-5 transform group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>

        <!-- Left: Image Gallery -->
        <div class="md:w-1/2 bg-gray-100 relative h-64 md:h-auto overflow-hidden">
            <div id="modal-image-container" class="w-full h-full flex transition-transform duration-500">
                <!-- Images will be injected here -->
            </div>
            
            <!-- Gallery Navigation -->
            <div id="gallery-nav" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20">
                <!-- Dots will be injected here -->
            </div>

            <!-- Arrows -->
            <button onclick="prevModalImage()" class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/20 hover:bg-white/40 backdrop-blur-md rounded-full text-white transition-all z-20 hidden gallery-arrow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button onclick="nextModalImage()" class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/20 hover:bg-white/40 backdrop-blur-md rounded-full text-white transition-all z-20 hidden gallery-arrow">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>

        <!-- Right: Testimony Content -->
        <div class="md:w-1/2 p-10 md:p-14 overflow-y-auto flex flex-col">
            <div class="mb-10">
                <span class="inline-block px-4 py-1.5 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-4">Blessed Union</span>
                <h2 id="modal-title" class="text-3xl font-black text-gray-900 leading-tight"></h2>
                <div class="flex gap-1 mt-2 text-yellow-400">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="flex-grow">
                <div class="text-blue-100 mb-6">
                    <svg class="w-12 h-12 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                    </svg>
                </div>
                <p id="modal-description" class="text-lg text-gray-600 leading-relaxed italic"></p>
            </div>

            <div class="mt-12 pt-8 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs ring-4 ring-blue-50">
                        ✝
                    </div>
                    <div>
                        <p class="text-xs font-black text-gray-900 uppercase tracking-widest">Guideway Network</p>
                        <p class="text-[10px] text-gray-400 font-bold uppercase">Faith-Based Matchmaking</p>
                    </div>
                </div>
                <div></div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentModalImageIndex = 0;
    let modalImages = [];

    function openTestimonyModal(data) {
        const modal = document.getElementById('testimony-modal');
        const container = document.getElementById('modal-image-container');
        const nav = document.getElementById('gallery-nav');
        const arrows = document.querySelectorAll('.gallery-arrow');
        
        // Reset
        container.innerHTML = '';
        nav.innerHTML = '';
        modalImages = [];
        currentModalImageIndex = 0;

        // Collect images
        for(let i=1; i<=5; i++) {
            if(data['image'+i]) modalImages.push(data['image'+i]);
        }

        if(modalImages.length === 0) modalImages.push('https://via.placeholder.com/800x800?text=No+Image');

        // Populate images
        modalImages.forEach((src, idx) => {
            const img = document.createElement('img');
            img.src = src;
            img.className = 'w-full h-full object-cover flex-shrink-0';
            container.appendChild(img);

            // Nav dots
            if(modalImages.length > 1) {
                const dot = document.createElement('button');
                dot.className = `w-2 h-2 rounded-full transition-all ${idx === 0 ? 'bg-white w-6' : 'bg-white/40'}`;
                dot.onclick = () => goToModalImage(idx);
                nav.appendChild(dot);
            }
        });

        // Show arrows if multiple images
        arrows.forEach(a => a.style.display = modalImages.length > 1 ? 'block' : 'none');

        // Text content
        document.getElementById('modal-title').innerText = data.name;
        document.getElementById('modal-description').innerText = '"' + data.description + '"';

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        updateModalGallery();
    }

    function updateModalGallery() {
        const container = document.getElementById('modal-image-container');
        const dots = document.querySelectorAll('#gallery-nav button');
        
        container.style.transform = `translateX(-${currentModalImageIndex * 100}%)`;
        
        dots.forEach((dot, idx) => {
            if(idx === currentModalImageIndex) {
                dot.classList.add('bg-white', 'w-6');
                dot.classList.remove('bg-white/40');
            } else {
                dot.classList.remove('bg-white', 'w-6');
                dot.classList.add('bg-white/40');
            }
        });
    }

    function nextModalImage() {
        if(modalImages.length <= 1) return;
        currentModalImageIndex = (currentModalImageIndex + 1) % modalImages.length;
        updateModalGallery();
    }

    function prevModalImage() {
        if(modalImages.length <= 1) return;
        currentModalImageIndex = (currentModalImageIndex - 1 + modalImages.length) % modalImages.length;
        updateModalGallery();
    }

    function goToModalImage(idx) {
        currentModalImageIndex = idx;
        updateModalGallery();
    }

    function closeTestimonyModal() {
        document.getElementById('testimony-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close on background click
    window.addEventListener('click', (e) => {
        const modal = document.getElementById('testimony-modal');
        if(e.target === modal) closeTestimonyModal();
    });
</script>
