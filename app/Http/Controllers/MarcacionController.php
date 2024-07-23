<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcacion;

class MarcacionController extends Controller
{
    public function index(Request $request)
{
    // Obtener parámetros de la solicitud
    $empleado = $request->input('empleado');
    $inicio = $request->input('inicio');
    $fin = $request->input('fin');

    // Si no se proporciona un rango de fechas, usa la fecha actual
    if (!$inicio || !$fin) {
        $inicio = $fin = date('Y-m-d');
    }

    // Construir la consulta
    $query = Marcacion::query();

    // Filtrar por empleado si se proporciona
    if ($empleado) {
        $query->where('empleado', 'LIKE', "%$empleado%");
    }

    // Filtrar por rango de fechas
    $query->whereBetween('fecha', [$inicio, $fin]);

    // Ordenar resultados
    $marcaciones = $query->orderBy('empleado_id')->orderBy('hora')->get();

    $marcacionesFiltradas = [];
    $ultimoEmpleadoId = null;
    $ultimaHora = null;
    $intervalo = 1800; // Intervalo en segundos (1 minuto)

    foreach ($marcaciones as $marcacion) {
        if ($marcacion->empleado_id === $ultimoEmpleadoId && strtotime($marcacion->hora) - strtotime($ultimaHora) < $intervalo) {
            // Si la marcación es del mismo empleado y está dentro del intervalo, la ignoramos
            continue;
        }

        $marcacionesFiltradas[] = $marcacion;
        $ultimoEmpleadoId = $marcacion->empleado_id;
        $ultimaHora = $marcacion->hora;
    }

    $marcacionesAgrupadas = [];
    foreach ($marcacionesFiltradas as $marcacion) {
        $empleadoId = $marcacion->empleado_id;
        $fecha = $marcacion->fecha;
        if (!isset($marcacionesAgrupadas[$empleadoId])) {
            $marcacionesAgrupadas[$empleadoId] = [];
        }
        if (!isset($marcacionesAgrupadas[$empleadoId][$fecha])) {
            $marcacionesAgrupadas[$empleadoId][$fecha] = [];
        }
        $marcacionesAgrupadas[$empleadoId][$fecha][] = $marcacion;
    }

    $resultado = [];
    foreach ($marcacionesAgrupadas as $empleadoId => $marcacionesPorFecha) {
        foreach ($marcacionesPorFecha as $fecha => $marcaciones) {
            $entradasSalidas = [
                'empleado_id' => $empleadoId,
                'empleado' => $marcaciones[0]->empleado,
                'biometrico' => $marcaciones[0]->biometrico,
                'fecha' => $fecha,
                'entrada' => null,
                'salida_almuerzo' => null,
                'entrada_almuerzo' => null,
                'salida' => null,
            ];

            $marcacionesDelDia = $marcaciones;
            if (count($marcacionesDelDia) > 0) {
                $entradasSalidas['entrada'] = $marcacionesDelDia[0]->hora;
            }
            if (count($marcacionesDelDia) > 1) {
                $entradasSalidas['salida_almuerzo'] = $marcacionesDelDia[1]->hora;
            }
            if (count($marcacionesDelDia) > 2) {
                $entradasSalidas['entrada_almuerzo'] = $marcacionesDelDia[2]->hora;
            }
            if (count($marcacionesDelDia) > 3) {
                //ultimo index
                $key = count($marcacionesDelDia) - 1;
                $entradasSalidas['salida'] = $marcacionesDelDia[$key]->hora;
            }

            $resultado[] = $entradasSalidas;
        }
    }

    // Ordenar el resultado por la hora de entrada
    usort($resultado, function ($a, $b) {
        return strcmp($a['entrada'], $b['entrada']);
    });

    return response()->json($resultado);
}

    public function show($empleado, $fecha)
    {
        $marcaciones = Marcacion::where('empleado_id', $empleado)
            ->where('fecha', $fecha)
            ->orderBy('fecha_hora', 'asc')
            ->get();

        return response()->json($marcaciones);
    }
}
