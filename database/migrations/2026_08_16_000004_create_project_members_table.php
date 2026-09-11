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
        Schema::create('project_members', function (Blueprint $table) {
            $table->foreignUuid('project_id')->constrained('projects', 'project_id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->string('role_in_project');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
