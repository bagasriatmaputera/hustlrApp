<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_experiences', function (Blueprint $table) {
            $table->uuid('experience_id')->primary();
            $table->string('company_name');
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('location');
            $table->string('workplace_type');
            $table->string('contract_type');
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_experiences');
    }
};
