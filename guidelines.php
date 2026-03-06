<?php
session_start();
include 'includes/header.php';
?>

<div class="pt-24 pb-12 bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/40 border border-gray-100 p-6 md:p-10">
            
            <div class="text-center mb-10 reveal reveal-up">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 mb-2 tracking-tight">Website Guidelines</h1>
                <h2 class="text-lg md:text-xl font-bold text-blue-600">වෙබ් අඩවිය භාවිතා කිරීම සඳහා උපදෙස්</h2>
                <div class="w-16 h-1 bg-blue-600 mx-auto mt-4 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-blue-50/30 rounded-3xl p-6 border border-blue-100 group hover:border-blue-300 transition-all duration-300 reveal reveal-up delay-100 relative overflow-hidden hover:shadow-lg hover:-translate-y-1 flex flex-col">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 font-black rounded-2xl flex items-center justify-center text-xl shrink-0 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all shadow-inner">1</div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-none mb-1">Registration</h3>
                            <h4 class="text-sm font-bold text-blue-700">ලියාපදිංචි වීම</h4>
                        </div>
                    </div>
                    <div class="space-y-4 flex-grow">
                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                            Create an account with accurate details. Your profile will stay <span class="text-blue-600 font-bold">"In Review"</span> until admin approval.
                        </p>
                        <div class="w-full h-px bg-blue-100"></div>
                        <p class="text-[13px] text-gray-700 leading-relaxed font-semibold">
                            නිවැරදි තොරතුරු සපයා ගිණුමක් සාදන්න. අපගේ පරිපාලකයින් අනුමත කරන තෙක් පෝරමය <span class="text-blue-600">"සමාලෝචනය"</span> යටතේ පවතී.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="bg-green-50/30 rounded-3xl p-6 border border-green-100 group hover:border-green-300 transition-all duration-300 reveal reveal-up delay-200 relative overflow-hidden hover:shadow-lg hover:-translate-y-1 flex flex-col">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-green-100 text-green-600 font-black rounded-2xl flex items-center justify-center text-xl shrink-0 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all shadow-inner">2</div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-none mb-1">Find Matches</h3>
                            <h4 class="text-sm font-bold text-green-700">සහකරුවන් සෙවීම</h4>
                        </div>
                    </div>
                    <div class="space-y-4 flex-grow">
                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                            Browse verified profiles. Use our <span class="text-green-600 font-bold">search filters</span> to find someone compatible with your faith.
                        </p>
                        <div class="w-full h-px bg-green-100"></div>
                        <p class="text-[13px] text-gray-700 leading-relaxed font-semibold">
                            තහවුරු කළ පෝරමය පරීක්ෂා කරන්න. ඔබේ ඇදහිල්ලට ගැලපෙන කෙනෙකු සොයන්න අපගේ <span class="text-green-600">සෙවුම් පෙරහන්</span> භාවිත කරන්න.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="bg-purple-50/30 rounded-3xl p-6 border border-purple-100 group hover:border-purple-300 transition-all duration-300 reveal reveal-up delay-300 relative overflow-hidden hover:shadow-lg hover:-translate-y-1 flex flex-col">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 font-black rounded-2xl flex items-center justify-center text-xl shrink-0 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all shadow-inner">3</div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-none mb-1">Connect Safely</h3>
                            <h4 class="text-sm font-bold text-purple-700">ආරක්ෂිතව සම්බන්ධ වීම</h4>
                        </div>
                    </div>
                    <div class="space-y-4 flex-grow">
                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                            Express your interest. Communicate respectfully inline with faith and keep <span class="text-purple-600 font-bold">safety top of mind</span>.
                        </p>
                        <div class="w-full h-px bg-purple-100"></div>
                        <p class="text-[13px] text-gray-700 leading-relaxed font-semibold">
                            කැමැත්ත ප්‍රකාශ කරන්න. ගෞරවනීයව අදහස් හුවමාරු කර ගන්නා අතර අන්තර්ජාල සම්බන්ධතා වලදී <span class="text-purple-600">ආරක්ෂාව</span> ගැන සැලකිලිමත් වන්න.
                        </p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="bg-orange-50/30 rounded-3xl p-6 border border-orange-100 group hover:border-orange-300 transition-all duration-300 reveal reveal-up delay-400 relative overflow-hidden hover:shadow-lg hover:-translate-y-1 flex flex-col">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-orange-100 text-orange-600 font-black rounded-2xl flex items-center justify-center text-xl shrink-0 group-hover:scale-110 group-hover:bg-orange-600 group-hover:text-white transition-all shadow-inner">4</div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-none mb-1">Success Stories</h3>
                            <h4 class="text-sm font-bold text-orange-700">සාර්ථක කථා</h4>
                        </div>
                    </div>
                    <div class="space-y-4 flex-grow">
                        <p class="text-sm text-gray-600 leading-relaxed font-medium">
                            Found a partner? Report via dashboard. Mark your profile as <span class="text-orange-500 font-bold">"Partner Found"</span> and share your story.
                        </p>
                        <div class="w-full h-px bg-orange-100"></div>
                        <p class="text-[13px] text-gray-700 leading-relaxed font-semibold">
                            සහකරු සොයා ගත්තේ නම් දැනුම් දෙන්න. පෝරමය <span class="text-orange-600">"සහකරු හමු විය"</span> කර කතාව අන් අයට බෙදා ගැනීමට හැකිය.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-10 border-t border-gray-100 pt-8">
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="registration_type.php" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 bg-primary text-white font-bold rounded-full hover:bg-primary-hover transition-all shadow-lg hover:-translate-y-1 text-sm md:text-base">
                        Register / ලියාපදිංචි වන්න
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="login.php" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3.5 bg-white text-primary border-2 border-primary font-bold rounded-full hover:bg-gray-50 transition-all shadow-md hover:-translate-y-1 text-sm md:text-base">
                        Login / පිවිසෙන්න
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
