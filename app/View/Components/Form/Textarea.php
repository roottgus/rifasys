<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Textarea extends Component
{
    public string $name;
    public string $label;

    public function __construct(string $name, string $label)
    {
        $this->name  = $name;
        $this->label = $label;
    }

    public function render()
    {
        return view('components.form.textarea');
    }
}
