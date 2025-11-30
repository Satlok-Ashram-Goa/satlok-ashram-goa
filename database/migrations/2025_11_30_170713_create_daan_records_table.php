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
        Schema::create('daan_records', function (Blueprint $table) {
            $table->id();
            $table->string('pledge_id')->unique();
            $table->date('pledge_date');
            $table->foreignId('bhagat_id')->constrained('bhagats')->onDelete('cascade');
            $table->foreignId('seva_master_id')->constrained('seva_masters')->onDelete('cascade');
            $table->decimal('original_amount', 10, 2);
            $table->enum('status', ['Pending', 'In Progress', 'Completed'])->default('Pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daan_records');
    }
};
