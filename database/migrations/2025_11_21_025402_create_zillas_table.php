<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zillas', function (Blueprint $table) {
            $table->id();
            // Foreign key linking to the 'districts' table
            $table->foreignId('district_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
            
            // Ensures a Zilla name is unique within a District
            $table->unique(['district_id', 'name']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zillas');
    }
};
