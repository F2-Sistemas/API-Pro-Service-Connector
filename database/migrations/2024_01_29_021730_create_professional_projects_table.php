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
        Schema::create('professional_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('professional_id')->index();
            $table->unsignedBigInteger('project_id')->index();
            $table->unsignedBigInteger('professional_project_status')->nullable()->index();
            $table->longText('personal_note')->nullable();
            $table->timestamp('archived_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('professional_id')->references('id')
                ->on('professionals')->onDelete('cascade'); // cascade|set null

            $table->foreign('project_id')->references('id')
                ->on('projects')->onDelete('cascade'); // cascade|set null

            $table->index(['id']);
            $table->index(['deleted_at']);
            $table->index(['created_at']);
            $table->index(['updated_at']);
            $table->unique(['professional_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professional_projects');
    }
};
