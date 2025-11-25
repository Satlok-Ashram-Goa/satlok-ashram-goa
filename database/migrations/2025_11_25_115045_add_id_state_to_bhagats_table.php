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
        Schema::table('bhagats', function (Blueprint $table) {
            // Add id_state_id field for Form Number generation (separate from address)
            $table->foreignId('id_state_id')->nullable()->after('user_id')->constrained('states')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bhagats', function (Blueprint $table) {
            $table->dropForeign(['id_state_id']);
            $table->dropColumn('id_state_id');
        });
    }
};
