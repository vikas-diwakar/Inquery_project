<!-- resources/views/components/follow-up-history.blade.php -->
@props(['inquiry'])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Follow-up History</h3>
    </div>
    
    <div class="px-4 py-5 sm:px-6">
        @if($inquiry->followUps->count() > 0)
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @foreach($inquiry->followUps->sortByDesc('created_at') as $followUp)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                            <div class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></div>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            @if($followUp->type === 'call')
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.797l.074.5c.037.251.183.468.385.633a7.465 7.465 0 002.748 1.821c.177.07.374.05.512-.165l.687-1.079a1 1 0 011.371-.138 9.042 9.042 0 015.514 5.514 1 1 0 01-.138 1.371l-1.079.687c-.215.138-.235.335-.165.512a7.465 7.465 0 001.821 2.748c.165.202.382.348.633.385l.5.074a1 1 0 01.797.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                            @elseif($followUp->type === 'email')
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                            @elseif($followUp->type === 'sms')
                                                <path d="M1 11a6 6 0 0112 0v-1a6 6 0 10-12 0v1z"/><path d="M16 8.5a.5.5 0 01.5.5v1.5a.5.5 0 01-1 0V9a.5.5 0 01.5-.5z"/>
                                            @else
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                            @endif
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex-1 bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($followUp->type) }} Follow-up
                                            @if($followUp->outcome)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ ucfirst(str_replace('_', ' ', $followUp->outcome)) }}
                                                </span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $followUp->created_at->format('M d, Y H:i') }}</p>
                                    </div>
                                    @if($followUp->notes)
                                    <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $followUp->notes }}</p>
                                    @endif
                                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        by {{ $followUp->user->name ?? 'Unknown' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No follow-ups yet</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Schedule the first follow-up for this inquiry.</p>
            </div>
        @endif
    </div>
</div>
