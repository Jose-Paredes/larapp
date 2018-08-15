<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articulo;
use Illuminate\Support\Facades\DB;

class ArticuloController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            ->select('articulos.id', 'articulos.idcategoria', 'articulos.codigo', 'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta', 'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
            ->orderBy('articulos.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $articulos = Articulo::join('categorias', 'articulos.idcategoria', '=', 'categorias.id')
            ->select('articulos.id', 'articulos.idcategoria', 'articulos.codigo', 'articulos.nombre', 'categorias.nombre as nombre_categoria', 'articulos.precio_venta', 'articulos.stock', 'articulos.descripcion', 'articulos.condicion')
            ->where('articulos.'.$criterio, 'like', '%'.$buscar.'%')
            ->orderBy('articulos.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        }

        // Listar todos los registros de la tabla categoria
        //$articulos = Articulo::all(); // Alamacenamos todo lo que devuelva el metodo all
        return [
            'pagination' => [
                'total'         => $articulos->total(),
                'current_page'  => $articulos->currentPage(),
                'per_page'      => $articulos->perPage(),
                'last_page'     => $articulos->lastPage(),
                'from'          => $articulos->firstItem(),
                'to'            => $articulos->lastItem(),
            ],
            'articulos' => $articulos
        ];
    }

    public function buscarArticulo(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;

        $articulos = Articulo::where('codigo', '=', $filtro)
            ->select('id', 'nombre')->orderBy('nombre', 'asc')->take(1)->get(); // Toma 1 solo registro

        return ['articulos' => $articulos];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo               = new Articulo();
        $articulo->idcategoria  = $request->idcategoria;
        $articulo->codigo       = $request->codigo;
        $articulo->nombre       = $request->nombre; // Del objeto articulo, en su propiedad nombre le enviamos lo que
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock        = $request->stock;
        $articulo->descripcion  = $request->descripcion;    // recbimos del objeto request recibido del formulario
        $articulo->condicion    = '1';
        $articulo->save(); // Insertamos el objeto en la tabla categoria de la db
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        // Reliza una comprobacion antes de actualizar mediante el metodo find/finfOr...
        $articulo               = Articulo::findOrFail($request->id); // Buscamos el articulo ya registrada en la db, luego actualizamos
        $articulo->idcategoria  = $request->idcategoria;
        $articulo->codigo       = $request->codigo;
        $articulo->nombre       = $request->nombre; // Del objeto articulo, en su propiedad nombre le enviamos lo que
        $articulo->precio_venta = $request->precio_venta;
        $articulo->stock        = $request->stock;
        $articulo->descripcion  = $request->descripcion;    // recbimos del objeto request recibido del formulario
        $articulo->condicion    = '1';
        $articulo->save(); // Insertamos el objeto en la tabla articulo de la db
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id); // Buscamos el articulo ya registrada en la db, luego desactivamos
        $articulo->condicion = '0';
        $articulo->save(); // Actualizamos
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $articulo = Articulo::findOrFail($request->id); // Buscamos el articulo ya registrada en la db, luego activamos
        $articulo->condicion = '1';
        $articulo->save(); // Actualizamos
    }

    public function validar(Request $request) {
        if (!$request->ajax()) return redirect('/');
//        $respuesta = DB::select('SELECT nombre FROM articulos WHERE nombre = ?', [$request->nombre]);
        $respuesta = DB::select('SELECT nombre FROM articulos WHERE nombre = :nombre', ['nombre' => $request->nombre]);
        return $respuesta;
    }

}
