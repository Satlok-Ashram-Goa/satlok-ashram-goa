<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id')->unique(); // Unique Transaction ID
            $table->date('txn_date');          // Invoice Date
            $table->string('supplier_name');
            $table->string('invoice_no')->unique();
            $table->string('vehicle_no')->nullable();
            $table->decimal('total_qty', 10, 0)->default(0); // Calculated Total Quantity
            $table->decimal('total_amount', 10, 2)->default(0.00); // Calculated Total Amount
            $table->string('invoice_copy_path')->nullable(); // Upload field
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
