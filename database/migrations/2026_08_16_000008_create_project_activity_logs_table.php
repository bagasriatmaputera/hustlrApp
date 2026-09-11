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
        Schema::create('project_activity_logs', function (Blueprint $table) {
            $table->uuid('log_id')->primary();
            $table->foreignUuid('project_id')->constrained('projects', 'project_id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->string('action');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_activity_logs');
    }
};
