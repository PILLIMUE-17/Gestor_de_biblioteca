<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'nombre_categoria',
        'codigo_categoria',
    ];

    // Una categoría tiene muchas subcategorías
    public function subcategorias()
    {
        return $this->hasMany(Subcategoria::class, 'categoria_id');
    }

    // Acceder a libros ATRAVÉS de subcategorías (relación de 3 niveles)
    public function libros()
    {
        return $this->hasManyThrough(
            Libro::class,       // modelo final que quiero
            Subcategoria::class, // modelo intermedio
            'categoria_id',     // FK en subcategorias
            'subcategoria_id',  // FK en libros
            'id',               // PK de categorias
            'id'                // PK de subcategorias
        );
    }
}