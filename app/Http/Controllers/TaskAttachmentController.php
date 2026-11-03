<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TaskAttachmentController extends Controller
{
    public function download(Request $request, Project $project, Task $task, TaskAttachment $attachment): StreamedResponse
    {
        $this->authorizeAttachmentAccess($request, $project, $task, $attachment);

        $disk = $this->resolveDisk($attachment->file_path);
        abort_unless($disk->exists($attachment->file_path), 404);

        return $disk->download($attachment->file_path, $attachment->file_name);
    }

    public function preview(Request $request, Project $project, Task $task, TaskAttachment $attachment): StreamedResponse
    {
        $this->authorizeAttachmentAccess($request, $project, $task, $attachment);

        abort_unless($attachment->isPreviewable(), 415, __('Preview is not available for this file type.'));

        $disk = $this->resolveDisk($attachment->file_path);
        abort_unless($disk->exists($attachment->file_path), 404);

        return $disk->response($attachment->file_path, $attachment->file_name, [
            'Content-Type' => $attachment->mime_type === 'text/csv'
                ? 'text/plain; charset=UTF-8'
                : (string) $attachment->mime_type,
            'X-Content-Type-Options' => 'nosniff',
        ], 'inline');
    }

    private function resolveDisk(string $path): Filesystem
    {
        $defaultDisk = (string) config('filesystems.default', 's3');
        $disk = Storage::disk($defaultDisk);

        if (! $disk->exists($path) && Storage::disk('local')->exists($path)) {
            return Storage::disk('local');
        }

        return $disk;
    }

    private function authorizeAttachmentAccess(Request $request, Project $project, Task $task, TaskAttachment $attachment): void
    {
        $user = $request->user();

        if ($user === null || ! $user->can('IPS - PROJECT - Can Show Tasks')) {
            abort(403, __('Unauthorized action.'));
        }

        abort_if(! Project::visibleTo($user)->where('project_id', $project->project_id)->exists(), 403, __('Unauthorized action.'));
        abort_if($task->project_id !== $project->project_id, 404);
        abort_if($attachment->task_id !== $task->task_id, 404);
    }
}
