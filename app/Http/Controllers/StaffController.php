<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $staff = Staff::with('department')
            ->orderBy('name')
            ->get();

        return view('staff.index', compact('staff'));
    }

    public function create()
    {

        $departments = Department::orderBy('name')->get();
        return view('staff.create', compact('departments'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create user first
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Then create staff record
        $staff = Staff::create([
            'name' => $validated['name'],
            'user_id' => $user->id,
            'department_id' => $validated['department_id'],
        ]);

        // Assign default role if needed
        $user->assignRole('staff');

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member created successfully');
    }

    public function edit(Staff $staff)
    {


        $departments = Department::orderBy('name')->get();
        return view('staff.edit', compact('staff', 'departments'));
    }

    public function update(Request $request, Staff $staff)
    {


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->user_id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update user
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $staff->user->update($userData);

        // Update staff record
        $staff->update([
            'name' => $validated['name'],
            'department_id' => $validated['department_id'],
        ]);

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member updated successfully');
    }

    public function destroy(Staff $staff)
    {
        $this->authorize('delete', $staff);

        // Delete associated user if exists
        if ($staff->user) {
            $staff->user->delete();
        }

        $staff->delete();

        return redirect()
            ->route('staff.index')
            ->with('success', 'Staff member deleted successfully');
    }
}
