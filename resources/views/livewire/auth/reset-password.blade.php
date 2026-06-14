<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-2 mb-2">
            <x-icon name="o-rocket-launch" class="text-primary w-10 h-10" />
        </div>
        <h1 class="text-3xl font-extrabold text-primary">Réseau Entrepreneurs</h1>
        <p class="text-base-content/60 mt-1">Définir votre mot de passe</p>
    </div>

    @if(session('success'))
        <x-alert icon="o-check-circle" class="alert-success mb-4">
            {{ session('success') }}
        </x-alert>
    @endif

    <x-card shadow class="border border-base-200">
        <x-form wire:submit="save">
            <x-input
                label="Adresse email"
                wire:model="email"
                type="email"
                icon="o-envelope"
                readonly
            />
            <x-input
                label="Nouveau mot de passe"
                wire:model="password"
                type="password"
                icon="o-lock-closed"
                placeholder="Minimum 8 caractères"
            />
            <x-input
                label="Confirmer le mot de passe"
                wire:model="passwordConfirmation"
                type="password"
                icon="o-lock-closed"
                placeholder="Répétez le mot de passe"
            />
            <x-slot:actions>
                <x-button
                    label="Enregistrer le mot de passe"
                    icon="o-check"
                    class="btn-primary w-full"
                    type="submit"
                    spinner="save"
                />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
