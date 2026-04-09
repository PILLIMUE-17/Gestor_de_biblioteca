<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $table = 'prestamos';
    protected $primaryKey = 'id_prestamo';

    protected $fillable = [
        'usuario_id',
        'ejemplar_id',
        'fecha_prestamo',
        'fecha_devolucion_esperada',
        'fecha_devolucion_real',
        'estado_prestamo',
    ];

    public function ejemplar()
    {
        return $this->belongsTo(Ejemplar::class, 'ejemplar_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}