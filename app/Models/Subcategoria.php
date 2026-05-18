<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    protected $table = 'subcategorias';

    protected $fillable = [
        'categoria_id',
        'nombre_subcategoria',
        'codigo_subcategoria',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function libros()
    {
        return $this->hasMany(Libro::class, 'subcategoria_id');
    }
}
