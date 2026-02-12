<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="bg-gray-900 py-20 text-center relative overflow-hidden">
     <div class="absolute inset-0 bg-gradient-to-br from-primary to-blue-900 z-0"></div>
     <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] z-10"></div>
    <div class="relative z-20 container mx-auto px-4">
        <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Contact Us</h1>
        <p class="text-xl text-blue-100 max-w-2xl mx-auto">We'd love to hear from you. Get in touch with us today.</p>
    </div>
</div>

<div class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            
            <!-- Contact Form -->
            <div class="bg-white p-8 md:p-10 rounded-3xl shadow-lg border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                <form onsubmit="event.preventDefault(); alert('Thank you for your message! This is a demo form.');" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" id="name" name="name" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="John Doe" required>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="john@example.com" required>
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="(555) 123-4567">
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select id="subject" name="subject" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <option>General Inquiry</option>
                            <option>Prayer Request</option>
                            <option>Membership</option>
                            <option>Volunteering</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all" placeholder="How can we help you?" required></textarea>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-lg hover:bg-blue-900 transition-all duration-300 shadow-md transform hover:-translate-y-0.5">
                        Send Message
                    </button>
                </form>
            </div>

            <!-- Contact Info & Map -->
            <div class="space-y-12">
                <!-- Info Cards -->
                <div class="grid grid-cols-1 gap-6">
                    <div class="flex items-start p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 mr-6">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">Visit Us</h4>
                            <p class="text-gray-600">123 Grace Avenue<br>Springfield, IL 62704</p>
                        </div>
                    </div>

                    <div class="flex items-start p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 mr-6">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">Call Us</h4>
                            <p class="text-gray-600">(555) 123-4567<br>Mon-Fri, 9am - 5pm</p>
                        </div>
                    </div>

                    <div class="flex items-start p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0 mr-6">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-1">Email Us</h4>
                            <p class="text-gray-600">info@gracecommunity.org<br>prayer@gracecommunity.org</p>
                        </div>
                    </div>
                </div>

                <!-- Google Map -->
                <div class="bg-gray-200 rounded-3xl overflow-hidden shadow-lg h-80">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d199666.5651251294!2d-121.58334177520186!3d38.56165006739519!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x809ac672b28397f9%3A0x921f6aaa74197fdb!2sSacramento%2C%20CA%2C%20USA!5e0!3m2!1sen!2sba!4v1617226463990!5m2!1sen!2sba" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
