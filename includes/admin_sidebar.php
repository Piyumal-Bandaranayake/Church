<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Mobile Toggle Button -->
<button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" onclick="toggleAdminSidebar()" class="inline-flex items-center p-2 mt-2 ms-3 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600 fixed top-4 left-2 z-50 bg-white border border-gray-200 shadow-md">
   <span class="sr-only">Open sidebar</span>
   <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
   </svg>
</button>

<aside id="admin-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full bg-[#0a2540] border-r border-white/10 sm:translate-x-0" aria-label="Sidebar">
   <div class="h-full px-3 pb-4 overflow-y-auto">
      <!-- Logo Section -->
      <div class="flex items-center justify-center py-6 mb-4 border-b border-white/10">
         <a href="index.php" class="flex items-center group">
            <img src="assets/images/logo.png" alt="Logo" class="h-16 w-auto logo-blend transform group-hover:scale-105 transition-transform duration-300">
         </a>
      </div>
      <ul class="space-y-2 font-medium">
         <li>
            <a href="admin_dashboard.php" class="flex items-center p-3 text-white/80 rounded-lg hover:bg-white/10 group text-base font-semibold <?php echo $current_page == 'admin_dashboard.php' ? 'bg-white/10 text-white font-bold' : ''; ?>">
               <svg class="w-5 h-5 transition duration-75 text-white/50 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                  <path d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z"/>
                  <path d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z"/>
               </svg>
               <span class="ms-3">Dashboard</span>
            </a>
         </li>
         
         <div class="py-2 font-semibold">
            <p class="px-3 text-[10px] font-bold text-white/30 uppercase tracking-widest">Pending Registrations</p>
            <li class="mt-1">
               <a href="pending_catholic.php" class="flex items-center p-3 text-white/70 rounded-lg hover:bg-white/10 group text-sm <?php echo ($current_page == 'pending_catholic.php' || (isset($active_page) && $active_page == 'pending_catholic')) ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-4 h-4 text-blue-400 group-hover:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <span class="ms-3">Catholic Pending</span>
               </a>
            </li>
            <li>
               <a href="pending_christian.php" class="flex items-center p-3 text-white/70 rounded-lg hover:bg-white/10 group text-sm <?php echo ($current_page == 'pending_christian.php' || (isset($active_page) && $active_page == 'pending_christian')) ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-4 h-4 text-purple-400 group-hover:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <span class="ms-3">Christian Pending</span>
               </a>
            </li>
         </div>

         <div class="py-2 border-t border-white/5 font-semibold">
            <p class="px-3 text-[10px] font-bold text-white/30 uppercase tracking-widest">Approved Directory</p>
            <li class="mt-1">
               <a href="approved_catholic.php" class="flex items-center p-3 text-white/70 rounded-lg hover:bg-white/10 group text-sm <?php echo ($current_page == 'approved_catholic.php' || (isset($active_page) && $active_page == 'approved_catholic')) ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-4 h-4 text-green-400 group-hover:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <span class="ms-3">Catholic Members</span>
               </a>
            </li>
            <li>
               <a href="approved_christian.php" class="flex items-center p-3 text-white/70 rounded-lg hover:bg-white/10 group text-sm <?php echo ($current_page == 'approved_christian.php' || (isset($active_page) && $active_page == 'approved_christian')) ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-4 h-4 text-green-400 group-hover:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                  <span class="ms-3">Christian Members</span>
               </a>
            </li>
         </div>

         <div class="py-2 border-t border-white/5 font-semibold">
            <p class="px-3 text-[10px] font-bold text-white/30 uppercase tracking-widest">Community</p>
            <li>
               <a href="manage_testimonies.php" class="flex items-center p-3 text-white/70 rounded-lg hover:bg-white/10 group text-sm <?php echo $current_page == 'manage_testimonies.php' ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-5 h-5 text-white/40 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                  <span class="ms-3">Testimonies</span>
               </a>
            </li>
         </div>

         <div class="py-2 border-t border-white/5 font-semibold">
            <p class="px-3 text-[10px] font-bold text-white/30 uppercase tracking-widest">Settings</p>
            <li>
               <a href="create_admin.php" class="flex items-center p-3 text-white/80 rounded-lg hover:bg-white/10 group text-base <?php echo $current_page == 'create_admin.php' ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-5 h-5 transition duration-75 text-white/50 group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                     <path d="M14 2a3.963 3.963 0 0 0-1.4.267 6.439 6.439 0 0 1-1.331 6.638A4 4 0 1 0 14 2Zm1 9h-1.264A6.957 6.957 0 0 1 15 15v2a2.97 2.97 0 0 1-.184 1H19a1 1 0 0 0 1-1v-1a5.006 5.006 0 0 0-5-5ZM6.5 9a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9ZM8 11H5a5.006 5.006 0 0 0-5 5v2a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1v-2a5.006 5.006 0 0 0-5-5Z"/>
                  </svg>
                  <span class="ms-3 whitespace-nowrap">Add Admin</span>
               </a>
            </li>
            <li>
               <a href="admin_profile.php" class="flex items-center p-3 text-white/80 rounded-lg hover:bg-white/10 group text-base <?php echo $current_page == 'admin_profile.php' ? 'bg-white/10 text-white font-bold' : ''; ?>">
                  <svg class="w-5 h-5 transition duration-75 text-white/50 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                  <span class="ms-3 whitespace-nowrap">Security</span>
               </a>
            </li>
         </div>

         <li class="pt-4 mt-4 border-t border-white/10">
            <a href="logout.php" class="flex items-center p-3 text-red-400 rounded-lg hover:bg-red-500/10 group text-base font-bold">
               <svg class="w-6 h-6 transition duration-75 text-red-400/50 group-hover:text-red-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 16">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 8h11m0 0L8 4m4 4-4 4m4-11h3a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-3"/>
               </svg>
               <span class="ms-3 whitespace-nowrap uppercase tracking-widest text-xs">Sign Out</span>
            </a>
         </li>
      </ul>
   </div>
</aside>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    sidebar.classList.toggle('-translate-x-full');
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('admin-sidebar');
    const toggleBtn = document.querySelector('[onclick="toggleAdminSidebar()"]');
    
    if (window.innerWidth < 640) { // sm breakpoint
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target) && !sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.add('-translate-x-full');
        }
    }
});
</script>
