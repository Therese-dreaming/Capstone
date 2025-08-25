<!-- Notification Button and Dropdown -->
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
    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-128 max-w-[90vw] bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <div class="p-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-semibold text-gray-700">Notifications</h3>
            <div class="flex items-center space-x-2">
                <a href="{{ route('notifications.all') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                <button id="markAllRead" class="text-sm text-red-600 hover:text-red-800">Mark all as read</button>
            </div>
        </div>
        <div id="notificationList" class="max-h-96 overflow-y-auto">
            <!-- Notifications will be loaded here -->
        </div>
    </div>
</div>

<script>
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
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
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
        fetch(`{{ url('notifications') }}/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
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
                                    <div class="flex-1 min-w-0 flex-grow">
                                        <a href="/notifications/go/${notification.id}" 
                                           class="block group">
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
                                            class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50 flex-shrink-0">
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
            return new Intl.RelativeTimeFormat('en', { numeric: 'auto' })
                .format(-Math.round(diff / 3600000), 'hour');
        }

        // Less than 7 days
        if (diff < 604800000) {
            return new Intl.RelativeTimeFormat('en', { numeric: 'auto' })
                .format(-Math.round(diff / 86400000), 'day');
        }

        // Otherwise return formatted date
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    // Initial load of unread count
    updateUnreadCount();
});
</script> 