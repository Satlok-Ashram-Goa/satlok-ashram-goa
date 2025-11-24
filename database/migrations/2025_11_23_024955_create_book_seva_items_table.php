<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('book_seva_items')) {
            Schema::create('book_seva_items', function (Blueprint $table) {
                $table->id();
                // Link to Header
                $table->foreignId('book_seva_id')->constrained('book_sevas')->onDelete('cascade');
                
                // Link to Book Master
                $table->foreignId('book_id')->constrained('books')->onDelete('restrict');
                
                $table->integer('quantity');
                $table->decimal('price', 10, 2);
                $table->decimal('amount', 10, 2); // Line Total
                
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_seva_items');
    }
};
