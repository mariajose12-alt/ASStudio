<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // Para actualizar la contraseña del usuario
    public function updatePassword(Request $request): RedirectResponse
    {
        // Si el usuario todavía no tiene contraseña (se registró con Google),
        // no le pedimos "contraseña actual" porque no existe ninguna.
        $tieneContrasena = ! is_null($request->user()->contrasena);

        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => [$tieneContrasena ? 'required' : 'nullable', 'current_password'],
            'password'          => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'contrasena' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
