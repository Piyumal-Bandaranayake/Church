<?php include 'includes/header.php'; ?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900">Marriage Candidate Registration</h1>
            <p class="mt-2 text-gray-600">Please fill in your details accurately. Your profile will be reviewed by the admin before approval.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Form -->
            <form class="p-8 space-y-8" onsubmit="event.preventDefault(); window.location.href='login.php'; alert('Registration successful! Please wait for admin approval.');">
                
                <!-- Account Information -->
                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <h2 class="text-xl font-bold text-primary mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        Account Setup
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <input type="text" name="username" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" name="password" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" name="fullname" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sex</label>
                            <select name="sex" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="dob" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                            <input type="number" name="age" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                            <input type="text" name="nationality" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mother Tongue</label>
                            <input type="text" name="language" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Height (ft/cm)</label>
                            <input type="text" name="height" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Location</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                            <textarea name="address" rows="2" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Hometown</label>
                            <input type="text" name="hometown" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                            <input type="text" name="district" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Province</label>
                            <input type="text" name="province" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                 <!-- Professional & Education -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Education & Profession</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
                            <input type="text" name="occupation" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Educational Qualifications</label>
                            <textarea name="edu_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Degrees, Diplomas, etc."></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Additional Qualifications</label>
                            <textarea name="add_qual" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Skills, Certifications, etc."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Marital Status & Habits -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Personal Background</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marital Status</label>
                            <select name="marital_status" onchange="toggleChildren(this.value)" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="unmarried">Unmarried</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>
                        <div id="children_field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Do you have children?</label>
                            <select name="children" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Long-term Illness (requiring continuous treatment)</label>
                            <textarea name="illness" rows="2" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Describe if any, otherwise leave blank or type 'None'"></textarea>
                        </div>
                         <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Habits (Betel chewing / Smoking / Alcohol / Drugs)</label>
                            <div class="flex gap-4 flex-wrap">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="betel" class="rounded text-primary focus:ring-primary">
                                    <span>Betel Chewing</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="smoking" class="rounded text-primary focus:ring-primary">
                                    <span>Smoking</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="alcohol" class="rounded text-primary focus:ring-primary">
                                    <span>Alcohol</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="drugs" class="rounded text-primary focus:ring-primary">
                                    <span>Drugs</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="habit[]" value="none" class="rounded text-primary focus:ring-primary">
                                    <span>None</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Religious & Family -->
                <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Religious & Family Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Denomination / Church Name</label>
                            <input type="text" name="church" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name of Pastor</label>
                            <input type="text" name="pastor_name" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pastor's WhatsApp</label>
                            <input type="tel" name="pastor_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent's WhatsApp</label>
                            <input type="tel" name="parent_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your WhatsApp</label>
                            <input type="tel" name="my_phone" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Photo Upload -->
                 <div>
                    <h2 class="text-lg font-bold text-gray-900 mb-4 pb-2 border-b">Verification</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Recent Photograph (Face clearly visible)</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-primary transition-colors cursor-pointer bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-primary hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary">
                                            <span>Upload a file</span>
                                            <input id="file-upload" name="file-upload" type="file" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-200">
                    <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg text-lg font-bold text-white bg-primary hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all transform hover:-translate-y-1">
                        Submit Application
                    </button>
                    <p class="mt-4 text-center text-sm text-gray-500">
                        By submitting this form, you certify that all information provided is true and accurate.
                    </p>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function toggleChildren(status) {
    const childrenField = document.getElementById('children_field');
    if (status === 'divorced' || status === 'widowed') {
        childrenField.classList.remove('hidden');
    } else {
        childrenField.classList.add('hidden');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
