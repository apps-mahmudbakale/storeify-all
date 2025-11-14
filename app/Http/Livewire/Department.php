<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Department extends Base
{
    public $sortBy = 'name';
    public function render()
    {
        if($this->search) {
            $departments = \App\Models\Department::query()
                ->where('name', 'like', '%' . $this->search . '%')
                ->paginate(10);

            return view(
                'livewire.department',
                ['departments' => $departments]
            );
        } else {
            $departments = \App\Models\Department::query()->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
            return view(
                'livewire.department',
                ['departments' => $departments]
            );
        }
    }
}
