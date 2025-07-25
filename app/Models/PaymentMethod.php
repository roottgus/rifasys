<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
    'key', 'name', 'alias', 'enabled', 'details'
];

    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }

    protected $casts = [
    'details' => 'array',
];

}
