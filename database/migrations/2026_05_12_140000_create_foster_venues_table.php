<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foster_venues', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 12)->unique();
            $table->string('name');
            $table->string('type');                         // cafe / restaurant / shelter / vet_clinic
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('city');
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('business_hours')->nullable();
            $table->string('website_url')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('line_id')->nullable();
            $table->json('pet_types')->nullable();          // ['cat', 'dog']
            $table->json('services')->nullable();           // ['adoption', 'foster', 'cafe']
            $table->boolean('is_verified')->default(false);
            $table->string('status')->default('active');    // active / closed / pending
            $table->foreignId('user_id')->nullable()
                ->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foster_venues');
    }
};
