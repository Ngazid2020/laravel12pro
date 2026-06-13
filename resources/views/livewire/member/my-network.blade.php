<div class="p-4 lg:p-6 space-y-6">

    <h1 class="text-2xl font-bold">Mon Réseau</h1>

    {{-- Stats rapides --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
        <x-stat
            title="Affiliés directs"
            :value="$directMentees->count()"
            icon="o-user-plus"
            color="text-primary"
        />
        <x-stat
            title="Réseau total"
            :value="$subtreeCount"
            icon="o-share"
            color="text-secondary"
        />
        <x-stat
            title="Points parrainage"
            :value="$referralPoints"
            icon="o-star"
            color="text-warning"
            class="col-span-2 sm:col-span-1"
        />
    </div>

    {{-- Mentor --}}
    @if($mentor)
        <x-card shadow class="border border-base-200">
            <x-slot:title>
                <x-icon name="o-academic-cap" class="w-5 h-5" />
                Mon mentor
            </x-slot:title>

            <div class="flex items-center gap-3">
                <x-avatar
                    :placeholder="substr($mentor->name, 0, 1)"
                    class="!w-12 !h-12 bg-secondary text-secondary-content"
                />
                <div>
                    <p class="font-semibold">{{ $mentor->full_name }}</p>
                    @if($mentor->profile?->company_name)
                        <p class="text-sm text-base-content/60">{{ $mentor->profile->company_name }}</p>
                    @endif
                    @if($mentor->profile?->sector)
                        <span class="badge badge-ghost badge-xs">{{ $mentor->profile->sector }}</span>
                    @endif
                </div>
            </div>
        </x-card>
    @else
        <x-alert icon="o-information-circle" class="alert-info">
            Vous n'avez pas encore de mentor assigné.
        </x-alert>
    @endif

    {{-- Affiliés directs --}}
    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-users" class="w-5 h-5" />
            Mes affiliés ({{ $directMentees->count() }})
        </x-slot:title>

        @if($directMentees->isEmpty())
            <div class="text-center py-8">
                <x-icon name="o-user-plus" class="w-12 h-12 text-base-content/20 mx-auto mb-2" />
                <p class="text-base-content/50 text-sm">Vous n'avez pas encore d'affiliés.</p>
                @if($profile?->referral_code)
                    <p class="text-sm mt-2">Partagez votre code de parrainage :
                        <span class="font-mono font-bold text-primary">{{ $profile->referral_code }}</span>
                    </p>
                @endif
            </div>
        @else
            @if($profile?->referral_code)
                <div class="mb-4 p-3 bg-base-200 rounded-lg flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs text-base-content/60">Votre code de parrainage</p>
                        <p class="font-mono font-bold text-primary text-lg tracking-widest">{{ $profile->referral_code }}</p>
                    </div>
                    <x-button
                        icon="o-clipboard"
                        class="btn-ghost btn-sm"
                        tooltip="Copier"
                        x-on:click="navigator.clipboard.writeText('{{ $profile->referral_code }}')"
                    />
                </div>
            @endif

            <div class="space-y-3">
                @foreach($directMentees as $mentee)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-base-200 transition-colors">
                        <x-avatar
                            :placeholder="substr($mentee->user->name, 0, 1)"
                            class="!w-10 !h-10 bg-primary text-primary-content flex-shrink-0"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $mentee->user->full_name }}</p>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($mentee->company_name)
                                    <p class="text-xs text-base-content/60 truncate">{{ $mentee->company_name }}</p>
                                @endif
                                <span class="badge badge-xs {{ $mentee->statusColor() }}">
                                    {{ $mentee->statusLabel() }}
                                </span>
                            </div>
                        </div>
                        @if($mentee->activated_at)
                            <p class="text-xs text-base-content/40 flex-shrink-0">
                                depuis {{ $mentee->activated_at->format('M Y') }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    {{-- Note anti-pyramidale --}}
    <x-alert icon="o-shield-check" class="alert-info">
        <p class="text-sm">
            <strong>Transparence :</strong> les points de parrainage sont crédités uniquement pour le recrutement direct d'un nouveau membre actif. Il n'y a aucune commission sur les cotisations ou l'activité des affiliés de vos affiliés.
        </p>
    </x-alert>

</div>
