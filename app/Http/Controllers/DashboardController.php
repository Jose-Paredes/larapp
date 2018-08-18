<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Usamos invoke por que el controlador solo cuenta con una sola funcion
    public function __invoke(Request $request)
    {
        $anio = date('Y');
        $ingresos = DB::table('ingresos as i')
            ->select(DB::raw('monthname(i.fecha_hora) as mes'),
                     DB::raw('YEAR(i.fecha_hora) as anio'),
                     DB::raw('SUM(i.total) as total'))
            ->whereYear('i.fecha_hora', $anio) // Filtro de meses que ya transcurrieron hasta la fecha actual
            ->groupBy(DB::raw('monthname(i.fecha_hora)'), DB::raw('YEAR(i.fecha_hora)'))
            ->get();

        $ventas = DB::table('ventas as v')
            ->select(DB::raw('monthname(v.fecha_hora) as mes'),
                DB::raw('YEAR(v.fecha_hora) as anio'),
                DB::raw('SUM(v.total) as total'))
            ->whereYear('v.fecha_hora', $anio) // Filtro de meses que ya transcurrieron hasta la fecha actual
            ->groupBy(DB::raw('monthname(v.fecha_hora)'), DB::raw('YEAR(v.fecha_hora)'))
            ->get();

        return ['ingresos' => $ingresos, 'ventas' => $ventas, 'anio'=>$anio];
    }
}
//Monthname