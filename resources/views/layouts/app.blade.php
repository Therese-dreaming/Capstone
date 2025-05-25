<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asset Management System</title>
    <!-- Add Poppins font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif !important;
        }

        .nav-active svg {
            stroke: #FEB35A !important;
        }

        .nav-active {
            color: white !important;
            font-weight: bold;
            background-color: rgb(185 28 28) !important;
        }

        .menu-open {
            display: block !important;
        }

    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-red-800 text-white py-4 px-6 flex items-center justify-between fixed top-0 left-0 right-0 z-50 shadow-lg">
        <!-- Left side - Logo and Title -->
        <div class="flex items-center space-x-4">
            <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="h-10 w-10">
            <h1 class="text-xl font-bold uppercase">Operations Management System</h1>
        </div>

        @auth
        <!-- Right side - User Profile and Notifications -->
        <div class="flex items-center space-x-6">
            <!-- User Profile Dropdown -->
            <div class="relative">
                <button id="profileDropdown" class="flex items-center space-x-3 hover:text-gray-200">
                    <img src="{{ Auth::user()->profile_picture ?? asset('images/default-profile.png') }}" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    <span>{{ Auth::user()->name ?? 'User Name' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 text-gray-700 hidden">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Edit Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="relative">
                <button id="notificationButton" class="hover:text-gray-200 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span id="notificationCount" class="absolute -top-1 -right-1 bg-red-500 text-xs rounded-full h-5 w-5 flex items-center justify-center {{ $unreadCount ?? 0 ? '' : 'hidden' }}">
                        {{ $unreadCount ?? 0 }}
                    </span>
                </button>

                <!-- Notification Dropdown -->
                <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg overflow-hidden z-50">
                    <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="font-semibold text-gray-700">Notifications</h3>
                        <button id="markAllRead" class="text-sm text-red-600 hover:text-red-800">Mark all as read</button>
                    </div>
                    <div id="notificationList" class="max-h-96 overflow-y-auto">
                        <!-- Notifications will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
        @endauth
    </header>

    <div class="flex pt-16">
        <!-- Sidebar -->
        @auth
        <aside class="w-72 bg-red-800 text-white min-h-screen fixed left-0 top-16 pt-10">
            <div class="p-4 pl-8">
                <!-- Added pl-8 for more right padding -->
                <h2 class="text-sm text-[#D5999B] mb-8">MENU</h2>
                <nav class="space-y-3">
                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3])) 
                    <a href="{{ route('my.tasks') }}" class="font-bold flex items-center px-6 py-2.5 text-white-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        My Tasks
                    </a>
                    @endif

                    <!-- Dashboard - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3]))
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-4 py-1.5 hover:bg-red-700 rounded-md text-[#D5999B] text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    <!-- Secretary Dashboard - Show only to secretary -->
                    @if(auth()->check() && auth()->user()->group_id === 2)
                    <a href="{{ url('/secretary-dashboard') }}" class="flex items-center space-x-2 px-4 py-1.5 hover:bg-red-700 rounded-md text-[#D5999B] text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Secretary Dashboard</span>
                    </a>
                    @endif
                    @endauth

                    <!-- User Management - Hide from secretary -->
                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3]))
                    <div class="space-y-1.5">
                        <button onclick="toggleUserMenu()" class="w-full flex items-center px-4 py-1.5 text-[#D5999B] hover:bg-red-700 rounded-md text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>User Management</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="userMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('users.index') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                View Users
                            </a>
                            <a href="{{ route('groups.index') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                View Groups
                            </a>
                        </div>
                    </div>
                    @endif
                    @endauth

                    <!-- Categories - Hide from secretary -->
                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3]))
                    <a href="{{ route('categories.index') }}" class="flex items-center space-x-2 px-4 py-1.5 hover:bg-red-700 rounded-md text-[#D5999B] text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Categories</span>
                    </a>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && auth()->user()->group_id !== 3)
                    <!-- Assets -->
                    <!-- Asset List -->
                    <a href="{{ route('assets.index') }}" class="flex items-center space-x-2 px-4 py-1.5 hover:bg-red-700 rounded-md text-[#D5999B] text-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path>
                        </svg>
                        <span>Asset List</span>
                    </a>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && auth()->user()->group_id !== 3)
                    <!-- Lab Maintenance -->
                    <div class="space-y-1.5">
                        <button onclick="toggleMaintenanceMenu()" class="w-full flex items-center px-4 py-1.5 text-[#D5999B] hover:bg-red-700 rounded-md text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Lab Maintenance</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="maintenanceMenu" class="hidden ml-8 space-y-1.5">
                        @auth
                        @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('maintenance.schedule') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Schedule Maintenance
                            </a>
                        @endif
                        @endauth
                            <a href="{{ route('maintenance.upcoming') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Upcoming Maintenance
                            </a>
                        @auth
                        @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('maintenance.history') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Maintenance History
                            </a>
                        </div>
                        @endif
                        @endauth
                    </div>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && auth()->user()->group_id !== 3)
                    <!-- Asset Repair -->
                    <div class="space-y-1.5">
                        <button onclick="toggleRepairMenu()" class="w-full flex items-center px-4 py-1.5 text-[#D5999B] hover:bg-red-700 rounded-md text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                            <span>Asset Repair</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="repairMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('repair.request') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Repair Request
                            </a>
                            <a href="{{ route('repair.urgent') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Urgent Repairs
                            </a>
                            <a href="{{ route('repair.status') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Repair Status
                            </a>
                        @auth
                        @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('repair.completed') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Repair History
                            </a>
                        </div>
                        @endif
                        @endauth
                    </div>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && auth()->user()->group_id !== 2)
                    <!-- Lab Schedule -->
                    <div class="space-y-1.5">
                        <button onclick="toggleLabScheduleMenu()" class="w-full flex items-center px-4 py-1.5 text-[#D5999B] hover:bg-red-700 rounded-md text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Lab Schedule</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="labScheduleMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('lab.logging') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Lab Logging
                            </a>
                            <a href="{{ route('lab-schedule.history') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Lab History
                            </a>
                            @endif
                            @endauth
                        </div>
                    </div>

                    <!-- View Reports - Hide from secretary -->
                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3]))
                    <div class="space-y-1.5">
                        <button onclick="toggleReportMenu()" class="w-full flex items-center px-4 py-1.5 text-[#D5999B] hover:bg-red-700 rounded-md text-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>View Reports</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reportMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('reports.category') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Category-Based Report
                            </a>
                            <a href="{{ route('reports.location') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Location-Based Report
                            </a>
                            <a href="{{ route('reports.disposal-history') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Disposal History
                            </a>
                            <a href="{{ route('reports.procurement-history') }}" class="block py-1.5 px-4 text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1] rounded-md text-sm">
                                Procurement History
                            </a>
                        </div>
                    </div>
                    @endif
                    @endauth
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        @yield('content')
    </div>

    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Update selector to include all navigation items
            const navItems = document.querySelectorAll('a[href], button[onclick]');
            const currentPath = window.location.pathname;

            function setActiveState(element) {
                // Remove active state from all items
                navItems.forEach(item => {
                    if (!item.closest('#profileMenu')) { // Exclude profile menu items
                        item.classList.remove('nav-active');
                        item.classList.remove('bg-red-700');
                    }
                });

                if (element && !element.closest('#profileMenu')) {
                    // Add active state to clicked element
                    element.classList.add('nav-active');
                    element.classList.add('bg-red-700');

                    // Handle dropdown menus
                    const parentDropdown = element.closest('.space-y-1.5');
                    if (parentDropdown) {
                        const dropdownButton = parentDropdown.querySelector('button');
                        const dropdownMenu = parentDropdown.querySelector('[id$="Menu"]');

                        if (dropdownButton) {
                            dropdownButton.classList.add('nav-active');
                            dropdownButton.classList.add('bg-red-700');
                        }

                        if (dropdownMenu) {
                            dropdownMenu.classList.remove('hidden');
                            localStorage.setItem(dropdownMenu.id, 'open');
                        }
                    }

                    // Store active path
                    localStorage.setItem('activePath', currentPath);
                }
            }

            // Check if current path is within a dropdown and open it
            function toggleReportMenu() {
                const menu = document.getElementById('reportMenu');
                menu.classList.toggle('hidden');
                localStorage.setItem('reportMenu', menu.classList.contains('hidden') ? 'closed' : 'open');
            }

            // Update the dropdowns object in checkAndOpenDropdown function
            function checkAndOpenDropdown() {
                const dropdowns = {
                    'userMenu': ['/users', '/groups']
                    , 'maintenanceMenu': ['/maintenance']
                    , 'repairMenu': ['/repair']
                    , 'reportMenu': ['/reports']
                };

                for (const [menuId, paths] of Object.entries(dropdowns)) {
                    if (paths.some(path => currentPath.includes(path))) {
                        const menu = document.getElementById(menuId);
                        const button = menu.previousElementSibling;
                        menu.classList.remove('hidden');
                        button.classList.add('nav-active');
                        button.classList.add('bg-red-700');
                        localStorage.setItem(menuId, 'open');
                    }
                }
            }

            // Set initial active state and open appropriate dropdown
            navItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && href !== '#' && !item.closest('#profileMenu')) {
                    try {
                        const url = new URL(href, window.location.origin);
                        if (url.pathname === currentPath) {
                            setActiveState(item);
                        }
                    } catch (e) {
                        console.log('Invalid URL:', href);
                    }
                }
            });

            // Check and open dropdown for current path
            checkAndOpenDropdown();

            // Handle clicks on navigation items
            navItems.forEach(item => {
                if (!item.closest('#profileMenu')) { // Exclude profile menu items
                    item.addEventListener('click', function(e) {
                        if (this.tagName === 'BUTTON') {
                            if (this.id !== 'profileDropdown') {
                                e.preventDefault();
                                const menuId = this.getAttribute('onclick').match(/toggle(\w+)Menu/)[1].toLowerCase() + 'Menu';
                                const menu = document.getElementById(menuId);
                                const isHidden = menu.classList.contains('hidden');
                                menu.classList.toggle('hidden');
                                this.classList.toggle('nav-active');
                                this.classList.toggle('bg-red-700');
                                localStorage.setItem(menuId, isHidden ? 'open' : 'closed');
                            }
                        } else if (this.getAttribute('href') !== '#') {
                            setActiveState(this);
                        }
                    });
                }
            });

            // Set initial active state for current page
            navItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href && href !== '#' && !item.closest('#profileMenu')) {
                    try {
                        const url = new URL(href, window.location.origin);
                        if (url.pathname === currentPath) {
                            setActiveState(item);
                        }
                    } catch (e) {
                        console.log('Invalid URL:', href);
                    }
                }
            });

            // Check and open dropdown for current path
            checkAndOpenDropdown();
        });

        // Profile dropdown functionality
        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');

        // Toggle dropdown when clicking the profile button
        profileDropdown.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileDropdown.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });

        // Notification
        document.addEventListener('DOMContentLoaded', function() {
            const notificationButton = document.getElementById('notificationButton');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const notificationList = document.getElementById('notificationList');
            const markAllReadButton = document.getElementById('markAllRead');
            const notificationCount = document.getElementById('notificationCount');

            // Toggle notification dropdown
            notificationButton.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationDropdown.classList.toggle('hidden');
                if (!notificationDropdown.classList.contains('hidden')) {
                    loadNotifications();
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }
            });

            // Mark all as read
            markAllReadButton.addEventListener('click', function() {
                fetch('{{ route("notifications.markAllAsRead") }}', {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            , 'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            updateNotificationCount(0);
                            loadNotifications();
                        }
                    });
            });

            // Make markAsRead function globally accessible
            window.markAsRead = function(id) {
                fetch(`/notifications/${id}/read`, {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            , 'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                            updateUnreadCount();
                        }
                    });
            };

            function loadNotifications() {
                fetch('{{ route("notifications.index") }}')
                    .then(response => response.json())
                    .then(data => {
                                               notificationList.innerHTML = data.length ?
                            data.map(notification => `
                                <div class="p-4 border-b border-gray-100 transition-colors duration-200 hover:bg-gray-50 ${notification.is_read ? 'bg-white' : 'bg-red-50'}" 
                                     data-id="${notification.id}">
                                    <div class="flex justify-between items-start gap-4">
                                        <div class="flex items-start gap-3 flex-1 min-w-0">
                                            <div class="flex-shrink-0 mt-0.5">
                                                ${notification.type === 'repair' ? `
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${notification.is_read ? 'text-gray-400' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4" />
                                                    </svg>
                                                ` : notification.type === 'maintenance' ? `
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${notification.is_read ? 'text-gray-400' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                ` : `
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${notification.is_read ? 'text-gray-400' : 'text-red-500'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                `}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <a href="${notification.link || '#'}" 
                                                   class="block group" 
                                                   onclick="event.preventDefault(); window.location.href = '${notification.link || '#'}'">
                                                    <p class="text-sm text-gray-800 mb-1 line-clamp-2 group-hover:text-red-600 transition-colors duration-200">
                                                        ${notification.message}
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="text-xs text-gray-500">${formatDate(notification.created_at)}</p>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        ${!notification.is_read ? `
                                            <button onclick="markAsRead(${notification.id})" 
                                                    class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Mark as read
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            `).join('') :
                            '<div class="p-8 text-center text-gray-500 flex flex-col items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>No notifications</div>';
                    });
            }

            function markAsRead(id) {
                fetch(`/notifications/${id}/read`, {
                        method: 'POST'
                        , headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            , 'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadNotifications();
                            updateUnreadCount();
                        }
                    });
            }

            function updateUnreadCount() {
                fetch('{{ route("notifications.unreadCount") }}')
                    .then(response => response.json())
                    .then(data => {
                        updateNotificationCount(data.count);
                    });
            }

            function updateNotificationCount(count) {
                notificationCount.textContent = count;
                notificationCount.classList.toggle('hidden', count === 0);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;

                // Less than 24 hours
                if (diff < 86400000) {
                    return new Intl.RelativeTimeFormat('en', {
                            numeric: 'auto'
                        })
                        .format(-Math.round(diff / 3600000), 'hour');
                }

                // Less than 7 days
                if (diff < 604800000) {
                    return new Intl.RelativeTimeFormat('en', {
                            numeric: 'auto'
                        })
                        .format(-Math.round(diff / 86400000), 'day');
                }

                // Otherwise return formatted date
                return date.toLocaleDateString('en-US', {
                    year: 'numeric'
                    , month: 'short'
                    , day: 'numeric'
                });
            }

            // Initial load of unread count
            updateUnreadCount();
        });

        function toggleLabScheduleMenu() {
            const menu = document.getElementById('labScheduleMenu');
            const button = document.querySelector('[onclick="toggleLabScheduleMenu()"]');
            if (menu && button) {
                menu.classList.toggle('hidden');
                button.classList.toggle('nav-active');
            }
        }

        // Add this function to initialize the Lab Schedule menu state
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const labScheduleButton = document.querySelector('[onclick="toggleLabScheduleMenu()"]');
            const labScheduleMenu = document.getElementById('labScheduleMenu');

            // Check if current path is lab schedule related
            if (currentPath.includes('/lab-schedule') || currentPath.includes('/lab-history') || currentPath.includes('/lab-logging')) {
                if (labScheduleButton && labScheduleMenu) {
                    labScheduleButton.classList.add('nav-active');
                    labScheduleMenu.classList.remove('hidden');
                }
            }
        });

    </script>
</body>
</html>
