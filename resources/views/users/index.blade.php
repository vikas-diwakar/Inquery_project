@extends('layouts.app')

@section('title', 'Users Management - Property Inquiry SaaS')

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Users Management</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage team members, assign roles, and configure project access permissions.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-primary space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Add New User</span>
        </a>
    </div>

    <!-- Table Card Container -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/80 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Created Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200/80 bg-white">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Name Avatar -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold text-sm flex items-center justify-center shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900">{{ $user->name }}</span>
                                        @if($user->id === auth()->id())
                                            <span class="text-[10px] font-semibold text-indigo-600">(You)</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600">
                                {{ $user->email }}
                            </td>

                            <!-- Role Badge -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleName = $user->role->name ?? 'No Role';
                                    $badgeClass = match($roleName) {
                                        'Admin' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'Manager' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badgeClass }}">
                                    {{ $roleName }}
                                </span>
                            </td>

                            <!-- Created Date -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('users.edit', $user) }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 transition-colors">
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button type="button" 
                                            onclick="showConfirmationModal('Delete User', 'Are you sure you want to delete {{ addslashes($user->name) }}? This action cannot be undone.', function() { document.getElementById('delete-form-{{ $user->id }}').submit(); })" 
                                            class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-100 transition-colors">
                                            Delete
                                        </button>
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                                            @csrf 
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">
                                No users found in this company workspace.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pt-2">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

