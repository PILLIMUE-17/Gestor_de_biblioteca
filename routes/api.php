<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\EjemplarController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\Donantecontroller;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\TipoIdentificacionController;

// Catálogos
Route::get('/autores',      [AutorController::class,    'index']);
Route::post('/autores',     [AutorController::class,    'store']);
Route::get('/categorias',   [CategoriaController::class,'index']);
Route::post('/categorias',  [CategoriaController::class,'store']);
Route::get('/categorias/{id}', [CategoriaController::class,'show']);
Route::put('/categorias/{id}', [CategoriaController::class,'update']);
Route::delete('/categorias/{id}', [CategoriaController::class,'destroy']);
Route::get('/editoriales',  [EditorialController::class,'index']);
Route::post('/editoriales', [EditorialController::class,'store']);
Route::get('/editoriales/{id}', [EditorialController::class,'show']);
Route::put('/editoriales/{id}', [EditorialController::class,'update']);
Route::delete('/editoriales/{id}', [EditorialController::class,'destroy']);

// Libros
Route::get   ('/libros',       [LibroController::class,'index']);
Route::post  ('/libros',       [LibroController::class,'store']);
Route::get   ('/libros/{id}',  [LibroController::class,'show']);
Route::put   ('/libros/{id}',  [LibroController::class,'update']);
Route::delete('/libros/{id}',  [LibroController::class,'destroy']);
Route::get  ('/libros/buscar/avanzado',  [LibroController::class,'buscar']);
Route::get  ('/libros/{id}/disponibilidad',  [LibroController::class,'disponibilidad']);

// Ejemplares
Route::post('/ejemplares/lote',[EjemplarController::class,'ejemplarlote']);
Route::get ('/ejemplares',     [EjemplarController::class,'index']);
Route::get ('/ejemplares/{id}',  [EjemplarController::class,'show']);
Route::put ('/ejemplares/{id}',  [EjemplarController::class,'update']);
Route::delete('/ejemplares/{id}',  [EjemplarController::class,'destroy']);

// Usuarios
Route::get ('/usuarios',      [UsuarioController::class,'index']);
Route::post('/usuarios',      [UsuarioController::class,'store']);
Route::get ('/usuarios/{id}', [UsuarioController::class,'show']);
Route::put ('/usuarios/{id}', [UsuarioController::class,'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class,'destroy']);
Route::get('/usuarios/{id}/historial-prestamos', [UsuarioController::class,'historialPrestamos']);
Route::get('/usuarios/{id}/prestamos-activos', [UsuarioController::class,'prestamosActivos']);
Route::get('/usuarios/{id}/prestamos-devueltos', [UsuarioController::class,'prestamosDevueltos']);

// Préstamos
Route::post('/prestamos',                [PrestamoController::class,'store']);
Route::get ('/prestamos',                [PrestamoController::class,'index']);
Route::get ('/prestamos/{id}',           [PrestamoController::class,'show']);
Route::put ('/prestamos/{id}/devolver',  [PrestamoController::class,'devolver']);
Route::put ('/prestamos/{id}',           [PrestamoController::class,'update']);
Route::delete('/prestamos/{id}',         [PrestamoController::class,'destroy']);

// Subcategorías
Route::get('/subcategorias',   [SubcategoriaController::class,'index']);
Route::post('/subcategorias',  [SubcategoriaController::class,'store']);
Route::get('/subcategorias/{id}',  [SubcategoriaController::class,'show']);
Route::put('/subcategorias/{id}',  [SubcategoriaController::class,'update']);
Route::delete('/subcategorias/{id}',  [SubcategoriaController::class,'destroy']);

// Donantes
Route::get('/donantes',   [Donantecontroller::class,'index']);
Route::post('/donantes',  [Donantecontroller::class,'store']);
Route::get('/donantes/{id}',  [Donantecontroller::class,'show']);
Route::put('/donantes/{id}',  [Donantecontroller::class,'update']);
Route::delete('/donantes/{id}',  [Donantecontroller::class,'destroy']);
Route::get('/donantes/{id}/ejemplares', [Donantecontroller::class,'ejemplares']);

// Autores
Route::get('/autores/{id}',  [AutorController::class,'show']);
Route::put('/autores/{id}',  [AutorController::class,'update']);
Route::delete('/autores/{id}',  [AutorController::class,'destroy']);

// Reportes
Route::get('/reportes/prestamos-por-estado', [ReportController::class,'prestamosPorEstado']);
Route::get('/reportes/libros-mas-solicitados', [ReportController::class,'librosMasSolicitados']);
Route::get('/reportes/libros-sin-movimiento', [ReportController::class,'librosSinMovimiento']);
Route::get('/reportes/disponibilidad-por-categoria', [ReportController::class,'disponibilidadPorCategoria']);
Route::get('/reportes/inventario-total', [ReportController::class,'inventarioTotal']);
Route::get('/reportes/usuarios-mas-prestamos', [ReportController::class,'usuariosConMasPrestamos']);
Route::get('/reportes/historial-usuario/{usuario_id}', [ReportController::class,'historialUsuario']);

// Filtros
Route::get('/filtrar/ejemplares', [FilterController::class,'filtrar']);
Route::get('/filtrar/ejemplares-por-estado/{estado}', [FilterController::class,'ejemplaresPorEstado']);
Route::get('/filtrar/disponibles-libro/{libro_id}', [FilterController::class,'disponiblesPorLibro']);

// Tipos de Identificación
Route::get('/tipos-identificacion', [TipoIdentificacionController::class,'index']);
Route::post('/tipos-identificacion', [TipoIdentificacionController::class,'store']);
Route::get('/tipos-identificacion/{id}', [TipoIdentificacionController::class,'show']);
Route::put('/tipos-identificacion/{id}', [TipoIdentificacionController::class,'update']);
Route::delete('/tipos-identificacion/{id}', [TipoIdentificacionController::class,'destroy']);