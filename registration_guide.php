<?php
session_start();
include 'includes/header.php';
?>

<div class="pt-24 pb-12 bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 md:p-10">
            
            <div class="text-center mb-10 reveal reveal-up">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2 tracking-tight">Registration Guide</h1>
                <h2 class="text-lg md:text-xl font-bold text-blue-600">ලියාපදිංචි වීමේ මාර්ගෝපදේශය</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-8">
                <!-- Step 1 -->
                <div class="bg-blue-50/40 rounded-3xl p-8 border hover:border-blue-200 transition-all duration-300 reveal reveal-up group relative overflow-hidden flex flex-col hover:shadow-2xl hover:bg-white hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full flex items-start justify-end p-4 transition-transform group-hover:scale-110 opacity-50 z-0"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-16 h-16 bg-blue-600 text-white font-black rounded-2xl flex items-center justify-center text-3xl shadow-lg mb-6 group-hover:rotate-6 transition-transform">1</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-1">Initial Registration</h3>
                        <h4 class="text-lg font-bold text-blue-700 mb-4">මුලික ලියාපදිංචිය</h4>
                        <div class="flex-grow space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                Click on the <strong>Register</strong> button from the homepage. Fill in your basic information securely.
                            </p>
                            <div class="w-full h-px bg-blue-200/50"></div>
                            <p class="text-sm font-semibold text-gray-600 leading-relaxed">
                                මුල් පිටුවෙන් <span class="text-blue-600">Register</span> බොත්තම ඔබා ඔබගේ මූලික තොරතුරු නිවැරදිව පුරවන්න.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-indigo-50/40 rounded-3xl p-8 border hover:border-indigo-200 transition-all duration-300 reveal reveal-up delay-100 group relative overflow-hidden flex flex-col hover:shadow-2xl hover:bg-white hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-100 rounded-bl-full flex items-start justify-end p-4 transition-transform group-hover:scale-110 opacity-50 z-0"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-16 h-16 bg-indigo-600 text-white font-black rounded-2xl flex items-center justify-center text-3xl shadow-lg mb-6 group-hover:rotate-6 transition-transform">2</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-1">Profile Details</h3>
                        <h4 class="text-lg font-bold text-indigo-700 mb-4">පෝරමය තොරතුරු</h4>
                        <div class="flex-grow space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                Upload a clear photo and fill in personal details. Honesty is crucial for finding the right match. Your profile will be marked as "In Review".
                            </p>
                            <div class="w-full h-px bg-indigo-200/50"></div>
                            <p class="text-sm font-semibold text-gray-600 leading-relaxed">
                                පැහැදිලි ඡායාරූපයක් එක් කර සවිස්තරාත්මකව තොරතුරු පුරවන්න. ඔබගේ ගිණුම <span class="text-indigo-600">"සමාලෝචනයේ"</span> පවතිනු ඇත.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-green-50/40 rounded-3xl p-8 border hover:border-green-200 transition-all duration-300 reveal reveal-up delay-200 group relative overflow-hidden flex flex-col hover:shadow-2xl hover:bg-white hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-bl-full flex items-start justify-end p-4 transition-transform group-hover:scale-110 opacity-50 z-0"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-16 h-16 bg-green-600 text-white font-black rounded-2xl flex items-center justify-center text-3xl shadow-lg mb-6 group-hover:rotate-6 transition-transform">3</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-1">Admin Approval</h3>
                        <h4 class="text-lg font-bold text-green-700 mb-4">පරිපාලක අනුමැතිය</h4>
                        <div class="flex-grow space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                Our administration team will review your profile to ensure safety and authenticity. Once approved, you will be notified and can access full features.
                            </p>
                            <div class="w-full h-px bg-green-200/50"></div>
                            <p class="text-sm font-semibold text-gray-600 leading-relaxed">
                                අපගේ පරිපාලක මණ්ඩලය ඔබගේ තොරතුරු තහවුරු කර අනුමත කරනු ඇත.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-orange-50/40 rounded-3xl p-8 border hover:border-orange-200 transition-all duration-300 reveal reveal-up delay-300 group relative overflow-hidden flex flex-col hover:shadow-2xl hover:bg-white hover:-translate-y-2">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-100 rounded-bl-full flex items-start justify-end p-4 transition-transform group-hover:scale-110 opacity-50 z-0"></div>
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-16 h-16 bg-orange-600 text-white font-black rounded-2xl flex items-center justify-center text-3xl shadow-lg mb-6 group-hover:rotate-6 transition-transform">4</div>
                        <h3 class="text-2xl font-black text-gray-900 mb-1">Login & Browse</h3>
                        <h4 class="text-lg font-bold text-orange-700 mb-4">පිවිසීම සහ සෙවීම</h4>
                        <div class="flex-grow space-y-4">
                            <p class="text-gray-700 leading-relaxed">
                                Use your email and password to log in. You can now securely browse matching profiles and communicate.
                            </p>
                            <div class="w-full h-px bg-orange-200/50"></div>
                            <p class="text-sm font-semibold text-gray-600 leading-relaxed">
                                ඔබගේ විද්‍යුත් තැපෑල සහ මුරපදය භාවිතයෙන් පිවිසී ගැලපෙන සහකරුවන් සොයන්න.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="mt-12 border-t border-gray-100 pt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="registration_type.php" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-10 py-4 bg-primary text-white font-bold rounded-full hover:bg-primary-hover transition-all shadow-xl hover:-translate-y-1 text-base md:text-lg">
                    Start Registration Now
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="login.php" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-10 py-4 bg-white text-primary border-2 border-primary font-bold rounded-full hover:bg-gray-50 transition-all shadow-md hover:-translate-y-1 text-base md:text-lg">
                    Login to Account
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </a>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
