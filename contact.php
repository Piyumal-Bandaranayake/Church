<?php $hide_spacer = true;
include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="bg-primary pt-32 pb-24 text-center relative overflow-hidden">
     <div class="absolute inset-0 bg-gradient-to-br from-[#0a2540] via-[#1a3a5a] to-[#0a2540] z-0"></div>
     <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')] z-10"></div>
    <div class="relative z-20 container mx-auto px-4 mt-8">
        <h1 class="text-5xl md:text-7xl font-black text-white mb-6 animate-fade-in tracking-tight">Get in Touch</h1>
        <p class="text-xl md:text-2xl text-blue-100 max-w-2xl mx-auto font-medium opacity-90 leading-relaxed">We're here to answer your questions and support you.</p>
    </div>
</div>

<div class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Contact Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12 max-w-4xl mx-auto">
            <!-- Phone Card -->
            <a href="tel:+94775842820" class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 text-center flex flex-col items-center transition-all duration-500 hover:shadow-2xl hover:shadow-blue-900/10 hover:-translate-y-2 reveal reveal-scale">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors duration-500">
                    <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Voice Call</h3>
                <p class="text-md font-bold text-primary">+94 77 584 2820</p>
                <span class="mt-4 text-xs font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">Call Now →</span>
            </a>

            <!-- Location Card -->
            <div class="group bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 text-center flex flex-col items-center transition-all duration-500 hover:shadow-2xl hover:shadow-purple-900/10 hover:-translate-y-2 reveal reveal-scale delay-200">
                <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-purple-600 transition-colors duration-500">
                    <svg class="w-7 h-7 text-purple-600 group-hover:text-white transition-colors duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Our Location</h3>
                <p class="text-md font-bold text-primary">Grace Community Church</p>
                <p class="text-[10px] text-slate-500 font-bold mt-1">No. 125, St. Peter's RD, Gedara Watta, Maggona, Sri Lanka, 12060</p>
            </div>
        </div>

        <!-- Map & Experience Section -->
        <div class="relative rounded-[3rem] overflow-hidden shadow-2xl bg-white border border-slate-100 reveal reveal-up">
            <div class="md:flex">
                <div class="md:w-1/2 p-12 flex flex-col justify-center reveal reveal-left delay-200">
                    <h2 class="text-3xl font-black text-primary mb-4 leading-tight">Join Us to be with Christ</h2>
                    <p class="text-slate-600 mb-8 leading-relaxed font-medium">
                        Whether you're visiting for the first time or looking for a home church, we'd love to welcome you. Our services are filled with authentic worship and practical teaching.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-slate-700 reveal reveal-up delay-300">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-black text-xs">01</div>
                            <span class="font-bold">Sunday Service: 8:30 AM</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-700 reveal reveal-up delay-400">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-black text-xs">02</div>
                            <span class="font-bold">Prayer Group: 8:30 AM | 11:30 PM</span>
                        </div>
                        <div class="flex items-center gap-4 text-slate-700 reveal reveal-up delay-500">
                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-primary font-black text-xs">03</div>
                            <span class="font-bold text-sm">Wed: Fasting (8:30 AM) | Study (11:30 AM)</span>
                        </div>
                    </div>
                </div>
                <div class="md:w-1/2 min-h-[400px] relative reveal reveal-right delay-200">
                    <iframe
                        src="https://maps.google.com/maps?q=No.+125,+St.+Peters+RD,+Gedara+Watta,+Maggona,+Sri+Lanka,+12060&t=&z=16&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="100%"
                        style="border:0; min-height:400px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Grace Community Church - No. 125, St. Peter's RD, Gedara Watta, Maggona, Sri Lanka">
                    </iframe>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
