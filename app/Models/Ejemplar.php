<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ejemplar extends Model
{
    protected $table = 'ejemplares';

    protected $fillable = [
        'libro_id',
        'donante_id',
        'numero_copia_ejemplar',
        'fecha_ingreso_ejemplar',
        'estado_ejemplar',
        'ubicacion_ejemplar',
        'signatura_topografica',
    ];

    // Pertenece a un libro
    public function libro()
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    // Pertenece a un donante (puede ser null)
    public function donante()
    {
        return $this->belongsTo(Donante::class, 'donante_id');
    }

    // Tiene muchos préstamos
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'ejemplar_id');
    }
}