<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Editorial;

class EditorialController extends Controller
{
    // metodo que devuelve una lista de todas las editoriales 
    public function index()
    {
        return Editorial::all();
    }
    // metodo que crea una nueva editorial
    public function store(Request $request)
    {
        $request->validate([
            'nombre_editorial' => 'required|string|max:100',
            'pais_origen_editorial' => 'nullable|string|max:100',
            'telefono_editorial'   => 'nullable|string|max:20',
            'email_editorial'      => 'nullable|email|max:100',
        ]);

        return response()->json(Editorial::create($request->all()), 201);
    }

    public function show($id)
    {
        $editorial = Editorial::find($id);
        if (!$editorial) {
            return response()->json(['message' => 'Editorial no encontrada'], 404);
        }
        return response()->json($editorial, 200);
    }

    public function update(Request $request, $id)
    {
        $editorial = Editorial::find($id);
        if (!$editorial) {
            return response()->json(['message' => 'Editorial no encontrada'], 404);
        }

        $request->validate([
            'nombre_editorial' => 'sometimes|string|max:100',
            'pais_origen_editorial' => 'nullable|string|max:100',
            'telefono_editorial'   => 'nullable|string|max:20',
            'email_editorial'      => 'nullable|email|max:100',
        ]);

        $editorial->update($request->all());
        return response()->json($editorial, 200);
    }

    public function destroy($id)
    {
        $editorial = Editorial::find($id);
        if (!$editorial) {
            return response()->json(['message' => 'Editorial no encontrada'], 404);
        }

        $editorial->delete();
        return response()->json(['message' => 'Editorial eliminada correctamente'], 200);
    }
}