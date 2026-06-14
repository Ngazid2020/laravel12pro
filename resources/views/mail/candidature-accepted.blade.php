<x-mail::message>
# Bienvenue dans le réseau ! 🎉

Cher(e) **{{ $candidature->user->full_name }}**,

Nous avons le plaisir de vous informer que votre candidature au **Réseau des Jeunes Entrepreneurs des Comores** a été **acceptée**.

Votre compte membre est désormais actif. Vous pouvez dès maintenant accéder à votre espace personnel et profiter de tous les avantages du réseau :

- 📚 Catalogue de formations
- 💼 Opportunités (appels d'offres, missions, financements)
- 🗓️ Événements networking et conférences
- 🤝 Mise en relation avec d'autres entrepreneurs
- 📈 Suivi de votre progression

@if($setupUrl)
**Première étape : définissez votre mot de passe en cliquant sur le bouton ci-dessous.**

<x-mail::button :url="$setupUrl" color="primary">
Définir mon mot de passe
</x-mail::button>

> Ce lien est valable **60 minutes**. Si vous ne l'utilisez pas à temps, vous pourrez en demander un nouveau depuis la page de connexion.

---

Une fois votre mot de passe défini, connectez-vous avec :
- Email : `{{ $candidature->user->email }}`
@else
<x-mail::button :url="route('member.login')" color="primary">
Accéder à mon espace membre
</x-mail::button>
@endif

---

En cas de question, contactez-nous à **contact@reseau-entrepreneurs.km**

Cordialement,
**L'équipe du Réseau Entrepreneurs Comores**
</x-mail::message>
