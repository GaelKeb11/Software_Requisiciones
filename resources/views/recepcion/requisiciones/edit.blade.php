@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>Editar Requisición</h1>
    <form action="{{ route('recepcion.requisiciones.update', $requisicion) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="folio" class="form-label">Folio</label>
            <input type="text" class="form-control" id="folio" name="folio" value="{{ old('folio', $requisicion->folio) }}">
        </div>
        <div class="mb-3">
            <label for="fecha_creacion" class="form-label">Fecha de Creación</label>
            <input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" value="{{ old('fecha_creacion', $requisicion->fecha_creacion->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="fecha_recepcion" class="form-label">Fecha de Recepción</label>
            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ old('fecha_recepcion', $requisicion->fecha_recepcion->format('Y-m-d')) }}">
        </div>
        <div class="mb-3">
            <label for="hora_recepcion" class="form-label">Hora de Recepción</label>
            <input type="time" class="form-control" id="hora_recepcion" name="hora_recepcion" value="{{ old('hora_recepcion', $requisicion->hora_recepcion) }}">
        </div>
        <div class="mb-3">
            <label for="concepto" class="form-label">Concepto</label>
            <textarea class="form-control" id="concepto" name="concepto">{{ old('concepto', $requisicion->concepto) }}</textarea>
        </div>
        <div class="mb-3">
            <label for="id_departamento" class="form-label">Departamento</label>
            <select class="form-control" id="id_departamento" name="id_departamento">
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id_departamento }}" @if($departamento->id_departamento == old('id_departamento', $requisicion->id_departamento)) selected @endif>{{ $departamento->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_clasificacion" class="form-label">Clasificación</label>
            <select class="form-control" id="id_clasificacion" name="id_clasificacion">
                @foreach($clasificaciones as $clasificacion)
                    <option value="{{ $clasificacion->id_clasificacion }}" @if($clasificacion->id_clasificacion == old('id_clasificacion', $requisicion->id_clasificacion)) selected @endif>{{ $clasificacion->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Usuario Asignado</label>
            <select class="form-control" id="id_usuario" name="id_usuario">
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" @if($usuario->id == old('id_usuario', $requisicion->id_usuario)) selected @endif>{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_estatus" class="form-label">Estatus</label>
            <select class="form-control" id="id_estatus" name="id_estatus">
                @foreach($estatus as $est)
                    <option value="{{ $est->id_estatus }}" @if($est->id_estatus == old('id_estatus', $requisicion->id_estatus)) selected @endif>{{ $est->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection
