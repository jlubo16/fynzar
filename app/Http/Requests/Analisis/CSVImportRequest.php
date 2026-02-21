<?php
// app/Http/Requests/Analisis/CSVImportRequest.php

namespace App\Http\Requests\Analisis;

use Illuminate\Foundation\Http\FormRequest;

class CSVImportRequest extends FormRequest
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
            'archivo_csv' => 'required|file|mimes:csv,txt'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'archivo_csv.required' => 'Debes seleccionar un archivo CSV',
            'archivo_csv.file' => 'El archivo debe ser válido',
            'archivo_csv.mimes' => 'El archivo debe ser de tipo CSV o TXT'
        ];
    }
}