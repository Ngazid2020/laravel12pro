<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveMemberApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Admins ont accès complet
        if ($user->hasRole(['super_admin', 'admin'])) {
            return $next($request);
        }

        $profile = $user->profile;

        if (! $profile) {
            return response()->json(['message' => 'Profil membre introuvable.'], 403);
        }

        if ($profile->membership_status === 'excluded') {
            return response()->json(['message' => 'Votre compte a été exclu.'], 403);
        }

        if ($profile->membership_status === 'suspended') {
            return response()->json([
                'message'  => 'Votre compte est suspendu. Veuillez régulariser votre cotisation.',
                'code'     => 'ACCOUNT_SUSPENDED',
                'redirect' => 'payments',
            ], 403);
        }

        // Cotisation expirée même si le statut est encore "active"
        $expired = $profile->membership_status === 'active'
            && $profile->membership_expires_at !== null
            && $profile->membership_expires_at->isPast();

        if ($expired) {
            return response()->json([
                'message'  => 'Votre adhésion a expiré. Régularisez votre cotisation.',
                'code'     => 'MEMBERSHIP_EXPIRED',
                'redirect' => 'payments',
            ], 403);
        }

        return $next($request);
    }
}