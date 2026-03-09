<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IT-ARMS - Asset Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif !important;
        }
        
        @media (min-width: 768px) {
            .content-shifted {
                margin-left: 15rem !important;
            }
        }
        
        @media (max-width: 768px) {
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
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2.5">
                <img src="{{ asset('images/logo-small.png') }}" alt="Logo" class="h-8 w-8">
                <div class="flex flex-col">
                    <h1 class="text-sm font-semibold text-gray-800 hidden sm:block leading-tight">IT-ARMS</h1>
                    <span class="text-xs text-gray-500 hidden sm:block leading-tight">Asset Management</span>
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
        <aside id="sidebar" class="w-60 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-14 z-40 transform -translate-x-full transition-transform duration-300 ease-in-out shadow-sm">
            <div class="p-4 h-[calc(100vh-3.5rem)] overflow-y-auto">
                <nav class="space-y-6">
                    <!-- Main Section -->
                    <div class="space-y-1">
                        <!-- My Tasks -->
                        <a href="{{ route('my.tasks') }}" class="sidebar-link {{ request()->routeIs('my.tasks') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('my.tasks') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span>My Tasks</span>
                        </a>

                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Dashboard</span>
                        </a>

                        <!-- Notifications -->
                        <a href="{{ route('notifications.all') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('notifications.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <span>Notifications</span>
                        </a>
                    </div>

                    <!-- System Section -->
                    @if(auth()->check() && !in_array(auth()->user()->group_id, [2, 3, 4]))
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">System</h3>
                        </div>

                        <!-- User Management -->
                        <div class="space-y-1">
                            <button onclick="toggleUserMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('users.*') || request()->routeIs('groups.*') ? 'bg-red-50 text-red-600' : '' }}">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span>User Management</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform" id="userMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="userMenu" class="hidden ml-7 space-y-1 mt-1">
                                <a href="{{ route('users.index') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('users.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('users.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                    <span>Users</span>
                                </a>
                                <a href="{{ route('groups.index') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('groups.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('groups.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                    <span>Groups</span>
                                </a>
                            </div>
                        </div>

                        <!-- Categories -->
                        <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('categories.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <span>Categories</span>
                        </a>

                        <!-- Locations -->
                        <a href="{{ route('locations.index') }}" class="sidebar-link {{ request()->routeIs('locations.*') || request()->routeIs('buildings.*') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('locations.*') || request()->routeIs('buildings.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Locations</span>
                        </a>

                        <!-- Vendors -->
                        <a href="{{ route('vendors.index') }}" class="sidebar-link {{ request()->routeIs('vendors.*') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('vendors.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Vendors</span>
                        </a>
                    </div>

                    <!-- Assets Section -->
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assets</h3>
                        </div>

                        <!-- Asset Management -->
                        <div class="space-y-1">
                            <button onclick="toggleAssetMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('assets.*') || request()->routeIs('scanner') || request()->routeIs('qr.*') ? 'bg-red-50 text-red-600' : '' }}">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span>Asset Management</span>
                                    @if(Auth::user()->getTotalWarrantyIssues() > 0)
                                        <span class="ml-1 bg-gradient-to-br from-red-500 to-red-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center shadow-sm">
                                            {{ Auth::user()->getTotalWarrantyIssues() }}
                                        </span>
                                    @endif
                                </div>
                                <svg class="w-4 h-4 transition-transform" id="assetMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="assetMenu" class="hidden ml-7 space-y-1 mt-1">
                                <a href="{{ route('assets.index') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('assets.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('assets.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                    <span>Asset List</span>
                                    @if(Auth::user()->getTotalWarrantyIssues() > 0)
                                        <span class="ml-auto bg-gradient-to-br from-red-500 to-red-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center shadow-sm">
                                            {{ Auth::user()->getTotalWarrantyIssues() }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('scanner') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('scanner') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('scanner') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                    <span>Asset Scanner</span>
                                </a>
                                <a href="{{ route('qr.list') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('qr.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                    <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('qr.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                    <span>QR Code List</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Maintenance Section -->
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Maintenance</h3>
                        </div>

                        <!-- Lab Maintenance -->
                    <div class="space-y-1">
                        <button onclick="toggleMaintenanceMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('maintenance.*') ? 'bg-red-50 text-red-600' : '' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Lab Maintenance</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" id="maintenanceMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="maintenanceMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('maintenance.schedule') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('maintenance.schedule') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('maintenance.schedule') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Schedule Maintenance</span>
                            </a>
                            <a href="{{ route('maintenance.upcoming') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('maintenance.upcoming') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('maintenance.upcoming') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Upcoming Maintenance</span>
                            </a>
                            <a href="{{ route('maintenance.pending-approval') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('maintenance.pending-approval') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('maintenance.pending-approval') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Pending Approval</span>
                            </a>
                            <a href="{{ route('maintenance.history') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('maintenance.history') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('maintenance.history') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Maintenance History</span>
                            </a>
                        </div>
                    </div>

                    <!-- Services Section -->
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Services</h3>
                        </div>

                        <!-- Asset Repair -->
                        <div class="space-y-1">
                            <button onclick="toggleRepairMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('repair.*') || request()->routeIs('non-registered-assets.*') ? 'bg-red-50 text-red-600' : '' }}">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                    </svg>
                                    <span>Asset Repair</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform" id="repairMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="repairMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('repair.request') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('repair.request') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('repair.request') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Repair Request</span>
                            </a>
                            <a href="{{ route('repair.status') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('repair.status') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('repair.status') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Repair Status</span>
                            </a>
                            <a href="{{ route('non-registered-assets.index') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('non-registered-assets.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('non-registered-assets.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Non-Registered Assets</span>
                            </a>
                            <a href="{{ route('repair.completed') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('repair.completed') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('repair.completed') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Repair History</span>
                            </a>
                        </div>
                    </div>

                    <!-- Lab Schedule -->
                    <div class="space-y-1">
                        <button onclick="toggleLabScheduleMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('lab.*') || request()->routeIs('laboratories.*') ? 'bg-red-50 text-red-600' : '' }}">
                            <div class="flex items-center space-x-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Lab Schedule</span>
                            </div>
                            <svg class="w-4 h-4 transition-transform" id="labScheduleMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="labScheduleMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('lab.logging') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('lab.logging') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('lab.logging') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Lab Logging</span>
                            </a>
                            <a href="{{ route('lab-schedule.history') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('lab-schedule.history') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('lab-schedule.history') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Lab History</span>
                            </a>
                            <a href="{{ route('lab.manualLogout') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('lab.manualLogout') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('lab.manualLogout') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Manual Logout</span>
                            </a>
                            <a href="{{ route('laboratories.index') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('laboratories.*') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('laboratories.*') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Laboratories</span>
                            </a>
                        </div>
                    </div>
                    </div>
                    @endif

                    <!-- Analytics Section -->
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Analytics</h3>
                        </div>

                        <!-- Reports -->
                        <div class="space-y-1">
                            <button onclick="toggleReportMenu()" class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all {{ request()->routeIs('reports.*') ? 'bg-red-50 text-red-600' : '' }}">
                                <div class="flex items-center space-x-2.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span>Reports</span>
                                </div>
                                <svg class="w-4 h-4 transition-transform" id="reportMenuIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="reportMenu" class="hidden ml-7 space-y-1 mt-1">
                            <a href="{{ route('reports.category') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.category') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.category') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Category Report</span>
                            </a>
                            <a href="{{ route('reports.location') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.location') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.location') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Location Report</span>
                            </a>
                            <a href="{{ route('reports.vendor-analysis') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.vendor-analysis') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.vendor-analysis') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Vendor Analysis</span>
                            </a>
                            <a href="{{ route('reports.disposal-history') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.disposal-history') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.disposal-history') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Disposal History</span>
                            </a>
                            <a href="{{ route('reports.procurement-history') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.procurement-history') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.procurement-history') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Procurement History</span>
                            </a>
                            <a href="{{ route('reports.lab-usage') }}" class="flex items-center space-x-2 py-2 px-3 rounded-md text-sm transition-all {{ request()->routeIs('reports.lab-usage') ? 'text-red-600 bg-red-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-700' }}">
                                <div class="w-1.5 h-1.5 rounded-full {{ request()->routeIs('reports.lab-usage') ? 'bg-red-600' : 'bg-gray-400' }}"></div>
                                <span>Lab Usage</span>
                            </a>
                        </div>
                    </div>

                    <!-- Borrowing Section -->
                    <div class="space-y-1">
                        <div class="px-3 mt-6 mb-3">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Borrowing</h3>
                        </div>

                        <!-- Asset Borrowing -->
                        <a href="{{ route('borrowing.dashboard') }}" class="sidebar-link {{ request()->routeIs('borrowing.*') ? 'active' : '' }} flex items-center space-x-2.5 px-3 py-2.5 rounded-lg text-sm font-medium transition-all {{ request()->routeIs('borrowing.*') ? 'bg-red-50 text-red-600 shadow-sm ring-1 ring-red-100' : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>Asset Borrowing</span>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>
        @endif

        <!-- Main Content -->
        <main id="mainContent" class="w-full flex-1 transition-all duration-300 ease-in-out p-4 sm:p-5">
            @yield('content')
        </main>
    </div>

    @yield('scripts')

    <script>
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
                    localStorage.setItem('sidebarOpen', isOpen ? 'true' : 'false');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('opacity-0');
                    sidebarOverlay.classList.add('pointer-events-none');
                    mainContent.classList.remove('content-shifted');
                    localStorage.setItem('sidebarOpen', 'false');
                });

                // Handle window resize
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
                        }
                        mainContent.classList.remove('content-shifted');
                    }
                });
            }

            // Profile dropdown functionality
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

            // Menu toggle functions
            function toggleUserMenu() {
                const menu = document.getElementById('userMenu');
                const icon = document.getElementById('userMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            function toggleAssetMenu() {
                const menu = document.getElementById('assetMenu');
                const icon = document.getElementById('assetMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            function toggleMaintenanceMenu() {
                const menu = document.getElementById('maintenanceMenu');
                const icon = document.getElementById('maintenanceMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            function toggleRepairMenu() {
                const menu = document.getElementById('repairMenu');
                const icon = document.getElementById('repairMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            function toggleLabScheduleMenu() {
                const menu = document.getElementById('labScheduleMenu');
                const icon = document.getElementById('labScheduleMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            function toggleReportMenu() {
                const menu = document.getElementById('reportMenu');
                const icon = document.getElementById('reportMenuIcon');
                menu.classList.toggle('hidden');
                if (menu.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            // Auto-open dropdowns based on current path
            const currentPath = window.location.pathname;
            
            if (currentPath.includes('/users') || currentPath.includes('/groups')) {
                const userMenu = document.getElementById('userMenu');
                const userMenuIcon = document.getElementById('userMenuIcon');
                if (userMenu && userMenuIcon) {
                    userMenu.classList.remove('hidden');
                    userMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            if (currentPath.includes('/assets') || currentPath.includes('/scanner') || currentPath.includes('/qr')) {
                const assetMenu = document.getElementById('assetMenu');
                const assetMenuIcon = document.getElementById('assetMenuIcon');
                if (assetMenu && assetMenuIcon) {
                    assetMenu.classList.remove('hidden');
                    assetMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            if (currentPath.includes('/maintenance')) {
                const maintenanceMenu = document.getElementById('maintenanceMenu');
                const maintenanceMenuIcon = document.getElementById('maintenanceMenuIcon');
                if (maintenanceMenu && maintenanceMenuIcon) {
                    maintenanceMenu.classList.remove('hidden');
                    maintenanceMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            if (currentPath.includes('/repair') || currentPath.includes('/non-registered-assets')) {
                const repairMenu = document.getElementById('repairMenu');
                const repairMenuIcon = document.getElementById('repairMenuIcon');
                if (repairMenu && repairMenuIcon) {
                    repairMenu.classList.remove('hidden');
                    repairMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            if (currentPath.includes('/lab') || currentPath.includes('/laboratories')) {
                const labScheduleMenu = document.getElementById('labScheduleMenu');
                const labScheduleMenuIcon = document.getElementById('labScheduleMenuIcon');
                if (labScheduleMenu && labScheduleMenuIcon) {
                    labScheduleMenu.classList.remove('hidden');
                    labScheduleMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            if (currentPath.includes('/reports')) {
                const reportMenu = document.getElementById('reportMenu');
                const reportMenuIcon = document.getElementById('reportMenuIcon');
                if (reportMenu && reportMenuIcon) {
                    reportMenu.classList.remove('hidden');
                    reportMenuIcon.style.transform = 'rotate(180deg)';
                }
            }

            // Make toggle functions globally available
            window.toggleUserMenu = toggleUserMenu;
            window.toggleAssetMenu = toggleAssetMenu;
            window.toggleMaintenanceMenu = toggleMaintenanceMenu;
            window.toggleRepairMenu = toggleRepairMenu;
            window.toggleLabScheduleMenu = toggleLabScheduleMenu;
            window.toggleReportMenu = toggleReportMenu;
        });
    </script>
</body>
</html>
