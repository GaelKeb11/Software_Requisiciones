@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>Nueva Requisición</h1>
    <form action="{{ route('recepcion.requisiciones.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="folio" class="form-label">Folio</label>
            <input type="text" class="form-control" id="folio" name="folio" value="{{ old('folio') }}">
        </div>
        <div class="mb-3">
            <label for="fecha_creacion" class="form-label">Fecha de Creación</label>
            <input type="date" class="form-control" id="fecha_creacion" name="fecha_creacion" value="{{ old('fecha_creacion') }}">
        </div>
        <div class="mb-3">
            <label for="fecha_recepcion" class="form-label">Fecha de Recepción</label>
            <input type="date" class="form-control" id="fecha_recepcion" name="fecha_recepcion" value="{{ old('fecha_recepcion') }}">
        </div>
        <div class="mb-3">
            <label for="hora_recepcion" class="form-label">Hora de Recepción</label>
            <input type="time" class="form-control" id="hora_recepcion" name="hora_recepcion" value="{{ old('hora_recepcion') }}">
        </div>
        <div class="mb-3">
            <label for="concepto" class="form-label">Concepto</label>
            <textarea class="form-control" id="concepto" name="concepto">{{ old('concepto') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="id_departamento" class="form-label">Departamento</label>
            <select class="form-control" id="id_departamento" name="id_departamento">
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id_departamento }}">{{ $departamento->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_clasificacion" class="form-label">Clasificación</label>
            <select class="form-control" id="id_clasificacion" name="id_clasificacion">
                @foreach($clasificaciones as $clasificacion)
                    <option value="{{ $clasificacion->id_clasificacion }}">{{ $clasificacion->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_usuario" class="form-label">Usuario Asignado</label>
            <select class="form-control" id="id_usuario" name="id_usuario">
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="id_estatus" class="form-label">Estatus</label>
            <select class="form-control" id="id_estatus" name="id_estatus">
                @foreach($estatus as $est)
                    <option value="{{ $est->id_estatus }}">{{ $est->nombre }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>
@endsection
