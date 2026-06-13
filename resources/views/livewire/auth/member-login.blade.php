<div class="w-full max-w-md">
    {{-- Brand --}}
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-2 mb-2">
            <x-icon name="o-rocket-launch" class="text-primary w-10 h-10" />
        </div>
        <h1 class="text-3xl font-extrabold text-primary">Réseau Entrepreneurs</h1>
        <p class="text-base-content/60 mt-1">Connectez-vous à votre espace membre</p>
    </div>

    <x-card shadow class="border border-base-200">
        <x-form wire:submit="login">
            <x-input
                label="Adresse email"
                wire:model="email"
                type="email"
                icon="o-envelope"
                placeholder="vous@exemple.com"
                autofocus
            />

            <x-input
                label="Mot de passe"
                wire:model="password"
                type="password"
                icon="o-lock-closed"
                placeholder="••••••••"
            />

            <x-checkbox label="Se souvenir de moi" wire:model="remember" />

            <x-slot:actions>
                <x-button
                    label="Se connecter"
                    icon="o-arrow-right-end-on-rectangle"
                    class="btn-primary w-full"
                    type="submit"
                    spinner="login"
                />
            </x-slot:actions>
        </x-form>
    </x-card>

    <p class="text-center text-sm text-base-content/50 mt-6">
        Pas encore membre ?
        <span class="font-medium text-primary">Contactez l'administration pour soumettre votre candidature.</span>
    </p>
</div>
