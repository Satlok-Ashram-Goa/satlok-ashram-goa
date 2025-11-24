<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            // Link to the Purchase Master record
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            // Link to the Book Master record
            $table->foreignId('book_id')->constrained()->onDelete('restrict');
            
            $table->integer('quantity');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('line_total', 10, 2); // Calculated Qty * Price
            $table->timestamps();
            
            // Ensure you can't add the same book twice to the same purchase order
            $table->unique(['purchase_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
