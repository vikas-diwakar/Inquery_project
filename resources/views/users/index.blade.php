@extends('layouts.app')

@section('title', 'Users Management - PropDrip')

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Header Title & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Users Management</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Manage team members, view and assign roles, and configure project access permissions.</p>
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
            <table class="min-w-[680px] w-full divide-y divide-slate-200">
                <thead>
                    <tr class="bg-slate-50/80 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-4 whitespace-nowrap">User</th>
                        <th class="px-6 py-4 whitespace-nowrap">Email</th>
                        <th class="px-6 py-4 whitespace-nowrap">Assigned Role</th>
                        <th class="px-6 py-4 whitespace-nowrap">Project Access</th>
                        <th class="px-6 py-4 whitespace-nowrap">Created Date</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Actions</th>
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

                            <!-- Role Badge with Icon -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleName = $user->role->name ?? 'No Role';
                                @endphp
                                @if($roleName === 'Admin')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-purple-50 text-purple-700 border-purple-200 space-x-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                        <span>Admin (Full Access)</span>
                                    </span>
                                @elseif($roleName === 'Manager')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-indigo-50 text-indigo-700 border-indigo-200 space-x-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Manager</span>
                                    </span>
                                @elseif($roleName === 'Sales Executive')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border bg-emerald-50 text-emerald-700 border-emerald-200 space-x-1.5 shadow-xs">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Sales Executive</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border bg-slate-100 text-slate-700 border-slate-200">
                                        {{ $roleName }}
                                    </span>
                                @endif
                            </td>

                            <!-- Project Access -->
                            <td class="px-6 py-4">
                                @if(($user->role->name ?? '') === 'Admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        All Projects
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1.5 max-w-xs">
                                        @forelse($user->projects as $proj)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                {{ $proj->name }}
                                            </span>
                                        @empty
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                No projects assigned
                                            </span>
                                        @endforelse
                                    </div>
                                @endif
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
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500">
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