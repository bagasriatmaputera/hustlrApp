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
        Schema::table('portfolio_profiles', function (Blueprint $table) {
            $table->string('location')->nullable()->after('summary');
            $table->string('resume_url')->nullable()->after('location');
            $table->boolean('available_for_work')->default(true)->after('resume_url');
            $table->json('socials')->nullable()->after('available_for_work');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('portfolio_profiles', function (Blueprint $table) {
            $table->dropColumn(['location', 'resume_url', 'available_for_work', 'socials']);
        });
    }
};
