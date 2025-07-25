<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    /** @var string */
    public $name;

    /** @var string */
    public $label;

    /** @var array */
    public $options;

    /** @var mixed */
    public $selected;

    /**
     * @param  string  $name      El name/id del select
     * @param  string  $label     La etiqueta a mostrar
     * @param  array   $options   Array ['valor'=>'Etiqueta', …]
     * @param  mixed   $selected  Valor seleccionado por defecto
     */
    public function __construct(string $name, string $label, array $options = [], $selected = null)
    {
        $this->name     = $name;
        $this->label    = $label;
        $this->options  = $options;
        $this->selected = $selected;
    }

    public function render()
    {
        return view('components.form.select');
    }
}
