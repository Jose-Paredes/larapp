<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Persona;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $personas = Persona::orderBy('id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $personas = Persona::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(10); // Realiza la busqueda por criterio
        }

        // Listar todos los registros de la tabla persona
        //$personas = Persona::all(); // Alamacenamos todo lo que devuelva el metodo all
        return [
            'pagination' => [
                'total'         => $personas->total(),
                'current_page'  => $personas->currentPage(),
                'per_page'      => $personas->perPage(),
                'last_page'     => $personas->lastPage(),
                'from'          => $personas->firstItem(),
                'to'            => $personas->lastItem(),
            ],
            'personas' => $personas
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $persona = new Persona();
        $persona->nombre = $request->nombre; // Del objeto persona, en su propiedad nombre le enviamos lo que
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;

        $persona->save(); // Insertamos el objeto en la tabla persona de la db
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Reliza una comprobacion antes de actualizar mediante el metodo find/finfOr...
        $persona = Persona::findOrFail($request->id); // Buscamos la persona ya registrada en la db, luego actualizamos
        $persona->nombre = $request->nombre; // Del objeto persona, en su propiedad nombre le enviamos lo que
        $persona->tipo_documento = $request->tipo_documento;
        $persona->num_documento = $request->num_documento;
        $persona->direccion = $request->direccion;
        $persona->telefono = $request->telefono;
        $persona->email = $request->email;

        $persona->save(); // Actualizamos
    }

}
