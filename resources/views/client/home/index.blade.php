@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>{{ __('Inicio') }}</div>
                    {{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                        {{ __('Subir Nueva Imagen') }}
                    </button> --}}
                </div>
                <div class="card-body">
                    {{--
                    @if ($images->isEmpty())
                        <p>{{ __('No hay imágenes disponibles.') }}</p>
                    @else
                        <div class="table-responsive">
                             <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Imagen') }}</th>
                                        <th>{{ __('Slug') }}</th>
                                        <th>{{ __('Creado por') }}</th>
                                        <th>{{ __('Fecha de creación') }}</th>
                                        <th>{{ __('Acciones') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($images as $image)
                                        <tr>
                                            <td>
                                                <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $image->slug ?? 'Imagen' }}" style="max-width: 100px; height: auto;">
                                            </td>
                                            <td>{{ $image->slug ?? '' }}</td>
                                            <td>{{ $image->user->name ?? 'Desconocido' }}</td>
                                            <td>{{ $image->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-primary btn-sm m-1" onclick="copyToClipboard('{{ config('app.url') }}/api/images/{{ $image->slug }}/render')">{{ __('Copiar URL') }}</button>
                                                    <button type="button" class="btn btn-success btn-sm m-1" data-bs-toggle="modal" data-bs-target="#viewImageModal" onclick="showImage('{{ config('app.url') }}/api/images/{{ $image->slug }}/render?border=1')">{{ __('Ver Imagen') }}</button>
                                                    <form action="{{ route('images.destroy', $image->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('¿Estás seguro de que deseas eliminar esta imagen?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm m-1">{{ __('Eliminar') }}</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> 
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $images->links() }}
                        </div>
                    @endif
                    --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>

</script>
@endsection