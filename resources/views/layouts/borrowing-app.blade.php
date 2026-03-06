<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Asset Borrowing System - IT-ARMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif !important;
        }
        .menu-open {
            display: block !important;
        }
        @media (min-width: 768px) {
            .content-shifted {
                margin-left: 15rem !important;
            }
        }
        @media (max-width: 768px) {
            .sidebar-open {
                transform: translateX(0) !important;
            }
            .sidebar-overlay {
                display: block !important;
                opacity: 0.5 !important;
            }
            .content-shifted {
                margin-left: 0 !important;
            }
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        /* Smooth transitions */
        .sidebar-link {
            position: relative;
        }
        
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: -16px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 60%;
            background: linear-gradient(to bottom, #dc2626, #ef4444);
            border-radius: 0 2px 2px 0;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 py-2.5 px-4 flex items-center justify-between fixed top-0 left-0 right-0 z-50 shadow-sm">
        <div class="flex items-center space-x-3">
            @auth
            <button id="sidebarToggle" class="text-gray-600 hover:text-gray-900 focus:outline-none p-1 rounded hover:bg-gray-100 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            @endauth
            <a href="{{ route('borrowing.dashboard') }}" class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="h-8 w-8">
                <div class="flex flex-col">
                    <h1 class="text-sm font-semibold text-gray-800 hidden sm:block leading-tight">Asset Borrowing</h1>
                    <span class="text-xs text-gray-500 hidden sm:block leading-tight">IT-ARMS</span>
                </div>
            </a>
        </div>

        @auth
        <div class="flex items-center space-x-2 sm:space-x-4">
            <!-- User Profile Dropdown -->
            <div class="relative">
                <button id="profileDropdown" class="flex items-center space-x-2 hover:bg-gray-50 rounded-lg px-2 py-1.5 transition-colors">
                    @if(Auth::user()->getProfilePictureUrl())
                    <img src="{{ Auth::user()->getProfilePictureUrl() }}" alt="Profile" class="h-7 w-7 rounded-full object-cover border border-gray-200">
                    @else
                    <img src="{{ asset('images/default-profile.png') }}" alt="Profile" class="h-7 w-7 rounded-full object-cover border border-gray-200">
                    @endif
                    <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'User Name' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="profileMenu" class="absolute right-0 mt-2 w-44 bg-white rounded-lg shadow-lg border border-gray-200 py-1 text-gray-700 hidden z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm hover:bg-gray-50 transition-colors">Edit Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-50 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            @include('partials.notifications')
        </div>
        @endauth
    </header>

    <!-- Sidebar overlay for mobile -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-0 pointer-events-none z-40 transition-opacity duration-300 ease-in-out md:hidden"></div>

    <div class="flex pt-14">
        <!-- Sidebar -->
        @auth
        <aside id="sidebar" class="w-60 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-14 z-40 transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto shadow-sm">
            <div class="p-4">
                <nav class="space-y-6 overflow-y-auto max-h-[calc(100vh-80px)]">
                    <!-- Main Section -->
                    <div class="space-y-1">
                        <a href="{{ route('borrowing.dashboard') }}" class="sidebar-link {{ request()->routeIs('borrowing.dashboard') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('borrowing.dashboard') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('borrowing.create') }}" class="sidebar-link {{ request()->routeIs('borrowing.create') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('borrowing.create') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>New Borrowing</span>
                        </a>
                    </div>

                    <!-- Asset Management -->
                    <div class="space-y-1">
                        <div class="px-3 mb-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assets</h3>
                        </div>
                        <button onclick="toggleAssetMenu()" class="w-full flex items-center px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                            <svg class="w-4 h-4 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <span>Asset Management</span>
                            <svg class="w-3 h-3 ml-auto transition-transform" id="assetMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="assetMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('borrowing.assets.create') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.assets.create') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.assets.create') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Add New Asset</span>
                            </a>
                            <a href="{{ route('borrowing.assets.all') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.assets.all') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.assets.all') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>All Assets</span>
                            </a>
                            <a href="{{ route('borrowing.assets.available') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.assets.available') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.assets.available') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Available Assets</span>
                            </a>
                            <a href="{{ route('borrowing.assets.borrowed') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.assets.borrowed') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.assets.borrowed') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Borrowed Assets</span>
                            </a>
                            <a href="{{ route('borrowing.history') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.history') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.history') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Borrowing History</span>
                            </a>
                        </div>
                    </div>

                    <!-- Reports -->
                    <div class="space-y-1">
                        <div class="px-3 mb-2">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Analytics</h3>
                        </div>
                        <button onclick="toggleReportsMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span>Reports</span>
                            </div>
                            <svg id="reportsChevron" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reportsMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('borrowing.reports.by-user') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.reports.by-user') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.reports.by-user') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>By User</span>
                            </a>
                            <a href="{{ route('borrowing.reports.by-asset') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('borrowing.reports.by-asset') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('borrowing.reports.by-asset') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>By Asset</span>
                            </a>
                        </div>
                    </div>
                </nav>
            </div>
        </aside>
        @endauth

        <!-- Main Content -->
        <main id="mainContent" class="w-full flex-1 transition-all duration-300 ease-in-out p-4 sm:p-5">
            @yield('content')
        </main>
    </div>

    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle && sidebar && sidebarOverlay) {
                const persistedOpen = localStorage.getItem('borrowingSidebarOpen') === 'true';
                if (persistedOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    mainContent.classList.add('content-shifted');
                    if (window.innerWidth < 768) {
                        sidebarOverlay.classList.remove('opacity-0');
                        sidebarOverlay.classList.remove('pointer-events-none');
                    }
                }

                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    if (window.innerWidth < 768) {
                        sidebarOverlay.classList.toggle('opacity-0');
                        sidebarOverlay.classList.toggle('pointer-events-none');
                    }
                    mainContent.classList.toggle('content-shifted');

                    const isOpen = !sidebar.classList.contains('-translate-x-full');
                    localStorage.setItem('borrowingSidebarOpen', isOpen ? 'true' : 'false');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                    mainContent.classList.remove('content-shifted');
                    localStorage.setItem('borrowingSidebarOpen', 'false');
                });

                window.addEventListener('resize', function() {
                    const isOpen = localStorage.getItem('borrowingSidebarOpen') === 'true';
                    if (isOpen) {
                        sidebar.classList.remove('-translate-x-full');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                    }

                    if (window.innerWidth >= 768) {
                        sidebarOverlay.classList.add('opacity-0');
                        sidebarOverlay.classList.add('pointer-events-none');
                        if (isOpen) {
                            mainContent.classList.add('content-shifted');
                        } else {
                            mainContent.classList.remove('content-shifted');
                        }
                    } else {
                        if (isOpen) {
                            sidebarOverlay.classList.remove('opacity-0');
                            sidebarOverlay.classList.remove('pointer-events-none');
                        }
                        mainContent.classList.remove('content-shifted');
                    }
                });
            }

            // Profile dropdown
            const profileDropdown = document.getElementById('profileDropdown');
            const profileMenu = document.getElementById('profileMenu');

            if (profileDropdown && profileMenu) {
                profileDropdown.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', (e) => {
                    if (!profileDropdown.contains(e.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
            }

            // Asset Management menu - auto-expand if on asset pages
            const assetMenu = document.getElementById('assetMenu');
            const assetMenuIcon = document.getElementById('assetMenuIcon');
            if (assetMenu && assetMenuIcon) {
                const isAssetPage = {{ request()->routeIs('borrowing.assets.*') ? 'true' : 'false' }};
                const persistedAssetMenuOpen = localStorage.getItem('borrowingAssetMenuOpen') === 'true';
                
                if (isAssetPage || persistedAssetMenuOpen) {
                    assetMenu.classList.remove('hidden');
                    assetMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            // Initialize notification count on page load
            if (typeof updateUnreadCount === 'function') {
                updateUnreadCount();
            }
        });

        function toggleAssetMenu() {
            const menu = document.getElementById('assetMenu');
            const icon = document.getElementById('assetMenuIcon');
            menu.classList.toggle('hidden');
            
            // Rotate the icon
            if (menu.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
            
            // Persist state
            const isOpen = !menu.classList.contains('hidden');
            localStorage.setItem('borrowingAssetMenuOpen', isOpen ? 'true' : 'false');
        }

        function toggleReportsMenu() {
            const menu = document.getElementById('reportsMenu');
            const icon = document.getElementById('reportsChevron');
            menu.classList.toggle('hidden');
            
            // Rotate the icon
            if (menu.classList.contains('hidden')) {
                icon.style.transform = 'rotate(0deg)';
            } else {
                icon.style.transform = 'rotate(180deg)';
            }
            
            // Persist state
            const isOpen = !menu.classList.contains('hidden');
            localStorage.setItem('borrowingReportsMenuOpen', isOpen ? 'true' : 'false');
        }

        // Restore reports menu state on load
        if (localStorage.getItem('borrowingReportsMenuOpen') === 'true') {
            document.getElementById('reportsMenu')?.classList.remove('hidden');
            const icon = document.getElementById('reportsChevron');
            if (icon) icon.style.transform = 'rotate(180deg)';
        }
    </script>
</body>
</html>
