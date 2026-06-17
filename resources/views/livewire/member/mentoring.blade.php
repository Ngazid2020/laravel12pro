<div class="p-4 lg:p-6 space-y-6">

    <h1 class="text-2xl font-bold">Mentorat</h1>

    {{-- ── Onglets ── --}}
    <div role="tablist" class="tabs tabs-bordered">
        <button role="tab" wire:click="$set('activeTab','sessions')"
                class="tab {{ $activeTab === 'sessions' ? 'tab-active font-semibold' : '' }}">
            <x-icon name="o-calendar-days" class="w-4 h-4 mr-1" /> Sessions
        </button>
        <button role="tab" wire:click="$set('activeTab','organigramme')"
                class="tab {{ $activeTab === 'organigramme' ? 'tab-active font-semibold' : '' }}">
            <x-icon name="o-rectangle-group" class="w-4 h-4 mr-1" /> Organigramme
        </button>
    </div>

    {{-- ======================================================
         ONGLET SESSIONS
    ====================================================== --}}
    @if($activeTab === 'sessions')

    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-user-circle" class="w-5 h-5" /> Mes sessions (en tant que mentoré)
        </x-slot:title>

        @if(!$hasMentor)
            <x-alert icon="o-information-circle" class="alert-info">
                Vous n'avez pas encore de mentor assigné. Contactez l'administration pour être mis en relation.
            </x-alert>
        @else
            <div class="mb-3">
                <x-button
                    label="Demander une session"
                    icon="o-plus"
                    class="btn-primary btn-sm"
                    wire:click="$set('showRequest', true)"
                />
            </div>

            @forelse($menteeSessions as $session)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-lg bg-base-200 mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $statusColors[$session->status] ?? 'badge-ghost' }} badge-xs">
                                {{ $statusLabels[$session->status] ?? $session->status }}
                            </span>
                            <span class="text-sm font-medium">
                                {{ $session->scheduled_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                            </span>
                        </div>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            Mentor : {{ $session->mentor->name ?? '—' }}
                        </p>
                        @if($session->notes)
                            <p class="text-xs text-base-content/60 mt-1 italic">{{ $session->notes }}</p>
                        @endif
                    </div>

                    @if($session->status === 'confirmed' && !$session->confirmed_by_mentee)
                        <x-button
                            label="Confirmer la tenue"
                            icon="o-check"
                            class="btn-success btn-sm"
                            wire:click="confirmSession({{ $session->id }})"
                        />
                    @elseif($session->confirmed_by_mentee)
                        <span class="badge badge-success badge-xs gap-1">
                            <x-icon name="o-check" class="w-3 h-3" /> Confirmée
                        </span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-base-content/50 italic">Aucune session pour le moment.</p>
            @endforelse
        @endif
    </x-card>

    @if($mentorSessions->isNotEmpty())
        <x-card shadow class="border border-base-200">
            <x-slot:title>
                <x-icon name="o-academic-cap" class="w-5 h-5" /> Sessions avec mes affiliés (en tant que mentor)
            </x-slot:title>

            @foreach($mentorSessions as $session)
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-lg bg-base-200 mb-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $statusColors[$session->status] ?? 'badge-ghost' }} badge-xs">
                                {{ $statusLabels[$session->status] ?? $session->status }}
                            </span>
                            <span class="text-sm font-medium">
                                {{ $session->scheduled_at->isoFormat('D MMM YYYY [à] HH[h]mm') }}
                            </span>
                        </div>
                        <p class="text-xs text-base-content/50 mt-0.5">
                            Mentoré : {{ $session->mentee->name ?? '—' }}
                        </p>
                        @if($session->notes)
                            <p class="text-xs text-base-content/60 mt-1 italic">{{ $session->notes }}</p>
                        @endif
                    </div>

                    <x-button
                        :label="$session->status === 'confirmed' ? 'Modifier notes' : 'Valider & noter'"
                        icon="o-pencil-square"
                        class="btn-outline btn-sm"
                        wire:click="openNotes({{ $session->id }})"
                    />
                </div>
            @endforeach
        </x-card>
    @endif

    @endif {{-- /sessions --}}

    {{-- ======================================================
         ONGLET ORGANIGRAMME
    ====================================================== --}}
    @if($activeTab === 'organigramme')

    <x-card shadow class="border border-base-200">
        <x-slot:title>
            <x-icon name="o-rectangle-group" class="w-5 h-5" /> Arbre de mentorat
        </x-slot:title>

        @if(!auth()->user()->profile || !$treeJson)
            <x-alert icon="o-information-circle" class="alert-info">
                Votre profil n'est pas encore configuré ou vous n'avez pas encore de liens de mentorat.
            </x-alert>
        @else

        {{--
            Le CSS utilise des noms de classes personnalisés (.oc-*) et non des classes Tailwind,
            pour éviter toute purge par le JIT de Vite.
        --}}
        <style>
            .oc-tree { display:inline-flex; min-width:100%; justify-content:center; padding:10px 20px 30px; }

            .oc-tree ul { display:flex; justify-content:center; padding-top:30px;
                          position:relative; list-style:none; margin:0; padding-left:0; }

            .oc-tree li { display:flex; flex-direction:column; align-items:center;
                          text-align:center; list-style:none; position:relative;
                          padding:0 10px; transition:all .3s; }

            /* Ligne verticale montant vers la barre horizontale */
            .oc-tree li::before { content:''; position:absolute; top:0; left:50%;
                                   transform:translateX(-50%); width:2px; height:30px; background:#cbd5e1; }

            /* Barre horizontale reliant les frères */
            .oc-tree li::after { content:''; position:absolute; top:0; left:0;
                                  right:0; height:2px; background:#cbd5e1; }

            .oc-tree li:first-child::after { left:50%; }
            .oc-tree li:last-child::after  { right:50%; }
            .oc-tree li:only-child::after  { display:none; }

            /* Le premier niveau (racine) n'a pas de connecteurs vers le haut */
            .oc-tree > ul > li::before,
            .oc-tree > ul > li::after { display:none; }

            /* Nœud replié : masque ses enfants */
            .oc-tree li.oc-collapsed > ul { display:none; }

            /* Conteneur de la carte (avatar + card) */
            .oc-wrap { display:inline-flex; flex-direction:column; align-items:center;
                       position:relative; padding-top:28px; }

            /* Ligne verticale descendant vers les enfants */
            .oc-wrap::after { content:''; position:absolute; bottom:-30px; left:50%;
                               transform:translateX(-50%); width:2px; height:30px; background:#cbd5e1; }

            .oc-wrap.no-ch::after,
            .oc-collapsed .oc-wrap::after { display:none; }

            /* Carte */
            .oc-card { padding:.85rem 1.1rem; background:#fff; border-radius:8px;
                       display:inline-block; min-width:140px; max-width:180px;
                       box-shadow:0 4px 6px -1px rgba(0,0,0,.08),0 2px 4px -1px rgba(0,0,0,.05);
                       border-top:4px solid; transition:box-shadow .2s,transform .2s; cursor:default; }
            .oc-card:hover { box-shadow:0 10px 15px -3px rgba(0,0,0,.1); transform:translateY(-2px); }

            /* Avatar circulaire positionné au-dessus de la carte */
            .oc-avatar { width:52px; height:52px; border-radius:50%; border:3px solid;
                         background:#fff; position:absolute; top:0; left:50%;
                         transform:translate(-50%,0); z-index:1; object-fit:cover; }

            .oc-name  { font-weight:700; color:#334155; font-size:13px; line-height:1.3;
                        margin-top:2px; }
            .oc-role  { font-size:11px; color:#64748b; margin-top:2px; max-width:160px;
                        overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
            .oc-badge { display:inline-block; font-size:10px; font-weight:600;
                        padding:1px 7px; border-radius:9999px; margin-top:5px; }
            .oc-b-self   { background:#dbeafe; color:#1e40af; }
            .oc-b-mentor { background:#ede9fe; color:#4c1d95; }

            /* Bouton collapse */
            .oc-toggle { position:absolute; bottom:-10px; left:50%; transform:translateX(-50%);
                         width:20px; height:20px; background:#fff; color:#475569;
                         border-radius:50%; display:flex; align-items:center; justify-content:center;
                         font-size:13px; font-weight:bold; cursor:pointer;
                         border:1px solid #cbd5e1; user-select:none; z-index:10;
                         transition:background .15s; line-height:1; }
            .oc-toggle:hover { background:#f1f5f9; }
        </style>

        {{--
            x-data + x-init : Alpine initialise le conteneur APRÈS que Livewire a injecté le DOM.
            $nextTick garantit que la peinture du navigateur est terminée avant l'init du chart.
            wire:ignore : protège le contenu JS-généré contre les re-rendus Livewire ultérieurs.
            initOrgChart est défini dans resources/js/orgchart.js (bundle Vite).
        --}}
        <div id="oc-container"
             class="w-full overflow-hidden rounded-xl bg-slate-50 relative"
             style="min-height:480px; cursor:grab;"
             x-data
             x-init="$nextTick(() => window.initOrgChart($el))"
             wire:ignore>
            <div id="oc-chart" class="oc-tree" data-tree="{{ json_encode($treeJson) }}"></div>

            {{-- Boutons zoom/recentrer --}}
            <div class="absolute bottom-3 right-3 flex gap-1 z-20">
                <button onclick="window.ocZoom(.12)"
                        class="w-8 h-8 bg-white shadow rounded-lg flex items-center justify-center text-lg font-bold text-slate-500 hover:bg-slate-50 select-none">+</button>
                <button onclick="window.ocZoom(-.12)"
                        class="w-8 h-8 bg-white shadow rounded-lg flex items-center justify-center text-lg font-bold text-slate-500 hover:bg-slate-50 select-none">−</button>
                <button onclick="window.ocReset()"
                        class="w-8 h-8 bg-white shadow rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-50 select-none"
                        title="Recentrer">
                    <x-icon name="o-arrows-pointing-in" class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Légende --}}
        <div class="flex flex-wrap gap-4 mt-3 text-xs text-base-content/60 justify-center">
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded" style="background:#dbeafe;border:2px solid #6366f1"></span> Vous
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded" style="background:#ede9fe;border:2px solid #7c3aed"></span> Mentor
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block w-3 h-3 rounded" style="background:#ccfbf1;border:2px solid #2dd4bf"></span> Mentorés
            </span>
            <span class="flex items-center gap-1.5 text-base-content/40">
                <x-icon name="o-cursor-arrow-rays" class="w-3.5 h-3.5" />
                Glisser · Molette pour zoomer · <strong>+/−</strong> pour plier
            </span>
        </div>

        @endif
    </x-card>

    @endif {{-- /organigramme --}}

    {{-- Modal demande de session --}}
    <x-modal wire:model="showRequest" title="Demander une session de mentorat" class="backdrop-blur">
        <x-form wire:submit="requestSession">
            <x-input
                label="Date et heure souhaitées"
                wire:model="scheduledAt"
                type="datetime-local"
                required
            />
            <x-textarea
                label="Notes / sujet de la session (optionnel)"
                wire:model="requestNotes"
                rows="3"
                placeholder="Points que vous souhaitez aborder…"
            />
            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showRequest', false)" class="btn-ghost" />
                <x-button label="Envoyer la demande" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- Modal notes mentor --}}
    <x-modal wire:model="showNotes" title="Notes de session" class="backdrop-blur">
        <x-form wire:submit="saveNotes">
            <x-textarea
                label="Notes de la session"
                wire:model="sessionNotes"
                rows="4"
                placeholder="Résumé de la session, actions de suivi…"
            />
            <x-slot:actions>
                <x-button label="Annuler" wire:click="$set('showNotes', false)" class="btn-ghost" />
                <x-button label="Enregistrer et confirmer" type="submit" class="btn-success" />
            </x-slot:actions>
        </x-form>
    </x-modal>

</div>
