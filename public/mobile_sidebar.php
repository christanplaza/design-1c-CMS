<nav :class="isOpen ? 'flex': 'hidden'" class="flex flex-col pt-4">
    <a href="dashboard.php" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    <a href="blank.php" class="flex items-center active-nav-link text-white py-2 pl-4 nav-item">
        <i class="fas fa-sticky-note mr-3"></i>
        Blank Page
    </a>
    <a href="#" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
        <i class="fas fa-user mr-3"></i>
        My Account
    </a>
    <a href="#" class="flex items-center text-white opacity-75 hover:opacity-100 py-2 pl-4 nav-item">
        <i class="fas fa-sign-out-alt mr-3"></i>
        Sign Out
    </a>
    <!-- <button class="w-full bg-white cta-btn font-semibold py-2 mt-3 rounded-lg shadow-lg hover:shadow-xl hover:bg-gray-300 flex items-center justify-center">
        <i class="fas fa-arrow-circle-up mr-3"></i> Upgrade to Pro!
    </button> -->
</nav>