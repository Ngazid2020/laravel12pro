<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' — Réseau Entrepreneurs' : 'Réseau Entrepreneurs' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

{{-- Navbar mobile --}}
<x-nav sticky class="lg:hidden">
    <x-slot:brand>
        <span class="font-bold text-primary text-lg">Réseau</span>
    </x-slot:brand>
    <x-slot:actions>
        <label for="main-drawer" class="lg:hidden me-3">
            <x-icon name="o-bars-3" class="cursor-pointer" />
        </label>
    </x-slot:actions>
</x-nav>

<x-main full-width>
    {{-- SIDEBAR --}}
    <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-100 shadow-sm">

        {{-- Brand --}}
        <div class="px-5 pt-5 pb-3 flex items-center gap-2">
            <x-icon name="o-rocket-launch" class="text-primary w-7 h-7" />
            <span class="font-extrabold text-xl text-primary">Réseau</span>
            <span class="font-light text-xl text-base-content">Entrepreneurs</span>
        </div>

        <x-menu activate-by-route>

            {{-- Profil utilisateur --}}
            @auth
                @php $profile = auth()->user()->profile; @endphp
                <div class="px-4 py-3 border-b border-base-200">
                    <div class="flex items-center gap-3">
                        <x-avatar
                            :placeholder="substr(auth()->user()->name, 0, 1)"
                            class="!w-10 !h-10 bg-primary text-primary-content"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm truncate">{{ auth()->user()->name }}</p>
                            @if($profile)
                                <span class="badge badge-xs {{ $profile->statusColor() }}">
                                    {{ $profile->statusLabel() }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endauth

            <x-menu-separator />

            <x-menu-item title="Tableau de bord" icon="o-home" :link="route('membre.dashboard')" />
            <x-menu-item title="Mon profil" icon="o-user-circle" :link="route('membre.profile')" />
            <x-menu-item title="Annuaire" icon="o-users" :link="route('membre.directory')" />

            <x-menu-separator />

            <x-menu-item title="Opportunités" icon="o-briefcase" :link="route('membre.opportunities')" />
            <x-menu-item title="Formations" icon="o-book-open" :link="route('membre.trainings')" />
            <x-menu-item title="Événements" icon="o-calendar-days" :link="route('membre.events')" />

            <x-menu-separator />

            <x-menu-item title="Recommandations" icon="o-arrow-path-rounded-square" :link="route('membre.recommendations')" />
            <x-menu-item title="Mise en relation" icon="o-user-plus" :link="route('membre.contacts')" />

            <x-menu-separator />

            <x-menu-item title="Mes paiements" icon="o-banknotes" :link="route('membre.payments')" />
            <x-menu-item title="Mentorat" icon="o-academic-cap" :link="route('membre.mentoring')" />
            <x-menu-item title="Ma progression" icon="o-trophy" :link="route('membre.progress')" />

            @auth
                @if(auth()->user()->profile?->directMentees()->exists())
                    <x-menu-item title="Mon réseau" icon="o-share" :link="route('membre.network')" />
                @endif
            @endauth

            <x-menu-separator />

            @auth
                @if(auth()->user()->hasRole(['super_admin', 'admin']))
                    <x-menu-item title="Back-office" icon="o-shield-check" link="/admin" class="text-warning" no-wire-navigate />
                    <x-menu-separator />
                @endif
            @endauth

            <form id="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
            </form>
            <x-menu-item
                title="Déconnexion"
                icon="o-arrow-right-start-on-rectangle"
                x-on:click.prevent="document.getElementById('logout-form').submit()"
            />
        </x-menu>
    </x-slot:sidebar>

    {{-- Contenu principal --}}
    <x-slot:content>
        {{-- Alerte suspension / warning flash --}}
        @if(session('warning'))
            <div class="alert alert-warning mb-4 mx-4 mt-4">
                <x-icon name="o-exclamation-triangle" class="w-5 h-5" />
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        {{ $slot }}
    </x-slot:content>
</x-main>

<x-toast />

</body>
</html>
