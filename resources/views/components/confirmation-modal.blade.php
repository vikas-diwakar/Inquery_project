{{-- Global Confirmation + Alert Modal --}}
<div id="confirmationModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[999] transition-all p-4" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 scale-95 opacity-0 transition-all duration-200" id="confirmationModalInner">
        <div class="flex items-start space-x-4">
            {{-- Icon (swapped via JS) --}}
            <div id="modalIconWrap" class="h-11 w-11 rounded-2xl flex items-center justify-center shrink-0">
                <svg id="modalIconDanger" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <svg id="modalIconInfo" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg id="modalIconSuccess" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <svg id="modalIconWarning" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h2 id="modalTitle" class="text-base font-extrabold text-slate-900 leading-tight"></h2>
                <p id="modalMessage" class="text-sm text-slate-600 leading-relaxed mt-1"></p>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3 pt-1">
            <button id="cancelBtn" type="button"
                class="px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                Cancel
            </button>
            <button id="confirmBtn" type="button"
                class="px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm">
                Confirm
            </button>
        </div>
    </div>
</div>
