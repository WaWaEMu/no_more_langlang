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
        Schema::create('case_diary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adoption_case_id')->constrained('adoption_cases')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->json('photos');                     // 必填，1~5 張圖片路徑
            $table->text('content')->nullable();        // 選填，描述
            $table->string('location')->nullable();     // 選填，地點標記
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_diary_entries');
    }
};
