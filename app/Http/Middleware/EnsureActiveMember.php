<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $user    = $request->user();
        $profile = $user?->profile;

        if (! $user) {
            return redirect()->route('member.login');
        }

        // Les admins peuvent accéder à l'espace membre
        if ($user->hasRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        if (! $profile) {
            abort(403, 'Aucun profil membre associé à ce compte.');
        }

        // Suspendu : accès limité à la page paiements uniquement
        if ($profile->isSuspended() && ! $request->routeIs('membre.payments')) {
            return redirect()->route('membre.payments')
                ->with('warning', 'Votre adhésion est suspendue. Régularisez votre cotisation pour retrouver un accès complet.');
        }

        if ($profile->membership_status === 'excluded') {
            abort(403, 'Votre compte a été exclu du réseau.');
        }

        return $next($request);
    }
}
