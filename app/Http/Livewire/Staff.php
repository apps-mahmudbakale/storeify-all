<?php

namespace App\Http\Livewire;

use App\Models\Staff as ModelsStaff;
use Livewire\Component;

class Staff extends Base
{
    public $sortBy = 'name';

    public function render()
    {
   if ($this->search) {
            $staffs = ModelsStaff::query()
                ->where('name', 'like', '%' . $this->search . '%')
                ->Orwhere('staff_no', 'like', '%' . $this->search . '%')
                ->paginate(10);

            return view(
                'livewire.staff',
                ['staffs' => $staffs]
            );
        } else {
            $staffs = ModelsStaff::query()->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
            return view(
                'livewire.staff',
                ['staffs' => $staffs]
            );
        }
    }
}
