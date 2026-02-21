<?php
// app/Http/Requests/Analisis/DetalladoRequest.php

namespace App\Http\Requests\Analisis;

use Illuminate\Foundation\Http\FormRequest;

class DetalladoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ingresos_detallados' => 'required|array|min:1',
            'ingresos_detallados.*.mes' => 'required|date_format:Y-m',
            'ingresos_detallados.*.monto' => 'required|numeric|min:0',
            'gastos_detallados' => 'required|array|min:1',
            'gastos_detallados.*.fecha' => 'required|date',
            'gastos_detallados.*.categoria_id' => 'required|exists:categories,id',
            'gastos_detallados.*.subcategoria_id' => 'nullable|exists:subcategories,id',
            'gastos_detallados.*.monto' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ingresos_detallados.required' => 'Debes ingresar al menos un ingreso detallado',
            'ingresos_detallados.*.mes.required' => 'El mes es requerido',
            'ingresos_detallados.*.mes.date_format' => 'El formato del mes debe ser YYYY-MM',
            'ingresos_detallados.*.monto.required' => 'El monto es requerido',
            'ingresos_detallados.*.monto.numeric' => 'El monto debe ser un número',
            'ingresos_detallados.*.monto.min' => 'El monto no puede ser negativo',
            
            'gastos_detallados.required' => 'Debes ingresar al menos un gasto detallado',
            'gastos_detallados.*.fecha.required' => 'La fecha es requerida',
            'gastos_detallados.*.fecha.date' => 'La fecha debe ser válida',
            'gastos_detallados.*.categoria_id.required' => 'La categoría es requerida',
            'gastos_detallados.*.categoria_id.exists' => 'La categoría seleccionada no existe',
            'gastos_detallados.*.monto.required' => 'El monto es requerido',
            'gastos_detallados.*.monto.numeric' => 'El monto debe ser un número',
            'gastos_detallados.*.monto.min' => 'El monto no puede ser negativo',
        ];
    }
}