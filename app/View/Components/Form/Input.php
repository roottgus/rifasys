<?php
namespace App\View\Components\Form;

use Illuminate\View\Component;

class Input extends Component
{
    public $name, $label, $type, $value, $required;
    public function __construct($name, $label, $type = 'text', $value = null, $required = false)
    {
        $this->name     = $name;
        $this->label    = $label;
        $this->type     = $type;
        $this->value    = $value;
        $this->required = $required;
    }
    public function render()
    {
        return view('components.form.input');
    }
}
