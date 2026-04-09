<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'autores';

    // id y timestamps los maneja Laravel automáticamente
    protected $fillable = [
        'nombre_autor',
        'nombre2_autor',
        'apellido_autor',
        'apellido2_autor',
        'nacionalidad_autor',
        'fecha_nacimiento_autor',
    ];

    // Un autor tiene muchos libros
    public function libros()
    {
        return $this->belongsToMany(Libro::class, 'libro_autor', 'autor_id', 'libro_id');
    }
}