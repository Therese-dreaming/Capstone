<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-xl w-96">
        <h2 class="text-xl font-bold mb-4">Mark as Complete</h2>
        <form id="completeForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <p class="text-gray-700 mb-4">Are you sure you want to mark this request as complete?</p>
            </div>

            <input type="hidden" name="status" value="completed">
            <input type="hidden" name="completed_at" value="{{ now() }}">
            <input type="hidden" name="technician_id" value="{{ auth()->user()->id }}">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="complete_remarks">
                    Completion Remarks
                </label>
                <textarea id="complete_remarks" name="remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter completion details..." required></textarea>
            </div>

            <div class="flex justify-end">
                <button type="button" onclick="closeCompleteModal()" class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Cancel</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Complete</button>
            </div>
        </form>
    </div>
</div>