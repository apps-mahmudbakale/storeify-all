<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class DepartmentImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        // Trim whitespace and normalize
        $name = isset($row['name']) ? trim($row['name']) : null;

        // Skip if empty
        if (empty($name)) {
            return null;
        }

        // Skip if department already exists (by name)
        $exists = Department::where('name', $name)->exists();
        if ($exists) {
            return null;
        }

        // Create department
        return new Department([
            'name' => $name,
        ]);
    }
}
