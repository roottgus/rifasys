<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoLoteria extends Model
{
    protected $table = 'tipos_loteria';
    protected $fillable = ['nombre', 'loteria_id'];
    public $timestamps = false;

    public function loteria()
    {
        return $this->belongsTo(Loteria::class);
    }
}
