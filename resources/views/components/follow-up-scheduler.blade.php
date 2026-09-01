@props(['inquiry'])

<div class="card overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Schedule Follow-up</h3>
    </div>
    <div class="px-4 py-5 sm:px-6">
        @if($inquiry->next_follow_up_date)
            <div class="mb-4 p-4 bg-primary-50 rounded-xl">
                <p class="text-sm text-slate-700"><span class="font-semibold">Next Follow-up:</span> {{ $inquiry->next_follow_up_date->format('M d, Y H:i A') }}</p>
                @if($inquiry->last_follow_up_date)
                    <p class="text-sm text-slate-700 mt-2"><span class="font-semibold">Last Follow-up:</span> {{ $inquiry->last_follow_up_date->format('M d, Y H:i A') }}</p>
                @endif
            </div>
        @endif

        <form action="{{ route('follow-ups.store', $inquiry) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="follow_up_date" class="block text-sm font-medium text-slate-700 mb-1">Follow-up Date & Time *</label>
                <input type="datetime-local" id="follow_up_date" name="follow_up_date" required min="{{ now()->format('Y-m-d\TH:i') }}" value="{{ old('follow_up_date', now()->addDays(3)->format('Y-m-d\T10:00')) }}" class="input-field border px-3 py-2.5">
                @error('follow_up_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" class="input-field border px-3 py-2.5" placeholder="Add any notes for this follow-up...">{{ old('notes', $inquiry->follow_up_notes) }}</textarea>
                @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-primary">Schedule Follow-up</button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200">
            <p class="text-sm font-medium text-slate-700 mb-3">Quick Schedule:</p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach(['Today' => 0, 'Tomorrow' => 1, '3 Days' => 3, '1 Week' => 7] as $label => $days)
                    <form action="{{ route('follow-ups.store', $inquiry) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="follow_up_date" value="{{ now()->addDays($days)->setHour(10)->setMinute(0)->format('Y-m-d H:i:s') }}">
                        <button type="submit" class="w-full px-3 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors">{{ $label }}</button>
                    </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
