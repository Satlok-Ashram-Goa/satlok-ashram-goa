<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_balances', function (Blueprint $table) {
            // Links the record to the user who created it (the admin user)
            $table->foreignId('adjusted_by_user_id')
                  ->nullable() 
                  ->constrained('users') 
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_balances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('adjusted_by_user_id');
        });
    }
};
