<?php

namespace App\Http\Controllers\Recepcion;

use App\Http\Controllers\Controller;
use App\Models\Recepcion\Requisicion;
use Illuminate\Http\Request;
use App\Models\Recepcion\Departamento;
use App\Models\Recepcion\Clasificacion;
use App\Models\Recepcion\Estatus;
use App\Models\User;
use App\Http\Requests\Recepcion\StoreRequisicionRequest;
use App\Http\Requests\Recepcion\UpdateRequisicionRequest;

class RequisicionController extends Controller
{
    public function index()
    {
        $requisiciones = Requisicion::with(['departamento', 'clasificacion', 'usuario', 'estatus'])->get();
        return view('recepcion.requisiciones.index', compact('requisiciones'));
    }

    public function create()
    {
        $departamentos = Departamento::all();
        $clasificaciones = Clasificacion::all();
        $usuarios = User::all();
        $estatus = Estatus::all();
        return view('recepcion.requisiciones.create', compact('departamentos', 'clasificaciones', 'usuarios', 'estatus'));
    }

    public function store(StoreRequisicionRequest $request)
    {
        $requisicion = Requisicion::create($request->validated());
        return redirect()->route('recepcion.requisiciones.show', $requisicion)->with('success', 'Requisición creada correctamente.');
    }

    public function show(Requisicion $requisicion)
    {
        return view('recepcion.requisiciones.show', compact('requisicion'));
    }

    public function edit(Requisicion $requisicion)
    {
        $departamentos = Departamento::all();
        $clasificaciones = Clasificacion::all();
        $usuarios = User::all();
        $estatus = Estatus::all();
        return view('recepcion.requisiciones.edit', compact('requisicion', 'departamentos', 'clasificaciones', 'usuarios', 'estatus'));
    }

    public function update(UpdateRequisicionRequest $request, Requisicion $requisicion)
    {
        $requisicion->update($request->validated());
        return redirect()->route('recepcion.requisiciones.show', $requisicion)->with('success', 'Requisición actualizada correctamente.');
    }

    public function destroy(Requisicion $requisicion)
    {
        $requisicion->delete();
        return redirect()->route('recepcion.requisiciones.index');
    }
}