<?php

namespace App\Http\Requests\Recepcion;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequisicionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'folio' => 'required|unique:requisiciones,folio',
            'fecha_creacion' => 'required|date',
            'fecha_recepcion' => 'required|date',
            'hora_recepcion' => 'required',
            'concepto' => 'required|string|max:255',
            'id_departamento' => 'required|exists:departamentos,id_departamento',
            'id_clasificacion' => 'required|exists:clasificaciones,id_clasificacion',
            'id_usuario' => 'required|exists:users,id',
            'id_estatus' => 'required|exists:estatus,id_estatus'
        ];
    }
}