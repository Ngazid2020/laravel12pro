# Guide d'utilisation — Réseau des Entrepreneurs des Comores

> Version 1.2 · Juin 2026

---

## Sommaire

1. [Présentation de la plateforme](#1-présentation)
2. [Premiers pas — Membres](#2-premiers-pas-membres)
3. [Tableau de bord](#3-tableau-de-bord)
4. [Mon profil](#4-mon-profil)
5. [Annuaire des membres](#5-annuaire)
6. [Formations](#6-formations)
7. [Opportunités](#7-opportunités)
8. [Événements](#8-événements)
9. [Mes paiements](#9-paiements)
10. [Recommandations](#10-recommandations)
11. [Mentorat](#11-mentorat)
12. [Ma progression](#12-progression)
13. [Mise en relation](#13-mise-en-relation)
14. [Mon réseau](#14-mon-réseau)
15. [Guide administrateur](#15-guide-administrateur)
16. [Tableaux de bord graphiques](#16-tableaux-de-bord-graphiques)
17. [Export des membres](#17-export-des-membres)
18. [Politique de confidentialité (RGPD)](#18-rgpd)

---

## 1. Présentation

La plateforme du **Réseau des Jeunes Entrepreneurs des Comores** regroupe deux espaces :

| Espace | URL | Pour qui |
|---|---|---|
| **Vitrine publique** | `/` | Tout visiteur — candidature en ligne |
| **Espace membre** | `/membre/dashboard` | Membres actifs |
| **Back-office** | `/admin` | Administrateurs |

### Accès rapide

- **Connexion membre** : `/login`
- **Mot de passe oublié** : `/mot-de-passe-oublie`
- **Candidature** : `/` (section "Postuler")

---

## 2. Premiers pas — Membres

### Étape 1 — Soumettre sa candidature

1. Rendez-vous sur la vitrine : **`/`**
2. Faites défiler jusqu'à la section **"Déposez votre candidature"**
3. Remplissez le formulaire :
   - Prénom, nom, email, téléphone
   - Entreprise / projet (facultatif)
   - Secteur d'activité (facultatif)
   - **Lettre de motivation** (minimum 50 caractères — soyez précis sur votre activité et vos attentes)
4. Cliquez sur **"Envoyer ma candidature"**

> Votre dossier est transmis à l'équipe qui vous répondra sous **7 jours ouvrés**.

---

### Étape 2 — Définir son mot de passe

Lorsque votre candidature est **acceptée**, vous recevez un email intitulé :
> *"🎉 Votre candidature a été acceptée — Réseau Entrepreneurs Comores"*

Cet email contient un bouton **"Définir mon mot de passe"**.

1. Cliquez sur ce bouton (lien valable **60 minutes**)
2. Entrez un mot de passe sécurisé (minimum 8 caractères)
3. Confirmez le mot de passe
4. Cliquez sur **"Enregistrer le mot de passe"**
5. Vous êtes redirigé vers la page de connexion

> **Le lien a expiré ?** Rendez-vous sur `/mot-de-passe-oublie` et entrez votre email pour en recevoir un nouveau.

---

### Étape 3 — Se connecter

1. Accédez à `/login`
2. Entrez votre **email** et votre **mot de passe**
3. Cochez "Se souvenir de moi" si vous êtes sur votre appareil personnel
4. Cliquez sur **"Se connecter"**

Vous êtes redirigé vers votre tableau de bord.

---

## 3. Tableau de bord

Le tableau de bord (`/membre/dashboard`) est votre page d'accueil. Il affiche :

- **4 statistiques** : points totaux, formations suivies, événements assistés, mois de membre
- **Annonces récentes** du réseau (ciblées selon votre statut)
- **Prochains événements** (3 à venir)
- **Dernières opportunités** publiées

---

## 4. Mon profil

Accédez à `/membre/profil` via le menu latéral → **"Mon profil"**.

### Informations modifiables

| Champ | Description |
|---|---|
| Photo de profil | JPG ou PNG, max 2 Mo |
| Prénom / Nom | Votre identité publique dans l'annuaire |
| Téléphone | Visible uniquement par les admins |
| Entreprise / Projet | Nom affiché dans l'annuaire |
| Secteur | Catégorie de votre activité |
| Ville | Localisation |
| Biographie | Présentation libre (visible dans l'annuaire) |
| Compétences offertes | Tags séparés par virgule ou Entrée |
| Besoins exprimés | Ce que vous recherchez dans le réseau |
| Liens sociaux | LinkedIn, site web, etc. (format `clé: valeur`) |

### Informations en lecture seule

- Statut de membre (Actif, Candidat, Suspendu…)
- Date d'activation
- Expiration de la cotisation
- Code de parrainage

> Cliquez sur **"Enregistrer"** pour sauvegarder vos modifications.

---

## 5. Annuaire

Accédez à `/membre/annuaire` → **"Annuaire"**.

### Filtres disponibles

- **Recherche par nom** : tapez les premières lettres
- **Secteur** : sélectionnez dans la liste des secteurs enregistrés
- **Ville** : filtrez par localisation
- **Compétence** : trouvez un expert sur un sujet précis

Chaque carte membre affiche : nom, entreprise, secteur, ville, compétences principales (3 max).

> Pour contacter un membre, utilisez la page **Mise en relation**.

---

## 6. Formations

Accédez à `/membre/formations` → **"Formations"**.

### Onglet Catalogue

Liste toutes les formations publiées. Pour chaque formation :

- **Badges** : format (présentiel / en ligne / hybride) et tarif (gratuite / incluse / premium)
- **Formateur** et description
- **Sessions à venir** avec date, lieu ou lien visio, places restantes

#### S'inscrire à une session

1. Trouvez la formation souhaitée
2. Repérez une session à venir avec des places disponibles
3. Cliquez sur **"S'inscrire"**
4. Confirmation affichée → la session apparaît dans l'onglet "Mes formations"

> Les formations **premium** nécessitent un paiement validé par l'administration. Contactez l'équipe.

---

### Onglet Mes formations

**À venir** — Vos sessions inscrites :
- Date, lieu ou lien de connexion
- Matériaux disponibles (PDF, vidéos) si fournis par le formateur
- Bouton **"Annuler"** (uniquement avant le début)

**Passées** — Historique :
- Statut : Suivi ✅ ou Absent
- Si statut = Suivi et pas encore noté → bouton **"Évaluer"**

#### Évaluer une formation

1. Cliquez sur **"Évaluer"** sur une session suivie
2. Choisissez une note de 1 à 5 étoiles
3. Rédigez un commentaire (facultatif)
4. Cliquez sur **"Envoyer"**

---

## 7. Opportunités

Accédez à `/membre/opportunites` → **"Opportunités"**.

Types d'opportunités disponibles :

| Type | Description |
|---|---|
| Appel d'offres | Marchés publics ou privés |
| Mission | Prestations freelance |
| Stage | Offres de stage |
| Financement | Subventions, prêts, investisseurs |
| Concours | Prix entrepreneuriat |

### Postuler

1. Trouvez une opportunité qui vous correspond (utilisez les filtres type et secteur)
2. Cliquez sur **"Postuler"**
3. Votre candidature est enregistrée — le badge **"Candidature envoyée"** apparaît

> Vous ne pouvez postuler qu'une seule fois par opportunité.

---

## 8. Événements

Accédez à `/membre/evenements` → **"Événements"**.

### S'inscrire à un événement

1. Basculez sur l'onglet **"À venir"**
2. Vérifiez les places disponibles
3. Cliquez sur **"S'inscrire"**
4. Confirmation affichée

### QR Code d'entrée

Après inscription, un bouton **"Mon QR"** apparaît sur l'événement.

1. Cliquez sur **"Mon QR"** le jour de l'événement
2. Un QR code s'affiche à l'écran
3. Présentez-le au staff à l'entrée pour validation de votre présence

> Gardez l'application ouverte sur cette page à l'entrée. Pas besoin d'imprimer.

### Annuler une inscription

Cliquez sur **"Annuler"** → confirmation demandée. Disponible uniquement avant le début de l'événement.

---

## 9. Paiements

Accédez à `/membre/paiements` → **"Mes paiements"**.

### Déclarer un paiement

1. Cliquez sur **"Déclarer un paiement"**
2. Sélectionnez le **plan de cotisation** (mensuel ou annuel)
3. Choisissez le **moyen de paiement** :
   - **MVola** → saisissez la référence de transaction
   - **Holo Money** → saisissez la référence de transaction
   - **Espèces** → pas de référence requise
   - **Chèque** → numéro de chèque + nom de la banque
4. Joignez un **justificatif** (capture d'écran MVola, photo du chèque) — facultatif mais recommandé
5. Ajoutez des notes si nécessaire
6. Cliquez sur **"Envoyer la déclaration"**

> Votre paiement sera validé par l'administration sous **48h ouvrées**. Vous recevrez un email de confirmation avec le **reçu PDF** en pièce jointe.

### Statuts des paiements

| Statut | Signification |
|---|---|
| ⏳ En attente | Déclaration reçue, en cours de vérification |
| ✅ Validé | Paiement confirmé — cotisation mise à jour |
| ❌ Rejeté | Paiement non validé — voir les notes |

### Télécharger un reçu

Sur un paiement validé, un lien **"Reçu"** est disponible pour télécharger le PDF officiel.

### Alerte d'expiration

Un bandeau d'alerte apparaît en haut de la page si votre cotisation expire dans moins de 30 jours ou est déjà expirée.

---

## 10. Recommandations

Accédez à `/membre/recommandations` → **"Recommandations"**.

Une recommandation est une demande de **mise en relation facilitée** par l'équipe du réseau, vers une entreprise partenaire ou un autre membre.

### Soumettre une demande

1. Cliquez sur **"Nouvelle demande"**
2. Choisissez la cible :
   - **Entreprise partenaire** : sélectionnez dans la liste
   - **Membre du réseau** : sélectionnez un membre actif
3. Décrivez votre besoin (minimum 20 caractères — soyez précis)
4. Cliquez sur **"Envoyer"**

### Suivi du cycle de vie

| Statut | Signification |
|---|---|
| En attente | Transmise à l'équipe |
| En cours d'examen | Un administrateur l'analyse |
| Transmise | Votre dossier a été transmis à la cible |
| Rendez-vous obtenu | Un rendez-vous est planifié |
| Conclue | Partenariat ou accord abouti |
| Refusée | Non retenue — voir les notes |

---

## 11. Mentorat

Accédez à `/membre/mentorat` → **"Mentorat"**.

### En tant que mentoré

Si vous avez un mentor assigné (visible dans votre profil) :

1. Cliquez sur **"Demander une session"**
2. Choisissez une **date et heure** souhaitées
3. Ajoutez des notes sur les sujets à aborder (facultatif)
4. Cliquez sur **"Envoyer la demande"**

Votre mentor recevra la demande et pourra valider la session.

**Confirmer la tenue** : après la session, cliquez sur **"Confirmer la tenue"** pour valider que la session a bien eu lieu. Cela déclenchera l'attribution des points associés.

### En tant que mentor

Si vous avez des affiliés dans votre réseau, une section supplémentaire affiche leurs demandes de session.

1. Cliquez sur **"Valider & noter"** pour une session confirmée
2. Rédigez vos notes de session (résumé, actions de suivi)
3. Cliquez sur **"Enregistrer et confirmer"**

---

## 12. Ma progression

Accédez à `/membre/progression` → **"Ma progression"**.

### Statistiques en temps réel

- **Points totaux** accumulés
- **Formations suivies** (statut "suivi")
- **Mois de membre** depuis l'activation

### Niveaux

Le réseau propose plusieurs niveaux débloquables selon 3 critères cumulatifs :

| Critère | Description |
|---|---|
| Points | Total des points accumulés |
| Formations | Nombre de formations suivies |
| Ancienneté | Nombre de mois de membre actif |

La barre de progression indique votre avancement vers le niveau suivant, avec le détail des critères manquants.

### Comment gagner des points ?

| Action | Points |
|---|---|
| Candidature acceptée | Crédité à l'activation |
| Formation suivie | Par formation complétée |
| Événement assisté | Par présence validée (check-in) |
| Session de mentorat | Par session confirmée des deux côtés |
| Parrainage | À l'activation d'un affilié direct |
| Recommandation aboutie | Quand une recommandation est marquée "conclue" |
| Crédit manuel | Attribué par l'administration |

> Les points de parrainage sont **uniquement sur le premier niveau** (vos affiliés directs). Il n'y a aucune commission multi-niveaux.

### Historique des points

La section "Historique" liste toutes les entrées de points avec la source, la description et la date.

---

## 13. Mise en relation

Accédez à `/membre/contacts` → **"Mise en relation"**.

### Envoyer une demande

1. Cliquez sur **"Nouvelle demande"**
2. Sélectionnez un membre dans la liste (seuls les membres actifs sans demande existante sont affichés)
3. Ajoutez un message de présentation (facultatif)
4. Cliquez sur **"Envoyer"**

### Gérer les demandes reçues

Les demandes en attente apparaissent en haut avec un badge rouge indiquant le nombre.

- **Accepter** → la relation est établie
- **Refuser** → la demande est archivée

---

## 14. Mon réseau

Accédez à `/membre/mon-reseau` → **"Mon réseau"** (visible uniquement si vous avez des affiliés).

Cette page affiche :

- **Statistiques** : affiliés directs, réseau total, points de parrainage
- **Votre mentor** (si assigné) avec ses informations
- **Liste de vos affiliés** avec leur statut, entreprise et date d'activation
- **Votre code de parrainage** avec bouton de copie

### Parrainer un nouvel entrepreneur

Partagez votre code de parrainage `XXXX2026` avec vos contacts. Lors de leur candidature, ils pourront mentionner votre code. L'administrateur fera le lien lors de l'acceptation.

---

## 15. Guide administrateur

### Accès

URL : `/admin`  
Compte super-admin créé en installation :
- Email : `admin@reseau.km`
- Mot de passe : `Admin@2026!`

> Changez ce mot de passe dès la mise en production.

---

### Navigation

Le back-office est organisé en groupes :

| Groupe | Ressources |
|---|---|
| Membres | Utilisateurs, Profils membres, Candidatures |
| Finances | Paiements, Plans de cotisation |
| Réseau | Recommandations, Entreprises partenaires |
| Formations | Formations |
| Activités | Opportunités, Événements |
| Gamification | Niveaux |
| Communication | Annonces |
| Administration | Rôles & Permissions (Shield) |

---

### Workflow : Traiter une candidature

Les candidatures en attente affichent un **badge orange** sur l'entrée de menu.

1. Cliquez sur **Candidatures** dans le menu
2. Filtrez par statut "En attente" si nécessaire
3. Cliquez sur **"Voir"** pour lire la lettre de motivation
4. Choisissez une action :

| Action | Effet |
|---|---|
| **Accepter** | Crée le profil membre (statut actif), envoie l'email de bienvenue avec le lien de setup mot de passe |
| **Refuser** | Demande un motif de refus, envoie une notification |
| **Mettre en suspens** | Passe en statut "on_hold" pour décision ultérieure |

---

### Workflow : Valider un paiement

Les paiements en attente affichent un **badge orange** sur l'entrée de menu.

1. Cliquez sur **Paiements** dans le menu
2. Identifiez les paiements au statut "En attente"
3. Vérifiez la capture d'écran ou la référence fournie
4. Cliquez sur l'action **"Valider"** :
   - Met le statut à "Validé"
   - Met à jour la date d'expiration de la cotisation du membre
   - Envoie un email de confirmation avec le **reçu PDF** en pièce jointe
5. En cas de problème : action **"Refuser"** avec motif

> Pour télécharger le reçu PDF manuellement : bouton **"Reçu PDF"** dans la colonne Actions (visible uniquement sur les paiements validés).

---

### Workflow : Gérer les recommandations

1. Cliquez sur **Recommandations** dans le menu
2. Les nouvelles recommandations sont au statut "En attente"
3. Workflow en 3 étapes :
   - **Examiner** → passe le statut à "En cours d'examen"
   - **Transmettre** → passe à "Transmise" avec date de transmission
   - **Clore** → sélectionnez le résultat final (rendez-vous, accord conclu, refus) et ajoutez des notes

---

### Check-in événement (staff)

Le staff qui accueille les participants à un événement :

1. Demande à chaque participant d'afficher son QR code (`/membre/evenements` → bouton "Mon QR")
2. Scanne le QR avec un smartphone (application appareil photo standard)
3. La page de confirmation s'ouvre dans le navigateur avec le nom du participant et l'événement
4. La présence est enregistrée automatiquement

> Le lien de check-in est signé cryptographiquement — il est impossible de falsifier un QR code.

---

### Gestion des niveaux et récompenses

1. Cliquez sur **Niveaux** dans le groupe "Gamification"
2. Créez ou modifiez un niveau :
   - Nom et slug
   - Points minimum requis
   - Nombre de formations requises
   - Ancienneté requise (mois)
   - Couleur du badge (code hexadécimal)
   - Cocher "Donne le statut mentor" si ce niveau permet de devenir mentor
3. Ajoutez les **récompenses** associées via le formulaire intégré (type + description + valeur)

---

### Gestion des annonces

1. Cliquez sur **Annonces** dans le groupe "Communication"
2. Créez une annonce avec :
   - Titre et contenu
   - **Audience cible** : tous les membres, actifs seulement, admins, ou public
   - **Date de publication** et **date d'expiration** (optionnelle)
3. Publiez en cochant "Publié"

Les annonces apparaissent automatiquement sur le tableau de bord des membres concernés.

---

### Gestion des permissions (Shield)

1. Cliquez sur **Rôles & Permissions** dans le groupe "Administration"
2. Les rôles disponibles :
   - `super_admin` : accès total
   - `admin` : accès back-office complet (sans gestion des rôles)
3. Pour créer un admin : créez un utilisateur, puis assignez-lui le rôle `admin`

---

### Rappels de cotisation (automatique)

La commande suivante est schedulée **tous les lundis à 8h** :

```bash
php artisan membres:expiry-reminders
```

Elle envoie automatiquement un email à tous les membres actifs dont la cotisation expire dans les 30 jours.

**En production**, assurez-vous que le scheduler Laravel est actif :

```bash
# Ajouter au crontab du serveur
* * * * * cd /chemin/vers/projet && php artisan schedule:run >> /dev/null 2>&1
```

**Pour les emails et les PDFs en file d'attente :**

```bash
php artisan queue:work --tries=3
```

---

## Annexe — Variables d'environnement importantes

| Variable | Valeur recommandée en production |
|---|---|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://votre-domaine.km` |
| `MAIL_MAILER` | `smtp` (configurer avec votre fournisseur) |
| `MAIL_FROM_ADDRESS` | `noreply@reseau-entrepreneurs.km` |
| `QUEUE_CONNECTION` | `database` |
| `FILESYSTEM_DISK` | `public` |

---

---

## 16. Tableaux de bord graphiques

Le dashboard administrateur (`/admin`) affiche, sous les indicateurs clés, **4 graphiques interactifs** mis à jour en temps réel :

| Graphique | Type | Données affichées |
|---|---|---|
| **Nouveaux membres actifs** | Courbe | Évolution mensuelle sur 12 mois |
| **Paiements par statut** | Barres groupées | En attente / Validés / Rejetés sur 6 mois |
| **Répartition par secteur** | Donut | Top 9 secteurs des membres actifs |
| **Inscriptions par événement** | Barres horizontales | Inscrits vs Capacité pour les 6 derniers événements |

Les graphiques utilisent Chart.js, intégré nativement à Filament — aucune installation supplémentaire.

---

## 17. Export des membres

Depuis **`/admin/member-profiles`**, le bouton **"Exporter ↓"** (haut à droite) permet de télécharger la liste des membres en trois formats.

### Formats disponibles

| Format | Extension | Caractéristiques |
|---|---|---|
| **CSV** | `.csv` | Compatible Excel, LibreOffice, Google Sheets |
| **Excel** | `.xlsx` | En-tête bleu, colonnes auto-dimensionnées, prêt à imprimer |
| **PDF** | `.pdf` | A4 paysage, badges de statut colorés, compteurs par catégorie |

### Utilisation

1. Cliquez sur **"Exporter ↓"**
2. Choisissez le format souhaité
3. Une fenêtre s'ouvre avec un filtre **"Statut"** :
   - Tous les membres
   - Actifs uniquement
   - Candidats / Suspendus / Exclus / Alumni
4. Cliquez sur **"Exporter"** — le fichier se télécharge automatiquement

### Colonnes exportées

Prénom · Nom · Email · Téléphone · Entreprise/Projet · Secteur · Ville · Statut · Date d'adhésion · Expiration · Mentor · Code de parrainage

---

## 18. Politique de confidentialité (RGPD)

### Pour les visiteurs

La page **`/politique-de-confidentialite`** détaille en 10 sections :
- Les données collectées et leur finalité
- La base légale de chaque traitement
- Les durées de conservation
- Les droits des utilisateurs (accès, rectification, effacement, portabilité…)
- Les mesures de sécurité mises en place
- La politique en matière de cookies (session uniquement, aucun traceur publicitaire)

Un lien vers cette page est présent dans le **footer de toutes les pages publiques**.

### Bannière de consentement

À la première visite, une bannière apparaît en bas de page. Elle disparaît après clic sur **"J'accepte"** et ne se réaffiche plus (mémorisée via `localStorage`).

### Contact RGPD

Pour exercer vos droits : **contact@reseau-entrepreneurs.km** (réponse sous 30 jours).

---

*Guide rédigé pour la version 1.2 de la plateforme — Réseau des Jeunes Entrepreneurs des Comores*
