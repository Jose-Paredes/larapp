<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categoria;  // Importamos el modelo

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar === '') {
            $categorias = Categoria::orderBy('id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $categorias = Categoria::where($criterio, 'like', '%'.$buscar.'%')->orderBy('id', 'desc')->paginate(10); // Realiza la busqueda por criterio
        }

        // Listar todos los registros de la tabla categoria
        //$categorias = Categoria::all(); // Alamacenamos todo lo que devuelva el metodo all
        return [
            'pagination' => [
                'total'         => $categorias->total(),
                'current_page'  => $categorias->currentPage(),
                'per_page'      => $categorias->perPage(),
                'last_page'     => $categorias->lastPage(),
                'from'          => $categorias->firstItem(),
                'to'            => $categorias->lastItem(),
            ],
            'categorias' => $categorias
        ];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = new Categoria();
        $categoria->nombre = $request->nombre; // Del objeto categoria, en su propiedad nombre le enviamos lo que
        $categoria->descripcion = $request->descripcion;    // recbimos del objeto request recibido del formulario
        $categoria->condicion = '1';
        $categoria->save(); // Insertamos el objeto en la tabla categoria de la db
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Reliza una comprobacion antes de actualizar mediante el metodo find/finfOr...
        $categoria = Categoria::findOrFail($request->id); // Buscamos la categoria ya registrada en la db, luego actualizamos
        $categoria->nombre = $request->nombre;
        $categoria->descripcion = $request->descripcion;
        $categoria->condicion = '1';
        $categoria->save(); // Actualizamos
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Categoria::findOrFail($request->id); // Buscamos la categoria ya registrada en la db, luego desactivamos
        $categoria->condicion = '0';
        $categoria->save(); // Actualizamos
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $categoria = Categoria::findOrFail($request->id); // Buscamos la categoria ya registrada en la db, luego activamos
        $categoria->condicion = '1';
        $categoria->save(); // Actualizamos
    }
}
