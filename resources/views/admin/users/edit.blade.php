@extends('layouts.app')

@section('content')
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
@endsection
