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
        Schema::create('pet_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->text('adoption_description')->nullable();
            $table->text('health_description')->nullable();
            $table->text('adoption_condition')->nullable();
            $table->timestamps();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['adoption_description', 'health_description', 'adoption_condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the column to pets table
        Schema::table('pets', function (Blueprint $table) {
            $table->text('adoption_description')->nullable();
            $table->text('health_description')->nullable();
            $table->string('adoption_condition')->nullable();
        });

        Schema::dropIfExists('pet_details');
    }
};
