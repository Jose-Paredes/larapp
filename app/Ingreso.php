<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{
    protected $fillable = [
        'idproveedor',
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_hora',
        'impuesto',
        'total',
        'estado'
    ];

    // Permite obtener el usuario que ha registrado el ingreso
    public function usuario() {
        return $this->belongsTo('App\User');
    }

    // Permite obtener el proveedor que ha abastecido los productos en el ingreso
    public function proveedor() {
        return $this->belongsTo('App\Proveedor');
    }
}
