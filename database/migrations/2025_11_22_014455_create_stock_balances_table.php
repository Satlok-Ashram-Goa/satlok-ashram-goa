<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id')->unique(); // Unique Transaction ID
            $table->date('closing_balance_month'); // The date of the stocktake
            $table->integer('total_qty'); // Final calculated quantity across all books
            $table->json('adjustment_details'); // Stores the final Qty of every book
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
