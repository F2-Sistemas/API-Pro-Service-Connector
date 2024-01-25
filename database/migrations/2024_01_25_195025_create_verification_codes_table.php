<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Basic line note
            $table->string('value'); // Hashed value
            $table->string('token')->index(); // Value to get check code
            $table->dateTime('expires_in')->nullable()->index();
            $table->dateTime('checked_in')->nullable()->index();
            $table->string('provider')->nullable(); // Provider generator

            $table->timestamps();

            $table->index(['id']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};
