<div id="preloader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#0a2540] transition-opacity duration-500">
    <div class="relative flex flex-col items-center">
        <!-- Logo with pulse effect -->
        <div class="relative mb-8">
            <div class="absolute inset-0 rounded-full bg-blue-500/20 animate-ping"></div>
            <img src="assets/images/logo.png" alt="Logo" class="h-24 md:h-32 w-auto relative z-10 logo-blend">
        </div>
        
        <!-- Premium Loader -->
        <div class="flex flex-col items-center">
            <div class="w-48 h-1 bg-white/10 rounded-full overflow-hidden relative">
                <div class="absolute top-0 left-0 h-full w-1/3 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full animate-loader"></div>
            </div>
            <p class="mt-4 text-blue-200 text-xs font-bold uppercase tracking-[0.3em] animate-pulse">
                Initializing Grace
            </p>
        </div>
    </div>
</div>

<style>
    @keyframes loader {
        0% { left: -30%; width: 30%; }
        50% { left: 40%; width: 50%; }
        100% { left: 100%; width: 30%; }
    }
    .animate-loader {
        animation: loader 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
    }
    
    body.loading {
        overflow: hidden !important;
    }
    
    #preloader.fade-out {
        opacity: 0;
        pointer-events: none;
    }
</style>

<script>
    // Add loading class to body
    document.body.classList.add('loading');

    // Function to hide preloader
    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        if (!preloader || preloader.classList.contains('fade-out')) return;
        
        preloader.classList.add('fade-out');
        document.body.classList.remove('loading');
        
        // Remove from DOM after transition
        setTimeout(() => {
            if (preloader.parentNode) {
                preloader.remove();
            }
        }, 500);
    }

    // Hide on window load
    window.addEventListener('load', function() {
        setTimeout(hidePreloader, 500); // Small buffer for smooth transition
    });

    // Safety timeout: Hide preloader after 3 seconds anyway (especially for mobile)
    setTimeout(hidePreloader, 3000);
</script>
