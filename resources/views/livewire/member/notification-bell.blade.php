<div x-data="{ open: false }" @click.outside="open = false" class="relative">

    {{-- Bouton cloche --}}
    <button @click="open = !open"
            class="btn btn-ghost btn-circle btn-sm"
            title="Notifications">
        <div class="indicator">
            <x-icon name="o-bell" class="w-5 h-5" />
            @if($unreadCount > 0)
                <span class="badge badge-xs badge-error indicator-item">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </div>
    </button>

    {{-- Dropdown --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute right-0 top-full mt-2 w-80 bg-base-100 rounded-xl shadow-xl border border-base-200 z-50 overflow-hidden"
         style="display:none;">

        {{-- En-tête --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-base-200 bg-base-50">
            <span class="font-semibold text-sm">Notifications</span>
            @if($unreadCount > 0)
                <button wire:click="markAllRead"
                        class="text-xs text-primary hover:underline">
                    Tout marquer comme lu
                </button>
            @endif
        </div>

        {{-- Liste --}}
        <div class="divide-y divide-base-200/60 max-h-96 overflow-y-auto">
            @forelse($notifications as $notif)
                @php
                    $data        = $notif->data;
                    $isUnread    = is_null($notif->read_at);
                    $isMentoring = ($data['type'] ?? '') === 'mentoring_request_reviewed';
                    $notifIcon   = $isMentoring ? 'o-academic-cap' : 'o-user-plus';
                @endphp
                <div class="px-4 py-3 {{ $isUnread ? 'bg-primary/5' : '' }} hover:bg-base-200/60 transition-colors">
                    <div class="flex gap-3 items-start">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 mt-0.5">
                            <x-icon :name="$notifIcon" class="w-4 h-4 text-primary" />
                        </div>
                        <div class="flex-1 min-w-0">
                            @if($isMentoring)
                                <p class="text-sm leading-snug">
                                    Votre demande de mentorat avec
                                    <span class="font-semibold">{{ $data['mentor_name'] ?? '?' }}</span>
                                    a été
                                    <span class="{{ ($data['status'] ?? '') === 'approved' ? 'text-success font-semibold' : 'text-error' }}">
                                        {{ ($data['status'] ?? '') === 'approved' ? 'approuvée ✓' : 'refusée' }}
                                    </span>
                                </p>
                            @else
                                <p class="text-sm leading-snug">
                                    <span class="font-semibold">{{ $data['sender_name'] ?? '?' }}</span>
                                    souhaite se mettre en relation avec vous
                                </p>
                                @if(!empty($data['message']))
                                    <p class="text-xs text-base-content/50 mt-0.5 truncate">
                                        « {{ $data['message'] }} »
                                    </p>
                                @endif
                            @endif
                            <p class="text-xs text-base-content/40 mt-1">
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @if($isUnread)
                            <div class="w-2 h-2 rounded-full bg-primary mt-1.5 shrink-0"></div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-base-content/40">
                    <x-icon name="o-bell-slash" class="w-8 h-8 mx-auto mb-2 opacity-40" />
                    <p>Aucune notification</p>
                </div>
            @endforelse
        </div>

        {{-- Pied --}}
        <div class="px-4 py-2.5 border-t border-base-200 bg-base-50">
            <a href="{{ route('membre.contacts') }}"
               @click="open = false"
               class="text-xs text-primary hover:underline block text-center">
                Voir toutes les demandes de mise en relation →
            </a>
        </div>
    </div>

</div>
