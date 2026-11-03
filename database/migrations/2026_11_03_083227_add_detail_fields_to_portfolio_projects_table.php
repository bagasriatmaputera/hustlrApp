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
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->json('media_gallery')->nullable()->after('gif_image');
            $table->json('problem_solution')->nullable()->after('description');
            $table->json('challenges_learnings')->nullable()->after('problem_solution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropColumn(['media_gallery', 'problem_solution', 'challenges_learnings']);
        });
    }
};
