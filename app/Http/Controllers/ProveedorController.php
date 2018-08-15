<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proveedor;
use App\Persona;
use Mockery\Exception;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
                        ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
                        'personas.num_documento', 'personas.direccion', 'personas.telefono',
                        'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
                        ->orderBy('personas.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $personas = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
                        ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
                        'personas.num_documento', 'personas.direccion', 'personas.telefono',
                        'personas.email', 'proveedores.contacto', 'proveedores.telefono_contacto')
                        ->where('personas.'.$criterio, 'like', '%'.$buscar.'%')
                        ->orderBy('personas.id', 'desc')->paginate(10); // Realiza la busqueda por criterio
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

    public function selectProveedor(Request $request) {
        //if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $proveedores = Proveedor::join('personas', 'proveedores.id', '=', 'personas.id')
            ->where('personas.nombre', 'like', '%'.$filtro.'%')
            ->orWhere('personas.num_documento', 'like', '%'.$filtro.'%')
            ->select('personas.id', 'personas.nombre', 'personas.num_documento')
            ->orderBy('personas.nombre', 'asc')->get();

        return ['proveedores' => $proveedores];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Utilizamos transacciones
            DB::beginTransaction();

            $persona = new Persona();
            $persona->nombre = $request->nombre; // Del objeto persona, en su propiedad nombre le enviamos lo que
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save(); // Insertamos el objeto en la tabla persona de la db

            $proveedor = new Proveedor();
            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->id = $persona->id;
            $proveedor->save();

            DB::commit();

        } catch (Exception $e) {
            // Para deshacer la transaccion
            DB::rollBack();
        }
    }

    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try {
            // Utilizamos transacciones
            DB::beginTransaction();

            // BUSCAR EL PROVEEDOR A BUSCAR
            $proveedor = Proveedor::findOrFail($request->id);

            $persona = Persona::findOrFail($proveedor->id);

            $persona->nombre = $request->nombre; // Del objeto persona, en su propiedad nombre le enviamos lo que
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;

            $persona->save(); // Insertamos el objeto en la tabla persona de la db

            $proveedor->contacto = $request->contacto;
            $proveedor->telefono_contacto = $request->telefono_contacto;
            $proveedor->save();

            DB::commit();

        } catch (Exception $e) {
            // Para deshacer la transaccion
            DB::rollBack();
        }

    }
}
