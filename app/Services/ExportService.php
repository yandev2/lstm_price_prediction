<?php

namespace App\Services;

use App\Models\EksportHistory;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ExportService
{
    /**
     * @param User $user
     * @param string $filename
     * @param string $filePath
     * @param string $module
     * @param string $disk
     * @return EksportHistory
     */
    public function recordExport(
        User $user,
        string $filename,
        string $filePath,
        string $module,

        string $disk = 'public'
    ): EksportHistory {
        $fullPath = Storage::disk($disk)->path($filePath);

        // 2. Baca metadata menggunakan fungsi native PHP agar aman
        $mimeType = file_exists($fullPath) ? mime_content_type($fullPath) : 'application/octet-stream';
        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

        return EksportHistory::createQuietly([
            'user_id'           => $user->id,
            'filename'          => $filename,
            'file_path'         => $filePath,
            'disk'              => $disk,
            'mime_type'         => $mimeType,
            'file_size'         => $fileSize,
            'module'            => $module,
        ]);
    }
}
