<!-- resources/views/components/follow-up-scheduler.blade.php -->
@props(['inquiry'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Schedule Follow-up</h3>
    </div>
    
    <div class="px-4 py-5 sm:px-6">
        @if($inquiry->next_follow_up_date)
            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    <span class="font-semibold">Next Follow-up:</span> 
                    {{ $inquiry->next_follow_up_date->format('M d, Y H:i A') }}
                </p>
                @if($inquiry->last_follow_up_date)
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">
                        <span class="font-semibold">Last Follow-up:</span> 
                        {{ $inquiry->last_follow_up_date->format('M d, Y H:i A') }}
                    </p>
                @endif
            </div>
        @endif

        <form action="{{ route('follow-ups.store', $inquiry) }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label for="follow_up_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Follow-up Date & Time *
                </label>
                <input 
                    type="datetime-local" 
                    id="follow_up_date" 
                    name="follow_up_date" 
                    required
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    value="{{ old('follow_up_date', now()->addDays(3)->format('Y-m-d\T10:00')) }}"
                >
                @error('follow_up_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                    Notes
                </label>
                <textarea 
                    id="notes" 
                    name="notes" 
                    rows="3"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Add any notes for this follow-up..."
                >{{ old('notes', $inquiry->follow_up_notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button 
                    type="submit"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    Schedule Follow-up
                </button>
            </div>
        </form>

        <!-- Quick schedule buttons -->
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quick Schedule:</p>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach(['Today' => 0, 'Tomorrow' => 1, '3 Days' => 3, '1 Week' => 7] as $label => $days)
                <form action="{{ route('follow-ups.store', $inquiry) }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="follow_up_date" value="{{ now()->addDays($days)->setHour(10)->setMinute(0)->format('Y-m-d H:i:s') }}">
                    <button 
                        type="submit"
                        class="w-full px-3 py-2 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600"
                    >
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
