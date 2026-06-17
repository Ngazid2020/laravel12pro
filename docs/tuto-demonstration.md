# Tutoriel de démonstration — Réseau Entrepreneurs Comores

> Scénario complet de A à Z · Durée estimée : 20–30 minutes  
> Prérequis : serveur démarré (`composer dev`), base de données fraîche (`php artisan migrate:fresh --seed`)

> **v1.2** — Données de démonstration pré-remplies (12 membres, événements, formations, opportunités)  
> Membres démo : `prenom.nom@reseau.km` / `Demo@2026!` (ex : `fatima.ahamada@reseau.km`)

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

---

## PARTIE 9 — Données de démonstration pré-remplies

> Cette partie ne fait pas partie du scénario Fatouma — c'est la présentation des données pré-chargées disponibles dès `migrate:fresh --seed`.

### Vue d'ensemble des données démo

| Catégorie | Contenu |
|---|---|
| Membres | 12 membres (10 actifs, 1 suspendu, 1 candidat) |
| Plans de cotisation | 2 plans : mensuel 1 500 KMF, annuel 15 000 KMF |
| Niveaux | 5 niveaux : Débutant → Junior → Confirmé → Expert → Champion |
| Entreprises partenaires | 3 sociétés (finances, conseil, distribution) |
| Événements | 4 événements (2 passés avec check-ins, 2 à venir) |
| Formations | 3 formations avec sessions planifiées |
| Opportunités | 4 opportunités (appels d'offres, missions, financement) |
| Annonces | 3 annonces actives |
| Sessions de mentorat | 7 sessions planifiées entre membres |
| Demandes de contact | 5 demandes entre membres |

### Connexion aux membres démo

Tous les membres démo partagent le même mot de passe : **`Demo@2026!`**

| Prénom Nom | Email |
|---|---|
| Fatima Ahamada | fatima.ahamada@reseau.km |
| Mohamed Said | mohamed.said@reseau.km |
| Aisha Ibrahim | aisha.ibrahim@reseau.km |
| Karim Hassani | karim.hassani@reseau.km |
| Zoubaïda Omar | zoubaida.omar@reseau.km |
| Hassan Madi | hassan.madi@reseau.km |
| Naïma Abdallah | naima.abdallah@reseau.km |
| Youssouf Combo | youssouf.combo@reseau.km |
| Mariam Halidi | mariam.halidi@reseau.km |
| Iliasse Djae | iliasse.djae@reseau.km |

> Se connecter avec l'un de ces comptes permet d'explorer immédiatement un profil complet avec points, formations inscrites, événements passés.

---

## PARTIE 10 — Tableaux de bord graphiques (admin)

*Onglet admin — `http://laravel12pro.test/admin`*

1. Se connecter : `admin@reseau.km` / `Admin@2026!`
2. Arriver sur le **tableau de bord**
3. Observer les 5 widgets affichés :

| Widget | Type | Ce qu'il montre |
|---|---|---|
| **KPIs (en haut)** | Statistiques | Membres actifs, candidatures en attente, paiements à valider, recommandations ouvertes |
| **Nouveaux membres** | Courbe (12 mois) | Évolution des nouvelles activations mois par mois |
| **Paiements** | Barres groupées (6 mois) | Paiements en attente / validés / rejetés par mois |
| **Répartition par secteur** | Donut (½ largeur) | Top 9 secteurs des membres actifs |
| **Activité événements** | Barres horizontales (½ largeur) | Inscrits vs capacité sur les 6 derniers événements |

> **À mentionner :** aucune dépendance externe — Chart.js est intégré nativement à Filament 3. Les données se mettent à jour en temps réel à chaque chargement de page.

---

## PARTIE 11 — Export de la liste des membres (admin)

*Onglet admin → menu "Profils membres"*

1. Cliquer sur **"Profils membres"** dans le menu gauche
2. En haut à droite, cliquer sur le bouton **"Exporter ↓"** (icône flèche bleue)
3. Un menu déroulant affiche 3 options :

### Option A — Export CSV

1. Cliquer sur **"Exporter en CSV"**
2. Un modal s'ouvre avec un filtre statut
3. Laisser **"Tous les membres"** → cliquer sur **"Exporter"**
4. Le fichier `membres-2026-06-16.csv` se télécharge
5. Ouvrir dans Excel ou LibreOffice Calc

### Option B — Export Excel

1. Cliquer sur **"Exporter en Excel (.xlsx)"**
2. Sélectionner **"Actifs uniquement"** → cliquer sur **"Exporter"**
3. Le fichier `membres-2026-06-16.xlsx` se télécharge
4. Ouvrir : l'en-tête est sur fond bleu, les colonnes se dimensionnent automatiquement

### Option C — Export PDF

1. Cliquer sur **"Exporter en PDF"**
2. Laisser le filtre sur "Tous les membres" → **"Exporter"**
3. Le fichier `membres-2026-06-16.pdf` se télécharge
4. Ouvrir : format A4 paysage, badges de statut colorés (vert = actif, rouge = suspendu…), compteurs en haut

**Colonnes exportées :** Prénom, Nom, Email, Téléphone, Entreprise/Projet, Secteur, Ville, Statut, Adhésion, Expiration, Mentor, Code parrainage

---

## PARTIE 12 — Conformité RGPD

### La bannière de consentement

1. Ouvrir un navigateur en navigation privée (ou effacer le localStorage)
2. Aller sur `http://laravel12pro.test/`
3. En bas de page, la **bannière de consentement** apparaît
4. Elle indique : *"Nous utilisons des cookies fonctionnels…"* avec un bouton **"J'accepte"**
5. Cliquer sur **"J'accepte"** → la bannière disparaît avec une animation
6. Rafraîchir la page → la bannière ne réapparaît plus (stockée dans `localStorage`)

### La page RGPD

1. Dans le footer de n'importe quelle page publique, repérer :
   - Le badge vert **"Conforme RGPD 🛡"**
   - Le lien **"Politique de confidentialité"**
2. Cliquer sur le lien → page `/politique-de-confidentialite`
3. La page couvre 10 sections :
   - Responsable du traitement (coordonnées)
   - Données collectées (tableau par catégorie)
   - Finalités et base légale (4 cartes)
   - Durées de conservation
   - Partage des données
   - Sécurité
   - Cookies
   - Droits des personnes (6 droits RGPD)
   - Modifications

> **À mettre en avant :** la page est entièrement en français, complète, et la bannière suit le modèle WordPress — familier pour le public cible.

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
| **Tableaux de bord** | 5 graphiques temps réel, aucune dépendance externe (Chart.js natif Filament) |
| **Export membres** | CSV / Excel / PDF avec filtre statut, en un clic depuis l'admin |
| **Conformité RGPD** | Bannière consentement + page complète 10 sections + badge footer |

---

*Tutoriel de démonstration v1.2 — Réseau des Jeunes Entrepreneurs des Comores*