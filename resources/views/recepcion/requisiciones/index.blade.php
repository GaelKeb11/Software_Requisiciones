@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>Requisiciones de Recepción</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Folio</th>
                <th>Fecha Creación</th>
                <th>Fecha Recepción</th>
                <th>Concepto</th>
                <th>Departamento</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requisiciones as $requisicion)
            <tr>
                <td>{{ $requisicion->folio }}</td>
                <td>{{ $requisicion->fecha_creacion->format('d/m/Y') }}</td>
                <td>{{ $requisicion->fecha_recepcion->format('d/m/Y') }}</td>
                <td>{{ $requisicion->concepto }}</td>
                <td>{{ $requisicion->departamento->nombre }}</td>
                <td>{{ $requisicion->estatus->nombre }}</td>
                <td>
                    <a href="{{ route('recepcion.requisiciones.show', $requisicion) }}" class="btn btn-info">Ver</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection