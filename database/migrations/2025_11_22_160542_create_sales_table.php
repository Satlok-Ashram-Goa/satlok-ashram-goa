<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id')->unique();
            $table->date('txn_date');
            // Link to the Bhagat (Receiver/Buyer)
            $table->foreignId('bhagat_id')->constrained('bhagats')->onDelete('cascade');
            $table->integer('total_qty')->default(0);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
