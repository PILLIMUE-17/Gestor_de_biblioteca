<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    // usamos el with para cargar las subcategorias relacionadas con cada categoria
    public function index()
    {
        return Categoria::with('subcategorias')->get();
    }
    //esta funcio se llama cuando hacemos POST /api/categorias
    public function store(Request $request)
    {
        $request->validate([
            'nombre_categoria' => 'required|string|max:100',
            'codigo_categoria' => 'nullable|string|max:10',
        ]);

        return response()->json(Categoria::create($request->all()), 201);
    }

    public function show($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return response()->json($categoria, 200);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $request->validate([
            'nombre_categoria' => 'sometimes|string|max:100',
            'codigo_categoria' => 'nullable|string|max:10',
        ]);

        $categoria->update($request->all());
        return response()->json($categoria, 200);
    }

    public function destroy($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $categoria->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente'], 200);
    }
}