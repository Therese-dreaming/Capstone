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
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="flex-1">
    <div class="p-4 md:p-6">
        <!-- Main Container -->
        <div class="bg-white rounded-lg shadow-lg p-4 md:p-6">
            <!-- Add Category Section -->
            <div class="mb-6 md:mb-8">
                <h2 class="text-xl md:text-2xl font-bold mb-4">ADD NEW CATEGORY</h2>
                <form action="{{ route('categories.store') }}" method="POST" class="space-y-4" id="createCategoryForm">
                    @csrf
                    <div>
                        <input type="text" name="name" placeholder="Category Name" 
                               class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-red-800">
                    </div>
                    <button type="submit" class="bg-red-800 text-white px-4 py-2 rounded-md hover:bg-red-700 w-full sm:w-auto">
                        Add
                    </button>
                </form>
            </div>

            <!-- Categories List Section -->
            <div>
                <h2 class="text-xl md:text-2xl font-bold mb-4">ALL CATEGORIES</h2>
                
                <!-- Mobile View Cards -->
                <div class="md:hidden space-y-4">
                    @foreach($categories as $category)
                    <div class="border rounded-lg p-4 bg-white shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-medium">{{ $category->name }}</h3>
                            <div class="flex space-x-2">
                                <button onclick="editCategory('{{ $category->id }}', '{{ $category->name }}')" 
                                        class="text-yellow-600 hover:text-yellow-900">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button type="button" onclick="showDeleteModal('{{ $category->id }}', '{{ $category->name }}')" class="text-red-600 hover:text-red-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <div class="flex justify-between py-1">
                                <span>ID:</span>
                                <span class="font-medium">#{{ $loop->iteration }}</span>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categories</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $category->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button onclick="editCategory('{{ $category->id }}', '{{ $category->name }}')" 
                                                class="text-yellow-600 hover:text-yellow-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" onclick="showDeleteModal('{{ $category->id }}', '{{ $category->name }}')" class="text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
            </div>
        </div>
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
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-800 text-white rounded-md hover:bg-red-700">
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