<?php

namespace App\Http\Controllers\Evento;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\EventoConversacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EventoConversacionController extends Controller
{
    /**
     * Bandeja de entrada de conversaciones de eventos.
     */
    public function index()
    {
        $userId = Auth::id();

        $conversaciones = EventoConversacion::query()
            ->with(['evento', 'comprador', 'organizador', 'mensajes' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->where('comprador_id', $userId)
            ->orWhere('organizador_id', $userId)
            ->latest()
            ->get();

        return Inertia::render('Eventos/Mensajes/Index', [
            'conversaciones' => $conversaciones,
        ]);
    }

    /**
     * Iniciar o recuperar una conversación sobre un evento.
     */
    public function store(Request $request, Evento $evento)
    {
        $user = Auth::user();

        if ($evento->user_id === $user->id) {
            return back()->with('error', 'No puedes iniciar una conversación sobre tu propio evento.');
        }

        $conversacion = EventoConversacion::firstOrCreate([
            'evento_id'      => $evento->id,
            'comprador_id'   => $user->id,
            'organizador_id' => $evento->user_id,
        ]);

        return redirect()->route('eventos.mensajes.show', $conversacion->id);
    }

    /**
     * Ver mensajes de una conversación.
     */
    /**
     * Ver mensajes de una conversación.
     */
    public function show(EventoConversacion $conversacion)
    {
        $userId = Auth::id();

        if ($conversacion->comprador_id !== $userId && $conversacion->organizador_id !== $userId) {
            abort(403);
        }

        // Cargar la conversación con sus mensajes y el usuario de cada mensaje
        $conversacion->load([
            'evento',
            'comprador',
            'organizador',
            'mensajes' => function ($query) {
                $query->orderBy('created_at', 'asc')->with('usuario');
            }
        ]);

        // Marcar como leídos los mensajes que no sean del usuario actual
        $conversacion->mensajes()
            ->where('user_id', '!=', $userId)
            ->whereNull('leido_at')
            ->update(['leido_at' => now()]);

        return Inertia::render('Eventos/Conversacion', [
            'conversacion' => $conversacion,
        ]);
    }
}
