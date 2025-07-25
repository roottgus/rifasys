<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loteria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'tipo_loteria'];

    public function tipos()
    {
        return $this->hasMany(TipoLoteria::class);
    }
}
