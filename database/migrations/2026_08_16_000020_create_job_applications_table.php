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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->uuid('job_application_id')->primary();
            $table->foreignId('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('position');
            $table->string('contract_status')->default('full_time');
            $table->string('apply_status')->default('applied');
            $table->string('salary')->nullable();
            $table->string('location')->nullable();
            $table->string('workplace_type')->default('remote');
            $table->string('job_url')->nullable();
            $table->text('benefits')->nullable();
            $table->text('notes')->nullable();
            $table->date('applied_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
