<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuarios';

    protected $fillable = [
        'tipo_identificacion_id',
        'nombre_usuario',
        'nombre2_usuario',
        'apellido_usuario',
        'apellido2_usuario',
        'numero_identificacion_usuario',
        'email_usuario',
        'telefono_usuario',
    ];

    // Tiene muchos préstamos
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'usuario_id');
    }

    // Pertenece a un tipo de identificación
    public function tipoIdentificacion()
    {
        return $this->belongsTo(TipoIdentificacion::class, 'tipo_identificacion_id');
    }
}