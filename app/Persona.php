<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    protected $fillable = ['nombre', 'tipo_documento', 'num_documento', 'direccion', 'telefono', 'email'];

    // Una persona esta relacionada a un proveedor
    public function proveedor() {
        return $this->hasOne('App\Proveedor');
    }
}
