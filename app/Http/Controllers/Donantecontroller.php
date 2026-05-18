<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donante;

class Donantecontroller extends Controller
{
    public function index(){
        return response()->json(Donante::all());
    }
    public function store (Request $request){
        $request->validate([
            "nombre_donante"=> "required|string|max:100",
            "tipo_donante"=> "required|in:persona,empresa,institución",
            "telefono_donante"=> "nullable|string|max:20",
            "email_donante"=> "nullable|email|max:100",
        ]);
        $donante = Donante::create($request->only(['nombre_donante','tipo_donante','telefono_donante','email_donante']));
        return response()->json($donante, 201);
    }
    public function ejemplares($id){
        $donante = Donante::find($id);
        if (!$donante) {
            return response()->json(['message' => 'Donante no encontrado'], 404);
        } 
        return response()->json($donante->ejemplares()->with('libro')->get());
    }
    public function show($id){
        $donante = Donante::find($id);
        if(!$donante){
            return response ()->json(["message"=>"Donante no encontrado"],404);
        }
        return response()->json($donante);

    }
    public function update(Request $request, $id){
        $donante = Donante::find($id);
        if(!$donante){
            return response ()->json(["message"=>"Donante no encontrado"],404);
        }
        $request->validate([
            "nombre_donante"=> "sometimes|string|max:100",
            "tipo_donante"=> "sometimes|in:persona,empresa,institución",
            "telefono_donante"=> "sometimes|string|max:20",
            "email_donante"=> "sometimes|email|max:100",
        ]);
        $donante->update($request->only(['nombre_donante','tipo_donante','telefono_donante','email_donante']));
        return response()->json($donante,200);

    }
    public function destroy($id){
        $donante = Donante::find($id);
        if (!$donante){
            return response ()->json(["message"=>"Donante no encontrado"],404);
        }
        $donante->delete();
        return response()->json(["message"=>"Donante eliminado"],200);

    }

    
}
