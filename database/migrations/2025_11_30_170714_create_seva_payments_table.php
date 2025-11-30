<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seva_payments', function (Blueprint $table) {
            $table->id();
            $table->string('txn_id')->unique();
            $table->foreignId('daan_record_id')->constrained('daan_records')->onDelete('cascade');
            $table->date('txn_date');
            $table->string('payment_type'); // Cash, UPI
            $table->decimal('amount', 10, 2);
            $table->string('collection_location')->default('Naamdaan Kendra');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seva_payments');
    }
};
