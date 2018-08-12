<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'usuario', 'password', 'condicion', 'idrol'];

    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    // RELACION CON TABLA ROL
    // Un usuario pertenece a un rol
    public function rol() {
        return $this->belongsTo('App\Rol');
    }

    // RELACION CON TABLA PERSONA
    // Un usuario pertenece a una persona
    public function persona() {
        return $this->belongsTo('App\Persona');
    }
}
