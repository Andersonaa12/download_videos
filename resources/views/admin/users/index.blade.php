@extends('layouts.app')

@section('content')
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <h3 class="mb-0">{{ __('Usuarios') }}</h3>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                {{ __('Agregar usuario') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($users->isEmpty())
                <p class="p-3 mb-0">{{ __('No hay usuarios registrados.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ __('Nombre') }}</th>
                                <th>{{ __('Correo') }}</th>
                                <th>{{ __('Tipo') }}</th>
                                <th>{{ __('Estado') }}</th>
                                <th>{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->Type->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->active ? 'success' : 'danger' }}">
                                            {{ $user->active ? __('Activo') : __('Inactivo') }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                            {{ __('Editar') }}
                                        </a>

                                        <form action="{{ route('admin.users.do_active', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm {{ $user->active ? 'btn-secondary' : 'btn-success' }}"
                                                    type="submit">
                                                {{ $user->active ? __('Desactivar') : __('Activar') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
