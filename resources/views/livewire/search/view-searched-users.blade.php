<?php

use Livewire\Volt\Component;

new class extends Component {

    public $query;

    public function mount($query)
    {
        $this->query = $query;
    }
}; ?>

<div>
    //
</div>
