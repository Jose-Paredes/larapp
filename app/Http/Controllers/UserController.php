<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Persona;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Determina si la peticion que se hace es diferente a una peticion ajax, redirije a raiz
        // Metodos accesibles solo mediante peticiones ajax
        if (!$request->ajax()) return redirect('/');

        $buscar =  $request->buscar; // A travez de ajax recibimos el parametro, mediante el metodo get
        $criterio =  $request->criterio;

        if ($buscar == '') {
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idrol', '=', 'roles.id')
                ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
                    'personas.num_documento', 'personas.direccion', 'personas.telefono',
                    'personas.email', 'users.usuario', 'users.password',
                    'users.condicion', 'users.idrol', 'roles.nombre as rol')
                ->orderBy('personas.id', 'desc')->paginate(10); // Obtenemos todos los datos y Paguimos con Eloquent
        } else {
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idrol', '=', 'roles.id')
                ->select('personas.id', 'personas.nombre', 'personas.tipo_documento',
                    'personas.num_documento', 'personas.direccion', 'personas.telefono',
                    'personas.email', 'users.usuario', 'users.password',
                    'users.condicion', 'users.idrol', 'roles.nombre as rol')
                ->where('personas.'.$criterio, 'like', '%'.$buscar.'%')
                ->orderBy('personas.id', 'desc')->paginate(10); // Realiza la busqueda por criterio
        }

        // Listar todos los registros de la tabla persona
        //$personas = Persona::all(); // Alamacenamos tuodo lo que devuelva el metodo all
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

            $user = new User();
            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);   // Ciframos el password
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            $user->id = $persona->id;
            $user->save();

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

            // BUSCAR EL USUARIO A BUSCAR
            $user = User::findOrFail($request->id);

            $persona = Persona::findOrFail($user->id);

            $persona->nombre = $request->nombre; // Del objeto persona, en su propiedad nombre le enviamos lo que
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->save(); // Insertamos el objeto en la tabla persona de la db

            $user->usuario = $request->usuario;
            $user->password = bcrypt($request->password);
            $user->condicion = '1';
            $user->idrol = $request->idrol;
            $user->save();

            DB::commit();

        } catch (Exception $e) {
            // Para deshacer la transaccion
            DB::rollBack();
        }

    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id); // Buscamos el user ya registrada en la db, luego desactivamos
        $user->condicion = '0';
        $user->save(); // Actualizamos
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id); // Buscamos el user ya registrada en la db, luego activamos
        $user->condicion = '1';
        $user->save(); // Actualizamos
    }
}
