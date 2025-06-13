<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Auth;
use Log;

use App\Jobs\ProcessVideoDownload;

use App\Models\User\User;
use App\Models\User\UserType;
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
                            ->orderBy('id', 'desc')
                            ->get();

        return view('admin.downloads.index', compact('downloads'));
    }

    public function do_create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $download = Download::create([
            'name' => $request->name,
            'url' => $request->url,
            'status_id' => DownloadStatus::ID_PENDIENTE,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        Log::info('Encolando descarga: ' . $download->id . ', URL: ' . $download->url . ', Name: ' . $download->name);
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
            'name' => $download->name
        ]);
    }

    public function show($id)
    {
        $download = Download::findOrFail($id);
        return response()->download(storage_path('app/videos/' . $download->file_name), $download->file_name);
    }
    
    public function status(): JsonResponse
    {
        $downloads = Download::with(['Status', 'createdBy'])
                            ->where('created_by', Auth::id())
                            ->select('id', 'name', 'url', 'status_id', 'file_name', 'error_message', 'created_by', 'created_at')
                            ->latest('id')
                            ->limit(50)
                            ->get()
                            ->map(function ($download) {
                                return [
                                    'id' => $download->id,
                                    'name' => $download->name,
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
