<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    // Indica al modelo con que tabla trabajar, por defecto es el nombre de la clase en plural
    //protected $table = 'categorias';

    // Indicamos el nombre de la columna que es el id, en caso de no llamarse id
    //protected $primaryKey = 'id';

    protected $fillable = ['nombre', 'descripcion', 'condicion'];

    // Hacemos referencia al modelo con el cual existe relacion
    // Una categoria puede tener varios articulos
    public function articulos() {
        return $this->hasMany('App\Articulo');
    }

}
