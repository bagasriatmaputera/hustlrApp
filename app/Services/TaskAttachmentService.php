<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;
use Throwable;

class TaskAttachmentService
{
    private const COMPRESSIBLE_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const IMAGE_MAX_DIMENSION = 1920;

    private const IMAGE_QUALITY = 80;

    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function storeMany(Task $task, array $files, int $userId): void
    {
        foreach ($files as $file) {
            if (in_array($file->getMimeType(), self::COMPRESSIBLE_IMAGE_MIMES, true)) {
                $this->storeImage($task, $file, $userId);

                continue;
            }

            $this->storeOriginal($task, $file, $userId);
        }
    }

    public function delete(TaskAttachment $attachment): void
    {
        Storage::disk('local')->delete($attachment->file_path);
        $attachment->delete();
    }

    public function deleteForTask(Task $task): void
    {
        $task->attachments()->get()->each(fn (TaskAttachment $attachment) => $this->delete($attachment));
    }

    private function storeImage(Task $task, UploadedFile $file, int $userId): void
    {
        try {
            $encoded = Image::decode($file)
                ->scaleDown(width: self::IMAGE_MAX_DIMENSION, height: self::IMAGE_MAX_DIMENSION)
                ->encodeUsingFormat(Format::WEBP, quality: self::IMAGE_QUALITY);

            $contents = (string) $encoded;

            if ($contents === '' || strlen($contents) >= (int) $file->getSize()) {
                $this->storeOriginal($task, $file, $userId);

                return;
            }

            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.webp';
            $path = 'task-attachments/'.$task->task_id.'/'.Str::uuid().'.webp';

            if (! Storage::disk('local')->put($path, $contents)) {
                throw new RuntimeException(__('Unable to store task attachment.'));
            }

            $task->attachments()->create([
                'uploaded_by' => $userId,
                'file_name' => $fileName,
                'file_path' => $path,
                'mime_type' => 'image/webp',
                'file_size' => strlen($contents),
            ]);
        } catch (Throwable $exception) {
            report($exception);
            $this->storeOriginal($task, $file, $userId);
        }
    }

    private function storeOriginal(Task $task, UploadedFile $file, int $userId): void
    {
        $path = $file->store('task-attachments/'.$task->task_id, 'local');

        if ($path === false) {
            throw new RuntimeException(__('Unable to store task attachment.'));
        }

        $task->attachments()->create([
            'uploaded_by' => $userId,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getMimeType(),
            'file_size' => (int) $file->getSize(),
        ]);
    }
}
