<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm() {
        // Retornamos la vista vista que esta dentro de la carpeta auth
        return view('auth.login');
    }

    public function login(Request $request) {
        $this->validateLogin($request);

        // Verifica si el user y pass ingresados son correctos. Y si la condicion es 1(activo)
        if (Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'condicion' => 1])) {
            return redirect()->route('main');   // Si el user existe
        }
        // Si ocurre un error en la verificacion que regrese a donde estaba

        return back()
            ->withErrors(['usuario' => trans('auth.failed')]) // Mostramos el error
            ->withInput(request(['usuario'])); // Retornamos el usuario que se ha escrito en el input usuario
    }

    protected function validateLogin(Request $request) {
        // Validar la peticion de inicio de sesion del usuario
        $this->validate($request, [
            'usuario' => 'required|string', // Campos obligatorios de tipo string
            'password'=> 'required|string'
        ]);
    }
}
