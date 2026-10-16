<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill: every existing project gets its creator as Owner member.
        // Acting as upsert to avoid primary-key collisions.
        DB::table('projects')
            ->orderBy('project_id')
            ->chunkById(500, function ($projects) {
                foreach ($projects as $project) {
                    $exists = DB::table('project_members')
                        ->where('project_id', $project->project_id)
                        ->where('user_id', $project->created_by)
                        ->exists();

                    if (! $exists) {
                        DB::table('project_members')->insert([
                            'project_id' => $project->project_id,
                            'user_id' => $project->created_by,
                            'role_in_project' => 'Owner',
                            'joined_at' => $project->created_at,
                            'created_at' => $project->created_at,
                            'updated_at' => $project->updated_at,
                        ]);
                    }
                }
            }, 'project_id');

        // Index tuned for permission/filter queries (where user_id = ...).
        Schema::table('project_members', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('project_members', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
    }
};
