<form method="post" action="{{ route('password.update') }}">
    @csrf
    @method('put')

    <div class="form-grid">
        <div class="form-group full">
            <label for="current_password">Contraseña Actual</label>
            <input id="current_password" name="current_password" type="password" autocomplete="current-password">
            @error('current_password', 'updatePassword')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Nueva Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password">
            @error('password', 'updatePassword')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirmar Contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
            <span class="error-msg">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
        @if(session('status') === 'password-updated')
            <span style="font-size:13px; color:var(--success); align-self:center;">✓ Contraseña actualizada</span>
        @endif
    </div>
</form>
