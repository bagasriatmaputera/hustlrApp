<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_job_descs', function (Blueprint $table) {
            $table->uuid('job_desc_id')->primary();
            $table->foreignUuid('experience_id')->constrained('portfolio_experiences', 'experience_id')->cascadeOnDelete();
            $table->text('description');
            $table->json('learning_outcomes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_job_descs');
    }
};
