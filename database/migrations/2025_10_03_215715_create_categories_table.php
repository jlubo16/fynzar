<?php
// database/migrations/2024_01_01_create_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Transporte, Alimentación, etc.
            $table->string('icon')->nullable(); // bi-car-front, bi-cart, etc.
            $table->string('color')->default('#007bff'); // Color para gráficos
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('subcategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Gasolina, Supermercado, etc.
            $table->text('keywords')->nullable(); // Palabras clave para autodetectar
            $table->timestamps();
        });

        // Modificar la tabla de gastos (si existe) o crear transacciones
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->foreignId('category_id')->constrained();
            $table->foreignId('subcategory_id')->nullable()->constrained();
            $table->date('date');
            $table->json('metadata')->nullable(); // Datos adicionales
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('subcategories');
        Schema::dropIfExists('categories');
    }
}