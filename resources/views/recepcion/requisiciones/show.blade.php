@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>Requisición: {{ $requisicion->folio }}</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Concepto:</strong> {{ $requisicion->concepto }}</p>
            <p><strong>Fecha Creación:</strong> {{ $requisicion->fecha_creacion->format('d/m/Y') }}</p>
            <p><strong>Fecha Recepción:</strong> {{ $requisicion->fecha_recepcion->format('d/m/Y') }}</p>
            <p><strong>Hora Recepción:</strong> {{ $requisicion->hora_recepcion }}</p>
            <p><strong>Departamento:</strong> {{ $requisicion->departamento->nombre }}</p>
            <p><strong>Clasificación:</strong> {{ $requisicion->clasificacion->nombre }}</p>
            <p><strong>Usuario Asignado:</strong> {{ $requisicion->usuario->name }}</p>
            <p><strong>Estatus:</strong> {{ $requisicion->estatus->nombre }}</p>
            
            <h3>Documentos Relacionados</h3>
            <ul>
                @foreach($requisicion->documentos as $documento)
                <li>{{ $documento->nombre_archivo }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection