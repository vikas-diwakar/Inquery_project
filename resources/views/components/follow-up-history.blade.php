@props(['inquiry'])

<div class="card overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-slate-200">
        <h3 class="text-lg font-semibold text-slate-900">Follow-up History</h3>
    </div>
    <div class="px-4 py-5 sm:px-6">
        @if($inquiry->followUps->count() > 0)
            <ul class="space-y-4">
                @foreach($inquiry->followUps->sortByDesc('created_at') as $followUp)
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                @if($followUp->type === 'call')<path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.797l.074.5c.037.251.183.468.385.633a7.465 7.465 0 002.748 1.821c.177.07.374.05.512-.165l.687-1.079a1 1 0 011.371-.138 9.042 9.042 0 015.514 5.514 1 1 0 01-.138 1.371l-1.079.687c-.215.138-.235.335-.165.512a7.465 7.465 0 001.821 2.748c.165.202.382.348.633.385l.5.074a1 1 0 01.797.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                @elseif($followUp->type === 'email')<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                @else<path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1 bg-slate-50 rounded-xl px-4 py-3">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-medium text-slate-900">{{ ucfirst($followUp->type) }} Follow-up
                                    @if($followUp->outcome)<span class="badge bg-primary-100 text-primary-800 ml-2">{{ ucfirst(str_replace('_', ' ', $followUp->outcome)) }}</span>@endif
                                </p>
                                <p class="text-xs text-slate-500">{{ $followUp->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            @if($followUp->notes)<p class="mt-2 text-sm text-slate-700">{{ $followUp->notes }}</p>@endif
                            <p class="mt-2 text-xs text-slate-500">by {{ $followUp->user->name ?? 'Unknown' }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="text-center py-8">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-medium text-slate-900">No follow-ups yet</h3>
                <p class="mt-1 text-sm text-slate-500">Schedule the first follow-up for this inquiry.</p>
            </div>
        @endif
    </div>
</div>
