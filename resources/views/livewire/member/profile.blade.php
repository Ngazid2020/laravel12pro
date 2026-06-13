<div class="p-4 lg:p-6 max-w-3xl mx-auto space-y-6">

    <h1 class="text-2xl font-bold">Mon profil</h1>

    <x-form wire:submit="save">

        {{-- Identité --}}
        <x-card title="Identité" shadow class="border border-base-200">
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                {{-- Avatar --}}
                <div class="flex flex-col items-center gap-2">
                    @if(auth()->user()->avatar)
                        <img
                            src="{{ Storage::url(auth()->user()->avatar) }}"
                            class="w-20 h-20 rounded-full object-cover ring-2 ring-primary"
                            alt="Avatar"
                        />
                    @else
                        <x-avatar
                            :placeholder="substr(auth()->user()->name, 0, 1)"
                            class="!w-20 !h-20 text-2xl bg-primary text-primary-content"
                        />
                    @endif
                    <x-file wire:model="avatar" label="" accept="image/*" class="file-input-xs" />
                </div>

                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                    <x-input label="Prénom" wire:model="first_name" />
                    <x-input label="Nom" wire:model="last_name" />
                    <x-input label="Nom d'affichage" wire:model="name" required />
                    <x-input label="Téléphone" wire:model="phone" type="tel" />
                </div>
            </div>
        </x-card>

        {{-- Entreprise / Projet --}}
        <x-card title="Entreprise & Projet" shadow class="border border-base-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input label="Entreprise" wire:model="company_name" />
                <x-input label="Projet" wire:model="project_name" />
                <x-input label="Secteur d'activité" wire:model="sector" />
                <x-input label="Ville" wire:model="city" />
            </div>
            <div class="mt-4">
                <x-textarea
                    label="Biographie / Présentation"
                    wire:model="bio"
                    rows="4"
                    hint="Max. 1 000 caractères"
                />
            </div>
        </x-card>

        {{-- Compétences & Besoins --}}
        <x-card title="Compétences & Besoins" shadow class="border border-base-200">
            <x-tags
                label="Compétences proposées"
                wire:model="skills_offered"
                hint="Appuyez sur Entrée pour ajouter"
            />
            <div class="mt-4">
                <x-tags
                    label="Besoins exprimés"
                    wire:model="needs_expressed"
                    hint="Appuyez sur Entrée pour ajouter"
                />
            </div>
        </x-card>

        {{-- Réseaux sociaux --}}
        <x-card title="Réseaux sociaux" shadow class="border border-base-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    label="LinkedIn"
                    wire:model="social_links.linkedin"
                    icon="o-link"
                    placeholder="https://linkedin.com/in/..."
                />
                <x-input
                    label="Facebook"
                    wire:model="social_links.facebook"
                    icon="o-link"
                    placeholder="https://facebook.com/..."
                />
                <x-input
                    label="Site web"
                    wire:model="social_links.website"
                    icon="o-globe-alt"
                    placeholder="https://..."
                />
                <x-input
                    label="Autre"
                    wire:model="social_links.other"
                    icon="o-link"
                />
            </div>
        </x-card>

        {{-- Infos adhésion (lecture seule) --}}
        @if($profile)
            <x-card title="Adhésion" shadow class="border border-base-200">
                <div class="flex flex-wrap gap-4">
                    <div>
                        <p class="text-xs text-base-content/50">Statut</p>
                        <span class="badge {{ $profile->statusColor() }}">{{ $profile->statusLabel() }}</span>
                    </div>
                    @if($profile->membership_expires_at)
                        <div>
                            <p class="text-xs text-base-content/50">Expiration</p>
                            <p class="font-semibold text-sm">{{ $profile->membership_expires_at->format('d/m/Y') }}</p>
                        </div>
                    @endif
                    @if($profile->referral_code)
                        <div>
                            <p class="text-xs text-base-content/50">Code parrainage</p>
                            <code class="badge badge-outline font-mono">{{ $profile->referral_code }}</code>
                        </div>
                    @endif
                    @if($profile->mentor)
                        <div>
                            <p class="text-xs text-base-content/50">Mon mentor</p>
                            <p class="font-semibold text-sm">{{ $profile->mentor->name }}</p>
                        </div>
                    @endif
                </div>
            </x-card>
        @endif

        <x-slot:actions>
            <x-button
                label="Enregistrer"
                icon="o-check"
                class="btn-primary"
                type="submit"
                spinner="save"
            />
        </x-slot:actions>
    </x-form>

</div>
