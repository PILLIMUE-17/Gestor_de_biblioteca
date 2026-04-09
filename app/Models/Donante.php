<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donante extends Model
{
    protected $table = 'donantes';

    protected $fillable = [
        'nombre_donante',
        'tipo_donante',
        'email_donante',
        'telefono_donante',
    ];

    // Un donante dona muchos EJEMPLARES (no libros)
    public function ejemplares()
    {
        return $this->hasMany(Ejemplar::class, 'donante_id');
    }
}