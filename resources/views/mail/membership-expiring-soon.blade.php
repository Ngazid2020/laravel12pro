<x-mail::message>
# Votre cotisation expire bientôt ⚠️

Cher(e) **{{ $profile->user->full_name }}**,

Votre cotisation au **Réseau des Jeunes Entrepreneurs des Comores** expire le **{{ $profile->membership_expires_at->format('d/m/Y') }}**, soit dans **{{ $profile->membership_expires_at->diffInDays() }} jour(s)**.

Pour continuer à bénéficier de tous les avantages du réseau, pensez à renouveler votre adhésion avant cette date.

<x-mail::button :url="route('membre.payments')" color="warning">
Renouveler ma cotisation
</x-mail::button>

**Comment renouveler ?**

1. Connectez-vous à votre espace membre
2. Rendez-vous dans la section **Mes Paiements**
3. Cliquez sur **Déclarer un paiement** et choisissez votre plan
4. Notre équipe validera votre paiement dans les meilleurs délais

---

En cas de question, contactez-nous à **contact@reseau-entrepreneurs.km**

Cordialement,
**L'équipe du Réseau Entrepreneurs Comores**
</x-mail::message>
