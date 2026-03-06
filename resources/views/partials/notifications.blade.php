<!-- Notification Button and Dropdown -->
<div class="relative">
    <button id="notificationButton" class="relative p-2 rounded-lg hover:bg-gray-100 transition-all duration-200 group">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 group-hover:text-gray-900 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span id="notificationCount" class="absolute -top-0.5 -right-0.5 bg-gradient-to-br from-red-500 to-red-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center shadow-lg shadow-red-500/30 {{ $unreadCount ?? 0 ? '' : 'hidden' }}">
                {{ $unreadCount ?? 0 }}
            </span>
        </div>
    </button>

    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-96 max-w-[90vw] bg-white rounded-xl shadow-xl border border-gray-200 overflow-hidden z-50 animate-slideDown">
        <div class="p-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="font-semibold text-gray-800">Notifications</h3>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('notifications.all') }}" class="text-xs text-gray-600 hover:text-red-600 font-medium transition-colors">View All</a>
                <button id="markAllRead" class="text-xs text-red-600 hover:text-red-700 font-medium transition-colors">Mark all read</button>
            </div>
        </div>
        <div id="notificationList" class="max-h-96 overflow-y-auto">
            <!-- Notifications will be loaded here -->
        </div>
    </div>
</div>

<style>
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-slideDown {
        animation: slideDown 0.2s ease-out;
    }
    
    #notificationCount {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.85;
        }
    }
</style>

<script>
(function() {
    'use strict';
    
    // Prevent multiple initializations
    if (window.notificationsInitialized) {
        return;
    }
    window.notificationsInitialized = true;

    // Wait for DOM to be ready
    function initNotifications() {
        const notificationButton = document.getElementById('notificationButton');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const notificationList = document.getElementById('notificationList');
        const markAllReadButton = document.getElementById('markAllRead');
        const notificationCount = document.getElementById('notificationCount');

        if (!notificationButton || !notificationDropdown || !notificationList) {
            return;
        }

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
        if (markAllReadButton) {
            markAllReadButton.addEventListener('click', function() {
                fetch('{{ route("notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        updateNotificationCount(0);
                        loadNotifications();
                    }
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
            });
        }

        // Make markAsRead function globally accessible
        window.markAsRead = function(id) {
            fetch('{{ url("notifications") }}/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    loadNotifications();
                    updateUnreadCount();
                }
            })
            .catch(error => {
                console.error('Error marking as read:', error);
            });
        };

        function loadNotifications() {
            fetch('{{ route("notifications.index") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Expected JSON response but got ' + contentType);
                    });
                }
                
                return response.json();
            })
            .then(data => {
                notificationList.innerHTML = data.length ?
                    data.map(notification => `
                        <div class="p-4 border-b border-gray-100 transition-all duration-200 hover:bg-gray-50 ${notification.is_read ? 'bg-white' : 'bg-red-50/50'}" 
                             data-id="${notification.id}">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex items-start gap-3 flex-1 min-w-0">
                                    <div class="flex-shrink-0 mt-0.5 ${notification.is_read ? '' : 'animate-pulse'}">
                                        ${notification.type === 'repair' ? `
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center ${notification.is_read ? 'bg-gray-100' : 'bg-red-100'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ${notification.is_read ? 'text-gray-500' : 'text-red-600'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4" />
                                                </svg>
                                            </div>
                                        ` : notification.type === 'maintenance' ? `
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center ${notification.is_read ? 'bg-gray-100' : 'bg-red-100'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ${notification.is_read ? 'text-gray-500' : 'text-red-600'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                        ` : `
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center ${notification.is_read ? 'bg-gray-100' : 'bg-red-100'}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ${notification.is_read ? 'text-gray-500' : 'text-red-600'}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            </div>
                                        `}
                                    </div>
                                    <div class="flex-1 min-w-0 flex-grow">
                                        <a href="/notifications/go/${notification.id}" 
                                           class="block group">
                                            <p class="text-sm ${notification.is_read ? 'text-gray-700' : 'text-gray-900 font-medium'} mb-1.5 line-clamp-2 group-hover:text-red-600 transition-colors duration-200">
                                                ${notification.message}
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-xs text-gray-500">${formatDate(notification.created_at)}</p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                ${!notification.is_read ? `
                                    <button onclick="markAsRead(${notification.id})" 
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 border border-red-200 rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>Read</span>
                                    </button>
                                ` : ''}
                            </div>
                        </div>
                    `).join('') :
                    '<div class="p-12 text-center text-gray-500 flex flex-col items-center justify-center"><div class="w-16 h-16 mb-3 rounded-full bg-gray-100 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg></div><p class="text-sm font-medium">No notifications yet</p><p class="text-xs text-gray-400 mt-1">You\'re all caught up!</p></div>';
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationList.innerHTML = '<div class="p-8 text-center"><p class="text-red-500 text-sm font-medium">Error loading notifications</p><p class="text-gray-500 text-xs mt-1">' + error.message + '</p></div>';
            });
        }

        function updateUnreadCount() {
            fetch('{{ route("notifications.unreadCount") }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Expected JSON response but got ' + contentType);
                }
                return response.json();
            })
            .then(data => {
                updateNotificationCount(data.count);
            })
            .catch(error => {
                console.error('Error updating unread count:', error);
            });
        }

        function updateNotificationCount(count) {
            if (notificationCount) {
                notificationCount.textContent = count;
                if (count === 0) {
                    notificationCount.classList.add('hidden');
                } else {
                    notificationCount.classList.remove('hidden');
                }
            }
        }

        function formatDate(dateString) {
            try {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;

                // Less than 24 hours
                if (diff < 86400000) {
                    const hours = Math.round(diff / 3600000);
                    if (hours === 0) return 'Just now';
                    return hours === 1 ? '1 hour ago' : `${hours} hours ago`;
                }

                // Less than 7 days
                if (diff < 604800000) {
                    const days = Math.round(diff / 86400000);
                    return days === 1 ? '1 day ago' : `${days} days ago`;
                }

                // Otherwise return formatted date
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (error) {
                console.error('Error formatting date:', error);
                return dateString;
            }
        }

        // Make updateUnreadCount globally accessible
        window.updateUnreadCount = updateUnreadCount;

        // Initial load of unread count
        updateUnreadCount();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNotifications);
    } else {
        initNotifications();
    }
})();
</script> 