<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Venta;
use App\DetalleVenta;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                    'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto',
                    'ventas.total', 'ventas.estado', 'personas.nombre', 'users.usuario')
                ->orderBy('ventas.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $ventas = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
                ->join('users', 'ventas.idusuario', '=', 'users.id')
                ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                    'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto',
                    'ventas.total', 'ventas.estado', 'personas.nombre', 'users.usuario')
                ->where('ventas.'.$criterio, 'like', '%'.$buscar.'%')
                ->orderBy('ventas.id', 'desc')->paginate(10); // Realiza la busqueda por criterio
        }

        return [
            'pagination' => [
                'total'         => $ventas->total(),
                'current_page'  => $ventas->currentPage(),
                'per_page'      => $ventas->perPage(),
                'last_page'     => $ventas->lastPage(),
                'from'          => $ventas->firstItem(),
                'to'            => $ventas->lastItem(),
            ],
            'ventas' => $ventas
        ];
    }

    // Obtiene los datos de la cabecera de venta
    public function obtenerCabecera(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                'ventas.num_comprobante', 'ventas.fecha_hora', 'ventas.impuesto',
                'ventas.total', 'ventas.estado', 'personas.nombre', 'users.usuario')
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'desc')->take(1)->get(); // Obtenemos un solo registro

        return ['venta' => $venta];
    }

    public function obtenerDetalles(Request $request) {
        if (!$request->ajax()) return redirect('/');

        $id =  $request->id;

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select('detalle_ventas.cantidad', 'detalle_ventas.precio', 'detalle_ventas.descuento', 'articulos.nombre as articulo')
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get(); // Obtenemos todos los detalles

        return ['detalles' => $detalles];
    }

    public function pdf(Request $request, $id) {
        $venta = Venta::join('personas', 'ventas.idcliente', '=', 'personas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->select('ventas.id', 'ventas.tipo_comprobante', 'ventas.serie_comprobante',
                'ventas.num_comprobante', 'ventas.created_at', 'ventas.impuesto', 'ventas.total',
                'ventas.estado', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento',
                'personas.direccion', 'personas.email',
                'personas.telefono', 'users.usuario')
            ->where('ventas.id', '=', $id)
            ->orderBy('ventas.id', 'des')->take(1)->get();

        $detalles = DetalleVenta::join('articulos', 'detalle_ventas.idarticulo', '=', 'articulos.id')
            ->select('detalle_ventas.cantidad', 'detalle_ventas.precio', 'detalle_ventas.descuento',
                'articulos.nombre as articulo')
            ->where('detalle_ventas.idventa', '=', $id)
            ->orderBy('detalle_ventas.id', 'desc')->get();

        // Numero de reporte
        $numventa = Venta::select('num_comprobante')->where('id', $id)->get();

        $pdf = \PDF::loadView('pdf.venta', ['venta'=>$venta, 'detalles'=>$detalles]);

        return $pdf->download('venta-'.$numventa[0]->num_comprobante.'.pdf');
    }

    public function store(Request $request) // Recibimos la venta y los detalles
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Utilizamos transacciones
            DB::beginTransaction();

            // Zona horaria
            $mytime = Carbon::now('America/Asuncion');

            $venta = new Venta();
            $venta->idcliente         = $request->idcliente;
            $venta->idusuario         = \Auth::user()->id;  // Le enviamos el usuario que se ha autenticado
            $venta->tipo_comprobante  = $request->tipo_comprobante;
            $venta->serie_comprobante = $request->serie_comprobante;
            $venta->num_comprobante   = $request->num_comprobante;
            $venta->fecha_hora        = $mytime->toDateTimeString();  // Combierte hora aceptable
            $venta->impuesto          = $request->impuesto;
            $venta->total             = $request->total;
            $venta->estado            = 'Registrado';
            $venta->save(); // Insertamos el objeto en la tabla venta de la db

            // ALMACENAMOS TODOS LOS DETALLES DE INGRESOS
            $detalles = $request->data; // Array de detalles

            // Recorro t0d0 el array de detalles
            foreach ($detalles as $ep=>$det) {
                // Hacemos referencia al modelo DetalleVenta
                $detalle = new DetalleVenta();
                // Le enviamos valores a c/u de las propiedades del objeto detalle
                $detalle->idventa    = $venta->id;
                $detalle->idarticulo = $det['idarticulo'];
                $detalle->cantidad   = $det['cantidad'];
                $detalle->precio     = $det['precio'];
                $detalle->descuento  = $det['descuento'];
                $detalle->save();
            }

            DB::commit();

            // Retornamos el id de la venta generada
            return ['id'=>$venta->id];

        } catch (Exception $e) {
            // Para deshacer la transaccion
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $venta = Venta::findOrFail($request->id); // Buscamos la venta ya registrada en la db, luego anulamos
        $venta->estado = 'Anulado';
        $venta->save(); // Actualizamos
    }
}
