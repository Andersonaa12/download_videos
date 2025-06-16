<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ProcessVideoDownload;

use App\Models\Download\Download;
use App\Models\Download\DownloadStatus;

class DownloadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $downloads = Download::with('Status')
                            ->where('created_by', Auth::id())
                            ->orderBy('id', 'desc')
                            ->get();

        return view('client.downloads.index', compact('downloads'));
    }

    public function do_create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $download = Download::create([
            'url' => $request->url,
            'status_id' => DownloadStatus::ID_PENDIENTE,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        try {
            ProcessVideoDownload::dispatch($download)->onQueue('downloads');
        } catch (\Exception $e) {
            Log::error('Error al despachar el trabajo: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno al encolar la descarga'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'La descarga ha sido encolada.',
            'download_id' => $download->id,
            'url' => $download->url
        ]);
    }

    public function show($id)
    {
        $download = Download::with(['Status', 'createdBy'])
                            ->where('created_by', Auth::id())
                            ->findOrFail($id);

        return view('client.downloads.show', compact('download'));
    }

    public function download($id)
    {
        $download = Download::where('created_by', Auth::id())->findOrFail($id);

        $filePath = str_starts_with($download->file_path, 'videos/') 
            ? substr($download->file_path, 7) 
            : $download->file_path;

        Log::info('Download attempt', ['id' => $id, 'file_path' => $filePath]);

        if (!Storage::disk('videos')->exists($filePath)) {
            Log::error('File not found', ['file_path' => $filePath]);
            abort(404, 'El archivo no se encuentra en el servidor.');
        }
        return Storage::disk('videos')->download(
            $filePath,
            $download->file_name
        );
    }

    public function status(): JsonResponse
    {
        $downloads = Download::with(['Status', 'createdBy'])
                            ->where('created_by', Auth::id())
                            ->select('id', 'url', 'status_id', 'file_name', 'error_message', 'created_by', 'created_at')
                            ->latest('id')
                            ->limit(50)
                            ->get()
                            ->map(function ($download) {
                                return [
                                    'id' => $download->id,
                                    'url' => $download->url,
                                    'status_id' => $download->status_id,
                                    'status_name' => $download->Status->name,
                                    'file_name' => $download->file_name,
                                    'error_message' => $download->error_message,
                                    'created_by' => $download->createdBy->name ?? '-',
                                    'created_at' => $download->created_at->toDateTimeString(),
                                ];
                            });

        return response()->json(['downloads' => $downloads]);
    }
}