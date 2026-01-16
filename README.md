# Ge'Event – Plateforme de gestion d'événements pour Sacrée Tech

**Ge'Event** est l'application web officielle de gestion d'événements développée pour l'association **Sacrée Tech**, communauté tech rémoise.

Elle remplace la dépendance à meetup.com en proposant une solution sur-mesure permettant :

- La publication d'événements à venir (Apéros & Meetups)
- La gestion des inscriptions des participants
- La mise en avant des sponsors
- Une communication fluide avec la communauté
- Une interface d'administration complète
- Elle est à été déployée à cette adresse : <http://2a4v3-31uvm0038.ad-urca.univ-reims.fr/>

## Auteur(s)

- [**Hugo ALFREDO**](https://iut-info.univ-reims.fr/gitlab/alfr0002)
- [**Adam BENAHMED**](https://iut-info.univ-reims.fr/gitlab/bena0167)
- [**Nathan BUFFET**](https://iut-info.univ-reims.fr/gitlab/buff0030)
- [**Eliott BETRY**](https://iut-info.univ-reims.fr/gitlab/betr0003)

## Stack Technique

| Couche             | Technologie                  | URL                                                              |
|--------------------|------------------------------|------------------------------------------------------------------|
| Framework          | Symfony 6+                   | <https://symfony.com/>                                           |
| ORM                | Doctrine ORM                 | <https://www.doctrine-project.org/projects/orm.html>             |
| Base de données    | MySQL                        | <https://www.mysql.com/>                                         |
| Templating         | Twig                         | <https://twig.symfony.com/>                                      |
| Administration     | EasyAdmin Bundle             | <https://symfony.com/bundles/EasyAdminBundle/current/index.html> |
| Cartographie       | Leaflet + OpenStreetMap      | <https://leafletjs.com/>                                         |
| Envoi d'emails     | Symfony Mailer               | <https://symfony.com/doc/current/mailer.html>                    |
| Tests              | PHPUnit                      | <https://phpunit.de/index.html>                                  |
| Qualité de code    | PHP-CS-Fixer (PSR-12)        | <https://cs.symfony.com/>                                        |
| Fixtures / Factory | FakerPHP + Zenstruck/Factory | <https://fakerphp.org/>                                          |
| Chatbot            | Api Mistral                  | <https://docs.mistral.ai/api>                                    |
| Markdown Chatbot   | Marked                       | <https://github.com/markedjs/marked>                             |

## Installation / Configuration

### Prérequis

- [PHP >= 8.1](https://www.php.net/downloads.php)
- [Composer](https://getcomposer.org/download/)
- [MySQL >= 8.0](https://www.mysql.com/downloads/)

### Prérequis (tests)

- Sqlite3

### 1. Clonage du projet

```bash
git clone https://iut-info.univ-reims.fr/gitlab/buff0030/sae-si-geevent.git
cd sae-si-geevent
```

### 2. Installation des dépendances

```bash
composer install
```

### 3. Configuration de l'environnement

Copiez le fichier `.env` en `.env.local` et adaptez les variables :

```dotenv
# .env.local
DATABASE_URL="mysql://root:@127.0.0.1:3306/ge_event?serverVersion=8.0&charset=utf8mb4"
MAILER_DSN="smtp://LE_EMAIL:LE_MOTDEEPASSE@smtp.gmail.com:587?encryption=tls&auth_mode=login" #Pour gmail
CHATBOT_API_KEY="CLE_API_MISTRAL"
```

**Important pour l'envoi d'emails (Gmail) :**
> Pour que le `MAILER_DSN` fonctionne avec un compte Google, vous ne devez pas utiliser votre mot de passe habituel, mais un **Mot de passe d'application** :
> 1.  Allez dans votre **Compte Google > Sécurité**.
> 2.  Activez la **Validation en deux étapes** (si ce n'est pas déjà fait).
> 3.  Cherchez **"Mots de passe d'application"** dans la barre de recherche.
> 4.  Créez un mot de passe (nommez-le "GeEvent") et copiez les 16 caractères générés dans la variable `MAILER_DSN`.

### 4. Initialisation complète de la base de données

Une commande personnalisée a été ajoutée pour tout faire en une seule fois :

```bash
composer db
```

Cette commande exécute dans l’ordre :

- Suppression de la base (si existante)
- Création de la base
- Exécution des migrations
- Chargement des fixtures (utilisateurs, événements, sponsors, etc.)

### 5. Lancement du serveur

```bash
composer start
```

L’application est alors accessible sur : **<http://127.0.0.1:8000>**

### Commandes utiles

| Commande            | Description                                  |
|---------------------|----------------------------------------------|
| `composer start`    | Lance le serveur Symfony                     |
| `composer db`       | Réinitialise complètement la base + fixtures |
| `composer fix`      | Formate le code avec PHP-CS-Fixer            |
| `composer test`     | Lance les tests PHPUnit                      |
| `composer fixtures` | Recharge uniquement les fixtures             |

## Fonctionnalités implémentées

### Front-Office (public)

- Page d'accueil avec carte interactive (Leaflet) des événements à Reims
- Liste et détail des événements (Apéros & Meetups)
- Inscription / Connexion
- Rejoindre / Quitter un événement (uniquement si email vérifié)
- Affichage des sponsors par événement
- Profil utilisateur

### Back-Office (administration – `/admin`)

- Gestion complète via **EasyAdmin** :
  - Utilisateurs
  - Événements (création, modification, géolocalisation automatique via OpenStreetMap)
  - Sponsors (avec upload de logo)
- Menu d’administration adapté selon le rôle

### Sécurité & Authentification

- Système d’authentification
- Vérification d’email à l’inscription (lien unique)
- Rôles : `ROLE_USER` (utilisateurs lambda), `ROLE_ADMIN` (administrateurs du site)

### Données de test (fixtures)

- 1 comptes admin principal :
  - username : `JeanJean` -> mot de passe : `jean`
- Plusieurs utilisateurs normaux et administrateurs (15% d'administrateurs parmis les users) -> mot de passe : `test`
- Plusieurs événements passés et à venir (avec coordonnées sur Reims)
- Plusieurs sponsors avec liens vers des sites web et logo

## Captures d’écran (facultatif – à ajouter plus tard)

- Page d’accueil avec carte
- Détail d’un événement
- Interface EasyAdmin
---

## Guide Utilisateur - Ge'Event

### 1. Compte Utilisateur

Pour interagir avec la communauté (s'inscrire à des événements, devenir sponsor, etc.), un compte membre est nécessaire.

#### Accès à l'espace membre
L'accès se fait via la barre de navigation située en haut de chaque page :
- **Sur ordinateur** : Cliquez sur le bouton **"Espace membre"** en haut à droite.
- **Sur mobile** : Ouvrez le menu (icône ☰) puis appuyez sur **"Espace membre"**.

Une fois connecté, ce menu vous permettra d'accéder à votre profil ou de vous déconnecter.

### 2. Inscription et Vérification

#### Procédure d'inscription
1.  Dans le menu "Espace membre", sélectionnez **"S'inscrire"**.
2.  Remplissez le formulaire d'inscription avec les informations suivantes :
    - **Nom d'utilisateur** : Votre pseudo public (visible sur les événements et annonces).
    - **Email** : Une adresse valide est obligatoire pour la sécurité du compte.
    - **Mot de passe** : Doit contenir au minimum **6 caractères** pour être accepté.
3.  Cochez la case **"J'accepte les conditions générales"**.
4.  Cliquez sur le bouton de validation.

#### Vérification de l'email (Obligatoire)
Après l'inscription, votre compte est créé mais restreint. **Vous ne pourrez pas rejoindre d'événements tant que votre email n'est pas vérifié.**

1.  Consultez votre boîte mail (vérifiez aussi vos spams).
2.  Ouvrez l'email envoyé par *Ge'Event* ayant pour objet "Vérifiez votre email".
3.  Cliquez sur le lien de confirmation présent dans l'email.
4.  Vous serez redirigé vers le site : votre compte est désormais **vérifié** et pleinement fonctionnel.

### 3. Gestion des inscriptions aux événements

Une fois connecté et votre email vérifié, vous pouvez gérer votre participation.

#### Rejoindre un événement
1.  Parcourez la liste des événements via l'onglet **"Events"** ou la **"Carte"**.
2.  Cliquez sur un événement pour afficher ses détails (date, lieu, description).
3.  Si l'événement est à venir, cliquez sur le bouton **"Rejoindre l'événement"**.
4.  Un message de succès confirmera votre inscription et vous apparaitrez dans la liste des participants.

> **Attention :**
> * Vous ne pouvez pas rejoindre un événement dont la date de fin est passée.
> * Si un message d'erreur indique "Votre email n'est pas vérifié", référez-vous à la section 2 de ce guide.

#### Se désinscrire
En cas d'imprévu, vous pouvez libérer votre place :
1.  Retournez sur la page de l'événement auquel vous êtes inscrit.
2.  Le bouton affichera désormais **"Quitter l'événement"**.
3.  Cliquez dessus pour annuler votre participation.

#### Retrouver mes événements
Pour voir rapidement votre agenda :
1.  Cliquez sur votre pseudo dans le menu (en haut à droite).
2.  Sélectionnez **"Mon profil"**.
3.  Vous y trouverez la liste des événements auxquels vous participez.


---

### 4. Système d'Annonces

Les annonces permettent aux administrateurs de communiquer avec les utilisateurs. Il existe un système de ciblage pour restreindre la visibilité de certaines informations.

### Types de visibilité
Lors de la création d'une annonce, deux niveaux de confidentialité sont disponibles (définis dans `Target Enum`) :

1.  **Tout le monde (`ALL`)** :
    - **Cible :** Grand public.
    - **Accès :** Visible par les visiteurs non connectés et les membres.
    - **Usage :** Informations générales, promotion de l'association.

2.  **Uniquement les membres (`MEMBERS`)** :
    - **Cible :** Utilisateurs possédant un compte et connectés.
    - **Accès :** Restreint. Si un visiteur tente d'accéder à une annonce réservée, il est automatiquement **redirigé vers la page d'accueil**.
    - **Usage :** Codes promo, informations internes, organisation des AG.

### Comportement sur le site
- **Page Liste (`/annonces`)** : Affiche les annonces paginées (6 par page).
- **Page Détail (`/annonces/{id}`)** : Le site vérifie automatiquement vos droits d'accès avant d'afficher le contenu.
