# Cahier des Charges & Proposition Budgétaire
## Plateforme Numérique pour Réseau Professionnel d'Entrepreneurs

> **Document de référence** — Version 1.0  
> **Marché cible :** Union Européenne (référentiel tarifaire France/Belgique)  
> **Méthodologie :** Function Point Analysis (ISO 14143) + Estimation analogique + PERT  
> **Niveau de confiance :** ±15 % (estimation affinée sur la base d'un périmètre fonctionnel complet)

---

## Table des matières

### PARTIE I — CAHIER DES CHARGES
1. [Contexte et objectifs](#1-contexte-et-objectifs)
2. [Périmètre fonctionnel](#2-périmètre-fonctionnel)
3. [Architecture technique](#3-architecture-technique)
4. [Exigences non-fonctionnelles](#4-exigences-non-fonctionnelles)
5. [Conformité et réglementation](#5-conformité-et-réglementation)
6. [Livrables attendus](#6-livrables-attendus)
7. [Planning prévisionnel](#7-planning-prévisionnel)

### PARTIE II — PROPOSITION BUDGÉTAIRE
8. [Méthodologie d'évaluation](#8-méthodologie-dévaluation)
9. [Équipe projet et grille tarifaire](#9-équipe-projet-et-grille-tarifaire)
10. [Décomposition par lot (WBS)](#10-décomposition-par-lot-wbs)
11. [Synthèse budgétaire](#11-synthèse-budgétaire)
12. [Options et variantes](#12-options-et-variantes)
13. [Infrastructure et coûts récurrents](#13-infrastructure-et-coûts-récurrents)
14. [Benchmark marché](#14-benchmark-marché)
15. [Conditions commerciales](#15-conditions-commerciales)

---

# PARTIE I — CAHIER DES CHARGES

---

## 1. Contexte et objectifs

### 1.1 Contexte général

La plateforme décrite dans ce document est un **système de gestion intégré pour réseau professionnel d'entrepreneurs**. Elle permet à une organisation de gérer l'ensemble du cycle de vie de ses membres : candidature, adhésion, cotisations, formations, événements, opportunités d'affaires, mentorat et gamification.

Elle s'adresse à tout organisme souhaitant **digitaliser la gestion de son réseau** : chambre de commerce, association professionnelle, incubateur, cluster sectoriel, fédération d'entrepreneurs.

### 1.2 Objectifs métier

| Objectif | Indicateur de succès |
|---|---|
| Réduire le temps de traitement des candidatures | < 48h vs. processus manuel |
| Automatiser la gestion des cotisations | Zéro relance manuelle en retard |
| Centraliser les activités réseau | Un seul outil pour membres et admins |
| Valoriser l'engagement des membres | Score de participation mesurable |
| Fournir des données de pilotage à la direction | Dashboard en temps réel |
| Conformité RGPD dès la conception | Privacy by design |

### 1.3 Parties prenantes

| Acteur | Rôle dans la plateforme |
|---|---|
| **Super Administrateur** | Gestion complète, paramétrage, Shield |
| **Administrateur** | Validation candidatures, paiements, contenu |
| **Membre actif** | Accès complet espace membre |
| **Candidat** | Formulaire candidature + espace restreint |
| **Visiteur public** | Consultation vitrine uniquement |
| **Staff événement** | Scan QR code check-in (interface dédiée) |

---

## 2. Périmètre fonctionnel

### 2.1 Module Gestion des membres

**F-MEM-01** — Formulaire de candidature public (vitrine, sans compte)  
**F-MEM-02** — Workflow de validation administrateur (accepter / refuser / suspendre)  
**F-MEM-03** — Email automatique d'accueil avec lien de création de mot de passe  
**F-MEM-04** — Profil membre complet (photo, biographie, secteur, compétences)  
**F-MEM-05** — Annuaire membres avec filtres multicritères (nom, secteur, ville, compétence)  
**F-MEM-06** — Gestion du statut : `candidat / actif / suspendu / exclu / alumni`  
**F-MEM-07** — Codes de parrainage uniques avec système d'affiliation tracé  

### 2.2 Module Finances et cotisations

**F-FIN-01** — Plans de cotisation paramétrables (mensuel / annuel / autres périodes)  
**F-FIN-02** — Déclaration de paiement déclarative (référence + justificatif)  
**F-FIN-03** — Validation manuelle des paiements par l'administrateur  
**F-FIN-04** — Génération automatique de reçus PDF (numérotés, signés)  
**F-FIN-05** — Email automatique d'envoi du reçu à la validation  
**F-FIN-06** — Alerte d'expiration de cotisation (J-30, J-7)  
**F-FIN-07** — Tableau de bord financier (revenus par mois, statuts)  
**F-FIN-08** — Contrôle d'accès basé sur l'expiration de l'adhésion  

### 2.3 Module Événements

**F-EVT-01** — Création d'événements (networking / conférence / masterclass / atelier)  
**F-EVT-02** — Inscription en ligne avec gestion des capacités maximales  
**F-EVT-03** — Génération de QR code individuel signé cryptographiquement  
**F-EVT-04** — Interface de check-in staff (scan ou saisie URL)  
**F-EVT-05** — Historique des présences par membre et par événement  
**F-EVT-06** — Widget graphique : inscrits vs capacité (6 derniers événements)  

### 2.4 Module Formations

**F-FOR-01** — Catalogue de formations avec filtres (format, tarif, statut)  
**F-FOR-02** — Gestion des sessions (planifiées, lieu, places limitées)  
**F-FOR-03** — Inscription à une session avec gestion des conflits de places  
**F-FOR-04** — Évaluation post-formation par les membres  
**F-FOR-05** — Formats : présentiel / en ligne / hybride  
**F-FOR-06** — Types de tarifs : gratuite / incluse cotisation / premium  

### 2.5 Module Opportunités d'affaires

**F-OPP-01** — Publication d'opportunités (appel d'offres / mission / stage / financement / concours)  
**F-OPP-02** — Candidature des membres avec message personnalisé  
**F-OPP-03** — Workflow de traitement administrateur (pending → contacté → clôturé)  
**F-OPP-04** — Filtres par type, secteur et statut  

### 2.6 Module Réseau et mise en relation

**F-NET-01** — Demandes de mise en relation entre membres  
**F-NET-02** — Messagerie de présentation (message d'introduction)  
**F-NET-03** — Statuts : en attente / accepté / refusé  
**F-NET-04** — Vue "Mon réseau" avec affiliés directs et codes parrainage  
**F-NET-05** — Garde-fous anti-pyramide (gamification limitée aux affiliés directs)  

### 2.7 Module Mentorat

**F-MEN-01** — Double vue mentor / mentoré  
**F-MEN-02** — Planification de sessions (date, durée, objectifs)  
**F-MEN-03** — Notes de session privées  
**F-MEN-04** — Statuts : planifié / confirmé / annulé  
**F-MEN-05** — Attribution des mentors par l'administrateur  

### 2.8 Module Recommandations d'affaires

**F-REC-01** — Dépôt de demandes de recommandations auprès d'entreprises partenaires  
**F-REC-02** — Cycle de vie en 6 statuts (brouillon → soumise → examinée → transmise → clôturée / rejetée)  
**F-REC-03** — Suivi de l'avancement par le membre  

### 2.9 Module Gamification et progression

**F-GAM-01** — Système de points par action (formation, mentorat, contribution, parrainage)  
**F-GAM-02** — Niveaux paramétrables (seuils, titres, badges)  
**F-GAM-03** — Historique détaillé des points avec source  
**F-GAM-04** — Barre de progression visuelle vers le niveau suivant  

### 2.10 Module Communication

**F-COM-01** — Annonces ciblées (tous / membres actifs / candidats)  
**F-COM-02** — Emails transactionnels automatiques (candidature, validation, expiration)  
**F-COM-03** — File d'attente asynchrone (aucun email bloquant le chargement)  
**F-COM-04** — Commande schedulée de rappels d'expiration (hebdomadaire)  

### 2.11 Back-office administrateur

**F-ADM-01** — Interface Filament 3 complète (12 ressources CRUD)  
**F-ADM-02** — Gestion des rôles et permissions granulaires (Shield / Spatie)  
**F-ADM-03** — Dashboard avec 5 widgets graphiques temps réel  
**F-ADM-04** — Export de la liste des membres : CSV / Excel / PDF avec filtres  
**F-ADM-05** — Recherche et filtres avancés sur toutes les ressources  
**F-ADM-06** — Actions groupées (accepter / refuser / suspendre en masse)  

### 2.12 API REST Mobile

**F-API-01** — Authentification par token Bearer (Laravel Sanctum)  
**F-API-02** — 39 endpoints couvrant tous les use-cases membres  
**F-API-03** — Rate limiting (5 req/min par IP, 3/min par email sur login)  
**F-API-04** — Réponses JSON standardisées (Resources Eloquent)  
**F-API-05** — Middleware de contrôle d'adhésion active (JSON, pas de redirect)  

---

## 3. Architecture technique

### 3.1 Stack recommandée

| Couche | Technologie | Version |
|---|---|---|
| Backend | Laravel | 12.x (LTS) |
| Langage | PHP | 8.2+ |
| Admin panel | Filament | 3.x |
| Frontend membre | Livewire + MaryUI + DaisyUI | 3.x |
| Base de données | PostgreSQL (prod) / SQLite (dev) | 16+ |
| File d'attente | Laravel Queue (database driver) | — |
| Auth API | Laravel Sanctum | — |
| Permissions | Spatie Laravel Permission + Filament Shield | — |
| PDF | barryvdh/laravel-dompdf | — |
| QR Code | simplesoftwareio/simple-qrcode | — |
| Export Excel | maatwebsite/excel | 3.1 |
| Arbre mentorat | staudenmeir/laravel-adjacency-list | — |
| Graphiques | Chart.js (natif Filament 3) | — |

### 3.2 Architecture applicative

```
┌────────────────────────────────────────────────────────┐
│                     LOAD BALANCER / CDN                │
└──────────────────┬─────────────────────────────────────┘
                   │
    ┌──────────────┼──────────────┐
    ▼              ▼              ▼
┌────────┐   ┌──────────┐   ┌──────────┐
│ Vitrine│   │  Espace  │   │  Admin   │
│ public │   │  Membre  │   │ Filament │
│  (SSR) │   │(Livewire)│   │    3     │
└────────┘   └──────────┘   └──────────┘
                   │              │
    ┌──────────────┼──────────────┘
    ▼              ▼
┌──────────┐  ┌──────────┐
│   API    │  │  Queue   │
│  REST    │  │  Worker  │
│(Sanctum) │  │ (emails) │
└──────────┘  └──────────┘
                   │
         ┌─────────┴──────────┐
         ▼                    ▼
   ┌──────────┐        ┌────────────┐
   │PostgreSQL│        │  Storage   │
   │  (RDS)   │        │ (S3/local) │
   └──────────┘        └────────────┘
```

### 3.3 Sécurité applicative

- Authentification à double facteur (2FA) pour l'administration (recommandé phase 2)
- HTTPS obligatoire, HSTS, headers de sécurité (`X-Frame-Options`, `X-Content-Type-Options`, etc.)
- Protection CSRF sur tous les formulaires
- Rate limiting sur les routes d'authentification
- Stockage privé des justificatifs de paiement (hors `public/`)
- IDOR corrigés sur toutes les ressources sensibles
- Validation et sanitisation strictes de tous les inputs

---

## 4. Exigences non-fonctionnelles

### 4.1 Performance

| Indicateur | Seuil acceptable | Seuil cible |
|---|---|---|
| Temps de chargement page (TTFB) | < 800 ms | < 400 ms |
| Score Lighthouse Performance | > 70 | > 85 |
| Disponibilité (SLA) | 99,5 % | 99,9 % |
| Temps de réponse API (P95) | < 500 ms | < 200 ms |
| Génération PDF reçu | < 3 s | < 1,5 s |

### 4.2 Scalabilité

- Support de 0 à 5 000 membres actifs simultanés sans refactorisation
- Architecture permettant le passage à un cluster multi-serveurs (cache Redis, queue séparée)
- Base de données indexée sur toutes les colonnes de recherche et de filtrage

### 4.3 Accessibilité

- Conformité **WCAG 2.1 niveau AA** (obligation légale en UE depuis la directive 2019/882)
- Support des lecteurs d'écran (NVDA, VoiceOver)
- Contraste minimal 4,5:1 sur tous les textes
- Navigation complète au clavier

### 4.4 Tests et qualité

- Couverture minimale de tests automatisés : **80 % du code métier**
- Tests unitaires, d'intégration et de sécurité (IDOR, escalade de privilèges)
- Pipeline CI/CD avec validation automatique avant déploiement
- Audit de sécurité OWASP Top 10 avant mise en production

### 4.5 Internationalisation

- Architecture i18n prête (fichiers de langue séparés)
- Support multi-devise (paramétrable, sans recompilation)
- Format de dates adaptable par locale

---

## 5. Conformité et réglementation

### 5.1 RGPD (Règlement Général sur la Protection des Données)

| Obligation | Implémentation |
|---|---|
| **Consentement explicite** | Bannière cookies + opt-in actif |
| **Politique de confidentialité** | Page dédiée (10 sections), accessible depuis le footer |
| **Droit d'accès** | Export données membre sur demande |
| **Droit à l'oubli** | Suppression complète ou anonymisation |
| **Portabilité** | Export JSON/CSV des données personnelles |
| **Minimisation des données** | Seules les données nécessaires sont collectées |
| **Privacy by design** | Stockage privé des justificatifs, chiffrement des tokens |
| **DPO** | Contact dédié identifié dans la politique |
| **Registre des traitements** | À maintenir par le responsable de traitement |

### 5.2 Accessibilité numérique (RAAN / EN 301 549)

Obligatoire pour les organismes publics et quasi-obligatoire en B2B européen depuis 2025. Un rapport de conformité WCAG est livré en fin de projet.

### 5.3 Sécurité des paiements

La plateforme utilise un modèle **déclaratif** (pas de paiement en ligne direct). En cas d'intégration future d'un prestataire de paiement (Stripe, PayPal, etc.), la conformité **PCI-DSS** sera requise et fera l'objet d'un avenant.

---

## 6. Livrables attendus

| # | Livrable | Format | Jalon |
|---|---|---|---|
| L1 | Maquettes UX/UI validées (Figma) | Fichier Figma + export PNG | Fin phase conception |
| L2 | Code source versionné | Dépôt Git privé | En continu |
| L3 | Documentation technique API (OpenAPI 3.0) | YAML/Swagger | Fin phase API |
| L4 | Guide utilisateur (admins + membres) | Markdown / PDF | Fin phase membre |
| L5 | Rapport de tests (couverture + résultats) | HTML/PDF | Recette |
| L6 | Rapport audit sécurité OWASP | PDF | Avant mise en prod |
| L7 | Rapport de conformité RGPD | PDF | Avant mise en prod |
| L8 | Rapport de conformité WCAG 2.1 | PDF | Avant mise en prod |
| L9 | Procédure de déploiement et runbook ops | Markdown | Mise en prod |
| L10 | Formation administrateurs (2h) | Présentiel ou visio | Post-déploiement |
| L11 | Données de démonstration (seeders) | Code + documentation | Recette |

---

## 7. Planning prévisionnel

### 7.1 Découpage en phases

```
PHASE 0 — Cadrage et conception         (3 semaines)
  ├── Ateliers de co-conception
  ├── Maquettes UX/UI Figma
  ├── Architecture technique détaillée
  └── Validation CDC + charte graphique

PHASE 1 — Socle plateforme              (8 semaines)
  ├── Setup environnements (dev/staging/prod)
  ├── Modèle de données + migrations
  ├── Auth + gestion membres + espace admin
  ├── Module finances + PDF
  └── Tests unitaires continus

PHASE 2 — Fonctionnalités réseau        (6 semaines)
  ├── Événements + QR code check-in
  ├── Formations + inscriptions
  ├── Opportunités + recommandations
  ├── Mentorat + contacts
  └── Gamification + progression

PHASE 3 — API mobile + données          (4 semaines)
  ├── 39 endpoints REST (Sanctum)
  ├── Dashboard graphiques (Chart.js)
  ├── Export CSV/Excel/PDF
  └── Seeders démo

PHASE 4 — Qualité et mise en prod       (3 semaines)
  ├── Audit sécurité OWASP
  ├── Audit WCAG 2.1
  ├── Tests de charge
  ├── Recette client
  └── Déploiement production + formation
```

**Durée totale estimée : 24 semaines (6 mois)**

### 7.2 Jalon de recette

La recette est déclenchée à réception d'un livrable. Le client dispose de **10 jours ouvrés** pour valider ou émettre des réserves documentées. Au-delà, le livrable est réputé accepté.

---

# PARTIE II — PROPOSITION BUDGÉTAIRE

---

## 8. Méthodologie d'évaluation

### 8.1 Méthodes utilisées

L'estimation de ce projet a été réalisée par **triangulation de trois méthodes** indépendantes, conformément aux bonnes pratiques d'estimation en génie logiciel :

#### Méthode 1 — Function Point Analysis (ISO 14143)

La méthode FPA mesure la taille fonctionnelle du logiciel indépendamment de la technologie. Elle décompose le périmètre en 5 types d'éléments :

| Type | Description | Nb identifiés | FP moyens | Total FP |
|---|---|---|---|---|
| **EI** (External Inputs) | Formulaires, saisies utilisateur | 38 | 4,2 | ~160 FP |
| **EO** (External Outputs) | Rapports, exports, PDF, emails | 14 | 5,1 | ~71 FP |
| **EQ** (External Inquiries) | Recherches, filtres, annuaire | 18 | 3,8 | ~68 FP |
| **ILF** (Internal Logical Files) | Entités métier en base | 22 | 7,5 | ~165 FP |
| **EIF** (External Interface Files) | API tierces, Sanctum, Shield | 6 | 5,5 | ~33 FP |
| | | | **TOTAL** | **~497 FP** |

> **Référence sectorielle (ISBSG 2024) :** pour une application web de gestion en PHP/Laravel, le ratio moyen est de **10–14 heures par Function Point** (développeurs seniors, Europe occidentale).  
> 497 FP × 12 h/FP = **~5 964 heures**, soit ~**149 jours** de 8h.

#### Méthode 2 — Estimation analogique

Comparaison avec 3 projets similaires réalisés sur le marché européen :

| Projet analogue | Périmètre | Budget constaté | Durée |
|---|---|---|---|
| Plateforme association professionnelle (150 membres) | CRM + portail + paiements | €85 000–110 000 | 4–5 mois |
| Intranet réseau entrepreneurs (500 membres) | Membres + formations + événements | €130 000–170 000 | 5–7 mois |
| SaaS gestion d'adhésions + API mobile | Full stack comparable | €160 000–220 000 | 6–9 mois |

> **Positionnement du projet :** entre les projets 2 et 3, avec API REST complète et API mobile = **fourchette analogique : €140 000–190 000**.

#### Méthode 3 — Estimation PERT (3 points)

Pour chaque lot de travail, trois scénarios ont été définis :

- **O** (optimiste) : tout se passe bien, pas d'aléa
- **M** (probable) : déroulement normal avec quelques ajustements
- **P** (pessimiste) : blocages techniques, révisions fonctionnelles

> Formule PERT : `E = (O + 4M + P) / 6`

Les résultats sont présentés dans le tableau du lot (section 10).

### 8.2 Unité de valorisation

L'ensemble des estimations est exprimé en **jours-personne (JP)** de 8 heures, puis valorisé au **taux journalier moyen (TJM)** correspondant au profil et au marché cible (France / Benelux).

---

## 9. Équipe projet et grille tarifaire

### 9.1 Profils et TJM — Marché France 2025–2026

> Sources : Baromètre SYNTEC Numérique, Malt.fr (P50–P75), APEC, offres ESN/SSII grandes villes.

| Profil | Niveau | TJM marché | TJM retenu |
|---|---|---|---|
| Chef de projet (PMP / PSM) | Senior 8+ ans | €650–950 | **€750** |
| Architecte logiciel | Lead 10+ ans | €850–1 200 | **€1 000** |
| Développeur Full-Stack Laravel | Senior 5–8 ans | €600–850 | **€700** |
| Développeur Full-Stack Laravel | Mid 3–5 ans | €450–600 | **€520** |
| Designer UX/UI | Senior 5+ ans | €500–750 | **€600** |
| Ingénieur DevOps / Cloud | Senior | €650–950 | **€800** |
| Ingénieur QA / Test | Senior | €500–750 | **€600** |
| Consultant Sécurité (OWASP) | Expert | €950–1 400 | **€1 100** |
| Consultant RGPD / DPO | Expert juridique | €300–500/h | **€350/h** |

### 9.2 Hypothèses de l'équipe recommandée

| Rôle | Implication | Phase principale |
|---|---|---|
| 1 Chef de projet | 60 % du temps | Toutes phases |
| 1 Architecte | 30 % du temps | Phase 0 et 1 |
| 2 Développeurs senior | 100 % (parallèle) | Phases 1–3 |
| 1 Designer UX/UI | 100 % | Phases 0–1, puis ponctuels |
| 1 DevOps | 40 % du temps | Phases 1 et 4 |
| 1 QA | 50 % du temps | Phases 2–4 |
| 1 Consultant Sécurité | Mission ponctuelle (5j) | Phase 4 |
| 1 Consultant RGPD | Mission ponctuelle (3j) | Phase 4 |

---

## 10. Décomposition par lot (WBS)

### Lot 0 — Cadrage et conception

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Ateliers de co-conception (3 sessions) | 3 j | 4 j | 6 j | 4,2 j | Chef de projet | €750 | €3 150 |
| Architecture technique + ADR | 2 j | 3 j | 5 j | 3,2 j | Architecte | €1 000 | €3 200 |
| Maquettes UX/UI Figma (toutes pages) | 8 j | 12 j | 18 j | 12,3 j | Designer | €600 | €7 380 |
| CDC rédigé + validé | 2 j | 3 j | 4 j | 3 j | Chef de projet | €750 | €2 250 |
| Setup environnements (dev/staging) | 2 j | 3 j | 5 j | 3,2 j | DevOps | €800 | €2 560 |
| **Total Lot 0** | | | | **25,9 j** | | | **€18 540** |

### Lot 1 — Socle plateforme

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Modèle de données (22 migrations) | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Auth + reset mot de passe | 3 j | 4 j | 6 j | 4,2 j | Senior Dev | €700 | €2 940 |
| Gestion membres + candidatures | 5 j | 8 j | 12 j | 8,2 j | Senior Dev | €700 | €5 740 |
| Admin Filament (12 resources) | 10 j | 15 j | 22 j | 15,3 j | Senior Dev | €700 | €10 710 |
| Permissions Shield (rôles + policies) | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Module finances + déclarations | 5 j | 8 j | 12 j | 8,2 j | Senior Dev | €700 | €5 740 |
| Génération reçus PDF | 2 j | 3 j | 5 j | 3,2 j | Senior Dev | €700 | €2 240 |
| Emails transactionnels (queue) | 3 j | 5 j | 7 j | 5 j | Mid Dev | €520 | €2 600 |
| Vitrine publique + formulaire candidature | 3 j | 5 j | 8 j | 5,2 j | Mid Dev | €520 | €2 704 |
| Tests unitaires (Lot 1) | 4 j | 6 j | 10 j | 6,3 j | QA / Senior | €600 | €3 780 |
| **Total Lot 1** | | | | **71,0 j** | | | **€50 934** |

### Lot 2 — Fonctionnalités réseau

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Module événements + check-in QR | 5 j | 8 j | 12 j | 8,2 j | Senior Dev | €700 | €5 740 |
| Module formations + sessions | 5 j | 7 j | 11 j | 7,3 j | Senior Dev | €700 | €5 110 |
| Module opportunités + candidatures | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Module recommandations (6 statuts) | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Module mentorat + sessions | 4 j | 6 j | 9 j | 6,2 j | Senior Dev | €700 | €4 340 |
| Module contacts + mise en relation | 2 j | 4 j | 6 j | 4 j | Mid Dev | €520 | €2 080 |
| Gamification + niveaux + historique | 4 j | 6 j | 9 j | 6,2 j | Senior Dev | €700 | €4 340 |
| Annuaire filtres avancés | 2 j | 3 j | 5 j | 3,2 j | Mid Dev | €520 | €1 664 |
| Dashboard graphiques (5 widgets) | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Export membres CSV/Excel/PDF | 2 j | 3 j | 5 j | 3,2 j | Senior Dev | €700 | €2 240 |
| Tests d'intégration (Lot 2) | 5 j | 8 j | 12 j | 8,2 j | QA | €600 | €4 920 |
| **Total Lot 2** | | | | **62,1 j** | | | **€41 354** |

### Lot 3 — API REST Mobile

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Architecture API + authentification Sanctum | 2 j | 3 j | 5 j | 3,2 j | Architecte | €1 000 | €3 200 |
| 10 contrôleurs API (39 endpoints) | 8 j | 12 j | 18 j | 12,3 j | Senior Dev | €700 | €8 610 |
| 10 Resources Eloquent JSON | 3 j | 4 j | 6 j | 4,2 j | Senior Dev | €700 | €2 940 |
| Middlewares sécurité API | 2 j | 3 j | 5 j | 3,2 j | Senior Dev | €700 | €2 240 |
| Documentation OpenAPI 3.0 (Swagger) | 3 j | 5 j | 7 j | 5 j | Senior Dev | €700 | €3 500 |
| Tests API (PHPUnit + Postman) | 4 j | 6 j | 9 j | 6,2 j | QA | €600 | €3 720 |
| **Total Lot 3** | | | | **34,1 j** | | | **€24 210** |

### Lot 4 — Qualité, sécurité et mise en production

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Audit sécurité OWASP Top 10 | 4 j | 5 j | 7 j | 5,2 j | Sécurité | €1 100 | €5 720 |
| Audit et rapport WCAG 2.1 AA | 3 j | 4 j | 6 j | 4,2 j | QA | €600 | €2 520 |
| Audit RGPD + registre traitements | 2,5 j | 3 j | 4 j | 3,1 j | RGPD (€350/h) | €2 800/j | €8 680 |
| Tests de charge (k6 / Gatling) | 2 j | 3 j | 5 j | 3,2 j | DevOps | €800 | €2 560 |
| Infrastructure production (IaC) | 3 j | 5 j | 8 j | 5,2 j | DevOps | €800 | €4 160 |
| Pipeline CI/CD (GitHub Actions) | 2 j | 3 j | 4 j | 3 j | DevOps | €800 | €2 400 |
| Recette fonctionnelle client | 3 j | 5 j | 7 j | 5 j | Chef de projet | €750 | €3 750 |
| Correction réserves de recette | 3 j | 5 j | 8 j | 5,2 j | Senior Dev | €700 | €3 640 |
| Déploiement production + monitoring | 1 j | 2 j | 3 j | 2 j | DevOps | €800 | €1 600 |
| Formation administrateurs (2h × 2 sessions) | 0,5 j | 1 j | 1,5 j | 1 j | Chef de projet | €750 | €750 |
| **Total Lot 4** | | | | **37,1 j** | | | **€35 780** |

### Lot 5 — Pilotage projet (transversal)

| Tâche | O | M | P | E (PERT) | Profil | TJM | Coût |
|---|---|---|---|---|---|---|---|
| Réunions de suivi + reporting | 8 j | 12 j | 16 j | 12 j | Chef de projet | €750 | €9 000 |
| Revues de code (peer review) | 4 j | 6 j | 9 j | 6,2 j | Architecte | €1 000 | €6 200 |
| Gestion du backlog (Jira/Linear) | 5 j | 7 j | 10 j | 7,2 j | Chef de projet | €750 | €5 400 |
| Documentation finale (guides) | 3 j | 5 j | 7 j | 5 j | Chef de projet | €750 | €3 750 |
| **Total Lot 5** | | | | **30,4 j** | | | **€24 350** |

---

## 11. Synthèse budgétaire

### 11.1 Récapitulatif par lot

| Lot | Intitulé | Jours estimés (PERT) | Coût HT |
|---|---|---|---|
| **Lot 0** | Cadrage et conception | 25,9 j | €18 540 |
| **Lot 1** | Socle plateforme | 71,0 j | €50 934 |
| **Lot 2** | Fonctionnalités réseau | 62,1 j | €41 354 |
| **Lot 3** | API REST Mobile | 34,1 j | €24 210 |
| **Lot 4** | Qualité, sécurité, mise en prod | 37,1 j | €35 780 |
| **Lot 5** | Pilotage projet (transversal) | 30,4 j | €24 350 |
| | **Sous-total charges** | **260,6 j** | **€195 168** |
| | **Provision pour aléas (12 %)** | +31,3 j | +€23 420 |
| | **TOTAL HT** | **291,9 j** | **€218 588** |
| | **TVA 20 %** | | +€43 718 |
| | **TOTAL TTC** | | **€262 306** |

> La provision de 12 % couvre les risques typiques d'un projet de cette envergure (changements de périmètre mineurs, révisions UX, intégrations tierce imprévues). Elle est utilisée uniquement sur justification documentée.

### 11.2 Répartition par type de prestation

```
Développement backend/frontend   ████████████████████  46 %   ~€100 700
Pilotage et architecture         ████████████          28 %   ~€61 200
QA, sécurité, conformité         ████████               18 %  ~€39 300
Design UX/UI                     ████                    8 %   ~€17 500
```

### 11.3 Fourchette de confiance (PERT ±1σ)

| Scénario | Budget HT | Probabilité |
|---|---|---|
| Optimiste (tout se passe bien) | €175 000 | ~16 % |
| **Probable (estimation PERT)** | **€218 588** | **~68 %** |
| Pessimiste (aléas importants) | €265 000 | ~16 % |

---

## 12. Options et variantes

### Option A — MVP réduit (sans API mobile, sans gamification)

Périmètre réduit : Lots 0, 1, 2 partiels (sans gamification, sans mentorat), Lot 4 allégé.

| | |
|---|---|
| **Durée** | 4 mois |
| **Budget HT** | ~€110 000–125 000 |
| **Usage** | Validation marché, pilote association |

### Option B — Standard (périmètre complet décrit ci-dessus)

| | |
|---|---|
| **Durée** | 6 mois |
| **Budget HT** | ~€195 000–220 000 |
| **Usage** | Déploiement opérationnel complet |

### Option C — Premium (+ application mobile native + notifications push + paiement en ligne)

Ajout : App React Native (iOS + Android), intégration Stripe/PayPal, notifications FCM, tableau de bord analytique avancé.

| | |
|---|---|
| **Durée** | 9–12 mois |
| **Budget HT** | ~€340 000–420 000 |
| **Usage** | Réseau national / régional à forte croissance |

---

## 13. Infrastructure et coûts récurrents

### 13.1 Hébergement recommandé (par an)

| Service | Fournisseur recommandé | Coût mensuel | Coût annuel |
|---|---|---|---|
| Serveur VPS (8 Go RAM, 4 vCPU) | Hetzner Cloud / OVHcloud | €40–80 | €480–960 |
| Base de données managée PostgreSQL | Hetzner / PlanetScale | €30–60 | €360–720 |
| Stockage objet (S3-compatible) | Hetzner Object Storage | €5–20 | €60–240 |
| CDN + protection DDoS | Cloudflare (Free–Pro) | €0–20 | €0–240 |
| Emails transactionnels | Resend / Mailgun | €10–30 | €120–360 |
| SSL (inclus dans Cloudflare) | — | €0 | €0 |
| Sauvegardes automatiques | Inclus VPS | €5–15 | €60–180 |
| Monitoring (Uptime + logs) | Better Stack / Sentry | €15–40 | €180–480 |
| **Total infrastructure** | | **€105–265/mois** | **€1 260–3 180/an** |

### 13.2 Maintenance applicative (contrat TMA)

| Niveau | Contenu | Coût mensuel |
|---|---|---|
| **Essentiel** | Mises à jour de sécurité, surveillance, 2h support/mois | €400–600 |
| **Standard** | Essentiel + mises à jour mineures, 8h dev/mois | €900–1 400 |
| **Premium** | Standard + 20h dev/mois, évolutions mineures, SLA 24h | €2 000–3 000 |

### 13.3 Total coût de possession (TCO) sur 3 ans

| Poste | Année 1 | Années 2–3 (×2) | Total 3 ans |
|---|---|---|---|
| Développement initial | €218 588 | — | €218 588 |
| Infrastructure | €2 200 | €4 400 | €6 600 |
| TMA Standard | €12 000 | €24 000 | €36 000 |
| Évolutions majeures (budget indicatif) | — | €30 000 | €30 000 |
| **TCO total** | | | **~€291 000** |

> Soit **~€97 000/an** — à comparer avec un ETP (Équivalent Temps Plein) développeur interne à ~€55 000–80 000 brut/an (charges incluses : €85 000–120 000/an), sans la même expertise technique ni le même périmètre fonctionnel.

---

## 14. Benchmark marché

### 14.1 Comparaison avec des solutions SaaS existantes

| Solution | Type | Coût annuel (jusqu'à 500 membres) | Limites |
|---|---|---|---|
| Wild Apricot | SaaS adhésions | €2 400–6 000/an | Pas de personnalisation, API limitée, en anglais |
| Springly | SaaS associations FR | €1 200–3 600/an | Fonctionnalités réseau limitées, pas d'API mobile |
| Hivebrite | SaaS réseau pro | €15 000–40 000/an | Coûteux, peu adapté aux petites structures |
| Développement sur mesure (ce projet) | Sur mesure | **€218 588** (one-shot) + TMA | Adapté exactement au besoin, propriété du code |

> **Breakeven vs. Hivebrite :** à €218 588 one-shot + €3 600/an TMA, le projet est rentabilisé vs. Hivebrite (€40 000/an) en moins de **6 ans**. Dès la 3ᵉ année vs. Hivebrite moyen.

### 14.2 Positionnement prix

| Segment | Budget | Positionnement |
|---|---|---|
| < €80 000 | Projet MVP réduit | Freelance senior, périmètre restreint |
| €80 000–150 000 | Projet standard marché français | Petite équipe, délai 4–6 mois |
| **€150 000–250 000** | **Notre estimation** | **Équipe complète, couverture tests, conformité** |
| €250 000–500 000 | Projet complexe / multi-tenant | ESN / agence senior, SaaS revendable |

---

## 15. Conditions commerciales

### 15.1 Modalités de paiement proposées

| Jalon | % | Montant indicatif (option B) |
|---|---|---|
| Signature du contrat + démarrage | 20 % | ~€43 700 |
| Livraison Lot 1 validé | 20 % | ~€43 700 |
| Livraison Lot 2 validé | 20 % | ~€43 700 |
| Livraison Lot 3 + API validée | 15 % | ~€32 800 |
| Recette client validée | 20 % | ~€43 700 |
| Mise en production + formation | 5 % | ~€10 900 |

### 15.2 Propriété intellectuelle

- Le **code source** est cédé intégralement au client à réception du solde final.
- Les **livrables de conception** (maquettes, documentation) sont inclus dans la cession.
- Le **droit moral** de l'équipe de développement est mentionné dans le colophon de la documentation technique (usage interne).
- Les **bibliothèques open source** utilisées conservent leurs licences respectives (MIT, Apache 2.0) — liste fournie dans le livrable L9.

### 15.3 Garantie

- **Garantie de parfait achèvement :** 3 mois après la mise en production. Correction gratuite de tout bug fonctionnel identifié dans le périmètre livré.
- **Garantie ne couvre pas :** les évolutions de périmètre, les mises à jour de dépendances tierces, les pannes d'infrastructure.

### 15.4 Clause de révision tarifaire

Pour les contrats TMA dépassant 12 mois, les tarifs sont révisables annuellement selon l'indice **Syntec** (activité informatique et conseil).

---

## Annexes

### A — Glossaire

| Terme | Définition |
|---|---|
| **TJM** | Taux Journalier Moyen — prix d'une journée de prestation d'un consultant |
| **JP** | Jour-Personne — unité de mesure de charge de travail (1 JP = 8h de travail effectif) |
| **FPA / FP** | Function Point Analysis / Function Points — mesure normalisée de la taille fonctionnelle d'un logiciel (ISO 14143) |
| **PERT** | Program Evaluation and Review Technique — méthode d'estimation à 3 points (O/M/P) |
| **WBS** | Work Breakdown Structure — découpage hiérarchique du projet en lots et tâches |
| **TMA** | Tierce Maintenance Applicative — contrat de maintenance post-livraison |
| **TCO** | Total Cost of Ownership — coût total de possession sur une période donnée |
| **ADR** | Architecture Decision Record — document de traçabilité des choix d'architecture |
| **WCAG** | Web Content Accessibility Guidelines — normes d'accessibilité numérique du W3C |
| **ISBSG** | International Software Benchmarking Standards Group — base de référence de productivité logicielle |
| **CI/CD** | Continuous Integration / Continuous Deployment — pipeline d'automatisation du déploiement |
| **IaC** | Infrastructure as Code — gestion de l'infrastructure via des fichiers de configuration versionnés |

### B — Références normatives

- **ISO/IEC 14143** — Mesure de la taille fonctionnelle des logiciels (base FPA)
- **ISO/IEC 25010** — Modèle qualité logiciel (fiabilité, maintenabilité, sécurité)
- **WCAG 2.1** (W3C, 2018) — Recommandations d'accessibilité web
- **RGPD** (UE) 2016/679 — Règlement général sur la protection des données
- **Directive (UE) 2019/882** — Accessibilité des produits et services numériques
- **OWASP Top 10** (2021) — Référentiel des vulnérabilités web les plus critiques
- **ISBSG Data Release 2024** — Benchmark de productivité logicielle (PHP/Laravel)
- **Baromètre Syntec Numérique 2025** — Référentiel tarifaire prestations IT France

---

*Cahier des charges et proposition budgétaire v1.0*  
*Réseau Professionnel d'Entrepreneurs — Document confidentiel*  
*Référentiel marché : Union Européenne, 2025–2026*
