<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImportCsvTest extends TestCase
{
    public function test_csv_simple_is_processed_and_returns_view()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent(
            'datos.csv',
            "Mes,Ingresos,Gastos Fijos,Gastos Dinamicos\n2024-01,2000,500,300"
        );

        $response = $this->post(route('analisis.importar.csv'), [
            'archivo_csv' => $file
        ]);

        $response->assertStatus(200);

        // Verifica que carga la vista correcta
        $response->assertViewIs('analisis.index');

        // Verifica que la sesión tiene datos
        $response->assertSessionHas('analisis_data');

        // Verifica mensaje de éxito
        $response->assertSessionHas('success');
    }
}
