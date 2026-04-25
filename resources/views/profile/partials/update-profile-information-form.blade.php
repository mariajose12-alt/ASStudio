<form method="post" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div class="form-grid">
        <div class="form-group">
            <label for="name">Nombre</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')<span class="error-msg">{{ $message }}</span>@enderror
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        @if(session('status') === 'profile-updated')
            <span style="font-size:13px; color:var(--success); align-self:center;">✓ Guardado correctamente</span>
        @endif
    </div>
</form>
