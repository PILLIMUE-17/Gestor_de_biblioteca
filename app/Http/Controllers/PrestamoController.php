<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Ejemplar;

class PrestamoController extends Controller
{
    public function index()
    {
        return Prestamo::with(['usuario', 'ejemplar.libro'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'ejemplar_id' => 'required|exists:ejemplares,id',
            'usuario_id'  => 'required|exists:usuarios,id',
        ]);

        // Bloquear si el usuario tiene multas pendientes
        $multaPendiente = Prestamo::where('usuario_id', $request->usuario_id)
                                  ->where('multa', '>', 0)
                                  ->where('multa_pagada', false)
                                  ->exists();

        if ($multaPendiente) {
            return response()->json([
                'message' => 'El usuario tiene multas pendientes de pago. No puede realizar nuevos préstamos hasta saldar su deuda.'
            ], 400);
        }

        // Validar límite de 10 préstamos activos
        $prestamosActivos = Prestamo::where('usuario_id', $request->usuario_id)
                                    ->where('estado_prestamo', 'activo')
                                    ->count();

        if ($prestamosActivos >= 10) {
            return response()->json([
                'message' => 'El usuario ha alcanzado el límite de 10 préstamos activos'
            ], 400);
        }

        $ejemplar = Ejemplar::find($request->ejemplar_id);

        if ($ejemplar->estado_ejemplar !== 'disponible') {
            return response()->json([
                'message' => 'Este ejemplar no está disponible'
            ], 400);
        }

        $prestamo                            = new Prestamo();
        $prestamo->usuario_id                = $request->usuario_id;
        $prestamo->ejemplar_id               = $request->ejemplar_id;
        $prestamo->fecha_prestamo            = now();
        $prestamo->fecha_devolucion_esperada = now()->addDays(7);
        $prestamo->estado_prestamo           = 'activo';
        $prestamo->multa                     = 0;
        $prestamo->multa_pagada              = true;
        $prestamo->save();

        $ejemplar->estado_ejemplar = 'prestado';
        $ejemplar->save();

        $prestamo->load(['usuario', 'ejemplar.libro']);

        return response()->json([
            'message'  => 'Préstamo registrado correctamente',
            'prestamo' => $prestamo
        ], 201);
    }

    public function devolver(Request $request, $id)
    {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        if ($prestamo->estado_prestamo === 'devuelto') {
            return response()->json(['message' => 'Este préstamo ya fue devuelto'], 400);
        }

        $request->validate([
            'estado_ejemplar' => 'sometimes|in:disponible,dañado,baja',
        ]);

        // Calcular multa por días de retraso
        $fechaEsperada = new \DateTime(substr($prestamo->fecha_devolucion_esperada, 0, 10));
        $fechaHoy      = new \DateTime(now()->toDateString());
        $multa         = 0;
        $diasRetraso   = 0;

        if ($fechaHoy > $fechaEsperada) {
            $diasRetraso = (int) $fechaHoy->diff($fechaEsperada)->days;
            $multa       = $diasRetraso * 1000;
        }

        $prestamo->fecha_devolucion_real = now();
        $prestamo->estado_prestamo       = 'devuelto';
        $prestamo->multa                 = $multa;
        $prestamo->multa_pagada          = ($multa === 0);
        $prestamo->save();

        $estadoFinal = $request->estado_ejemplar ?? 'disponible';
        $prestamo->ejemplar->estado_ejemplar = $estadoFinal;
        $prestamo->ejemplar->save();

        $prestamo->load(['usuario', 'ejemplar.libro']);

        return response()->json([
            'message'       => 'Devolución registrada correctamente',
            'prestamo'      => $prestamo,
            'dias_retraso'  => $diasRetraso,
            'multa'         => $multa,
        ]);
    }

    public function pagarMulta($id)
    {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        if ($prestamo->multa <= 0) {
            return response()->json(['message' => 'Este préstamo no tiene multa'], 400);
        }

        if ($prestamo->multa_pagada) {
            return response()->json(['message' => 'La multa ya fue pagada'], 400);
        }

        $prestamo->multa_pagada = true;
        $prestamo->save();

        return response()->json([
            'message'  => 'Multa pagada correctamente',
            'prestamo' => $prestamo
        ]);
    }

    public function show($id)
    {
        $prestamo = Prestamo::with(['usuario', 'ejemplar.libro'])->find($id);
        if (!$prestamo) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }
        return response()->json($prestamo, 200);
    }

    public function update(Request $request, $id)
    {
        $prestamo = Prestamo::find($id);
        if (!$prestamo) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        $request->validate([
            'ejemplar_id'              => 'sometimes|required|exists:ejemplares,id',
            'usuario_id'               => 'sometimes|required|exists:usuarios,id',
            'fecha_devolucion_esperada' => 'sometimes|date',
            'estado_prestamo'          => 'sometimes|in:activo,devuelto',
        ]);

        if ($request->has('ejemplar_id') && $prestamo->ejemplar_id != $request->ejemplar_id) {
            $nuevoEjemplar = Ejemplar::find($request->ejemplar_id);
            if ($nuevoEjemplar->estado_ejemplar !== 'disponible') {
                return response()->json(['message' => 'El ejemplar seleccionado no está disponible'], 400);
            }
            $prestamo->ejemplar->estado_ejemplar = 'disponible';
            $prestamo->ejemplar->save();
            $nuevoEjemplar->estado_ejemplar = 'prestado';
            $nuevoEjemplar->save();
        }

        if ($request->has('estado_prestamo') && $request->estado_prestamo === 'devuelto' && $prestamo->estado_prestamo !== 'devuelto') {
            $prestamo->fecha_devolucion_real = now();
        }

        $prestamo->update($request->all());
        $prestamo->load(['usuario', 'ejemplar.libro']);
        return response()->json($prestamo, 200);
    }

    public function destroy($id)
    {
        $prestamo = Prestamo::find($id);
        if (!$prestamo) {
            return response()->json(['message' => 'Préstamo no encontrado'], 404);
        }

        if ($prestamo->estado_prestamo === 'activo') {
            $prestamo->ejemplar->estado_ejemplar = 'disponible';
            $prestamo->ejemplar->save();
        }

        $prestamo->delete();
        return response()->json(['message' => 'Préstamo eliminado correctamente'], 200);
    }
}
