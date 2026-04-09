<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacion extends Model
{
    protected $table = 'tipos_identificacion';

    protected $fillable = ['nombre_tipo_identificacion'];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'tipo_identificacion_id');
    }
}