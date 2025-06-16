<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Str;

use App\Models\Download\Download;
use App\Models\Download\DownloadStatus;

class ProcessVideoDownload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $download;

    public function __construct(Download $download)
    {
        $this->download = $download;
        $this->queue = 'downloads';
    }

    public function handle()
    {
        $userId = $this->download->created_by;
        if (!$userId) {
            Log::error('User ID no definido para la descarga', ['download_id' => $this->download->id]);
            $this->download->update([
                'status_id' => DownloadStatus::ID_FALLIDO,
                'error_message' => 'User ID no definido',
                'updated_by' => $this->download->created_by ?? 0,
            ]);
            return;
        }

        $this->download->update([
            'status_id' => DownloadStatus::ID_PROCESANDO,
            'updated_by' => $userId,
        ]);

        $userFolder = "user_{$userId}";

        try {
            Storage::disk('videos')->makeDirectory($userFolder);
            if (!Storage::disk('videos')->exists($userFolder)) {
                throw new \Exception('No se pudo crear la carpeta: ' . $userFolder);
            }
            Log::info('Carpeta creada o ya existe', ['folder' => $userFolder]);
        } catch (\Exception $e) {
            Log::error('Error al crear carpeta', ['folder' => $userFolder, 'error' => $e->getMessage()]);
            $this->download->update([
                'status_id' => DownloadStatus::ID_FALLIDO,
                'error_message' => 'No se pudo crear la carpeta: ' . $e->getMessage(),
                'updated_by' => $userId,
            ]);
            return;
        }

        $existingVideos = Storage::disk('videos')->files($userFolder);
        $videoCount = count($existingVideos) + 1;

        $originalName = $this->download->original_name ?? 'video';
        $sanitizedName = Str::slug($originalName);
        $uniqueId = Str::uuid()->toString();
        $fileName = "video_{$videoCount}_{$uniqueId}.mp4";
        $filePath = "videos/{$userFolder}/{$fileName}";
        $fullPath = Storage::disk('videos')->path($filePath);

        Log::info('Preparando descarga', ['file_path' => $filePath, 'full_path' => $fullPath, 'file_name' => $fileName]);

        try {
            $process = new Process([
                'yt-dlp',
                '-o',
                Storage::disk('videos')->path("{$userFolder}/{$fileName}"),
                $this->download->url,
            ]);

            $process->setTimeout(3600); // 1 hora de timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            if (!Storage::disk('videos')->exists("{$userFolder}/{$fileName}")) {
                throw new \Exception('El archivo no se creó en la ruta esperada: ' . $filePath);
            }

            $this->download->update([
                'status_id' => DownloadStatus::ID_COMPLETADO,
                'file_path' => $filePath,
                'full_path' => $fullPath,
                'file_name' => $fileName,
                'updated_by' => $userId,
            ]);

            Log::info('Descarga completada', ['download_id' => $this->download->id, 'file_path' => $filePath, 'full_path' => $fullPath]);
        } catch (\Exception $e) {
            Log::error('Error al descargar video', ['download_id' => $this->download->id, 'error' => $e->getMessage()]);
            $this->download->update([
                'status_id' => DownloadStatus::ID_FALLIDO,
                'error_message' => $e->getMessage(),
                'updated_by' => $userId,
            ]);
        }
    }
}