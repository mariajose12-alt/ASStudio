<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $notificaciones = $request->user()
            ->notifications()
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'titulo'   => $n->data['titulo'] ?? '',
                'mensaje'  => $n->data['mensaje'] ?? '',
                'icono'    => $n->data['icono'] ?? 'bell',
                'url'      => $n->data['url'] ?? null,
                'leida'    => !is_null($n->read_at),
                'fecha'    => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas'      => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function marcarLeidas(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function marcarLeida(Request $request, string $id)
    {
        $notificacion = $request->user()->notifications()->findOrFail($id);
        $notificacion->markAsRead();

        return response()->json(['ok' => true]);
    }
}
