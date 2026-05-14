<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('foster_venues', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->nullable()->after('longitude');
            $table->unsignedInteger('user_rating_count')->nullable()->after('rating');
            $table->string('primary_type_display_name')->nullable()->after('type');
            $table->string('business_status')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('foster_venues', function (Blueprint $table) {
            $table->dropColumn([
                'rating',
                'user_rating_count',
                'primary_type_display_name',
                'business_status',
            ]);
        });
    }
};
