<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="flex items-center justify-center gap-2 mb-2">
            <x-icon name="o-rocket-launch" class="text-primary w-10 h-10" />
        </div>
        <h1 class="text-3xl font-extrabold text-primary">Réseau Entrepreneurs</h1>
        <p class="text-base-content/60 mt-1">Réinitialisation du mot de passe</p>
    </div>

    <x-card shadow class="border border-base-200">
        @if($sent)
            <div class="text-center py-4 space-y-3">
                <x-icon name="o-envelope-open" class="w-14 h-14 text-success mx-auto" />
                <p class="font-semibold">Vérifiez votre boîte mail</p>
                <p class="text-sm text-base-content/60">
                    Si un compte existe avec l'adresse <strong>{{ $email }}</strong>,
                    vous recevrez un email avec un lien pour définir votre mot de passe.
                </p>
            </div>
        @else
            <p class="text-sm text-base-content/60 mb-4">
                Entrez votre adresse email pour recevoir un lien de réinitialisation.
            </p>
            <x-form wire:submit="send">
                <x-input
                    label="Adresse email"
                    wire:model="email"
                    type="email"
                    icon="o-envelope"
                    placeholder="vous@exemple.com"
                    autofocus
                />
                <x-slot:actions>
                    <x-button
                        label="Envoyer le lien"
                        icon="o-paper-airplane"
                        class="btn-primary w-full"
                        type="submit"
                        spinner="send"
                    />
                </x-slot:actions>
            </x-form>
        @endif
    </x-card>

    <p class="text-center text-sm text-base-content/50 mt-6">
        <a href="{{ route('member.login') }}" class="link link-primary">
            ← Retour à la connexion
        </a>
    </p>
</div>
