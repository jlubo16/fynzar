<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => '🏠 Vivienda',
                'icon' => 'bi-house',
                'color' => '#dc3545',
                'subcategories' => ['Alquiler/Hipoteca', 'Luz', 'Agua', 'Gas', 'Internet', 'Mantenimiento', 'Decoración']
            ],
            [
                'name' => '🚗 Transporte',
                'icon' => 'bi-car-front',
                'color' => '#fd7e14',
                'subcategories' => ['Gasolina', 'Pasajes', 'Mantenimiento', 'Parqueo', 'Peajes', 'Seguro Vehicular']
            ],
            [
                'name' => '🛒 Alimentación',
                'icon' => 'bi-cart',
                'color' => '#28a745',
                'subcategories' => ['Supermercado', 'Mercado', 'Restaurantes', 'Comida Rápida', 'Delivery']
            ],
            [
                'name' => '🏥 Salud',
                'icon' => 'bi-heart-pulse',
                'color' => '#e83e8c',
                'subcategories' => ['Medicinas', 'Consultas Médicas', 'Seguro Salud', 'Dentista', 'Óptica', 'Gimnasio']
            ],
            [
                'name' => '🎓 Educación',
                'icon' => 'bi-book',
                'color' => '#6f42c1',
                'subcategories' => ['Colegio', 'Universidad', 'Cursos', 'Libros', 'Materiales', 'Actividades Extra']
            ],
            [
                'name' => '🎮 Entretenimiento',
                'icon' => 'bi-controller',
                'color' => '#20c997',
                'subcategories' => ['Cine', 'Streaming', 'Videojuegos', 'Conciertos', 'Deportes', 'Hobbies']
            ],
            [
                'name' => '👕 Vestimenta',
                'icon' => 'bi-bag',
                'color' => '#17a2b8',
                'subcategories' => ['Ropa', 'Calzado', 'Accesorios', 'Lavandería', 'Reparaciones']
            ],
            [
                'name' => '💼 Otros Gastos',
                'icon' => 'bi-wallet',
                'color' => '#6c757d',
                'subcategories' => ['Regalos', 'Donaciones', 'Impuestos', 'Multas', 'Gastos Bancarios']
            ]
        ];

        foreach ($categories as $categoryData) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $categoryData['name'],
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($categoryData['subcategories'] as $subcatName) {
                DB::table('subcategories')->insert([
                    'category_id' => $categoryId,
                    'name' => $subcatName,
                    'keywords' => $this->generateKeywords($subcatName),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function generateKeywords($name)
    {
        $keywords = [
            strtolower($name),
            strtolower(str_replace(' ', '', $name))
        ];

        $wordMap = [
            'Gasolina' => ['gas', 'combustible', 'estación', 'bomba'],
            'Supermercado' => ['mercado', 'compra', 'alimentos', 'despensa'],
            'Restaurantes' => ['comida', 'cenar', 'almorzar', 'menu'],
            'Alquiler/Hipoteca' => ['renta', 'casa', 'departamento', 'vivienda'],
            'Luz' => ['electricidad', 'energía', 'luz eléctrica'],
            'Agua' => ['agua potable', 'servicio agua'],
            'Mantenimiento' => ['reparación', 'arreglo', 'fix'],
            'Medicinas' => ['farmacia', 'remedios', 'pastillas'],
            'Consultas Médicas' => ['doctor', 'médico', 'consulta'],
            'Colegio' => ['escuela', 'colegiatura', 'matrícula'],
        ];

        if (isset($wordMap[$name])) {
            $keywords = array_merge($keywords, $wordMap[$name]);
        }

        return json_encode($keywords);
    }
}