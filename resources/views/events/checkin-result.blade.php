<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Événement · Réseau Entrepreneurs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4">
    <div class="card bg-base-100 shadow-xl w-full max-w-sm">
        <div class="card-body items-center text-center">

            @if($success)
                <div class="text-6xl mb-2">{{ $alreadyCheckedIn ? '✅' : '🎉' }}</div>
                <h2 class="card-title text-success text-xl">
                    {{ $alreadyCheckedIn ? 'Déjà enregistré' : 'Check-in réussi !' }}
                </h2>
            @else
                <div class="text-6xl mb-2">❌</div>
                <h2 class="card-title text-error text-xl">QR Code invalide</h2>
            @endif

            <div class="divider"></div>

            <div class="w-full text-left space-y-2">
                <div>
                    <p class="text-xs text-base-content/50 uppercase font-semibold">Membre</p>
                    <p class="font-semibold">{{ $registration->user->full_name }}</p>
                    <p class="text-sm text-base-content/60">{{ $registration->user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-base-content/50 uppercase font-semibold">Événement</p>
                    <p class="font-semibold">{{ $registration->event->title }}</p>
                    <p class="text-sm text-base-content/60">
                        {{ $registration->event->starts_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                    </p>
                </div>
                @if($success && !$alreadyCheckedIn)
                    <div>
                        <p class="text-xs text-base-content/50 uppercase font-semibold">Heure d'entrée</p>
                        <p class="font-semibold">{{ $registration->checked_in_at->format('H:i') }}</p>
                    </div>
                @endif
            </div>

            @if(!$success)
                <p class="text-sm text-error mt-2">{{ $message }}</p>
            @endif

        </div>
    </div>
</body>
</html>
