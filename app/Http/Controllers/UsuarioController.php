<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuarioController extends Controller
{
    //lista todos los usuarios con sus prestamos y los ejemplares prestados
    public function index()
    {
        return Usuario::all();
    }
    //registra un lector nuevo, validando que el numero de identificacion y el email sean unicos
    public function store(Request $request)
    {
        $request->validate([
           'nombre_usuario'                 => 'required|string|max:100',
            'apellido_usuario'               => 'required|string|max:100',
            'nombre2_usuario'                => 'nullable|string|max:100',
            'apellido2_usuario'              => 'nullable|string|max:100',
            'tipo_identificacion_id' => 'required|exists:tipos_identificacion,id',
            'numero_identificacion_usuario'  => 'required|string|unique:usuarios,numero_identificacion_usuario',
            'email_usuario'                  => 'required|email|unique:usuarios,email_usuario',
            'telefono_usuario'               => 'nullable|string|max:50',
        ]);

        return response()->json(Usuario::create($request->all()), 201);
    }
    //ver un usuario por su id; y su historial de prestamos con los ejemplares y libros relacionados

    public function show($id)
    {
        $usuario = Usuario::with('prestamos.ejemplar.libro')->find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $request->validate([
           'nombre_usuario'                 => 'sometimes|string|max:100',
            'apellido_usuario'               => 'sometimes|string|max:100',
            'nombre2_usuario'                => 'nullable|string|max:100',
            'apellido2_usuario'              => 'nullable|string|max:100',
            'tipo_identificacion_id' => 'sometimes|required|exists:tipos_identificacion,id',
            'numero_identificacion_usuario'  => 'sometimes|string|unique:usuarios,numero_identificacion_usuario,' . $id,
            'email_usuario'                  => 'sometimes|email|unique:usuarios,email_usuario,' . $id,
            'telefono_usuario'               => 'nullable|string|max:50',
        ]);

        $usuario->update($request->all());
        return response()->json($usuario, 200);
    }

    public function destroy($id)
    {
        $usuario = Usuario::find($id);
        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $usuario->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    // Historial completo de préstamos del usuario
    public function historialPrestamos($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $prestamos = $usuario->prestamos()->with(['ejemplar.libro.autores'])->get();

        return response()->json([
            'usuario_id' => $id,
            'nombre_usuario' => $usuario->nombre_usuario . ' ' . $usuario->apellido_usuario,
            'total_prestamos' => $prestamos->count(),
            'prestamos' => $prestamos,
        ]);
    }

    // Préstamos activos del usuario
    public function prestamosActivos($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $activos = $usuario->prestamos()
                          ->where('estado_prestamo', 'activo')
                          ->with(['ejemplar.libro.autores', 'ejemplar.libro.editorial'])
                          ->get();

        $vencidos = $activos->filter(function ($prestamo) {
            return $prestamo->fecha_devolucion_esperada < now();
        });

        return response()->json([
            'usuario_id' => $id,
            'nombre_usuario' => $usuario->nombre_usuario . ' ' . $usuario->apellido_usuario,
            'total_activos' => $activos->count(),
            'vencidos' => $vencidos->count(),
            'prestamos_activos' => $activos,
        ]);
    }

    // Préstamos devueltos del usuario
    public function prestamosDevueltos($id)
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $devueltos = $usuario->prestamos()
                            ->where('estado_prestamo', 'devuelto')
                            ->with(['ejemplar.libro.autores'])
                            ->get();

        return response()->json([
            'usuario_id' => $id,
            'nombre_usuario' => $usuario->nombre_usuario . ' ' . $usuario->apellido_usuario,
            'total_devueltos' => $devueltos->count(),
            'prestamos_devueltos' => $devueltos,
        ]);
    }
}