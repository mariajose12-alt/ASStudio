<p style="font-size:14px; color:#666; margin-bottom:1.5rem;">
    Una vez que elimines tu cuenta, todos tus datos serán eliminados permanentemente.
    Por favor descarga cualquier información que desees conservar antes de proceder.
</p>

<button
    class="btn btn-danger"
    onclick="document.getElementById('modal-delete').style.display='flex'"
>
    Eliminar mi cuenta
</button>

{{-- Modal --}}
<div id="modal-delete" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:32px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <h3 style="font-family:'Playfair Display',serif; font-size:18px; margin-bottom:8px; color:var(--black);">
            ¿Eliminar tu cuenta?
        </h3>
        <p style="font-size:13px; color:#888; margin-bottom:24px;">
            Esta acción es irreversible. Ingresa tu contraseña para confirmar.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <div class="form-group" style="margin-bottom:20px;">
                <label for="password_delete">Contraseña</label>
                <input id="password_delete" name="password" type="password" placeholder="Tu contraseña actual">
                @error('password', 'userDeletion')
                <span class="error-msg">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Sí, eliminar cuenta</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modal-delete').style.display='none'">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
