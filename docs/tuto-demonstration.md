# Tutoriel de démonstration — Réseau Entrepreneurs Comores

> Scénario complet de A à Z · Durée estimée : 20–30 minutes  
> Prérequis : serveur démarré (`composer dev`), base de données fraîche (`php artisan migrate:fresh --seed`)

---

## Personnages du scénario

| Rôle | Nom | Email | Mot de passe |
|---|---|---|---|
| Administrateur | (existant) | admin@reseau.km | Admin@2026! |
| Candidat / futur membre | Fatouma Soilihi | fatouma@example.km | *(défini à l'étape 3)* |
| Membre existant | Hamid Abdou | hamid@reseau.km | Membre@2026! |

---

## PARTIE 1 — La candidature (vue visiteur)

### Étape 1 · Soumettre une candidature depuis la vitrine

1. Ouvrir un navigateur en **navigation privée**
2. Aller sur **`http://laravel12pro.test/`**
3. Faire défiler jusqu'à la section **"Déposez votre candidature"**
4. Remplir le formulaire :

   | Champ | Valeur à saisir |
   |---|---|
   | Prénom | `Fatouma` |
   | Nom | `Soilihi` |
   | Email | `fatouma@example.km` |
   | Téléphone | `+269 321 45 67` |
   | Entreprise / Projet | `Épices de Ngazidja` |
   | Secteur | `Agroalimentaire` |
   | Lettre de motivation | `Je dirige une petite entreprise de transformation et commercialisation d'épices locales à Moroni depuis 2023. Je souhaite rejoindre le réseau pour accéder aux formations en gestion et développer des partenariats à l'export. Mon objectif est d'atteindre les marchés de La Réunion et de Mayotte d'ici 2027.` |

5. Cliquer sur **"Envoyer ma candidature"**
6. Le message de confirmation s'affiche : *"Votre candidature a bien été reçue"*

> **Ce qui s'est passé en coulisses :** un utilisateur + une candidature ont été créés en base. La candidature est en statut "En attente".

---

## PARTIE 2 — Le traitement admin

### Étape 2 · Accepter la candidature

1. Ouvrir un **nouvel onglet** (ou un autre navigateur)
2. Aller sur **`http://laravel12pro.test/admin`**
3. Se connecter : `admin@reseau.km` / `Admin@2026!`
4. Dans le menu gauche, cliquer sur **"Candidatures"**
   - Un badge **orange** indique qu'il y a une candidature en attente
5. La candidature de **Fatouma Soilihi** apparaît en tête de liste
6. Cliquer sur **"Voir"** pour lire sa lettre de motivation
7. Revenir à la liste, cliquer sur l'action **"Accepter"**
8. Confirmer dans le modal → cliquer sur **"Accepter"**

> La notification verte **"Candidature acceptée — email envoyé"** apparaît.  
> En coulisses : un profil membre actif est créé, un email avec lien de setup mot de passe est mis en file d'attente.

---

### Étape 3 · Récupérer le lien de setup (simulation email)

Puisqu'on est en développement, les emails sont enregistrés dans les logs.

1. Dans le terminal, taper :
   ```bash
   php artisan queue:work --once
   ```
   *(traite l'email en attente)*

2. Ouvrir le fichier de log :
   ```bash
   cat storage/logs/laravel.log | grep "password.reset"
   ```
   Ou, plus lisiblement, chercher l'URL dans le log :
   ```bash
   php artisan tinker
   # Puis :
   \Illuminate\Support\Facades\Password::broker()->createToken(\App\Models\User::where('email','fatouma@example.km')->first())
   ```
   Ce token + l'email serviront à construire l'URL :
   ```
   http://laravel12pro.test/reinitialiser-mot-de-passe/{TOKEN}?email=fatouma%40example.km
   ```

   > **En production :** Fatouma reçoit directement l'email avec le bouton cliquable.

---

## PARTIE 3 — Premier accès membre

### Étape 4 · Définir son mot de passe

1. Coller l'URL de reset dans la barre du navigateur (onglet navigation privée)
2. La page **"Définir mon mot de passe"** s'affiche
3. Saisir :
   - Mot de passe : `Fatouma@2026!`
   - Confirmation : `Fatouma@2026!`
4. Cliquer sur **"Enregistrer le mot de passe"**
5. Redirection automatique vers la page de connexion

---

### Étape 5 · Se connecter

1. Sur la page de connexion (`/login`) :
   - Email : `fatouma@example.km`
   - Mot de passe : `Fatouma@2026!`
2. Cliquer sur **"Se connecter"**
3. Arrivée sur le **tableau de bord membre**

---

### Étape 6 · Compléter son profil

1. Cliquer sur **"Mon profil"** dans le menu gauche
2. Remplir les champs :

   | Champ | Valeur |
   |---|---|
   | Biographie | `Entrepreneuse comorienne spécialisée dans la transformation d'épices. Fondatrice d'Épices de Ngazidja, je valorise les saveurs locales pour les marchés régionaux.` |
   | Ville | `Moroni` |
   | Secteur | `Agroalimentaire` |
   | Compétences offertes | `commerce, export, transformation alimentaire` |
   | Besoins exprimés | `financement, réseaux de distribution, formation en gestion` |

3. Cliquer sur **"Enregistrer"**

> Le profil est mis à jour. Fatouma est maintenant visible dans l'annuaire.

---

## PARTIE 4 — Les événements et le QR code

### Étape 7 · Créer un événement (admin)

*Basculer sur l'onglet admin.*

1. Menu gauche → **"Événements"**
2. Cliquer sur **"Nouveau"**
3. Remplir :

   | Champ | Valeur |
   |---|---|
   | Titre | `Networking Entrepreneurs — Moroni` |
   | Description | `Rencontre mensuelle des membres du réseau. Présentations croisées, échanges sectoriels et annonces d'opportunités.` |
   | Date de début | *(demain, 18h00)* |
   | Date de fin | *(demain, 21h00)* |
   | Lieu | `Centre Culturel Maecha, Moroni` |
   | Capacité max | `50` |
   | Statut | `Publié` |

4. Cliquer sur **"Enregistrer"**

---

### Étape 8 · S'inscrire à l'événement (membre)

*Basculer sur l'onglet de Fatouma.*

1. Menu gauche → **"Événements"**
2. L'événement **"Networking Entrepreneurs — Moroni"** apparaît dans l'onglet "À venir"
3. Cliquer sur **"S'inscrire"**
4. Confirmation affichée — le bouton devient **"Mon QR"**

---

### Étape 9 · Afficher le QR code

1. Cliquer sur **"Mon QR"**
2. Un QR code s'affiche dans un modal
3. Montrer au public : *"C'est ce QR que le staff scanne à l'entrée de l'événement"*
4. Copier l'URL de check-in depuis le QR (ou l'ouvrir dans un autre onglet pour simuler le scan)

> La page de confirmation affiche : **"✅ Présence enregistrée — Fatouma Soilihi"** avec le nom de l'événement.

---

## PARTIE 5 — Les paiements

### Étape 10 · Déclarer une cotisation (membre)

*Sur l'onglet de Fatouma.*

1. Menu gauche → **"Mes paiements"**
2. Cliquer sur **"Déclarer un paiement"**
3. Remplir :

   | Champ | Valeur |
   |---|---|
   | Plan | `Cotisation annuelle — 15 000 KMF` *(ou le plan disponible)* |
   | Moyen de paiement | `MVola` |
   | Référence de transaction | `MVOLA-2026-847291` |
   | Notes | `Paiement effectué le 14 juin 2026 à 10h35` |

4. Cliquer sur **"Envoyer la déclaration"**
5. Le paiement apparaît avec le statut **"⏳ En attente"**

---

### Étape 11 · Valider le paiement (admin)

*Basculer sur l'onglet admin.*

1. Menu gauche → **"Paiements"** (badge orange)
2. Le paiement de **Fatouma Soilihi** est visible
3. Vérifier la référence MVola fournie
4. Cliquer sur l'action **"Valider"**
5. Confirmer

> Le statut passe à **"✅ Validé"**. Un email avec le reçu PDF est envoyé (file d'attente). La cotisation de Fatouma est mise à jour.

---

### Étape 12 · Télécharger le reçu PDF (membre)

*Basculer sur l'onglet de Fatouma.*

1. Rafraîchir la page **"Mes paiements"**
2. Le paiement est maintenant **"✅ Validé"**
3. Cliquer sur **"Reçu"** → un PDF officiel se télécharge

> **Montrer le PDF :** il affiche le numéro de reçu, les coordonnées du réseau, le montant en KMF, la date de validation.

---

## PARTIE 6 — Les formations

### Étape 13 · Créer une formation (admin)

*Onglet admin.*

1. Menu gauche → **"Formations"**
2. Cliquer sur **"Nouveau"**
3. Remplir :

   | Champ | Valeur |
   |---|---|
   | Titre | `Gestion financière pour entrepreneurs` |
   | Description | `Les bases de la comptabilité, gestion de trésorerie et lecture de bilan adaptées aux TPE comoriennes.` |
   | Formateur | `Hamid Abdou` *(ou saisir librement)* |
   | Format | `Présentiel` |
   | Tarif | `Incluse dans la cotisation` |
   | Statut | `Publié` |

4. Enregistrer, puis aller dans **"Sessions"** de cette formation
5. Créer une session :

   | Champ | Valeur |
   |---|---|
   | Date de début | *(dans 3 jours, 09h00)* |
   | Date de fin | *(dans 3 jours, 12h00)* |
   | Lieu | `Salle de conférence — Centre REC, Moroni` |
   | Places max | `20` |
   | Statut | `Planifiée` |

---

### Étape 14 · S'inscrire à la formation (membre)

*Onglet Fatouma.*

1. Menu → **"Formations"**
2. La formation **"Gestion financière pour entrepreneurs"** apparaît dans le catalogue
3. Cliquer sur **"S'inscrire"** sur la session à venir
4. Confirmation → la session apparaît dans **"Mes formations"**

---

## PARTIE 7 — Le réseau et la progression

### Étape 15 · Consulter l'annuaire

1. Menu → **"Annuaire"**
2. Chercher `Hamid` dans la barre de recherche
3. Sa fiche apparaît : entreprise, secteur, compétences
4. Filtrer par secteur **"Technologie"** pour voir les profils du même type

---

### Étape 16 · Envoyer une demande de contact

1. Dans l'annuaire, cliquer sur la fiche de **Hamid Abdou**
2. Ou aller directement sur **"Mise en relation"** → "Nouvelle demande"
3. Sélectionner **Hamid Abdou**
4. Message : `Bonjour Hamid, je travaille dans l'agroalimentaire et souhaite échanger sur les outils de gestion que tu utilises. À bientôt !`
5. Cliquer sur **"Envoyer"**

---

### Étape 17 · Voir sa progression

1. Menu → **"Ma progression"**
2. Les statistiques se mettent à jour :
   - Points accumulés depuis l'activation
   - 1 formation inscrite
   - 1 mois de membre (ou moins)
3. La barre de progression vers le niveau suivant est visible
4. L'**historique des points** liste chaque gain avec sa source

---

## PARTIE 8 — Flux avancés (optionnel)

### Étape 18 · Déposer une recommandation

1. Menu → **"Recommandations"** → "Nouvelle demande"
2. Cible : **Entreprise partenaire** → *(sélectionner un partenaire existant)*
3. Description : `Je cherche un distributeur pour mes épices sur le marché de Mayotte. Notre gamme comprend vanille, girofle et ylang-ylang certifiés origine Comores.`
4. Cliquer sur **"Envoyer"**

*L'admin peut ensuite passer la recommandation par ses différents statuts pour illustrer le cycle de vie.*

---

### Étape 19 · Traiter la recommandation (admin)

1. Admin → **"Recommandations"** → ouvrir celle de Fatouma
2. Action **"Examiner"** → statut passe à "En cours d'examen"
3. Action **"Transmettre"** → statut passe à "Transmise"
4. Plus tard : action **"Clore"** avec résultat "Rendez-vous obtenu"

---

### Étape 20 · Tester le mot de passe oublié

1. Se déconnecter de l'espace membre
2. Sur la page `/login`, cliquer sur **"Mot de passe oublié ?"**
3. Saisir `fatouma@example.km`
4. Cliquer sur **"Envoyer le lien"**
5. Message de confirmation affiché
6. *(En dev : récupérer le lien dans les logs comme à l'étape 3)*

---

## Récapitulatif du scénario

```
Visiteur → Candidature vitrine
    ↓
Admin → Acceptation + email setup
    ↓
Fatouma → Définit son mot de passe → Connexion
    ↓
Fatouma → Complète son profil
    ↓
Admin → Crée un événement + une formation
    ↓
Fatouma → S'inscrit à l'événement → QR Code (check-in simulé)
    ↓
Fatouma → Déclare un paiement MVola
    ↓
Admin → Valide le paiement → Reçu PDF généré
    ↓
Fatouma → Télécharge son reçu PDF
    ↓
Fatouma → S'inscrit à la formation
    ↓
Fatouma → Envoie une recommandation + une demande de contact
    ↓
Fatouma → Consulte sa progression & historique de points
```

---

## Points forts à mettre en avant pendant la démo

| Aspect | Message clé |
|---|---|
| **QR code** | Signé cryptographiquement — impossible à falsifier, fonctionne sans app dédiée |
| **Reçu PDF** | Généré automatiquement à la validation, envoyé par email ET téléchargeable |
| **File d'attente emails** | Aucun email n'est bloquant — tout est asynchrone, jamais de timeout |
| **Gamification** | Points uniquement sur affiliés directs — pas de pyramide, conforme aux règles |
| **Déclaratif** | Pas de paiement en ligne — adapté à la réalité comorienne (MVola, espèces) |
| **Multiformat** | Présentiel, en ligne, hybride — adapté aux formateurs locaux et diaspora |

---

*Tutoriel de démonstration v1.0 — Réseau des Jeunes Entrepreneurs des Comores*