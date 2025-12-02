<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Requisiciones</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reporte de Requisiciones - Tesorería</h2>
        <p>Periodo: {{ $startDate }} - {{ $endDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Solicitante</th>
                <th>Depto</th>
                <th>Estatus</th>
                <th>Fecha</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisiciones as $req)
            <tr>
                <td>{{ $req->folio }}</td>
                <td>{{ $req->usuario->name ?? 'N/A' }}</td>
                <td>{{ $req->departamento->nombre ?? 'N/A' }}</td>
                <td>{{ $req->estatus->nombre ?? 'N/A' }}</td>
                <td>{{ $req->created_at->format('d/m/Y') }}</td>
                <td>
                    @php
                        $total = $req->cotizaciones->first()?->detalles->sum('subtotal') ?? 0;
                    @endphp
                    ${{ number_format($total, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

