<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('adoption_cases', function (Blueprint $table) {
            // Allow nullable for manual case creation (counterpart may not be registered)
            $table->foreignId('adopter_id')->nullable()->change();
            $table->foreignId('owner_id')->nullable()->change();

            // Track where the case was created from
            $table->string('source', 20)->default('platform')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('adoption_cases', function (Blueprint $table) {
            $table->foreignId('adopter_id')->nullable(false)->change();
            $table->foreignId('owner_id')->nullable(false)->change();
            $table->dropColumn('source');
        });
    }
};
