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

        if ($profile->membership_status === 'excluded') {
            abort(403, 'Votre compte a été exclu du réseau.');
        }

        // Cotisation expirée : traité comme suspendu (accès paiements uniquement)
        $expired = $profile->membership_status === 'active'
            && $profile->membership_expires_at !== null
            && $profile->membership_expires_at->isPast();

        if (($profile->isSuspended() || $expired) && ! $request->routeIs('membre.payments')) {
            $msg = $expired
                ? 'Votre adhésion a expiré. Régularisez votre cotisation pour retrouver un accès complet.'
                : 'Votre adhésion est suspendue. Régularisez votre cotisation pour retrouver un accès complet.';

            return redirect()->route('membre.payments')->with('warning', $msg);
        }

        return $next($request);
    }
}
