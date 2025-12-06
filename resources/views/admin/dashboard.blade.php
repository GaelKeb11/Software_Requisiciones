@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Panel de Administración</h1>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Usuarios en línea</h5>
                    <p>{{ $usuariosEnLinea }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Total de usuarios</h5>
                    <p>{{ $totalUsuarios }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gráficas con Chart.js -->
</div>
@endsection
