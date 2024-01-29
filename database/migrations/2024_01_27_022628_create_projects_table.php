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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->longText('description');
            $table->integer('status')->index();
            $table->integer('max_of_bids')->index();
            $table->integer('total_of_bids')->index();
            $table->integer('urgent')->default(false)->index();
            $table->datetime('expires_in')->index()->nullable();
            $table->unsignedBigInteger('project_category_id')->index();
            $table->unsignedBigInteger('owner_id')->index();
            $table->json('extra_info')->nullable();
            $table->string('coin_price')->nullable()->index();
            $table->string('percent_discount_applied')->nullable()->index();
            $table->boolean('promoted')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('owner_id')->references('id')
                ->on('users')->onDelete('cascade'); // cascade|set null

            $table->foreign('project_category_id')->references('id')
                ->on('project_categories')->onDelete('cascade'); // cascade|set null

            $table->index(['id']);
            $table->index(['deleted_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
