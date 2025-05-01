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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('city')->nullable();
            $table->string('town')->nullable();
            $table->boolean('is_stray')->nullable();
            $table->string('type')->nullable();
            $table->string('color')->nullable();
            $table->string('fur_type')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->boolean('is_neuter')->nullable();
            $table->text('description')->nullable();
            $table->text('health_description')->nullable();
            $table->string('condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
