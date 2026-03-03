<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->foreignId('adoption_form_template_id')
                ->nullable()
                ->after('status')
                ->constrained('adoption_form_templates')
                ->nullOnDelete();
        });

        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->json('custom_fields')->nullable()->after('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('adoption_form_template_id');
        });

        Schema::table('adoption_applications', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
