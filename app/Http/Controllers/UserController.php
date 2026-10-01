<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get or guarantee default roles exist for the company
     */
    private function getAvailableRoles()
    {
        $companyId = auth()->user()->company_id ?? Company::default()->id;
        $roles = Role::where('company_id', $companyId)->get();

        if ($roles->isEmpty()) {
            Role::firstOrCreate(
                ['company_id' => $companyId, 'name' => 'Admin'],
                ['permissions' => ['*']]
            );

            Role::firstOrCreate(
                ['company_id' => $companyId, 'name' => 'Manager'],
                [
                    'permissions' => [
                        'projects.view',
                        'projects.create',
                        'projects.edit',
                        'inquiries.view',
                        'inquiries.edit',
                    ]
                ]
            );

            Role::firstOrCreate(
                ['company_id' => $companyId, 'name' => 'Sales Executive'],
                [
                    'permissions' => [
                        'inquiries.view',
                        'inquiries.edit',
                    ]
                ]
            );

            $roles = Role::where('company_id', $companyId)->get();
        }

        // If still empty for any unexpected reason, fallback to any available roles
        if ($roles->isEmpty()) {
            $roles = Role::withoutGlobalScopes()->get()->unique('name');
        }

        return $roles;
    }

    /**
     * Display a listing of users
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);

        // When viewing users management, clear active project selection
        session()->forget('selected_project_id');

        $companyId = auth()->user()->company_id ?? Company::default()->id;

        $users = User::where('company_id', $companyId)
            ->with('role', 'projects')
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $this->authorize('create', User::class);

        $companyId = auth()->user()->company_id ?? Company::default()->id;
        $roles = $this->getAvailableRoles();
        $projects = Project::where('company_id', $companyId)->get();

        return view('users.create', compact('roles', 'projects'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $selectedRole = $request->input('role_id') ? Role::withoutGlobalScopes()->find($request->input('role_id')) : null;
        $isNonAdmin = $selectedRole && $selectedRole->name !== 'Admin';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'project_ids' => $isNonAdmin ? 'required|array|min:1' : 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
        ], [
            'project_ids.required' => 'Assigning at least one project is compulsory for non-admin users.',
            'project_ids.min' => 'Assigning at least one project is compulsory for non-admin users.',
        ]);

        $companyId = auth()->user()->company_id ?? Company::default()->id;

        // Verify or resolve role
        $role = Role::withoutGlobalScopes()->findOrFail($validated['role_id']);
        if ($role->company_id !== $companyId) {
            // Find or create matching role for this company
            $matchingRole = Role::where('company_id', $companyId)
                ->where('name', $role->name)
                ->first();

            if ($matchingRole) {
                $validated['role_id'] = $matchingRole->id;
                $role = $matchingRole;
            } else {
                $createdRole = Role::create([
                    'company_id' => $companyId,
                    'name' => $role->name,
                    'permissions' => $role->permissions ?? ['*'],
                ]);
                $validated['role_id'] = $createdRole->id;
                $role = $createdRole;
            }
        }

        // Verify projects belong to company
        if (!empty($validated['project_ids'])) {
            $validProjects = Project::where('company_id', $companyId)
                ->whereIn('id', $validated['project_ids'])
                ->count();
            
            if ($validProjects !== count($validated['project_ids'])) {
                return redirect()->back()->with('error', 'One or more selected projects are invalid.');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => $companyId,
            'role_id' => $validated['role_id'],
            'email_verified_at' => now(),
        ]);

        // Attach projects to user (if not admin)
        if (!empty($validated['project_ids']) && $role->name !== 'Admin') {
            $user->projects()->attach($validated['project_ids']);
        }

        // Send welcome email with login credentials
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)
                ->send(new \App\Mail\NewUserWelcomeMail($user, $validated['password']));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send welcome email to ' . $user->email . ': ' . $e->getMessage());
        }

        return redirect()->route('users.index')
            ->with('success', 'User created successfully with role ' . $role->name . '! Login credentials have been emailed to ' . $user->email . '.');
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $companyId = auth()->user()->company_id ?? Company::default()->id;
        $roles = $this->getAvailableRoles();
        $projects = Project::where('company_id', $companyId)->get();
        // specify table to avoid ambiguous 'id' when joining project_user
        $assignedProjectIds = $user->projects()->pluck('projects.id')->toArray();
        
        return view('users.edit', compact('user', 'roles', 'projects', 'assignedProjectIds'));
    }

    /**
     * Update the user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $selectedRole = $request->input('role_id') ? Role::withoutGlobalScopes()->find($request->input('role_id')) : null;
        $isNonAdmin = $selectedRole && $selectedRole->name !== 'Admin';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'project_ids' => $isNonAdmin ? 'required|array|min:1' : 'nullable|array',
            'project_ids.*' => 'exists:projects,id',
        ], [
            'project_ids.required' => 'Assigning at least one project is compulsory for non-admin users.',
            'project_ids.min' => 'Assigning at least one project is compulsory for non-admin users.',
        ]);

        $companyId = auth()->user()->company_id ?? Company::default()->id;

        // Verify role belongs to company
        $role = Role::withoutGlobalScopes()->findOrFail($validated['role_id']);
        if ($role->company_id !== $companyId) {
            $matchingRole = Role::where('company_id', $companyId)
                ->where('name', $role->name)
                ->first();

            if ($matchingRole) {
                $validated['role_id'] = $matchingRole->id;
                $role = $matchingRole;
            }
        }

        // Verify projects belong to company
        if (!empty($validated['project_ids'])) {
            $validProjects = Project::where('company_id', $companyId)
                ->whereIn('id', $validated['project_ids'])
                ->count();
            
            if ($validProjects !== count($validated['project_ids'])) {
                return redirect()->back()->with('error', 'One or more selected projects are invalid.');
            }
        }

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Update project assignments (if not admin)
        if ($role->name !== 'Admin') {
            $user->projects()->sync($validated['project_ids'] ?? []);
        } else {
            // Admins have access to all projects, so no project restrictions
            $user->projects()->detach();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Remove the user
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }
}