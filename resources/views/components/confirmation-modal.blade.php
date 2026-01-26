<div id="confirmationModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6">
        <h2 id="modalTitle" class="text-lg font-semibold mb-2"></h2>
        <p id="modalMessage" class="text-sm text-gray-600 mb-6"></p>

        <div class="flex justify-end space-x-3">
            <button onclick="closeConfirmationModal()"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                Cancel
            </button>

            <button onclick="confirmAction()"
                    class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                Confirm
            </button>
        </div>
    </div>
</div>
