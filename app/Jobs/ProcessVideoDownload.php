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
        Log::info('Procesando descarga: ' . $this->download->id);

        $this->download->update([
            'status_id' => DownloadStatus::ID_PROCESANDO,
            'updated_by' => $this->download->created_by,
        ]);

        $fileName = 'video_' . $this->download->id . '_' . time() . '.mp4';
        $filePath = 'videos/' . $fileName;

        try {
            $process = new Process([
                'yt-dlp',
                '-o',
                storage_path('app/' . $filePath),
                $this->download->url,
            ]);

            $process->setTimeout(3600); // 1 hora de timeout
            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            $this->download->update([
                'status_id' => DownloadStatus::ID_COMPLETADO,
                'file_path' => $filePath,
                'file_name' => $fileName,
                'updated_by' => $this->download->created_by,
            ]);

            Log::info('Descarga completada: ' . $this->download->id);

        } catch (\Exception $e) {
            Log::error('Error al descargar video: ' . $e->getMessage());
            $this->download->update([
                'status_id' => DownloadStatus::ID_FALLIDO,
                'error_message' => $e->getMessage(),
                'updated_by' => $this->download->created_by,
            ]);
        }
    }
}