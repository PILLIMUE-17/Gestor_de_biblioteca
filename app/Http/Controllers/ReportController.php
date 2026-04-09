<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Ejemplar;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Reporte de usuarios con préstamos activos y vencidos
    public function prestamosPorEstado()
    {
        $activos = Prestamo::where('estado_prestamo', 'activo')
                           ->with(['usuario', 'ejemplar.libro'])
                           ->get();

        $vencidos = Prestamo::where('estado_prestamo', 'activo')
                            ->where('fecha_devolucion_esperada', '<', now())
                            ->with(['usuario', 'ejemplar.libro'])
                            ->get();

        return response()->json([
            'prestamos_activos' => $activos,
            'prestamos_vencidos' => $vencidos,
            'total_activos' => $activos->count(),
            'total_vencidos' => $vencidos->count(),
        ]);
    }

    // Historial de préstamos de un usuario
    public function historialUsuario($usuario_id)
    {
        $usuario = Usuario::find($usuario_id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $prestamos = $usuario->prestamos()->with(['ejemplar.libro.autores'])->get();

        return response()->json([
            'usuario_id' => $usuario_id,
            'nombre_usuario' => $usuario->nombre_usuario . ' ' . $usuario->apellido_usuario,
            'total_prestamos' => $prestamos->count(),
            'activos' => $prestamos->where('estado_prestamo', 'activo')->count(),
            'devueltos' => $prestamos->where('estado_prestamo', 'devuelto')->count(),
            'prestamos' => $prestamos,
        ]);
    }

    // Libros más solicitados (con más préstamos)
    public function librosMasSolicitados($limite = 10)
    {
        $libros = Libro::with(['autores', 'editorial'])
                       ->withCount(['ejemplares as prestamos_totales' => function ($query) {
                           $query->whereHas('prestamos');
                       }])
                       ->orderBy('prestamos_totales', 'desc')
                       ->limit($limite)
                       ->get();

        return response()->json([
            'limite' => $limite,
            'total_resultados' => $libros->count(),
            'libros' => $libros,
        ]);
    }

    // Libros sin movimiento (que nunca han sido prestados)
    public function librosSinMovimiento()
    {
        $libros = Libro::with(['autores', 'editorial', 'subcategoria'])
                       ->doesntHave('ejemplares.prestamos')
                       ->get();

        return response()->json([
            'total_libros_sin_movimiento' => $libros->count(),
            'libros' => $libros,
        ]);
    }

    // Disponibilidad por categoría
    public function disponibilidadPorCategoria()
    {
        $categorias = Categoria::with(['subcategorias.libros.ejemplares'])
                               ->get()
                               ->map(function ($categoria) {
                                   $ejemplaresPorEstado = [];
                                   $totalEjemplares = 0;

                                   foreach ($categoria->subcategorias as $subcategoria) {
                                       foreach ($subcategoria->libros as $libro) {
                                           foreach ($libro->ejemplares as $ejemplar) {
                                               $totalEjemplares++;
                                               $estado = $ejemplar->estado_ejemplar;
                                               $ejemplaresPorEstado[$estado] = ($ejemplaresPorEstado[$estado] ?? 0) + 1;
                                           }
                                       }
                                   }

                                   return [
                                       'categoria_id' => $categoria->id,
                                       'nombre_categoria' => $categoria->nombre_categoria,
                                       'total_ejemplares' => $totalEjemplares,
                                       'disponibles' => $ejemplaresPorEstado['disponible'] ?? 0,
                                       'prestados' => $ejemplaresPorEstado['prestado'] ?? 0,
                                       'danados' => $ejemplaresPorEstado['dañado'] ?? 0,
                                       'baja' => $ejemplaresPorEstado['baja'] ?? 0,
                                   ];
                               });

        return response()->json([
            'total_categorias' => $categorias->count(),
            'categorias' => $categorias,
        ]);
    }

    // Inventario total de la biblioteca
    public function inventarioTotal()
    {
        $totalEjemplares = Ejemplar::count();
        $disponibles = Ejemplar::where('estado_ejemplar', 'disponible')->count();
        $prestados = Ejemplar::where('estado_ejemplar', 'prestado')->count();
        $danados = Ejemplar::where('estado_ejemplar', 'dañado')->count();
        $baja = Ejemplar::where('estado_ejemplar', 'baja')->count();

        $totalLibros = Libro::count();
        $totalAutores = DB::table('libro_autor')->distinct()->count('autor_id');
        $totalEditoriales = DB::table('libros')->distinct()->count('editorial_id');
        $totalCategorias = DB::table('categorias')->count();

        return response()->json([
            'inventario_ejemplares' => [
                'total' => $totalEjemplares,
                'disponibles' => $disponibles,
                'prestados' => $prestados,
                'danados' => $danados,
                'baja' => $baja,
            ],
            'inventario_libros' => [
                'total_libros' => $totalLibros,
                'total_autores' => $totalAutores,
                'total_editoriales' => $totalEditoriales,
                'total_categorias' => $totalCategorias,
            ],
        ]);
    }

    // Usuarios con más préstamos
    public function usuariosConMasPrestamos($limite = 10)
    {
        $usuarios = Usuario::with(['prestamos'])
                           ->withCount('prestamos')
                           ->orderBy('prestamos_count', 'desc')
                           ->limit($limite)
                           ->get();

        return response()->json([
            'limite' => $limite,
            'total_usuarios_listados' => $usuarios->count(),
            'usuarios' => $usuarios->map(function (Usuario $usuario) {
                $activos = $usuario->prestamos()->where('estado_prestamo', 'activo')->count();
                $devueltos = $usuario->prestamos()->where('estado_prestamo', 'devuelto')->count();

                return [
                    'usuario_id' => $usuario->id,
                    'nombre_usuario' => $usuario->nombre_usuario . ' ' . $usuario->apellido_usuario,
                    'email' => $usuario->email_usuario,
                    'total_prestamos' => $usuario->prestamos_count,
                    'activos' => $activos,
                    'devueltos' => $devueltos,
                ];
            }),
        ]);
    }
}
