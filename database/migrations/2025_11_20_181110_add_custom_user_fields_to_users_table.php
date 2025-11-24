<?php

// In: database/migrations/2025_11_20_181110_add_custom_user_fields_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations (Applying the changes).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1. Modify existing column to be nullable (as we use first/last name now)
            $table->string('name')->nullable()->change();

            // 2. Add new fields
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('mobile_no', 20)->nullable()->after('email'); 
            
            // 3. Add the tracking column for the dashboard display
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations (Undoing the changes).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverting the columns we added
            $table->dropColumn(['last_login_at', 'mobile_no', 'last_name', 'first_name']);

            // Revert 'name' back to its original state (not nullable)
            $table->string('name')->nullable(false)->change(); 
        });
    }
};
