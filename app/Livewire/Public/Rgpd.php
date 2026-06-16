<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.public')]
#[Title('Politique de confidentialité — RGPD')]
class Rgpd extends Component
{
    public function render()
    {
        return view('livewire.public.rgpd');
    }
}
