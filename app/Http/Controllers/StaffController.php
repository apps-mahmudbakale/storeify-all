<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Department;
use App\Imports\StaffImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
            'staff_no' => 'required|string|max:50|unique:staff,staff_no',
        ]);


        // Then create staff record
        $staff = Staff::create([
            'name' => $validated['name'],
            'staff_no' => $validated['staff_no'],
        ]);

        return redirect()
            ->route('app.staff.index')
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
            'staff_no' => 'required|string|max:50|unique:staff,staff_no,'.$staff->id,
        ]);


        // Update staff record
        $staff->update([
            'name' => $validated['name'],
           'staff_no' => $validated['staff_no'],
        ]);

        return redirect()
            ->route('app.staff.index')
            ->with('success', 'Staff member updated successfully');
    }

    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv'
    ]);

    Excel::import(new StaffImport, $request->file('file'));

    return back()->with('success', 'Staff imported successfully!');
}

public function importView()
{
    return view('staff.import');
}

    public function destroy(Staff $staff)
    {

        $staff->delete();

        return redirect()
            ->route('app.staff.index')
            ->with('success', 'Staff member deleted successfully');
    }
}
