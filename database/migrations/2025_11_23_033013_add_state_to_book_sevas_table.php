<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('book_sevas', function (Blueprint $table) {
            // Add State ID after user_id
            $table->foreignId('state_id')->nullable()->after('user_id')->constrained('states')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('book_sevas', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn('state_id');
        });
    }
};
