<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Swaps the foreign key from district_id to zilla_id in the pincodes table.
     */
    public function up(): void
    {
        Schema::table('pincodes', function (Blueprint $table) {
            // 1. Drop the old foreign key constraint and column
            $table->dropConstrainedForeignId('district_id'); 
            
            // 2. Add the new foreign key column linked to the zillas table
            $table->foreignId('zilla_id')
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * Restores the original district_id column.
     */
    public function down(): void
    {
        Schema::table('pincodes', function (Blueprint $table) {
            // 1. Drop the new zilla_id foreign key and column
            $table->dropConstrainedForeignId('zilla_id');
            
            // 2. Restore the original district_id foreign key column
            $table->foreignId('district_id')
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
        });
    }
};
