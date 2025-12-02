<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Requisiciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        .status-completada { background-color: #10b981; } /* Success */
        .status-rechazada { background-color: #ef4444; } /* Danger */
        .status-aprobada { background-color: #10b981; } /* Success */
        .section-title {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 14px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Requisiciones - Tesorería</h1>
        <p>Fecha de emisión: {{ now()->format('d/m/Y H:i') }}</p>
        <p>Filtro aplicado: {{ $filtro }}</p>
    </div>

    @if($requisiciones->isEmpty())
        <p>No se encontraron registros para este reporte.</p>
    @else
        @foreach($requisiciones->groupBy('estatus.nombre') as $estatus => $items)
            <h3 class="section-title">Estatus: {{ $estatus }} ({{ $items->count() }})</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 10%;">Folio</th>
                        <th style="width: 15%;">Fecha</th>
                        <th style="width: 30%;">Concepto</th>
                        <th style="width: 20%;">Solicitante</th>
                        <th style="width: 15%;">Departamento</th>
                        <th style="width: 10%;">Total Aprox.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $req)
                        <tr>
                            <td>{{ $req->folio }}</td>
                            <td>{{ $req->fecha_creacion->format('d/m/Y') }}</td>
                            <td>{{ Str::limit($req->concepto, 50) }}</td>
                            <td>{{ $req->usuario->name ?? 'N/A' }}</td>
                            <td>{{ $req->departamento->nombre ?? 'N/A' }}</td>
                            <td>
                                @php
                                    $total = 0;
                                    // Intentar obtener total de cotización aprobada si existe
                                    $cotizacion = $req->cotizaciones->first(); 
                                    if($cotizacion) {
                                        $total = $cotizacion->total_cotizado;
                                    }
                                @endphp
                                ${{ number_format($total, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>

