<?php

namespace App\Http\Controllers\Portfolio;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioMediaController extends Controller
{
    /**
     * Stream portfolio media file from storage (S3 or local fallback) with caching headers.
     */
    public function show(string $path): StreamedResponse
    {
        // Prevent path traversal and restrict to portfolio assets
        abort_if(str_contains($path, '..') || ! str_starts_with($path, 'portfolio/'), 404);

        $diskName = (string) config('filesystems.default', 's3');
        $disk = Storage::disk($diskName);

        if ($disk->exists($path)) {
            return $disk->response($path, null, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, null, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->response($path, null, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404);
    }
}
