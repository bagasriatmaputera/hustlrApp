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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->uuid('doc_id')->primary();
            $table->foreignUuid('project_id')->constrained('projects', 'project_id');
            $table->string('doc_name');
            $table->string('doc_url');
            $table->foreignId('uploaded_by')->constrained('users', 'id');
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
