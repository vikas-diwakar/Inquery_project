@extends('layouts.app')

@section('title', 'Edit User - PropDrip')

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
                
                <!-- Role Dropdown & Information Card -->
                <div class="md:col-span-2 space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="role_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assign User Role <span class="text-rose-500">*</span></label>
                        <span id="role-badge-preview" class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600">Select a role below</span>
                    </div>

                    <div class="relative rounded-xl shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <select name="role_id" id="role_id" required onchange="toggleProjectsField()" 
                            class="block w-full pl-10 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all cursor-pointer">
                            <option value="">-- Choose User Role --</option>
                            @foreach($roles as $role)
                                @php
                                    $desc = match($role->name) {
                                        'Admin' => 'Admin (Full Access: Dashboard, Users, Settings, Projects)',
                                        'Manager' => 'Manager (Manage Projects, Brochures, Inquiries & Leads)',
                                        'Sales Executive' => 'Sales Executive (Handle Assigned Inquiries & WhatsApp)',
                                        default => $role->name,
                                    };
                                @endphp
                                <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                    {{ $desc }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Role Explanation Helper Card -->
                    <div id="role-info-card" class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 flex items-start space-x-3 transition-all">
                        <div id="role-icon-container" class="shrink-0 p-1.5 rounded-lg bg-indigo-100 text-indigo-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <div class="font-bold text-slate-800" id="role-title-text">Select a user role</div>
                            <div id="role-desc-text" class="text-slate-500">Choosing a role determines what permissions the user has and whether project assignment is compulsory.</div>
                        </div>
                    </div>
                </div>

                <!-- Projects Checkboxes Field -->
                <div id="projects-field" class="md:col-span-2 space-y-2 pt-2" style="display: none;">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assign Projects <span class="text-rose-500">*</span></label>
                    <p class="text-xs text-slate-500">Compulsory for non-admin users (Sales Executive, Manager, etc.). Non-admin users can only access the projects you assign to them.</p>
                    <div id="projects-container" class="grid grid-cols-1 sm:grid-cols-2 gap-3 border {{ $errors->has('project_ids') ? 'border-rose-300 bg-rose-50/50' : 'border-slate-200 bg-slate-50/80' }} rounded-2xl p-4 transition-all">
                        @forelse($projects as $project)
                            <label for="project_{{ $project->id }}" class="flex items-center p-3 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 transition-all cursor-pointer space-x-3">
                                <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" id="project_{{ $project->id }}" 
                                    {{ in_array($project->id, old('project_ids', $assignedProjectIds)) ? 'checked' : '' }} 
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 project-checkbox">
                                <span class="text-sm font-medium text-slate-800">{{ $project->name }}</span>
                            </label>
                        @empty
                            <p class="col-span-2 text-xs text-slate-500 italic">No projects created yet.</p>
                        @endforelse
                    </div>
                    <div id="projects-error-msg" class="{{ $errors->has('project_ids') ? 'flex' : 'hidden' }} items-center space-x-1.5 mt-1.5 text-xs font-medium text-rose-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="projects-error-text">{{ $errors->first('project_ids', 'Assigning at least one project is compulsory for non-admin users.') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Submit & Cancel Actions -->
            <div class="pt-6 border-t border-slate-200 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                <a href="{{ route('users.index') }}" class="btn-secondary w-full sm:w-auto text-center justify-center">
                    Cancel
                </a>
                <button type="submit" class="btn-primary space-x-2 w-full sm:w-auto justify-center">
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
    function updateRolePreview(roleName) {
        const previewBadge = document.getElementById('role-badge-preview');
        const titleText = document.getElementById('role-title-text');
        const descText = document.getElementById('role-desc-text');
        const iconContainer = document.getElementById('role-icon-container');

        if (!roleName) {
            if (previewBadge) {
                previewBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600';
                previewBadge.textContent = 'Select a role below';
            }
            if (titleText) titleText.textContent = 'Select a user role';
            if (descText) descText.textContent = 'Choosing a role determines what permissions the user has and whether project assignment is compulsory.';
            return;
        }

        if (roleName === 'Admin') {
            if (previewBadge) {
                previewBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-700 border border-purple-200';
                previewBadge.textContent = 'Admin (Full Access)';
            }
            if (titleText) titleText.textContent = 'Administrator (Full Unrestricted Access)';
            if (descText) descText.textContent = 'Admins have complete access across the CRM, including user management, settings, WhatsApp API configuration, and all projects automatically.';
            if (iconContainer) iconContainer.className = 'shrink-0 p-1.5 rounded-lg bg-purple-100 text-purple-700';
        } else if (roleName === 'Manager') {
            if (previewBadge) {
                previewBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200';
                previewBadge.textContent = 'Manager';
            }
            if (titleText) titleText.textContent = 'Manager (Assigned Projects & Inquiries)';
            if (descText) descText.textContent = 'Managers can oversee inquiries, brochures, follow-ups, and lead drips for the specific projects assigned below.';
            if (iconContainer) iconContainer.className = 'shrink-0 p-1.5 rounded-lg bg-indigo-100 text-indigo-700';
        } else if (roleName === 'Sales Executive') {
            if (previewBadge) {
                previewBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200';
                previewBadge.textContent = 'Sales Executive';
            }
            if (titleText) titleText.textContent = 'Sales Executive (Assigned Leads & Inquiries)';
            if (descText) descText.textContent = 'Sales Executives can view and manage their assigned customer inquiries, send WhatsApp messages, and schedule follow-ups for assigned projects.';
            if (iconContainer) iconContainer.className = 'shrink-0 p-1.5 rounded-lg bg-emerald-100 text-emerald-700';
        } else {
            if (previewBadge) {
                previewBadge.className = 'inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200';
                previewBadge.textContent = roleName;
            }
            if (titleText) titleText.textContent = roleName + ' Role';
            if (descText) descText.textContent = 'Custom role permissions applied to assigned projects.';
            if (iconContainer) iconContainer.className = 'shrink-0 p-1.5 rounded-lg bg-slate-100 text-slate-700';
        }
    }

    function toggleProjectsField() {
        const roleSelect = document.getElementById('role_id');
        if (!roleSelect) return;
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : null;
        const projectsField = document.getElementById('projects-field');
        
        updateRolePreview(roleName);

        if (projectsField) {
            if (roleName && roleName !== 'Admin') {
                projectsField.style.display = 'block';
            } else {
                projectsField.style.display = 'none';
                hideProjectsInlineError();
            }
        }
    }

    function showProjectsInlineError(message) {
        const errorMsg = document.getElementById('projects-error-msg');
        const errorText = document.getElementById('projects-error-text');
        const container = document.getElementById('projects-container');
        
        if (errorMsg && errorText) {
            errorText.textContent = message || 'Assigning at least one project is compulsory for non-admin users.';
            errorMsg.classList.remove('hidden');
            errorMsg.classList.add('flex');
        }
        if (container) {
            container.classList.remove('border-slate-200', 'bg-slate-50/80');
            container.classList.add('border-rose-300', 'bg-rose-50/50');
        }
    }

    function hideProjectsInlineError() {
        const errorMsg = document.getElementById('projects-error-msg');
        const container = document.getElementById('projects-container');
        
        if (errorMsg) {
            errorMsg.classList.remove('flex');
            errorMsg.classList.add('hidden');
        }
        if (container) {
            container.classList.remove('border-rose-300', 'bg-rose-50/50');
            container.classList.add('border-slate-200', 'bg-slate-50/80');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        toggleProjectsField();

        document.querySelectorAll('.project-checkbox').forEach(function(cb) {
            cb.addEventListener('change', function() {
                const checkedProjects = document.querySelectorAll('.project-checkbox:checked');
                if (checkedProjects.length > 0) {
                    hideProjectsInlineError();
                }
            });
        });

        const userForm = document.querySelector('form[action="{{ route('users.update', $user) }}"]');
        if (userForm) {
            userForm.addEventListener('submit', function(e) {
                const roleSelect = document.getElementById('role_id');
                if (!roleSelect) return;
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') : null;

                if (roleName && roleName !== 'Admin') {
                    const checkedProjects = document.querySelectorAll('.project-checkbox:checked');
                    if (checkedProjects.length === 0) {
                        e.preventDefault();
                        showProjectsInlineError('Assigning at least one project is compulsory for non-admin users.');
                        document.getElementById('projects-field')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    } else {
                        hideProjectsInlineError();
                    }
                }
            });
        }
    });
</script>
@endsection