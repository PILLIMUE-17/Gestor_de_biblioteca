<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Autor;

class AutorController extends Controller
{
    public function index()
    {
        return response()->json(Autor::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
        'nombre_autor'          => 'required|string|max:100',
        'apellido_autor'        => 'required|string|max:100',
        'nombre2_autor'         => 'nullable|string|max:100',
        'apellido2_autor'       => 'nullable|string|max:100',
        'nacionalidad_autor'    => 'nullable|string|max:80',
        'fecha_nacimiento_autor' => 'nullable|date',
        ]);
        return response()->json(Autor::create($request->all()), 201);
    }

    public function show($id)
    {
        $autor = Autor::find($id);
        if (!$autor) {
            return response()->json(['message' => 'Autor no encontrado'], 404);
        }
        return response()->json($autor, 200);
    }

    public function update(Request $request, $id)
    {
        $autor = Autor::find($id);
        if (!$autor) {
            return response()->json(['message' => 'Autor no encontrado'], 404);
        }

        $request->validate([
            'nombre_autor'          => 'sometimes|string|max:100',
            'apellido_autor'        => 'sometimes|string|max:100',
            'nombre2_autor'         => 'nullable|string|max:100',
            'apellido2_autor'       => 'nullable|string|max:100',
            'nacionalidad_autor'    => 'nullable|string|max:80',
            'fecha_nacimiento_autor' => 'nullable|date',
        ]);

        $autor->update($request->all());
        return response()->json($autor, 200);
    }

    public function destroy($id)
    {
        $autor = Autor::find($id);
        if (!$autor) {
            return response()->json(['message' => 'Autor no encontrado'], 404);
        }

        $autor->delete();
        return response()->json(['message' => 'Autor eliminado correctamente'], 200);
    }
}