@extends('layouts.app')

@section('content')

<div class="container">
    @foreach (['success', 'error'] as $msg)
        @if (session($msg))
            <div class="alert alert-{{ $msg === 'success' ? 'success' : 'danger' }} alert-dismissible fade show" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('Editar usuario') }} — {{ $user->name }}
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.users.do_edit', $user->id) }}" method="POST">
                        @include('admin.users.partials.form', ['mode' => 'edit', 'user' => $user])
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
