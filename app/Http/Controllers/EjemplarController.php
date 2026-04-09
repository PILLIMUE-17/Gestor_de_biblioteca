<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ejemplar;
use App\Models\Libro;
use App\Models\Subcategoria;
use App\Models\Categoria;

class EjemplarController extends Controller
{
    // metodo que devuelve una lista de todos los ejemplares con sus relaciones
    public function index()
    {
        return Ejemplar::with(['libro', 'donante'])->get();
    }

    // metodo que crea un nuevo ejemplar
    public function ejemplarlote(Request $request)
    {
        $request->validate([
            'libro_id'  => 'required|exists:libros,id',
            'cantidad'  => 'required|integer|min:1|max:50',
            'ubicacion_ejemplar' => 'nullable|string|max:100',
        ]);

        // Buscar la familia del libro para armar la signatura
        $libro        = Libro::find($request->libro_id);
        $subcategoria = Subcategoria::find($libro->subcategoria_id);
        $categoria    = Categoria::find($subcategoria->categoria_id);

        // Códigos para la signatura topográfica
        $codigo_cat = $categoria->codigo_categoria;
        $codigo_sub = str_pad($subcategoria->codigo_subcategoria, 3, '0', STR_PAD_LEFT);

        // Último número de copia para ese libro
        $ultimo_numero = Ejemplar::where('libro_id', $request->libro_id)
                                 ->max('numero_copia_ejemplar') ?? 0;

        for ($i = 1; $i <= $request->cantidad; $i++) {
            $nuevo_numero = $ultimo_numero + $i;
            $codigo_cop   = str_pad($nuevo_numero, 3, '0', STR_PAD_LEFT);
            $signatura    = $codigo_cat . '-' . $codigo_sub . '-' . $codigo_cop;

            $ejemplar                        = new Ejemplar();
            $ejemplar->libro_id              = $request->libro_id;
            $ejemplar->donante_id            = $request->donante_id ?? null;
            $ejemplar->numero_copia_ejemplar          = $nuevo_numero;
            $ejemplar->fecha_ingreso_ejemplar        = now();
            $ejemplar->estado_ejemplar                = 'disponible';
            $ejemplar->ubicacion_ejemplar             = $request->ubicacion_ejemplar;
            $ejemplar->signatura_topografica = $signatura;
            $ejemplar->save();
        }

        return response()->json([
            'message'           => "Se registraron {$request->cantidad} ejemplares con éxito",
            'ejemplo_signatura' => "Última signatura generada: {$signatura}"
        ], 201);
    }
    // metodo que devuelve un ejemplar por su id con sus relaciones
    public function show($id){
        $ejemplar = Ejemplar::with(['libro', 'donante'])->find($id);
        if (!$ejemplar) {
            return response()->json(['message' => 'Ejemplar no encontrado'], 404);
        }
        return response()->json($ejemplar, 200);
    }
    // metodo que actualiza un ejemplar por su id
    public function update(Request $request, $id)
    {
        $ejemplar = Ejemplar::find($id);
        if (!$ejemplar) {
            return response()->json(['message' => 'Ejemplar no encontrado'], 404);
        }

        $request->validate([
            'libro_id'  => 'sometimes|required|exists:libros,id',
            'donante_id' => 'sometimes|nullable|exists:donantes,id',
            'estado_ejemplar' => 'sometimes|required|in:disponible,prestado,dañado,baja',
            'ubicacion_ejemplar' => 'sometimes|nullable|string|max:100',
        ]);

        // No permitir cambiar libro_id si tiene préstamos activos
        if ($request->has('libro_id') && $request->libro_id !== $ejemplar->libro_id) {
            $prestamosActivos = $ejemplar->prestamos()->where('estado_prestamo', 'activo')->exists();
            if ($prestamosActivos) {
                return response()->json(['message' => 'No se puede cambiar el libro si el ejemplar tiene préstamos activos'], 400);
            }
        }

        $ejemplar->update($request->all());
        return response()->json($ejemplar, 200);
    }

    // metodo que elimina un ejemplar por su id
    public function destroy($id)
    {
        $ejemplar = Ejemplar::find($id);
        if (!$ejemplar) {
            return response()->json(['message' => 'Ejemplar no encontrado'], 404);
        }

        if ($ejemplar->prestamos()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un ejemplar que tiene préstamos asociados'], 400);
        }

        if ($ejemplar->estado_ejemplar === 'prestado') {
            return response()->json(['message' => 'No se puede eliminar un ejemplar prestado'], 400);
        }

        $ejemplar->delete();
        return response()->json(['message' => 'Ejemplar eliminado con éxito'], 200);
    }
}

