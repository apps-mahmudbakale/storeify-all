<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use App\Imports\DepartmentImport;
use Maatwebsite\Excel\Facades\Excel;

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

        return view('departments.create');
    }

    public function store(Request $request)
    {

        $request->validate(['name' => 'required|string|unique:departments,name']);

        Department::create(['name' => $request->name]);

        return redirect()
            ->route('app.departments.index')
            ->with('success', 'Department created successfully');
    }

    public function edit(Department $department)
    {

        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {


        $request->validate([
            'name' => 'required|string|unique:departments,name,' . $department->id
        ]);

        $department->update(['name' => $request->name]);

        return redirect()
            ->route('app.departments.index')
            ->with('success', 'Department updated successfully');
    }

    public function importView()
    {
        return view('departments.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new DepartmentImport, $request->file('file'));

        return back()->with('success', 'Departments imported successfully!');
    }

    public function destroy(Department $department)
    {

        $department->delete();

        return redirect()
            ->route('app.departments.index')
            ->with('success', 'Department deleted successfully');
    }
}
