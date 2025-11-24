<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

// In: database/migrations/YYYY_MM_DD_create_pincodes_table.php

public function up(): void
{
    Schema::create('pincodes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('district_id')->constrained()->onDelete('cascade');
        $table->string('pincode', 10)->unique();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
