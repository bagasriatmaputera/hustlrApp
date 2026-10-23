<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\Show;
use App\Models\Project;
use App\Models\ProjectStatus;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TaskAttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_pdf_attachment_when_creating_task(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with attachment')
            ->set('newTaskAttachments', [UploadedFile::fake()->create('specification.pdf', 100, 'application/pdf')])
            ->call('createTask')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        $this->assertSame('specification.pdf', $attachment->file_name);
        $this->assertSame('application/pdf', $attachment->mime_type);
        $this->assertGreaterThan(0, $attachment->file_size);
        Storage::disk('local')->assertExists($attachment->file_path);
    }

    public function test_user_can_add_attachment_when_updating_task(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Editable task',
            'status' => 'Backlog',
            'priority' => 'Medium',
        ]);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('openEditTaskModal', $task->task_id)
            ->set('newTaskAttachments', [UploadedFile::fake()->create('notes.txt', 10, 'text/plain')])
            ->call('updateTask')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        $this->assertSame($task->task_id, $attachment->task_id);
        $this->assertSame('notes.txt', $attachment->file_name);
        Storage::disk('local')->assertExists($attachment->file_path);
    }

    public function test_jpeg_attachment_is_compressed_to_webp(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with image')
            ->set('newTaskAttachments', [UploadedFile::fake()->image('photo.jpg', 3000, 2000)])
            ->call('createTask')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        $this->assertSame('photo.webp', $attachment->file_name);
        $this->assertSame('image/webp', $attachment->mime_type);
        Storage::disk('local')->assertExists($attachment->file_path);

        [$width, $height] = getimagesize(Storage::disk('local')->path($attachment->file_path));

        $this->assertLessThanOrEqual(1920, $width);
        $this->assertLessThanOrEqual(1920, $height);
    }

    public function test_gif_attachment_keeps_original_format(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with gif')
            ->set('newTaskAttachments', [UploadedFile::fake()->image('animation.gif', 100, 100)])
            ->call('createTask')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        $this->assertSame('animation.gif', $attachment->file_name);
        $this->assertSame('image/gif', $attachment->mime_type);
    }

    public function test_attachment_with_disallowed_extension_is_rejected(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with executable')
            ->set('newTaskAttachments', [UploadedFile::fake()->create('virus.exe', 100, 'application/octet-stream')])
            ->call('createTask')
            ->assertHasErrors(['newTaskAttachments.0']);

        $this->assertDatabaseCount('tasks', 0);
        $this->assertDatabaseCount('task_attachments', 0);
    }

    public function test_attachment_larger_than_ten_megabytes_is_rejected(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with oversized file')
            ->set('newTaskAttachments', [UploadedFile::fake()->create('large.pdf', 10241, 'application/pdf')])
            ->call('createTask')
            ->assertHasErrors(['newTaskAttachments.0']);

        $this->assertDatabaseCount('task_attachments', 0);
    }

    public function test_user_can_delete_attachment(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task with removable attachment')
            ->set('newTaskAttachments', [UploadedFile::fake()->create('remove-me.pdf', 100, 'application/pdf')])
            ->call('createTask')
            ->assertHasNoErrors();

        $attachment = TaskAttachment::firstOrFail();

        Livewire::test(Show::class, ['project' => $project])
            ->call('deleteTaskAttachment', $attachment->attachment_id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('task_attachments', ['attachment_id' => $attachment->attachment_id]);
        Storage::disk('local')->assertMissing($attachment->file_path);
    }

    public function test_project_member_can_download_attachment(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Downloadable task',
            'status' => 'Backlog',
            'priority' => 'Medium',
        ]);

        Storage::disk('local')->put('task-attachments/'.$task->task_id.'/manual.pdf', 'pdf-content');

        $attachment = TaskAttachment::create([
            'task_id' => $task->task_id,
            'uploaded_by' => $user->id,
            'file_name' => 'manual.pdf',
            'file_path' => 'task-attachments/'.$task->task_id.'/manual.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 11,
        ]);

        $this->actingAs($user)
            ->get(route('tasks.attachments.download', [
                'project' => $project->project_id,
                'task' => $task->task_id,
                'attachment' => $attachment->attachment_id,
            ]))
            ->assertDownload('manual.pdf');
    }

    public function test_user_outside_project_cannot_download_attachment(): void
    {
        Storage::fake('local');

        $owner = $this->authorizedUser();
        $outsider = $this->authorizedUser();
        $project = $this->createProject($owner);

        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Private task',
            'status' => 'Backlog',
            'priority' => 'Medium',
        ]);

        Storage::disk('local')->put('task-attachments/'.$task->task_id.'/private.pdf', 'pdf-content');

        $attachment = TaskAttachment::create([
            'task_id' => $task->task_id,
            'uploaded_by' => $owner->id,
            'file_name' => 'private.pdf',
            'file_path' => 'task-attachments/'.$task->task_id.'/private.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 11,
        ]);

        $this->actingAs($outsider)
            ->get(route('tasks.attachments.download', [
                'project' => $project->project_id,
                'task' => $task->task_id,
                'attachment' => $attachment->attachment_id,
            ]))
            ->assertForbidden();
    }

    public function test_image_attachment_can_be_previewed_inline(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);
        $attachment = $this->createStoredAttachment($user, $project, 'photo.webp', 'image/webp');

        $response = $this->actingAs($user)->get(route('tasks.attachments.preview', [
            'project' => $project->project_id,
            'task' => $attachment->task_id,
            'attachment' => $attachment->attachment_id,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/webp');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_pdf_attachment_can_be_previewed_inline(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);
        $attachment = $this->createStoredAttachment($user, $project, 'document.pdf', 'application/pdf');

        $response = $this->actingAs($user)->get(route('tasks.attachments.preview', [
            'project' => $project->project_id,
            'task' => $attachment->task_id,
            'attachment' => $attachment->attachment_id,
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('Content-Disposition'));
    }

    public function test_unsupported_attachment_type_cannot_be_previewed(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);
        $attachment = $this->createStoredAttachment(
            $user,
            $project,
            'document.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $this->actingAs($user)->get(route('tasks.attachments.preview', [
            'project' => $project->project_id,
            'task' => $attachment->task_id,
            'attachment' => $attachment->attachment_id,
        ]))->assertStatus(415);
    }

    public function test_user_outside_project_cannot_preview_attachment(): void
    {
        Storage::fake('local');

        $owner = $this->authorizedUser();
        $outsider = $this->authorizedUser();
        $project = $this->createProject($owner);
        $attachment = $this->createStoredAttachment($owner, $project, 'private.pdf', 'application/pdf');

        $this->actingAs($outsider)->get(route('tasks.attachments.preview', [
            'project' => $project->project_id,
            'task' => $attachment->task_id,
            'attachment' => $attachment->attachment_id,
        ]))->assertForbidden();
    }

    public function test_preview_modal_opens_for_previewable_attachment(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);
        $attachment = $this->createStoredAttachment($user, $project, 'photo.webp', 'image/webp');

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('openAttachmentPreview', $attachment->attachment_id)
            ->assertSet('showAttachmentPreview', true)
            ->assertSet('previewAttachmentId', $attachment->attachment_id)
            ->assertSee('photo.webp');
    }

    public function test_preview_modal_does_not_open_for_unsupported_attachment(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);
        $attachment = $this->createStoredAttachment(
            $user,
            $project,
            'document.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        );

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->call('openAttachmentPreview', $attachment->attachment_id)
            ->assertSet('showAttachmentPreview', false);
    }

    public function test_deleting_task_removes_attachment_files(): void
    {
        Storage::fake('local');

        $user = $this->authorizedUser();
        $project = $this->createProject($user);

        $this->actingAs($user);

        Livewire::test(Show::class, ['project' => $project])
            ->set('taskTitle', 'Task to delete')
            ->set('newTaskAttachments', [UploadedFile::fake()->create('delete-me.pdf', 100, 'application/pdf')])
            ->call('createTask')
            ->assertHasNoErrors();

        $task = Task::firstOrFail();
        $attachment = TaskAttachment::firstOrFail();

        Livewire::test(Show::class, ['project' => $project])
            ->call('deleteTask', $task->task_id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('task_attachments', ['attachment_id' => $attachment->attachment_id]);
        Storage::disk('local')->assertMissing($attachment->file_path);
    }

    private function createProject(User $user): Project
    {
        $status = ProjectStatus::create(['status_name' => 'In Progress']);

        return Project::create([
            'project_name' => 'Attachment Project',
            'ownership_type' => 'Internal',
            'status_id' => $status->status_id,
            'created_by' => $user->id,
        ]);
    }

    private function createStoredAttachment(User $user, Project $project, string $fileName, string $mimeType): TaskAttachment
    {
        $task = Task::create([
            'project_id' => $project->project_id,
            'title' => 'Preview task',
            'status' => 'Backlog',
            'priority' => 'Medium',
        ]);

        $path = 'task-attachments/'.$task->task_id.'/'.$fileName;
        Storage::disk('local')->put($path, 'attachment-content');

        return TaskAttachment::create([
            'task_id' => $task->task_id,
            'uploaded_by' => $user->id,
            'file_name' => $fileName,
            'file_path' => $path,
            'mime_type' => $mimeType,
            'file_size' => 18,
        ]);
    }
}
