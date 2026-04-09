<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ejemplar;
use App\Models\Libro;

class FilterController extends Controller
{
    // Filtrar ejemplares por estado
    public function ejemplaresPorEstado($estado)
    {
        $estadosValidos = ['disponible', 'prestado', 'dañado', 'baja'];

        if (!in_array($estado, $estadosValidos)) {
            return response()->json([
                'message' => 'Estado inválido. Estados válidos: ' . implode(', ', $estadosValidos)
            ], 400);
        }

        $ejemplares = Ejemplar::with(['libro.autores', 'libro.editorial', 'donante'])
                              ->where('estado_ejemplar', $estado)
                              ->get();

        return response()->json([
            'estado_filtro' => $estado,
            'total_encontrados' => $ejemplares->count(),
            'ejemplares' => $ejemplares,
        ]);
    }

    // Filtrar ejemplares disponibles de un libro
    public function disponiblesPorLibro($libro_id)
    {
        $libro = Libro::find($libro_id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        $disponibles = Ejemplar::with(['donante'])
                               ->where('libro_id', $libro_id)
                               ->where('estado_ejemplar', 'disponible')
                               ->get();

        return response()->json([
            'libro_id' => $libro_id,
            'titulo_libro' => $libro->titulo_libro,
            'total_disponibles' => $disponibles->count(),
            'ejemplares' => $disponibles,
        ]);
    }

    // Obtener todos los ejemplares con filtros opcionales
    public function filtrar(Request $request)
    {
        $request->validate([
            'estado'       => 'nullable|in:disponible,prestado,dañado,baja',
            'libro_id'     => 'nullable|exists:libros,id',
            'donante_id'   => 'nullable|exists:donantes,id',
            'ubicacion'    => 'nullable|string',
        ]);

        $query = Ejemplar::with(['libro.autores', 'libro.editorial', 'donante']);

        if ($request->estado) {
            $query->where('estado_ejemplar', $request->estado);
        }

        if ($request->libro_id) {
            $query->where('libro_id', $request->libro_id);
        }

        if ($request->donante_id) {
            $query->where('donante_id', $request->donante_id);
        }

        if ($request->ubicacion) {
            $query->where('ubicacion_ejemplar', 'like', '%' . $request->ubicacion . '%');
        }

        $ejemplares = $query->get();

        return response()->json([
            'filtros_aplicados' => $request->all(),
            'total_encontrados' => $ejemplares->count(),
            'ejemplares' => $ejemplares,
        ]);
    }
}
