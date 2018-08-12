<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // Indicamos que el modelo va a hacer referencia a la tabla roles
    // El modelo es el singular de la tabla
    protected $table = 'roles';
    // Los campos en donde vamos a recibir y enviar valores
    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    // Por que la tabla roles no tiene los campos timestamp
    public $timestamps = false;

    // Un rol puede tener varios usuarios
    public function users() {
        return $this->hasMany('App\User');
    }
}
