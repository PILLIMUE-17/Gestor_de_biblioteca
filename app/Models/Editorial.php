<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editorial extends Model
{
    protected $table = 'editoriales';

    protected $fillable = [
        'nombre_editorial',
        'pais_origen_editorial',
        'telefono_editorial',
        'email_editorial',
    ];

    // Una editorial tiene muchos libros
    public function libros()
    {
        return $this->hasMany(Libro::class, 'editorial_id');
    }
}
