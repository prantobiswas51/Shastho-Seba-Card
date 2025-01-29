<?php

namespace App\Livewire;

use Livewire\Attributes\Computed;
use Livewire\Component;

class District extends Component
{


    public $district;
    public $subDistrict;

    public function render()
    {
        return view('livewire.district');
    }
}
