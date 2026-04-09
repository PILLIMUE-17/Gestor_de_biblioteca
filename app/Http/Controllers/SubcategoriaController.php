<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subcategoria;

class SubcategoriaController extends Controller
{
    // metodo que devuelve una lista de todas las subcategorias con su categoria relacionada
    public function index()
    {
        return Subcategoria::with('categoria')->get();
    }

    // metodo que crea una nueva subcategoria
    public function store(Request $request)
    {
        $request->validate([
            'categoria_id'         => 'required|exists:categorias,id',
            'nombre_subcategoria'  => 'required|string|max:100',
            'codigo_subcategoria'  => 'nullable|string|max:10',
        ]);

        return response()->json(Subcategoria::create($request->all()), 201);
    }

    public function show($id)
    {
        $subcategoria = Subcategoria::with('categoria')->find($id);
        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }
        return response()->json($subcategoria, 200);
    }

    public function update(Request $request, $id)
    {
        $subcategoria = Subcategoria::find($id);
        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $request->validate([
            'categoria_id'         => 'sometimes|required|exists:categorias,id',
            'nombre_subcategoria'  => 'sometimes|string|max:100',
            'codigo_subcategoria'  => 'nullable|string|max:10',
        ]);

        $subcategoria->update($request->all());
        return response()->json($subcategoria, 200);
    }

    public function destroy($id)
    {
        $subcategoria = Subcategoria::find($id);
        if (!$subcategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $subcategoria->delete();
        return response()->json(['message' => 'Subcategoría eliminada correctamente'], 200);
    }
}