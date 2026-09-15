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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('health_status')->default('On Track')->after('status_id');
            $table->string('link_repository')->nullable()->after('link_prod');
            $table->string('link_docs')->nullable()->after('link_repository');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['health_status', 'link_repository', 'link_docs']);
        });
    }
};
