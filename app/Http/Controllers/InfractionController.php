<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserInfraction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InfractionController extends Controller
{
    /**
     * Aplica una sanción a un usuario infractor (ejecutado por moderador/admin).
     */
    public function store(Request $request, User $user)
    {
        $validated = $request->validate([
            'rule_id' => 'required|integer|in:' . implode(',', array_keys(config('community_rules.rules'))),
            'detail' => 'required|string|max:1000',
        ]);

        // Contar sanciones previas para definir el strike
        $strikesCount = $user->infractions()->count() + 1;

        $infraction = $user->infractions()->create([
            'moderator_id' => auth()->id(),
            'rule_id' => $validated['rule_id'],
            'detail' => $validated['detail'],
            'strike_level' => $strikesCount,
        ]);

        // Si ya es strike 2, aplicamos bloqueo temporal con 'bloqueado_hasta'
        if ($strikesCount === 2) {
            $user->update(['bloqueado_hasta' => Carbon::now()->addDays(7)]);
        } elseif ($strikesCount >= 3) {
            // Strike 3: Bloqueo indefinido (ej. 100 años hacia adelante o flag de baneo)
            $user->update(['bloqueado_hasta' => Carbon::now()->addYears(100)]);
        }

        return back()->with('success', 'Advertencia registrada con éxito.');
    }

    /**
     * El usuario marca como leída y aceptada la infracción.
     */
    public function acknowledge(UserInfraction $infraction)
    {
        // Solo el propio usuario infractor puede marcarla como leída
        if (auth()->id() !== $infraction->user_id) {
            abort(403);
        }

        $infraction->update([
            'read_at' => now(),
        ]);

        return back()->with('success', 'Has confirmado las normas de la comunidad.');
    }
}
