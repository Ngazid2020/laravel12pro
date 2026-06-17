<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — ' : '' }}Réseau Entrepreneurs Comores</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased">

<x-nav sticky full-width class="bg-base-100/90 backdrop-blur border-b border-base-200 shadow-sm">
    <x-slot:brand>
        <x-icon name="o-rocket-launch" class="text-primary w-6 h-6" />
        <span class="font-extrabold text-primary ml-1">Réseau</span>
        <span class="font-light ml-1 hidden sm:inline">Entrepreneurs</span>
    </x-slot:brand>
    <x-slot:actions>
        @auth
            <div class="flex items-center gap-2">
                <div class="flex items-center gap-1.5 text-sm text-base-content/70">
                    <x-icon name="o-user-circle" class="w-5 h-5 text-primary" />
                    <span class="hidden sm:inline font-medium">{{ auth()->user()->first_name }}</span>
                </div>
                <a href="{{ route('portail') }}" class="btn btn-primary btn-sm gap-1">
                    <x-icon name="o-squares-2x2" class="w-4 h-4" />
                    Mon espace
                </a>
            </div>
        @else
            <a href="{{ route('member.login') }}" class="btn btn-ghost btn-sm">Se connecter</a>
            <a href="#postuler" class="btn btn-primary btn-sm">Rejoindre</a>
        @endauth
    </x-slot:actions>
</x-nav>

{{ $slot }}

<footer class="bg-base-200 border-t border-base-300 py-8 mt-16">
    <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-base-content/60">
        <div class="flex items-center gap-2">
            <x-icon name="o-rocket-launch" class="text-primary w-5 h-5" />
            <span class="font-bold text-base-content">Réseau Entrepreneurs Comores</span>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('rgpd') }}" class="hover:text-primary transition-colors">Politique de confidentialité</a>
            <span>·</span>
            <span>contact@reseau-entrepreneurs.km</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('rgpd') }}"
               title="Ce site respecte votre vie privée — conformité RGPD"
               class="flex items-center gap-1.5 border border-success/40 bg-success/10 text-success hover:bg-success/20 transition-colors rounded-full px-3 py-1 text-xs font-semibold">
                <x-icon name="o-shield-check" class="w-3.5 h-3.5" />
                Conforme RGPD
            </a>
            <span>·</span>
            <p>© {{ date('Y') }} Réseau Entrepreneurs Comores</p>
        </div>
    </div>
</footer>

{{-- Bannière consentement cookies (style WordPress) --}}
<div id="rgpd-banner"
     class="fixed bottom-0 left-0 right-0 z-50 p-4 hidden"
     role="dialog" aria-live="polite" aria-label="Politique de confidentialité">
    <div class="max-w-4xl mx-auto bg-base-100 border border-base-300 shadow-2xl rounded-2xl px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex items-start gap-3 flex-1 min-w-0">
            <x-icon name="o-shield-check" class="w-8 h-8 text-primary shrink-0 mt-0.5" />
            <div>
                <p class="font-semibold text-base-content text-sm">Ce site respecte votre vie privée</p>
                <p class="text-xs text-base-content/60 mt-0.5 leading-relaxed">
                    Nous utilisons uniquement des cookies essentiels au fonctionnement du site (session, sécurité).
                    Aucun traceur publicitaire ni partage de données à des tiers.
                    <a href="{{ route('rgpd') }}" class="link link-primary font-medium">En savoir plus →</a>
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0 w-full sm:w-auto">
            <a href="{{ route('rgpd') }}" class="btn btn-ghost btn-sm text-xs">Politique RGPD</a>
            <button onclick="acceptRgpd()" class="btn btn-primary btn-sm text-xs gap-1">
                <x-icon name="o-check" class="w-3.5 h-3.5" />
                J'accepte
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        if (!localStorage.getItem('rgpd_accepted')) {
            document.getElementById('rgpd-banner').classList.remove('hidden');
        }
    })();

    function acceptRgpd() {
        localStorage.setItem('rgpd_accepted', '1');
        var banner = document.getElementById('rgpd-banner');
        banner.style.transition = 'opacity 0.3s, transform 0.3s';
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(16px)';
        setTimeout(function () { banner.classList.add('hidden'); }, 320);
    }
</script>

</body>
</html>
