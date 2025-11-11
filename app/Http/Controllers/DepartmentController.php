<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $departments = Department::orderBy('name')->get();
        return view('departments.index', compact('departments'));
    }

    public function create()
    {
        $this->authorize('create', Department::class);
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Department::class);

        $request->validate(['name' => 'required|string|unique:departments,name']);

        Department::create(['name' => $request->name]);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department created successfully');
    }

    public function edit(Department $department)
    {
        $this->authorize('update', $department);
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);

        $request->validate([
            'name' => 'required|string|unique:departments,name,'.$department->id
        ]);

        $department->update(['name' => $request->name]);

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);

        // Check if department has staff members
        if ($department->staff()->exists()) {
            return redirect()
                ->route('departments.index')
                ->with('error', 'Cannot delete department with associated staff members');
        }

        $department->delete();

        return redirect()
            ->route('departments.index')
            ->with('success', 'Department deleted successfully');
    }
}
