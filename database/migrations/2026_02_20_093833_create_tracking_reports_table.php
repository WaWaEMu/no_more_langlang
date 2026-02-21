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
        Schema::create('tracking_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adoption_case_id')->constrained()->onDelete('cascade');
            $table->foreignId('reporter_id')->constrained('users')->comment('The adopter who submitted the report');
            $table->text('content');
            $table->json('images')->nullable()->comment('Array of image paths, max 3');
            $table->dateTime('reported_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_reports');
    }
};
