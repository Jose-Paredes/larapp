<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    // Campos de la tabla articulos que van a recibir y almacenar valores
    protected $fillable = [
        'idcategoria', 'codigo', 'nombre', 'precio_venta', 'stock', 'descripcion', 'condicion'
    ];

    // Hacemos referencia a la tabla relacionada
    // Un articulo pertenece a una categoria
    public function categoria() {
        return $this->belongsTo('App\Categoria');
    }
}
