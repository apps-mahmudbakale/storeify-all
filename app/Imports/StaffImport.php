<?php

namespace App\Imports;

use App\Models\Staff;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class StaffImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    /**
     * The unique identifier for the model.
     *
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'staff_no';
    }

    /**
     * Validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'staff_no' => 'required|string|max:50',
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'The name field is required.',
            'staff_no.required' => 'The staff number field is required.',
            'staff_no.unique' => 'A staff member with this staff number already exists.',
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            return Staff::updateOrCreate(
                ['staff_no' => $row['staff_no']],
                [
                    'name' => $row['name'],
                    // Add any other fields you want to update here
                ]
            );
        } catch (\Exception $e) {
            Log::error('Error importing staff: ' . $e->getMessage());
            return null;
        }
    }
}
