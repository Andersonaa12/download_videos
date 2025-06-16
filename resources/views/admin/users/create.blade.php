@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Crear nuevo usuario') }}</div>
                <div class="card-body">
                    <form action="{{ route('admin.users.do_create') }}" method="POST">
                        @include('admin.users.partials.form', ['mode' => 'create'])
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
