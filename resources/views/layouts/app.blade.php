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

        .menu-open {
            display: block !important;
        }

        /* Desktop content shift when sidebar is open */
        @media (min-width: 768px) {
            .content-shifted {
                margin-left: 18rem !important; /* matches w-72 */
            }
        }

        /* Mobile sidebar */
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

    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-red-800 text-white py-4 px-6 flex items-center justify-between fixed top-0 left-0 right-0 z-50 shadow-lg">
        <!-- Left side - Logo and Title -->
        <div class="flex items-center space-x-4">
            @auth
            @if(auth()->user()->group_id !== 4)
            <button id="sidebarToggle" class="text-white focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            @endif
            @endauth
            <a href="{{ auth()->check() ? (auth()->user()->group_id === 4 ? route('custodian.assets.index') : route('my.tasks')) : route('login') }}" class="flex items-center space-x-4">
                <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="h-10 w-10">
                <h1 class="text-xl font-bold uppercase hidden sm:block">IT Asset and Repair Management System</h1>
                <h1 class="text-xl font-bold uppercase sm:hidden">IT-ARMS</h1>
            </a>
        </div>

        @auth
        <!-- Right side - User Profile and Notifications -->
        <div class="flex items-center space-x-3 sm:space-x-6">
            <!-- User Profile Dropdown -->
            <div class="relative">
                <button id="profileDropdown" class="flex items-center space-x-1 sm:space-x-3 hover:text-gray-200">
                    @if(Auth::user()->getProfilePictureUrl())
                    <img src="{{ Auth::user()->getProfilePictureUrl() }}" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    @else
                    <img src="{{ asset('images/default-profile.png') }}" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    @endif
                    <span class="hidden sm:inline">{{ Auth::user()->name ?? 'User Name' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div id="profileMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 text-gray-700 hidden z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-gray-100">Edit Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 hover:bg-gray-100">
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
    @if(auth()->check() && auth()->user()->group_id !== 4)
    <div id="sidebarOverlay" class="fixed inset-0 bg-black opacity-0 pointer-events-none z-40 transition-opacity duration-300 ease-in-out md:hidden"></div>
    @endif

    <div class="flex pt-16">
        <!-- Sidebar -->
        @if(auth()->check() && auth()->user()->group_id !== 4)
        <aside id="sidebar" class="w-72 bg-red-800 text-white min-h-screen fixed left-0 top-16 z-40 transform -translate-x-full transition-transform duration-300 ease-in-out overflow-y-auto">
            <div class="p-4 pl-8">
                <nav class="space-y-3 overflow-y-auto max-h-[calc(100vh-120px)]">
                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3, 4]))
                    <a href="{{ route('my.tasks') }}" class="font-bold flex items-center px-6 py-2.5 rounded-lg transition-colors duration-200 {{ request()->routeIs('my.tasks') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-white-500 hover:bg-red-50 hover:text-red-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        My Tasks
                    </a>
                    @endif

                    @if(auth()->check() && auth()->user()->group_id === 3)
                    <a href="{{ route('repair.request') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('repair.request') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Repair Request</span>
                    </a>
                    <a href="{{ route('repair.calls') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('repair.calls') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Repair Calls</span>
                    </a>
                    <a href="{{ route('lab.logging') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('lab.logging') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Lab Logging</span>
                    </a>
                    @endif

                    <!-- Admin and Staff Menu Items -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3, 4]))
                    <!-- Dashboard - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3]))
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('dashboard') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    @endif

                    <!-- Secretary Dashboard - Show only to secretary -->
                    @if(auth()->check() && auth()->user()->group_id === 2)
                    <a href="{{ url('/secretary-dashboard') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('secretary-dashboard') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Secretary Dashboard</span>
                    </a>
                    @endif

                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3, 4]))
                    <!-- Notifications - Accessible to all authenticated users -->
                    <a href="{{ route('notifications.all') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('notifications.all') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span>Notifications</span>
                    </a>

                    <!-- My Performance Report - Show only to secretary -->
                    @if(false)
                    <a href="{{ route('secretary-performance') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('secretary-performance') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>My Performance</span>
                    </a>
                    @endif
                    @endif

                    <!-- User Management - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
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

                    <!-- Categories - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <a href="{{ route('categories.index') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('categories.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Categories</span>
                    </a>
                    @endif

                    <!-- Locations - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <a href="{{ route('locations.index') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('locations.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Locations</span>
                    </a>
                    @endif


                    <!-- Vendors - Hide from secretary -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <a href="{{ route('vendors.index') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('vendors.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>Vendors</span>
                    </a>
                    @endif

                    <!-- Assets -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3]))
                    <!-- Asset List -->
                    @if(auth()->user()->group_id === 4)
                        <a href="{{ route('custodian.assets.index') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('custodian.assets.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path>
                            </svg>
                            <span>Asset List</span>
                            <div class="ml-auto flex space-x-1">
                                @if(auth()->user()->getNavigationCount('warranty_expired') > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center" title="Expired Warranties">
                                        {{ auth()->user()->getNavigationCount('warranty_expired') }}
                                    </span>
                                @endif
                                @if(auth()->user()->getNavigationCount('warranty_expiring') > 0)
                                    <span class="bg-orange-600 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center" title="Expiring Warranties (30 days)">
                                        {{ auth()->user()->getNavigationCount('warranty_expiring') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @else
                        <a href="{{ route('assets.index') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('assets.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"></path>
                            </svg>
                            <span>Asset List</span>
                            <div class="ml-auto flex space-x-1">
                                @if(auth()->user()->getNavigationCount('warranty_expired') > 0)
                                    <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center" title="Expired Warranties">
                                        {{ auth()->user()->getNavigationCount('warranty_expired') }}
                                    </span>
                                @endif
                                @if(auth()->user()->getNavigationCount('warranty_expiring') > 0)
                                    <span class="bg-orange-600 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center" title="Expiring Warranties (30 days)">
                                        {{ auth()->user()->getNavigationCount('warranty_expiring') }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @endif

                    <!-- Asset Scanner -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [4]))
                    <a href="{{ route('scanner') }}" class="flex items-center space-x-2 px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('scanner') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                        <span>Asset Scanner</span>
                    </a>
                    @endif
                    @endif

                    <!-- Lab Maintenance -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3, 4]))
                    <div class="space-y-1.5">
                        <button onclick="toggleMaintenanceMenu()" class="w-full flex items-center px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('maintenance.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Lab Maintenance</span>
                            @if(auth()->user()->getNavigationCount('maintenance_scheduled') > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                                    {{ auth()->user()->getNavigationCount('maintenance_scheduled') }}
                                </span>
                            @else
                                <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif
                        </button>
                        <div id="maintenanceMenu" class="hidden ml-8 space-y-1.5">
                            @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('maintenance.schedule') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('maintenance.schedule') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Schedule Maintenance
                            </a>
                            @endif
                            <a href="{{ route('maintenance.upcoming') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('maintenance.upcoming') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Upcoming Maintenance
                            </a>
                            <a href="{{ route('maintenance.history') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('maintenance.history') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Maintenance History
                            </a>
                        </div>
                    </div>
                    @endif
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [3, 4]))
                    <!-- Asset Repair -->
                    <div class="space-y-1.5">
                        <button onclick="toggleRepairMenu()" class="w-full flex items-center px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('repair.*') || request()->routeIs('non-registered-assets.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                            <span>Asset Repair</span>
                            @if(auth()->user()->getNavigationCount('repair_pending') > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                                    {{ auth()->user()->getNavigationCount('repair_pending') }}
                                </span>
                            @else
                                <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            @endif
                        </button>
                        <div id="repairMenu" class="hidden ml-8 space-y-1.5">
                            @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('repair.request') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('repair.request') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Repair Request
                            </a>
                            @endif
                            <a href="{{ route('repair.status') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('repair.status') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Repair Status
                            </a>
                            <a href="{{ route('non-registered-assets.index') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('non-registered-assets.index') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                <div class="flex items-center justify-between w-full">
                                    <span>Non-Registered Assets</span>
                                    @if(auth()->user()->getNavigationCount('non_registered_pulled_out') > 0)
                                        <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center" title="Pulled Out Assets Not Yet Registered">
                                            {{ auth()->user()->getNavigationCount('non_registered_pulled_out') }}
                                        </span>
                                    @endif
                                </div>
                            </a>
                            @if(auth()->check() && auth()->user()->group_id !== 2)
                            <a href="{{ route('repair.completed') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('repair.completed') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Repair History
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <!-- Lab Schedule -->
                    <div class="space-y-1.5">
                        <button onclick="toggleLabScheduleMenu()" class="w-full flex items-center px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('lab-schedule.*') || request()->routeIs('lab-history') || request()->routeIs('lab-logging') || request()->routeIs('lab.manualLogout') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Lab Schedule</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="labScheduleMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('lab.logging') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('lab.logging') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Lab Logging
                            </a>
                            @if(auth()->check() && auth()->user()->group_id !== 3)
                            <a href="{{ route('lab-schedule.history') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('lab-schedule.history') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Lab History
                            </a>
                            @endif
                            <a href="{{ route('lab.manualLogout') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('lab.manualLogout') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Manual Logout
                            </a>
                            @if(auth()->check() && !in_array(auth()->user()->group_id, [2,3,4]))
                            <a href="{{ route('laboratories.index') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('laboratories.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Laboratories
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                    @endauth

                    @auth
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <!-- View Reports -->
                    <div class="space-y-1.5">
                        <button onclick="toggleReportMenu()" class="w-full flex items-center px-4 py-1.5 rounded-md text-sm {{ request()->routeIs('reports.*') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#D5999B] hover:bg-red-700' }}">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>View Reports</span>
                            <svg class="w-3.5 h-3.5 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reportMenu" class="hidden ml-8 space-y-1.5">
                            <a href="{{ route('reports.category') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.category') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Category-Based Report
                            </a>
                            <a href="{{ route('reports.location') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.location') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Location-Based Report
                            </a>
                            <a href="{{ route('reports.vendor-analysis') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.vendor-analysis') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Vendor Analysis
                            </a>
                            <a href="{{ route('reports.disposal-history') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.disposal-history') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Disposal History
                            </a>
                            <a href="{{ route('reports.procurement-history') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.procurement-history') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Procurement History
                            </a>
                            <a href="{{ route('reports.lab-usage') }}" class="block py-1.5 px-4 rounded-md text-sm {{ request()->routeIs('reports.lab-usage') ? 'bg-red-600 text-white hover:bg-red-500' : 'text-[#676161] bg-[#E6E8EC] hover:bg-[#d0d2d6] active:bg-[#bbbdc1]' }}">
                                Lab Usage
                            </a>

                        </div>
                    </div>
                    @endif
                    @endauth
                </nav>
            </div>
        </aside>
        @endif

        <!-- Main Content -->
        <main id="mainContent" class="w-full flex-1 transition-all duration-300 ease-in-out">
            @yield('content')
        </main>
    </div>

    @yield('scripts')

    <script>
        // Combined sidebar and navigation functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar functionality
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mainContent = document.getElementById('mainContent');

            if (sidebarToggle && sidebar && sidebarOverlay) {
                // Initialize from persisted state
                const persistedOpen = localStorage.getItem('sidebarOpen') === 'true';
                if (persistedOpen) {
                    sidebar.classList.remove('-translate-x-full');
                    mainContent.classList.add('content-shifted');
                    if (window.innerWidth < 768) {
                        sidebarOverlay.classList.remove('opacity-0');
                        sidebarOverlay.classList.remove('pointer-events-none');
                    } else {
                        sidebarOverlay.classList.add('opacity-0');
                        sidebarOverlay.classList.add('pointer-events-none');
                    }
                } else {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.remove('content-shifted');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                }

                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    if (window.innerWidth < 768) {
                        sidebarOverlay.classList.toggle('opacity-0');
                        sidebarOverlay.classList.toggle('pointer-events-none');
                    }
                    mainContent.classList.toggle('content-shifted');

                    const isOpen = !sidebar.classList.contains('-translate-x-full');
                    localStorage.setItem('sidebarOpen', isOpen ? 'true' : 'false');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                    mainContent.classList.remove('content-shifted');

                    localStorage.setItem('sidebarOpen', 'false');
                });

                // Handle window resize (preserve sidebar state)
                window.addEventListener('resize', function() {
                    const isOpen = localStorage.getItem('sidebarOpen') === 'true';
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
                        } else {
                            sidebarOverlay.classList.add('opacity-0');
                            sidebarOverlay.classList.add('pointer-events-none');
                        }
                        // On mobile, content should not be shifted visually
                        mainContent.classList.remove('content-shifted');
                    }
                });
            }

            // Navigation functionality
            // Update selector to include all navigation items
            const navItems = document.querySelectorAll('a[href], button[onclick]');
            const currentPath = window.location.pathname;

            function setActiveState(element) {
                // Remove active state from all items
                navItems.forEach(item => {
                    if (!item.closest('#profileMenu') && !item.closest('.filter-buttons')) { // Exclude profile menu items and filter buttons
                        item.classList.remove('nav-active');
                        item.classList.remove('bg-red-700');
                    }
                });

                if (element && !element.closest('#profileMenu') && !element.closest('.filter-buttons')) {
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

                    // Special handling for repair menu items
                    if (element.closest('#repairMenu')) {
                        const repairMenu = document.getElementById('repairMenu');
                        const repairButton = document.querySelector('[onclick="toggleRepairMenu()"]');
                        if (repairMenu && repairButton) {
                            repairMenu.classList.remove('hidden');
                            repairButton.classList.add('nav-active');
                            repairButton.classList.add('bg-red-700');
                            localStorage.setItem('repairMenu', 'open');
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
                    'userMenu': ['/users', '/groups'],
                    'maintenanceMenu': ['/maintenance'],
                    'repairMenu': ['/repair', '/non-registered-assets'],
                    'reportMenu': ['/reports']
                };

                for (const [menuId, paths] of Object.entries(dropdowns)) {
                    if (paths.some(path => currentPath.includes(path))) {
                        const menu = document.getElementById(menuId);
                        const button = menu.previousElementSibling;
                        if (menu && button) {
                            menu.classList.remove('hidden');
                            button.classList.add('nav-active');
                            button.classList.add('bg-red-700');
                            localStorage.setItem(menuId, 'open');
                        }
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
                        // Handle only our toggleXMenu buttons; ignore others
                        if (this.tagName === 'BUTTON') {
                            if (this.id !== 'profileDropdown') {
                                const onclickAttr = this.getAttribute('onclick') || '';
                                const match = onclickAttr.match(/toggle(\w+)Menu/);
                                if (match && match[1]) {
                                    e.preventDefault();
                                    const menuId = match[1].toLowerCase() + 'Menu';
                                    const menu = document.getElementById(menuId);
                                    if (menu) {
                                        const isHidden = menu.classList.contains('hidden');
                                        menu.classList.toggle('hidden');
                                        this.classList.toggle('nav-active');
                                        this.classList.toggle('bg-red-700');
                                        localStorage.setItem(menuId, isHidden ? 'open' : 'closed');
                                    }
                                }
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

            // Lab Schedule menu functionality
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

        // Profile dropdown functionality (guard for unauthenticated pages)
        const profileDropdown = document.getElementById('profileDropdown');
        const profileMenu = document.getElementById('profileMenu');

        if (profileDropdown && profileMenu) {
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
        }

        function toggleLabScheduleMenu() {
            const menu = document.getElementById('labScheduleMenu');
            const button = document.querySelector('[onclick="toggleLabScheduleMenu()"]');
            if (menu && button) {
                menu.classList.toggle('hidden');
                button.classList.toggle('nav-active');
            }
        }


    </script>
</body>
</html>