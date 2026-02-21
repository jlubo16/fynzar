<?php
// app/Http/Requests/Analisis/RapidoRequest.php

namespace App\Http\Requests\Analisis;

use Illuminate\Foundation\Http\FormRequest;

class RapidoRequest extends FormRequest
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
            'ingresos' => 'required|array|min:1',
            'ingresos.*' => 'required|numeric|min:0',
            'gastos_fijos' => 'sometimes|array|min:1',
            'gastos_fijos.*' => 'sometimes|numeric|min:0',
            'gastos_dinamicos' => 'sometimes|array|min:1',
            'gastos_dinamicos.*' => 'sometimes|numeric|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ingresos.required' => 'Debes ingresar al menos un ingreso',
            'ingresos.*.required' => 'Todos los ingresos deben tener un valor',
            'ingresos.*.numeric' => 'Los ingresos deben ser valores numéricos',
            'ingresos.*.min' => 'Los ingresos no pueden ser negativos'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Asegurar que los arrays existen (convertir null a array vacío)
        $this->merge([
            'ingresos' => $this->ingresos ?? [],
            'gastos_fijos' => $this->gastos_fijos ?? [],
            'gastos_dinamicos' => $this->gastos_dinamicos ?? []
        ]);
    }
}