<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            // Link to the Book Master record (one inventory record per book)
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->integer('current_stock_qty')->default(0); // The running balance
            $table->timestamps();
            
            $table->unique('book_id'); // Ensure only one inventory record per book
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
