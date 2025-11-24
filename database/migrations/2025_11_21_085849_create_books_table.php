// In: database/migrations/YYYY_MM_DD_create_books_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('sku_id')->unique();
            $table->string('name');
            $table->string('language', 50);
            $table->decimal('price', 8, 2); // Stores price up to 999999.99
            $table->boolean('can_be_sold')->default(true); // Yes/No Toggle
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
