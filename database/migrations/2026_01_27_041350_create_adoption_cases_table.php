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
        Schema::create('adoption_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->foreignId('adopter_id')->constrained('users')->comment('The person who adopted');
            $table->foreignId('owner_id')->constrained('users')->comment('The original rescuer/owner');
            $table->foreignId('application_id')->nullable()->constrained('adoption_applications')->onDelete('set null');
            $table->string('status')->default('active');
            $table->json('tracking_config')->nullable();
            $table->dateTime('next_report_due_at')->nullable();
            $table->dateTime('last_report_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_cases');
    }
};
