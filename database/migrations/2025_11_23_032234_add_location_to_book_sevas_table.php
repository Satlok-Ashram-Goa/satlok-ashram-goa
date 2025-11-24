<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_sevas', function (Blueprint $table) {
            // Adding the new Foreign Keys (Nullable, so old records don't break)
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('zilla_id')->nullable()->constrained('zillas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('book_sevas', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
            
            $table->dropForeign(['zilla_id']);
            $table->dropColumn('zilla_id');
        });
    }
};
