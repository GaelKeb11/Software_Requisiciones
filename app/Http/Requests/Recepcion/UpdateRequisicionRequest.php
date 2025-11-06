<?php

namespace App\Http\Requests\Recepcion;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequisicionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'folio' => 'sometimes|string|unique:requisiciones,folio,' . $this->requisicion . ',id_requisicion',
            'fecha_creacion' => 'sometimes|date',
            'fecha_recepcion' => 'sometimes|date',
            'hora_recepcion' => 'sometimes',
            'concepto' => 'sometimes|string|max:255',
            'id_departamento' => 'sometimes|exists:departamentos,id_departamento',
            'id_clasificacion' => 'sometimes|exists:clasificaciones,id_clasificacion',
            'id_usuario' => 'sometimes|exists:users,id',
            'id_estatus' => 'sometimes|exists:estatus,id_estatus'
        ];
    }
}
