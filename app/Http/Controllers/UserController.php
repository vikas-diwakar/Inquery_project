<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->with('role')
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::where('company_id', auth()->user()->company_id)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Verify role belongs to company
        $role = Role::findOrFail($validated['role_id']);
        if ($role->company_id !== auth()->user()->company_id) {
            return redirect()->back()->with('error', 'Invalid role selected.');
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_id' => auth()->user()->company_id,
            'role_id' => $validated['role_id'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the user
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        $roles = Role::where('company_id', auth()->user()->company_id)->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the user
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Verify role belongs to company
        $role = Role::findOrFail($validated['role_id']);
        if ($role->company_id !== auth()->user()->company_id) {
            return redirect()->back()->with('error', 'Invalid role selected.');
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
