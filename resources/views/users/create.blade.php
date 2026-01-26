@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New User</h2>
        
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password *</label>
                    <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Role *</label>
                    <select name="role_id" id="role_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" onchange="toggleProjectsField()">
                        <option value="">Select a role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="projects-field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Assign Projects (Optional)</label>
                    <p class="text-xs text-gray-500 mb-3">Non-admin users can only access the projects you assign to them.</p>
                    <div class="space-y-2 border border-gray-200 rounded-md p-4 bg-gray-50">
                        @forelse($projects as $project)
                            <div class="flex items-center">
                                <input type="checkbox" name="project_ids[]" value="{{ $project->id }}" id="project_{{ $project->id }}" {{ in_array($project->id, old('project_ids', [])) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="project_{{ $project->id }}" class="ml-2 text-sm text-gray-700">{{ $project->name }}</label>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No projects available</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleProjectsField() {
        const roleSelect = document.getElementById('role_id');
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const roleName = selectedOption.getAttribute('data-role-name');
        const projectsField = document.getElementById('projects-field');
        
        // Show projects field only if not Admin role
        if (roleName && roleName !== 'Admin') {
            projectsField.style.display = 'block';
        } else {
            projectsField.style.display = 'none';
        }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleProjectsField();
    });
</script>
@endsection
