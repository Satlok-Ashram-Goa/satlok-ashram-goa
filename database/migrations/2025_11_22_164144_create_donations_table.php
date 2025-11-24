<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            // Receipt Number (e.g., DON-0001)
            $table->string('receipt_no')->unique();
            $table->date('donation_date');
            
            // Link to Bhagat (Member) - Optional for guest donations
            $table->foreignId('bhagat_id')->nullable()->constrained('bhagats')->onDelete('set null');
            
            // If not a registered Bhagat, capture name manually
            $table->string('donor_name')->nullable();
            
            // Payment Details
            $table->decimal('amount', 10, 2);
            $table->string('payment_mode'); // Cash, UPI, Cheque, Bank Transfer
            $table->string('purpose'); // General, Bhandara, Construction, etc.
            $table->string('remarks')->nullable();
            
            // Metadata
            $table->foreignId('created_by_user_id')->nullable()->constrained('users'); // Track admin who created it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
