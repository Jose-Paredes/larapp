<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/main', function () {
    // Si la uri es la raiz, muestra el contenido
    return view('contenido/contenido');
})->name('main'); // Alias

// Cada vez que se ingresa a esta uri, hacemos una peticion al
// controlador y a la funcion index

// RUTAS PARA CATEGORIA
Route::get('/categoria', 'CategoriaController@index');
Route::post('/categoria/registrar', 'CategoriaController@store');
Route::put('/categoria/actualizar', 'CategoriaController@update');
Route::put('/categoria/desactivar', 'CategoriaController@desactivar');
Route::put('/categoria/activar', 'CategoriaController@activar');
Route::post('/validar/categoria', 'CategoriaController@validar');
Route::get('/categoria/selectCategoria', 'CategoriaController@selectCategoria');

// RUTAS PARA ARTICULO
Route::get('/articulo', 'ArticuloController@index');
Route::post('/articulo/registrar', 'ArticuloController@store');
Route::put('/articulo/actualizar', 'ArticuloController@update');
Route::put('/articulo/desactivar', 'ArticuloController@desactivar');
Route::put('/articulo/activar', 'ArticuloController@activar');
Route::post('/validar/articulo', 'ArticuloController@validar');

// RUTAS PARA CLIENTE
Route::get('/cliente', 'ClienteController@index');
Route::post('/cliente/registrar', 'ClienteController@store');
Route::put('/cliente/actualizar', 'ClienteController@update');

// RUTAS PARA PROVEEDOR
Route::get('/proveedor', 'ProveedorController@index');
Route::post('/proveedor/registrar', 'ProveedorController@store');
Route::put('/proveedor/actualizar', 'ProveedorController@update');

// RUTAS PARA ROLES
Route::get('/rol', 'RolController@index');
Route::get('/rol/selectRol', 'RolController@selectRol');

// RUTAS PARA USUARIOS
Route::get('/user', 'UserController@index');
Route::post('/user/registrar', 'UserController@store');
Route::put('/user/actualizar', 'UserController@update');
Route::put('/user/desactivar', 'UserController@desactivar');
Route::put('/user/activar', 'UserController@activar');
//Route::post('/validar/users', 'ArticuloController@validar');

// MOSTRAMOS EL FORMULARIO DE LOGUEO
Route::get('/', 'Auth\LoginController@showLoginForm');
Route::post('/login', 'Auth\LoginController@login')->name('login'); // Con alias login

Route::get('/home', 'HomeController@index')->name('home');
