@extends('layouts.app')

@section('content')
<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4 break-words">Delete Category</h3>
            <div class="mt-2 px-4 py-3">
                <p class="text-sm text-gray-500 break-words">Are you sure you want to delete this category?</p>
                <p class="text-sm text-gray-500 mt-2 break-words">Categories with assigned assets cannot be deleted.</p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="flex justify-center space-x-4">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="flex-1 p-4 md:p-8 bg-gray-50">
    <!-- Page Header with Background Design -->
    <div class="mb-6 md:mb-8">
        <div class="bg-red-800 rounded-xl shadow-lg p-4 md:p-6 text-white">
            <div class="flex items-center">
                <div class="bg-white/20 p-3 md:p-4 rounded-full backdrop-blur-sm mr-3 md:mr-4">
                    <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1 md:mb-2">Asset Categories Management</h1>
                    <p class="text-red-100 text-sm md:text-lg">Organize and manage asset categories</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Statistics -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Categories Overview
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4 md:gap-6">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 md:p-6 rounded-xl border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-blue-700 mb-1">Total Categories</p>
                        <p class="text-2xl md:text-3xl font-bold text-blue-900">{{ $categories->count() }}</p>
                    </div>
                    <div class="bg-blue-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 md:p-6 rounded-xl border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs md:text-sm font-medium text-green-700 mb-1">Active Categories</p>
                        <p class="text-2xl md:text-3xl font-bold text-green-900">{{ $categories->count() }}</p>
                        <p class="text-xs text-green-600 mt-1">All categories are active</p>
                    </div>
                    <div class="bg-green-200 p-2 md:p-3 rounded-full">
                        <svg class="w-6 h-6 md:w-8 md:h-8 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Section -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6 mb-6">
        <h2 class="text-lg md:text-xl font-semibold text-gray-900 mb-4 md:mb-6 flex items-center">
            <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Add New Category
        </h2>
        
        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4" id="createCategoryForm">
            @csrf
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="name" placeholder="Enter category name..." 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors duration-200 text-sm md:text-base">
                </div>
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200 text-sm md:text-base">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add Category
                </button>
            </div>
        </form>
    </div>

    <!-- Categories List Section -->
    <div class="bg-white rounded-xl shadow-md p-4 md:p-6">
        <div class="flex justify-between items-center mb-4 md:mb-6">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 mr-2 md:mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                All Categories
            </h2>
            <div class="flex items-center space-x-2">
                <span class="text-xs md:text-sm text-gray-500">Total: {{ $categories->count() }} categories</span>
            </div>
        </div>
        
        <!-- Mobile View Cards -->
        <div class="md:hidden space-y-3 md:space-y-4">
            @foreach($categories as $category)
            <div class="bg-gray-50 rounded-xl p-4 md:p-6 hover:bg-gray-100 transition-colors duration-200 border border-gray-200">
                <div class="flex justify-between items-start mb-3 md:mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-base md:text-lg mb-1 truncate">{{ $category->name }}</h3>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            ID: #{{ $loop->iteration }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="editCategory('{{ $category->id }}', '{{ $category->name }}')" 
                                class="p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-lg transition-colors duration-200" title="Edit Category">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button type="button" onclick="showDeleteModal('{{ $category->id }}', '{{ $category->name }}')" 
                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200" title="Delete Category">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($categories as $category)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">#{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="bg-red-100 p-2 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="flex items-center space-x-2">
                                <button onclick="editCategory('{{ $category->id }}', '{{ $category->name }}')" 
                                        class="text-yellow-600 hover:text-yellow-800 p-2 rounded hover:bg-yellow-50 transition-colors duration-200" title="Edit Category">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="showDeleteModal('{{ $category->id }}', '{{ $category->name }}')" 
                                        class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition-colors duration-200" title="Delete Category">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        @if($categories->count() === 0)
        <div class="text-center py-8 md:py-12 bg-gray-50 rounded-xl border border-dashed border-gray-300">
            <div class="bg-gray-100 rounded-full p-3 md:p-4 w-16 h-16 md:w-20 md:h-20 mx-auto mb-3 md:mb-4 flex items-center justify-center">
                <svg class="h-8 w-8 md:h-10 md:w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
            </div>
            <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">No categories found</h3>
            <p class="text-xs md:text-sm text-gray-500">Start by adding your first category above.</p>
        </div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Edit Category</h3>
            <form id="editForm" method="POST" class="mt-4">
                @csrf
                @method('PUT')
                <input type="text" name="name" id="editName" 
                       class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-800">
                <div class="mt-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700 transition-colors duration-200">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCategory(id, name) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editName').value = name;
    document.getElementById('editForm').action = `/categories/${id}`;
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function showDeleteModal(categoryId, categoryName) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    const title = modal.querySelector('h3');
    
    title.textContent = `Delete Category: ${categoryName}`;
    form.action = `/categories/${categoryId}`;
    modal.classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const deleteModal = document.getElementById('deleteModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
        closeEditModal();
    }
});
</script>
@endsection