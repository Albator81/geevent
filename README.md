# Ge'Event – Plateforme de gestion d'événements pour Sacrée Tech

**Ge'Event** est l'application web officielle de gestion d'événements développée pour l'association **Sacrée Tech**, communauté tech rémoise.

Elle remplace la dépendance à meetup.com en proposant une solution sur-mesure permettant :

- La publication d'événements à venir (Apéros & Meetups)
- La gestion des inscriptions des participants
- La mise en avant des sponsors
- Une communication fluide avec la communauté
- Une interface d'administration complète

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

- 2 comptes admin :
  - username : `JeanJean` -> mot de passe : `jean`
- Plusieurs utilisateurs normaux et administrateurs (15% d'administrateurs parmis les users)
- Plusieurs événements passés et à venir (avec coordonnées sur Reims)
- Plusieurs sponsors avec liens vers des sites web et logo

## Captures d’écran (facultatif – à ajouter plus tard)

- Page d’accueil avec carte
- Détail d’un événement
- Interface EasyAdmin