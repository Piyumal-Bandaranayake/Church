<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="bg-gray-900 py-16 text-center relative overflow-hidden">
     <div class="absolute inset-0 bg-gradient-to-br from-primary to-blue-900 z-0"></div>
    <div class="relative z-20 container mx-auto px-4">
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 animate-fade-in">Matrimonial Profiles</h1>
        <p class="text-lg text-blue-100 max-w-2xl mx-auto">Connecting lives in faith and love.</p>
    </div>
</div>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Filters (Mock) -->
        <div class="bg-white p-4 rounded-xl shadow-sm mb-8 flex flex-wrap gap-4 items-center justify-between">
            <div class="flex gap-4">
                <select class="border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option>All Ages</option>
                    <option>20-25</option>
                    <option>26-30</option>
                    <option>31-35</option>
                </select>
                <select class="border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                    <option>All Locations</option>
                    <option>Colombo</option>
                    <option>Kandy</option>
                    <option>Galle</option>
                </select>
            </div>
            <span class="text-xs text-gray-500">Showing approved profiles only</span>
        </div>

        <!-- Profiles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Sample Card 1 -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100">
                <div class="aspect-w-3 aspect-h-4 relative overflow-hidden bg-gray-200 h-96">
                     <!-- Placeholder Image -->
                    <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" alt="Profile">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 text-white pt-20">
                        <h3 class="text-2xl font-bold">James, 28</h3>
                        <p class="text-sm text-gray-200">Software Engineer</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Hometown</span>
                            Colombo
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Height</span>
                            5' 9"
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Religion</span>
                            Christian
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Marital Status</span>
                            Unmarried
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                         <button class="w-full py-2 px-4 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors font-medium text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            View Full Profile
                        </button>
                    </div>
                </div>
            </div>

             <!-- Sample Card 2 -->
             <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100">
                <div class="aspect-w-3 aspect-h-4 relative overflow-hidden bg-gray-200 h-96">
                     <!-- Placeholder Image -->
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" alt="Profile">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 text-white pt-20">
                        <h3 class="text-2xl font-bold">Sarah, 26</h3>
                        <p class="text-sm text-gray-200">Teacher</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Hometown</span>
                            Kandy
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Height</span>
                            5' 4"
                        </div>
                         <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Religion</span>
                            Christian
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Marital Status</span>
                            Unmarried
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                         <button class="w-full py-2 px-4 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors font-medium text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            View Full Profile
                        </button>
                    </div>
                </div>
            </div>

             <!-- Sample Card 3 -->
             <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-100">
                <div class="aspect-w-3 aspect-h-4 relative overflow-hidden bg-gray-200 h-96">
                     <!-- Placeholder Image -->
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" alt="Profile">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-6 text-white pt-20">
                        <h3 class="text-2xl font-bold">David, 30</h3>
                        <p class="text-sm text-gray-200">Doctor</p>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Hometown</span>
                            Galle
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Height</span>
                            5' 11"
                        </div>
                         <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Religion</span>
                            Christian
                        </div>
                        <div>
                            <span class="block text-xs text-gray-400 uppercase tracking-wider">Marital Status</span>
                            Unmarried
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                         <button class="w-full py-2 px-4 border border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors font-medium text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            View Full Profile
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
