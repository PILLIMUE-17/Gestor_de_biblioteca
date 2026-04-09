<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;

class LibroController extends Controller
{
    // metodo que devuelve una lista de todos los libros con sus relaciones
    public function index()
    {
        return Libro::with(['autores', 'editorial', 'subcategoria', 'ejemplares'])->get();
    }

    // metodo que crea un nuevo libro con múltiples autores
    public function store(Request $request)
    {
        $request->validate([
            'titulo_libro'           => 'required|string|max:255',
            'autores_ids'            => 'required|array|min:1',
            'autores_ids.*'          => 'exists:autores,id',
            'editorial_id'           => 'required|exists:editoriales,id',
            'subcategoria_id'        => 'required|exists:subcategorias,id',
            'isbn_libro'             => 'nullable|string|unique:libros,isbn_libro',
            'anio_publicacion_libro' => 'nullable|integer|min:1000|max:2100',
            'descripcion_libro'      => 'nullable|string',
        ]);

        $libro = Libro::create([
            'titulo_libro'           => $request->titulo_libro,
            'editorial_id'           => $request->editorial_id,
            'subcategoria_id'        => $request->subcategoria_id,
            'isbn_libro'             => $request->isbn_libro,
            'anio_publicacion_libro' => $request->anio_publicacion_libro,
            'descripcion_libro'      => $request->descripcion_libro,
        ]);

        // Sincronizar autores
        $libro->autores()->sync($request->autores_ids);

        return response()->json($libro->load(['autores', 'editorial', 'subcategoria']), 201);
    }

    // metodo busca un libro por su id
    public function show($id)
    {
        $libro = Libro::with(['autores', 'editorial', 'subcategoria', 'ejemplares'])->find($id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        return response()->json($libro);
    }

    // metodo actualiza un libro
    public function update(Request $request, $id)
    {
        $libro = Libro::find($id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        $request->validate([
            'titulo_libro'           => 'sometimes|string|max:255',
            'autores_ids'            => 'sometimes|array|min:1',
            'autores_ids.*'          => 'exists:autores,id',
            'editorial_id'           => 'sometimes|exists:editoriales,id',
            'subcategoria_id'        => 'sometimes|exists:subcategorias,id',
            'isbn_libro'             => 'nullable|string|unique:libros,isbn_libro,' . $id,
            'anio_publicacion_libro' => 'nullable|integer|min:1000|max:2100',
            'descripcion_libro'      => 'nullable|string',
        ]);

        $libro->update($request->except('autores_ids'));

        if ($request->has('autores_ids')) {
            $libro->autores()->sync($request->autores_ids);
        }

        return response()->json($libro->load(['autores', 'editorial', 'subcategoria']));
    }

    // metodo que elimina un libro por su id (solo si no tiene ejemplares)
    public function destroy($id)
    {
        $libro = Libro::find($id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        if ($libro->ejemplares()->exists()) {
            return response()->json(['message' => 'No se puede eliminar un libro que tiene ejemplares registrados'], 400);
        }

        $libro->delete();

        return response()->json(['message' => 'Libro eliminado correctamente']);
    }

    // Búsqueda avanzada de libros
    public function buscar(Request $request)
    {
        $request->validate([
            'titulo'    => 'nullable|string|max:255',
            'autor'     => 'nullable|string|max:100',
            'isbn'      => 'nullable|string|max:20',
            'categoria' => 'nullable|integer|exists:categorias,id',
        ]);

        $query = Libro::with(['autores', 'editorial', 'subcategoria', 'ejemplares']);

        if ($request->titulo) {
            $query->where('titulo_libro', 'like', '%' . $request->titulo . '%');
        }

        if ($request->autor) {
            $query->whereHas('autores', function ($q) use ($request) {
                $q->where('nombre_autor', 'like', '%' . $request->autor . '%')
                  ->orWhere('apellido_autor', 'like', '%' . $request->autor . '%');
            });
        }

        if ($request->isbn) {
            $query->where('isbn_libro', $request->isbn);
        }

        if ($request->categoria) {
            $query->whereHas('subcategoria', function ($q) use ($request) {
                $q->where('categoria_id', $request->categoria);
            });
        }

        return response()->json($query->get());
    }

    // Obtener disponibilidad de ejemplares de un libro
    public function disponibilidad($id)
    {
        $libro = Libro::with(['ejemplares'])->find($id);

        if (!$libro) {
            return response()->json(['message' => 'Libro no encontrado'], 404);
        }

        $disponibles = $libro->ejemplares()->where('estado_ejemplar', 'disponible')->count();
        $prestados = $libro->ejemplares()->where('estado_ejemplar', 'prestado')->count();
        $danados = $libro->ejemplares()->where('estado_ejemplar', 'dañado')->count();
        $baja = $libro->ejemplares()->where('estado_ejemplar', 'baja')->count();
        $total = $libro->ejemplares()->count();

        return response()->json([
            'libro_id' => $id,
            'titulo_libro' => $libro->titulo_libro,
            'total_ejemplares' => $total,
            'disponibles' => $disponibles,
            'prestados' => $prestados,
            'danados' => $danados,
            'baja' => $baja,
        ]);
    }
}