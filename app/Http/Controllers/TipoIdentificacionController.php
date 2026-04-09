<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TipoIdentificacion;

class TipoIdentificacionController extends Controller
{
    // Lista todos los tipos de identificación
    public function index()
    {
        return TipoIdentificacion::all();
    }

    // Crear un nuevo tipo de identificación
    public function store(Request $request)
    {
        $request->validate([
            'nombre_tipo_identificacion' => 'required|string|max:100|unique:tipos_identificacion',
            'descripcion_tipo_identificacion' => 'nullable|string|max:255',
        ]);

        $tipoIdentificacion = TipoIdentificacion::create($request->all());
        return response()->json($tipoIdentificacion, 201);
    }

    // Obtener un tipo de identificación por ID
    public function show($id)
    {
        $tipoIdentificacion = TipoIdentificacion::find($id);

        if (!$tipoIdentificacion) {
            return response()->json(['message' => 'Tipo de identificación no encontrado'], 404);
        }

        return response()->json($tipoIdentificacion);
    }

    // Actualizar un tipo de identificación
    public function update(Request $request, $id)
    {
        $tipoIdentificacion = TipoIdentificacion::find($id);

        if (!$tipoIdentificacion) {
            return response()->json(['message' => 'Tipo de identificación no encontrado'], 404);
        }

        $request->validate([
            'nombre_tipo_identificacion' => 'sometimes|string|max:100|unique:tipos_identificacion,nombre_tipo_identificacion,' . $id,
            'descripcion_tipo_identificacion' => 'nullable|string|max:255',
        ]);

        $tipoIdentificacion->update($request->all());
        return response()->json($tipoIdentificacion);
    }

    // Eliminar un tipo de identificación
    public function destroy($id)
    {
        $tipoIdentificacion = TipoIdentificacion::find($id);

        if (!$tipoIdentificacion) {
            return response()->json(['message' => 'Tipo de identificación no encontrado'], 404);
        }

        // No permitir eliminar si hay usuarios con este tipo de identificación
        if ($tipoIdentificacion->usuarios()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un tipo de identificación que tiene usuarios asociados'], 400);
        }

        $tipoIdentificacion->delete();
        return response()->json(['message' => 'Tipo de identificación eliminado correctamente']);
    }
}
