<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $table = 'libros';

    protected $fillable = [
        'autor_id',
        'editorial_id',
        'subcategoria_id',
        'titulo_libro',
        'isbn_libro',
        'anio_publicacion_libro',
        'descripcion_libro',
    ];

    // Tiene muchos autores
    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'libro_autor', 'libro_id', 'autor_id');
    }

    // Pertenece a una editorial
    public function editorial()
    {
        return $this->belongsTo(Editorial::class, 'editorial_id');
    }

    // Pertenece a una subcategoría
    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class, 'subcategoria_id');
    }

    // Un libro tiene muchos ejemplares físicos
    public function ejemplares()
    {
        return $this->hasMany(Ejemplar::class, 'libro_id');
    }
}