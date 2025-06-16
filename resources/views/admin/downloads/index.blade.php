@extends('layouts.app')

@section('content')
<div class="row mb-3">
    <div class="col d-flex justify-content-between align-items-center">
        <h3 class="mb-0">{{ __('Descargar Videos') }}</h3>

        <form id="download-form" action="{{ route('admin.downloads.do_create') }}" method="POST" class="d-flex align-items-center gap-2" novalidate>
            @csrf
            <input type="url" name="url" class="form-control" placeholder="Ingresar URL" required>
            <button type="submit" class="btn btn-success">
                {{ __('Descarga') }}
            </button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="downloads-table">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('URL') }}</th>
                        <th>{{ __('Estado') }}</th>
                        <th style="width:200px">{{ __('Progreso') }}</th>
                        <th>{{ __('Archivo') }}</th>
                        <th>{{ __('Usuario') }}</th>
                        <th>{{ __('Creado') }}</th>
                        <th>{{ __('Acciones') }}</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($downloads as $download)
                        <tr data-id="{{ $download->id }}">
                            <td>{{ $download->id }}</td>
                            <td class="text-truncate" style="max-width:200px">{{ $download->url }}</td>
                            <td class="status">{{ $download->Status->name }}</td>
                            <td>
                                <div class="progress" style="height:20px">
                                    <div class="progress-bar" role="progressbar"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td class="file-name">{{ $download->file_name ?? '-' }}</td>
                            <td class="created-by">{{ $download->createdBy->name ?? '-' }}</td>
                            <td class="created-at">{{ $download->created_at->toDateTimeString() }}</td>
                            <td class="action">
                                @if ($download->status_id == \App\Models\Download\DownloadStatus::ID_COMPLETADO)
                                    <a href="{{ route('admin.downloads.show', $download->id) }}" class="btn btn-primary btn-sm me-1">{{ __('Ver') }}</a>
                                    <a href="{{ route('admin.downloads.download', $download->id) }}" class="btn btn-success btn-sm">{{ __('Descargar') }}</a>
                                @elseif ($download->status_id == \App\Models\Download\DownloadStatus::ID_FALLIDO)
                                    <span class="text-danger">{{ $download->error_message }}</span>
                                @else
                                    <span>{{ __('Procesando') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ __('No hay registros de descargas.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/sweetalert2.js') }}"></script>
<script>
    
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('download-form');
    const table = document.querySelector('#downloads-table tbody');

    form.addEventListener('submit', async e => {
        e.preventDefault();
        const fd = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: fd
            });

            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status}`);
            }

            const json = await res.json();
            if (!json.success) {
                throw new Error(json.message || 'Error al crear la descarga');
            }

            const url = fd.get('url');
            form.reset();
            insertRow(json.download_id, url);
            Swal.fire({
                title: 'Éxito',
                text: 'La descarga ha sido encolada.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } catch (err) {
            console.error('Error:', err);
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: err.message,
            });
        }
    });

    function insertRow(id, url) {
        const row = table.insertRow(0);
        row.dataset.id = id;
        const currentUser = '{{ Auth::user()->name }}';
        const now = new Date().toLocaleString();
        row.innerHTML = `
            <td>${id}</td>
            <td class="text-truncate" style="max-width:200px">${url}</td>
            <td class="status">Pendiente</td>
            <td>
                <div class="progress" style="height:20px">
                    <div class="progress-bar" role="progressbar"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </td>
            <td class="file-name">-</td>
            <td class="created-by">${currentUser}</td>
            <td class="created-at">${now}</td>
            <td class="action"><span>Procesando</span></td>
        `;
        paintProgress(row, {{ \App\Models\Download\DownloadStatus::ID_PENDIENTE }});
    }

    function paintProgress(row, statusId, errorMsg = '') {
        const bar = row.querySelector('.progress-bar');
        bar.className = 'progress-bar';
        bar.style.width = '100%';
        bar.textContent = '';

        switch (statusId) {
            case {{ \App\Models\Download\DownloadStatus::ID_PENDIENTE }}:
                bar.style.width = '0%';
                bar.classList.add('bg-secondary');
                break;

            case {{ \App\Models\Download\DownloadStatus::ID_PROCESANDO }}:
                bar.classList.add('bg-info', 'progress-bar-striped', 'progress-bar-animated');
                break;

            case {{ \App\Models\Download\DownloadStatus::ID_COMPLETADO }}:
                bar.classList.add('bg-success');
                bar.textContent = '100%';
                break;

            case {{ \App\Models\Download\DownloadStatus::ID_FALLIDO }}:
                bar.classList.add('bg-danger');
                bar.textContent = 'Error';
                if (errorMsg)
                    row.querySelector('.action').innerHTML =
                        `<span class="text-danger">${errorMsg}</span>`;
                break;
        }
    }

    async function poll() {
        try {
            const res = await fetch('{{ route('admin.downloads.status') }}', {
                headers: { 'Accept': 'application/json' }
            });

            if (!res.ok) {
                throw new Error(`Error HTTP: ${res.status}`);
            }

            const { downloads } = await res.json();
            downloads.forEach(download => {
                const row = table.querySelector(`tr[data-id="${download.id}"]`);
                if (!row) return;

                row.querySelector('.status').textContent = download.status_name;
                row.querySelector('.file-name').textContent = download.file_name ?? '-';
                row.querySelector('.created-by').textContent = download.created_by ?? '-';
                row.querySelector('.created-at').textContent = download.created_at ?? '-';

                if (download.status_id == {{ \App\Models\Download\DownloadStatus::ID_COMPLETADO }}) {
                    row.querySelector('.action').innerHTML =
                        `<a href="${window.laravelRoutes['admin.downloads.show'].replace(':id', download.id)}" class="btn btn-primary btn-sm me-1">Ver</a>` +
                        `<a href="${window.laravelRoutes['admin.downloads.download'].replace(':id', download.id)}" class="btn btn-success btn-sm">Descargar</a>`;
                } else if (download.status_id == {{ \App\Models\Download\DownloadStatus::ID_FALLIDO }}) {
                    row.querySelector('.action').innerHTML =
                        `<span class="text-danger">${download.error_message}</span>`;
                } else {
                    row.querySelector('.action').textContent = 'Procesando';
                }

                paintProgress(row, download.status_id, download.error_message);
            });
        } catch (e) {
            console.error('Error en polling:', e);
        }
    }

    // Define named routes for JavaScript usage
    window.laravelRoutes = {
        'admin.downloads.show': '{{ route('admin.downloads.show', ':id') }}',
        'admin.downloads.download': '{{ route('admin.downloads.download', ':id') }}'
    };

    poll();
    setInterval(poll, 5000);
});
</script>
@endpush
@endsection