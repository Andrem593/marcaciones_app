<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
    use HasFactory;

    protected $table = 'marcaciones';

    protected $fillable = [
        'empleado_id',
        'fecha_hora',
        'fecha',
        'hora',
        'empleado',
        'biometrico'
    ];
}
