<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ingreso;
use App\DetalleIngreso;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                    'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
                    'ingresos.total', 'ingresos.estado', 'personas.nombre', 'users.usuario')
                ->orderBy('ingresos.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $ingresos = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
                ->join('users', 'ingresos.idusuario', '=', 'users.id')
                ->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                    'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
                    'ingresos.total', 'ingresos.estado', 'personas.nombre', 'users.usuario')
                ->where('ingresos.'.$criterio, 'like', '%'.$buscar.'%')
                ->orderBy('ingresos.id', 'desc')->paginate(10); // Realiza la busqueda por criterio
        }

        // Listar todos los registros de la tabla persona
        //$personas = Persona::all(); // Alamacenamos tuodo lo que devuelva el metodo all
        return [
            'pagination' => [
                'total'         => $ingresos->total(),
                'current_page'  => $ingresos->currentPage(),
                'per_page'      => $ingresos->perPage(),
                'last_page'     => $ingresos->lastPage(),
                'from'          => $ingresos->firstItem(),
                'to'            => $ingresos->lastItem(),
            ],
            'ingresos' => $ingresos
        ];
    }

    // Obtiene los datos de la cabecera de ingreso
    public function obtenerCabecera(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $ingreso = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
            ->join('users', 'ingresos.idusuario', '=', 'users.id')
            ->select('ingresos.id', 'ingresos.tipo_comprobante', 'ingresos.serie_comprobante',
                'ingresos.num_comprobante', 'ingresos.fecha_hora', 'ingresos.impuesto',
                'ingresos.total', 'ingresos.estado', 'personas.nombre', 'users.usuario')
            ->where('ingresos.id', '=', $id)
            ->orderBy('ingresos.id', 'desc')->take(1)->get(); // Obtenemos un solo registro

        return ['ingreso' => $ingreso];
    }

    public function obtenerDetalles(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleIngreso::join('articulos', 'detalle_ingresos.idarticulo', '=', 'articulos.id')
            ->select('detalle_ingresos.cantidad', 'detalle_ingresos.precio', 'articulos.nombre as articulo')
            ->where('detalle_ingresos.idingreso', '=', $id)
            ->orderBy('detalle_ingresos.id', 'desc')->get(); // Obtenemos un todos los detalles

        return ['detalles' => $detalles];
    }

    public function store(Request $request) // Recibimos el ingreso y los detalles
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Utilizamos transacciones
            DB::beginTransaction();

            // Zona horaria
            $mytime = Carbon::now('America/Asuncion');

            $ingreso = new Ingreso();
            $ingreso->idproveedor       = $request->idproveedor;
            $ingreso->idusuario         = \Auth::user()->id;  // Le enviamos el usuario que se ha autenticado
            $ingreso->tipo_comprobante  = $request->tipo_comprobante;
            $ingreso->serie_comprobante = $request->serie_comprobante;
            $ingreso->num_comprobante   = $request->num_comprobante;
            $ingreso->fecha_hora        = $mytime->toDateTimeString();  // Combierte hora aceptable
            $ingreso->impuesto          = $request->impuesto;
            $ingreso->total             = $request->total;
            $ingreso->estado            = 'Registrado';
            $ingreso->save(); // Insertamos el objeto en la tabla ingreso de la db

            // ALMACENAMOS TODOS LOS DETALLES DE INGRESOS
            $detalles = $request->data; // Array de detalles

            // Recorro todo el array de detalles
            foreach ($detalles as $ep=>$det) {
                // Hacemos referencia al modelo DetalleIngreso
                $detalle = new DetalleIngreso();
                // Le enviamos valores a c/u de las propiedades del objeto detalle
                $detalle->idingreso  = $ingreso->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad   = $det['cantidad'];
                $detalle->precio     = $det['precio'];
                $detalle->save();
            }

            DB::commit();

        } catch (Exception $e) {
            // Para deshacer la transaccion
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $ingreso = Ingreso::findOrFail($request->id); // Buscamos el ingreso ya registrado en la db, luego anulamos
        $ingreso->estado = 'Anulado';
        $ingreso->save(); // Actualizamos
    }
}
