<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_technologies', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained('projects', 'project_id');
            $table->foreignUuid('tech_id')->constrained('technologies', 'tech_id');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['project_id', 'tech_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_technologies');
    }
};
