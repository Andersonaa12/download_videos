@csrf
@if(isset($mode) && $mode === 'edit')
    @method('PUT')
@endif

<div class="mb-3">
    <label for="name" class="form-label">{{ __('Nombre') }}</label>
    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">{{ __('Correo electrónico') }}</label>
    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email ?? '') }}"  required>
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="type_id" class="form-label">{{ __('Tipo de usuario') }}</label>
    <select class="form-control @error('type_id') is-invalid @enderror" name="type_id" id="type_id" required>
        <option value="">{{ __('Seleccione una opción') }}</option>
        @foreach ($userTypes as $type)
            <option value="{{ $type->id }}" {{ old('type_id', $user->type_id ?? '') == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
        @endforeach
    </select>
    @error('type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>


@if(!isset($mode) || $mode === 'create')
    <div class="mb-3">
        <label for="password" class="form-label">{{ __('Contraseña') }}</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"  required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">{{ __('Confirmar contraseña') }}</label>
        <input type="password" name="password_confirmation"  id="password_confirmation"  class="form-control" required>
    </div>
@else
    <div class="mb-3">
        <label for="password" class="form-label">{{ __('Nueva contraseña (opcional)') }}</label>
        <input type="password"  name="password" id="password"  class="form-control @error('password') is-invalid @enderror">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label for="password_confirmation" class="form-label">{{ __('Confirmar contraseña') }}</label>
        <input type="password"  name="password_confirmation"  id="password_confirmation" class="form-control">
    </div>
@endif

<div class="form-check mb-4">
    <input type="checkbox" name="active"  id="active" class="form-check-input"  value="1"
           {{ old('active', $user->active ?? 1) ? 'checked' : '' }}>
    <label class="form-check-label" for="active">{{ __('Activo') }}</label>
</div>

<button class="btn btn-success" type="submit">{{ __('Guardar') }}</button>
<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
