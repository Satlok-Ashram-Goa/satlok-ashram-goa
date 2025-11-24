<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('book_sevas')) {
            Schema::create('book_sevas', function (Blueprint $table) {
                $table->id();
                $table->string('txn_id')->unique();
                $table->date('txn_date');
                
                // Link to the User (Admin/Sevadar) creating the form
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                
                $table->integer('total_sevadaar')->default(0)->comment('Optional field for head count');
                $table->integer('total_qty')->default(0);
                $table->decimal('total_amount', 10, 2)->default(0.00);
                
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('book_sevas');
    }
};
