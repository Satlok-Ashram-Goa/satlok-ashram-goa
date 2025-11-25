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
            // Add 4 attendance date fields
            $table->date('attendance_date_1')->nullable()->after('first_mantra_date');
            $table->date('attendance_date_2')->nullable()->after('attendance_date_1');
            $table->date('attendance_date_3')->nullable()->after('attendance_date_2');
            $table->date('attendance_date_4')->nullable()->after('attendance_date_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bhagats', function (Blueprint $table) {
            $table->dropColumn(['attendance_date_1', 'attendance_date_2', 'attendance_date_3', 'attendance_date_4']);
        });
    }
};
