@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">{{ __('Reproducir Video') }}</div>
                <div class="card-body">
                    <h5>{{ $download->file_name }}</h5>
                        <video controls class="w-100 rounded shadow-sm" style="max-height: 500px;">
                            <source src="{{ asset('storage/' . $download->file_path) }}" type="video/mp4">
                            Tu navegador no soporta la reproducción de videos.
                        </video>
                    <div class="mt-3">
                        <p><strong>{{ __('URL original') }}:</strong> {{ $download->url }}</p>
                        <p><strong>{{ __('Estado') }}:</strong> {{ $download->Status->name }}</p>
                        <p><strong>{{ __('Creado por') }}:</strong> {{ $download->createdBy->name ?? '-' }}</p>
                        <p><strong>{{ __('Creado el') }}:</strong> {{ $download->created_at->toDateTimeString() }}</p>
                        <p><strong>{{ __('Nombre del archivo') }}:</strong> {{ $download->file_name ?? '-' }}</p>
                        @if ($download->status_id == \App\Models\Download\DownloadStatus::ID_COMPLETADO)
                            <a href="{{ route('client.downloads.download', $download->id) }}" class="btn btn-primary" download>{{ __('Descargar') }}</a>
                        @endif
                    </div>
                    <a href="{{ route('client.downloads.index') }}" class="btn btn-secondary mt-3">{{ __('Volver') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection