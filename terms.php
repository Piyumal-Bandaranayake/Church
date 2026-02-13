<?php
session_start();
include 'includes/db.php';
?>
<?php include 'includes/header.php'; ?>

<main class="min-h-screen bg-[#fafbff] py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-black text-gray-900 mb-4 uppercase tracking-tight">Our Values & Policies</h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto font-medium">Please review our guidelines to understand how we operate and our commitment to building Christ-centered families.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- 01 Terms and Conditions -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
             
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Let all things be done decently and in order.” – 1 Corinthians 14:40
                    </div>
                    <p class="text-gray-700 font-medium">By using this website, you agree to follow Christian values, honesty, and respectful behaviour. This platform exists only for marriage proposals among Sri Lankan Non-Catholic Christians and is not a dating site.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: මෙමෙ වෙබ් අඩවිය භාවිතා කිරීමෙන් ඔබ ක්රිස්තියානි ආචාරධර්ම සහ නීති පිළිපදින බවට එකඟ වෙයි.</p>
                        <p>🇮🇳 தமிழ்: இந்த இணையதளத்தை பயன்படுத்துவதன் மூலம் கிறிஸ்தவ ஒழுக்க நெறிகளை பின்பற்ற நீங்கள் ஒப்புக்கொள்கிறீர்கள்.</p>
                    </div>
                </div>
            </div>

            <!-- 02 Privacy Policy -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
              
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “A good name is rather to be chosen than great riches.” – Proverbs 22:1
                    </div>
                    <p class="text-gray-700 font-medium">Your personal information is protected. We do not sell or share your data. Only limited details are visible to others.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: ඔබගේ පුද්ගලික දත්ත රහසිගතව ආරක්ෂා කරයි.</p>
                        <p>🇮🇳 தமிழ்: உங்கள் தனிப்பட்ட தகவல்கள் பாதுகாப்பாக கையாளப்படும்.</p>
                    </div>
                </div>
            </div>

            <!-- 03 Our Vision -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
               
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Except the LORD build the house, they labour in vain.” – Psalm 127:1
                    </div>
                    <p class="text-gray-700 font-medium">To build Christ-centred marriages that honour God and strengthen families.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: දේව වචනය මත පදනම් වූ විවාහ ගොඩනැගීමයි අපගේ දර්ශනය.</p>
                        <p>🇮🇳 தமிழ்: கிறிஸ்துவை மையமாகக் கொண்ட திருமணங்களை உருவாக்குவதே எங்கள் நோக்கம்.</p>
                    </div>
                </div>
            </div>

            <!-- 04 Our Mission -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
             
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Trust in the LORD with all thine heart.” – Proverbs 3:5
                    </div>
                    <p class="text-gray-700 font-medium">To prayerfully connect believers seeking marriage while upholding biblical purity and truth.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: ප්රාර්ථනාවෙන් සහ බයිබලය මත පදනම්ව සම්බන්ධතා ගොඩනැගීම.</p>
                        <p>🇮🇳 தமிழ்: ஜெபத்துடன் விசுவாசிகளை திருமணத்திற்காக இணைப்பதே எங்கள் பணியாகும்.</p>
                    </div>
                </div>
            </div>

            <!-- 05 Disclaimer -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
              
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Every man shall bear his own burden.” – Galatians 6:5
                    </div>
                    <p class="text-gray-700 font-medium">We introduce people only. Marriage decisions are personal and family-based. The platform bears no responsibility for interactions or outcomes.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: විවාහ තීරණය පුද්ගලික වගකීමකි.</p>
                        <p>🇮🇳 தமிழ்: திருமண முடிவுகள் தனிப்பட்ட பொறுப்பாகும்.</p>
                    </div>
                </div>
            </div>

            <!-- 06 Advice to Parents -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
              
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Train up a child in the way he should go.” – Proverbs 22:6
                    </div>
                    <p class="text-gray-700 font-medium">Your prayer, guidance, and wisdom matter greatly in your child’s marriage. Parental advice is encouraged but remains voluntary.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: දරුවන්ගේ විවාහයට ඔබගේ උපදෙස් ඉතා වැදගත්ය.</p>
                        <p>🇮🇳 தமிழ்: உங்கள் பிள்ளைகளின் திருமணத்தில் உங்கள் ஆலோசனை மதிப்புமிக்கது.</p>
                    </div>
                </div>
            </div>

            <!-- 07 Advice to Candidates -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
                
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Commit thy way unto the LORD.” – Psalm 37:5
                    </div>
                    <p class="text-gray-700 font-medium">Seek God, be honest, stay pure, and respect families. Users are responsible for their conduct and decisions.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: දෙවියන් වහන්සේව විශ්වාසයෙන් සොයන්න.</p>
                        <p>🇮🇳 தமிழ்: கர்த்தரிடம் உங்களை ஒப்படையுங்கள்.</p>
                    </div>
                </div>
            </div>

            <!-- 08 Taglines & Themes -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
                
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Husbands, love your wives, even as Christ also loved the church.” – Ephesians 5:25
                    </div>
                    <ul class="text-gray-700 font-medium space-y-2 list-disc pl-5">
                        <li>Where Faith Leads to Family</li>
                        <li>Building Christ-Centred Marriages</li>
                        <li>Not Dating. Preparing for Covenant</li>
                        <li>Prayerfully Connecting Lives</li>
                    </ul>
                </div>
            </div>

            <!-- 09 Image Guidelines -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
               
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Whether therefore ye eat, or drink… do all to the glory of God.” – 1 Corinthians 10:31
                    </div>
                    <p class="text-gray-700 font-medium">Use modest, respectful, faith-centred images only. Profiles with inappropriate imagery will be removed.</p>
                </div>
            </div>

            <!-- 10 Necessary Info -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
               
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Watch ye, stand fast in the faith.” – 1 Corinthians 16:13
                    </div>
                    <p class="text-gray-700 font-medium">All profiles are moderated by our admin team. Any misuse or violation of values will lead to immediate removal without notice.</p>
                </div>
            </div>

            <!-- 11 Code of Conduct -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full">
               
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Be ye holy; for I am holy.” – 1 Peter 1:16
                    </div>
                    <p class="text-gray-700 font-medium">Be truthful, respectful, pure, and Christ-like in all communication. Violation may result in permanent removal.</p>
                    <div class="pt-4 space-y-2 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: ශුද්ධත්වයෙන් සහ ගෞරවයෙන් හැසිරෙන්න.</p>
                        <p>🇮🇳 தமிழ்: பரிசுத்தத்திலும் மரியாதையிலும் நடந்து கொள்ளுங்கள்.</p>
                    </div>
                </div>
            </div>

            <!-- 12 Faith Statement -->
            <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col h-full md:col-span-2">
                 
                <div class="space-y-4 flex-grow">
                    <div class="bg-blue-50 p-4 rounded-2xl text-sm italic text-primary border border-blue-100">
                        “Jesus saith… I am the way, the truth, and the life.” – John 14:6
                    </div>
                    <p class="text-gray-700 font-medium text-lg">We believe the Bible, salvation through Jesus Christ, and God’s design for marriage as the final authority.</p>
                    <div class="flex flex-wrap gap-8 pt-6 border-t border-gray-50 text-xs font-bold uppercase tracking-widest text-gray-400">
                        <p>🇱🇰 සිංහල: බයිබලය අපගේ අවසාන අධිකාරියයි.</p>
                        <p>🇮🇳 தமிழ்: வேதாகமமே எங்கள் இறுதி அதிகாரம்.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="mt-16 text-center">
            <a href="register.php" class="inline-flex items-center gap-3 px-10 py-5 bg-primary text-white rounded-[2rem] font-black text-xl hover:bg-primary-hover shadow-xl shadow-primary/20 transition-all hover:-translate-y-1">
                Back to Registration
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
