<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->uuid('project_id')->primary();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->string('gif_image')->nullable();
            $table->json('tech_stack');
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('is_live')->default(false);
            $table->boolean('is_repo')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_projects');
    }
};
