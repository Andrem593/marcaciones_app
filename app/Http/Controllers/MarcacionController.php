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

        // Agrupar por empleado y fecha
        $marcacionesAgrupadas = [];
        foreach ($marcaciones as $marcacion) {
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
                // 1. Eliminar repetidas por intervalo de 1 minuto
                $filtradas = [];
                $lastHora = null;
                foreach ($marcaciones as $m) {
                    if ($lastHora === null || (strtotime($m->hora) - strtotime($lastHora)) >= 60) {
                        $filtradas[] = $m;
                        $lastHora = $m->hora;
                    }
                }

                // 2. Clasificación según cantidad y reglas
                $entradasSalidas = [
                    'empleado_id' => $empleadoId,
                    'empleado' => $filtradas[0]->empleado ?? '',
                    'biometrico' => $filtradas[0]->biometrico ?? '',
                    'fecha' => $fecha,
                    'entrada' => null,
                    'salida_almuerzo' => null,
                    'entrada_almuerzo' => null,
                    'salida' => null,
					'tipo_contrato'=>$filtradas[0]->tipo_contrato
                ];

                $count = count($filtradas);
                if ($count >= 4) {
                    // Regla especial para más de 4 marcaciones
                    $entrada = $filtradas[0]->hora;
                    $salida_almuerzo = null;
                    $entrada_almuerzo = null;
                    $salida = $filtradas[$count-1]->hora;

                    // Buscar salida de almuerzo (entre 10:00 y 14:00)
                    for ($i=1; $i<$count-1; $i++) {
                        $h = strtotime($filtradas[$i]->hora);
                        $h10 = strtotime('10:00:00');
                        $h14 = strtotime('14:00:00');
                        if ($h >= $h10 && $h <= $h14) {
                            $salida_almuerzo = $filtradas[$i]->hora;
                            // Buscar entrada de almuerzo después de salida, con intervalo >= 5 min
                            for ($j=$i+1; $j<$count-1; $j++) {
                                $h2 = strtotime($filtradas[$j]->hora);
                                if (($h2 - $h) >= 300) {
                                    $entrada_almuerzo = $filtradas[$j]->hora;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    $entradasSalidas['entrada'] = $entrada;
                    $entradasSalidas['salida_almuerzo'] = $salida_almuerzo;
                    $entradasSalidas['entrada_almuerzo'] = $entrada_almuerzo;
                    $entradasSalidas['salida'] = $salida;
                } elseif ($count === 4) {
                    $entradasSalidas['entrada'] = $filtradas[0]->hora;
                    $entradasSalidas['salida_almuerzo'] = $filtradas[1]->hora;
                    $entradasSalidas['entrada_almuerzo'] = $filtradas[2]->hora;
                    $entradasSalidas['salida'] = $filtradas[3]->hora;
                } elseif ($count === 3) {
                    $entradasSalidas['entrada'] = $filtradas[0]->hora;
                    $entradasSalidas['salida_almuerzo'] = $filtradas[1]->hora;
                    $entradasSalidas['salida'] = $filtradas[2]->hora;
                } elseif ($count === 2) {
                    $entradasSalidas['entrada'] = $filtradas[0]->hora;
                    $entradasSalidas['salida'] = $filtradas[1]->hora;
                } elseif ($count === 1) {
                    $entradasSalidas['entrada'] = $filtradas[0]->hora;
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

    public function export(Request $request)
    {
        // Obtener parámetros de la solicitud
        $empleado = $request->input('empleado');
        $inicio = $request->input('inicio');
        $fin = $request->input('fin');
        $data = $this->index(
            $request->merge([
                'empleado' => $empleado,
                'inicio' => $inicio,
                'fin' => $fin
            ])
        );
        
        $data = json_decode($data->content(), true);
        // Nombre del archivo CSV
        $fileName = 'reporte_marcaciones' . date('Y-m-d_H-i-s') . '.csv';

        // Crear el contenido del archivo CSV
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['CODIGO', 'EMPLEADO', 'FECHA', 'HORA', 'TIPO_MARCACION', 'CONTRATO', 'EQUIPO']; // Cambia los nombres de las columnas según tus datos

        $callback = function () use ($data, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($data as $row) {
                // Corregir la codificación del nombre del empleado
                // $row['empleado'] = mb_convert_encoding($row['empleado'], 'UTF-8', 'ISO-8859-1');
                if (!empty($row['entrada'])) {
                    fputcsv($file, [$row['empleado_id'],$row['empleado'], $row['fecha'], $row['entrada'], 'INGRESO', $row['tipo_contrato'], $row['biometrico']]);
                }
                if (!empty($row['salida_almuerzo'])) {
                    fputcsv($file, [$row['empleado_id'],$row['empleado'], $row['fecha'], $row['salida_almuerzo'], 'BREAK-OUT',  $row['tipo_contrato'], $row['biometrico']]);
                }
                if (!empty($row['entrada_almuerzo'])) {
                    fputcsv($file, [$row['empleado_id'],$row['empleado'], $row['fecha'], $row['entrada_almuerzo'], 'BREAK-IN', $row['tipo_contrato'], $row['biometrico']]);
                }
                if (!empty($row['salida'])) {
                    fputcsv($file, [$row['empleado_id'],$row['empleado'], $row['fecha'], $row['salida'], 'SALIDA', $row['tipo_contrato'], $row['biometrico']]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
