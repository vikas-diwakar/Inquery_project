<div id="confirmationModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-50 transition-all p-4">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 animate-scale-up">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h2 id="modalTitle" class="text-lg font-extrabold text-slate-900">Confirm Action</h2>
        </div>
        <p id="modalMessage" class="text-sm text-slate-600 leading-relaxed"></p>

        <div class="flex items-center justify-end space-x-3 pt-2">
            <button id="cancelBtn" type="button" class="btn-secondary">
                Cancel
            </button>
            <button id="confirmBtn" type="button" class="btn-danger">
                Confirm
            </button>
        </div>
    </div>
</div>
