<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create the table if it doesn't exist to avoid errors
        if (!Schema::hasTable('bhagats')) {
            Schema::create('bhagats', function (Blueprint $table) {
                $table->id();
                $table->string('user_id')->unique();
                $table->string('first_name');
                $table->string('last_name');
                $table->date('date_of_birth')->nullable();
                $table->string('guardian_type', 5);
                $table->string('guardian_name');
                
                $table->string('mobile_no', 15)->unique();
                $table->string('whatsapp_no', 15)->nullable();
                $table->string('email_id')->nullable();
                $table->string('aadhar_card_no', 12)->unique();

                // Current Address
                $table->string('current_addr_line_1');
                $table->string('current_addr_line_2')->nullable();
                $table->foreignId('current_state_id')->nullable()->constrained('states');
                $table->foreignId('current_district_id')->nullable()->constrained('districts');
                $table->foreignId('current_zilla_id')->nullable()->constrained('zillas');
                $table->string('current_pincode', 6)->nullable();

                // Permanent Address
                $table->boolean('same_as_current')->default(true);
                $table->string('perm_addr_line_1')->nullable();
                $table->string('perm_addr_line_2')->nullable();
                $table->foreignId('perm_state_id')->nullable()->constrained('states');
                $table->foreignId('perm_district_id')->nullable()->constrained('districts');
                $table->foreignId('perm_zilla_id')->nullable()->constrained('zillas');
                $table->string('perm_pincode', 6)->nullable();

                // Docs & Status
                $table->string('photo_path')->nullable();
                $table->string('aadhar_front_path')->nullable();
                $table->string('aadhar_rear_path')->nullable();
                $table->date('first_mantra_date')->nullable();
                $table->date('satnaam_mantra_date')->nullable();
                $table->date('sarnaam_mantra_date')->nullable();
                $table->enum('status', ['Active', 'Non-Active'])->default('Active');
                $table->boolean('blacklist_status')->default(false);

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bhagats');
    }
};
