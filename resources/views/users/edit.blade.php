@extends('layouts.app')

@section('title', 'Edit User - Property Inquiry SaaS')

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <!-- Back Button Link -->
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Users</span>
        </a>
    </div>

    <!-- Main Card Container -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        <!-- Card Header Banner -->
        <div class="bg-slate-900 p-6 sm:p-8 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1 relative z-10">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Edit User Account</h1>
                <p class="text-xs sm:text-sm text-slate-300">Update account details, role, and assigned project permissions for {{ $user->name }}.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center text-indigo-300 relative z-10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>

        <!-- Form Content -->
        <form action="{{ route('users.update', $user) }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Full Name <span class="text-rose-500">*</span></label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}" 
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                            placeholder="John Doe">
                    </div>
                </div>
                
                <!-- Email -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Email Address <span class="text-rose-500">*</span></label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                            </svg>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email', $user->email) }}" 
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                            placeholder="john@company.com">
                    </div>
                </div>
                
                <!-- Password (Optional) -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">New Password (Optional)</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                            placeholder="Leave blank to keep unchanged">
                    </div>
                </div>
                
                <!-- Password Confirmation -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Confirm New Password</label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                            placeholder="Confirm new password">
                    </div>
                </div>
                
                <!-- Role Dropdown -->
                <div class="md:col-span-2 space-y-1.5">
                    <label for="role_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">User Role <span class="text-rose-500">*</span></label>
                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <select name="role_id" id="role_id" required onchange="toggleProjectsField()" 
                            class="block w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all cursor-pointer">
                            <option value="">-- Select a role --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }} {{ $role->name === 'Admin' ? '(Full Access)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Projects Checkboxes Field -->
                <div id="projects-field" class="md:col-span-2 space-y-2 pt-2" style="display: none;">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assign Projects (Optional)</label>
                    <p class="text-xs text-slate-500">Non-admin users can only access the projects you assign to them.</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border border-slate-200 rounded-2xl p-4 bg-slate-50/80">
                        @forelse($projects as $project)
                            <label for="project_{{ $project->id }}" class="flex items-center p-3 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 transition-all cursor-pointer space-x-3">
                                <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" id="project_{{ $project->id }}" 
                                    {{ in_array($project->id, old('project_ids', $assignedProjectIds)) ? 'checked' : '' }} 
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-sm font-medium text-slate-800">{{ $project->name }}</span>
                            </label>
                        @empty
                            <p class="col-span-2 text-xs text-slate-500 italic">No projects created yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Submit & Cancel Actions -->
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('users.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary space-x-2">
                    <span>Update User Account</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleProjectsField() {
        const roleSelect = document.getElementById('role_id');
        if (!roleSelect) return;
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : null;
        const projectsField = document.getElementById('projects-field');
        
        if (projectsField) {
            if (roleName && roleName !== 'Admin') {
                projectsField.style.display = 'block';
            } else {
                projectsField.style.display = 'none';
            }
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        toggleProjectsField();
    });
</script>
@endsection

