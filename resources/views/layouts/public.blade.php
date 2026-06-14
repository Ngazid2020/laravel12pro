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
        <a href="{{ route('member.login') }}" class="btn btn-ghost btn-sm">Se connecter</a>
        <a href="#postuler" class="btn btn-primary btn-sm">Rejoindre</a>
    </x-slot:actions>
</x-nav>

{{ $slot }}

<footer class="bg-base-200 border-t border-base-300 py-8 mt-16">
    <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-base-content/60">
        <div class="flex items-center gap-2">
            <x-icon name="o-rocket-launch" class="text-primary w-5 h-5" />
            <span class="font-bold text-base-content">Réseau Entrepreneurs Comores</span>
        </div>
        <p>© {{ date('Y') }} · contact@reseau-entrepreneurs.km</p>
    </div>
</footer>

</body>
</html>
