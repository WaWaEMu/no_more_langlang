<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add nullable short_uid column (12-char random string)
            $table->string('uuid', 12)->nullable()->unique()->after('id');
        });

        // Populate short UIDs for existing users
        User::all()->each(function ($user) {
            if (!$user->uuid) {
                $user->uuid = Str::random(12);
                $user->save();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Set uuid column to not nullable
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
