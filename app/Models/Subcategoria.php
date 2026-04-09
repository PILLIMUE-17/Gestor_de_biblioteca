<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subcategoria extends Model
{
    //
    protected $table = "subcategoria";
    protected $primaryKey = 'id_subcategoria';
    public $timestamps = false;

    public function categoria(){
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categorias');
    }

    public function libros(){
        return $this->hasMany(Libro::class, 'id_subcategoria', 'id_subcategoria');
    }
}
