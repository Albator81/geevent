# Guide utilisateur – Ge'Event

## 1. Compte utilisateur

Pour interagir avec la communauté (s'inscrire à des événements, devenir sponsor, etc.), un compte membre est nécessaire.

### Accès à l'espace membre

L'accès se fait via la barre de navigation située en haut de chaque page :

- **Sur ordinateur** : cliquez sur le bouton **"Espace membre"** en haut à droite.
- **Sur mobile** : ouvrez le menu (icône ☰) puis appuyez sur **"Espace membre"**.

Une fois connecté, ce menu vous permettra d'accéder à votre profil ou de vous déconnecter.

## 2. Inscription et vérification

### Procédure d'inscription

1. Dans le menu "Espace membre", sélectionnez **"S'inscrire"**.
2. Remplissez le formulaire d'inscription avec les informations suivantes :
    - **Nom d'utilisateur** : votre pseudo public (visible sur les événements et annonces).
    - **Email** : une adresse valide est obligatoire pour la sécurité du compte.
    - **Mot de passe** : doit contenir au minimum **6 caractères** pour être accepté.
3. Cochez la case **"J'accepte les conditions générales"**.
4. Cliquez sur le bouton de validation.

### Vérification de l'email (obligatoire)

Après l'inscription, votre compte est créé mais restreint. **Vous ne pourrez pas rejoindre d'événements tant que votre email n'est pas vérifié.**

1. Consultez votre boîte mail (vérifiez aussi vos spams).
2. Ouvrez l'email envoyé par *Ge'Event* ayant pour objet "Vérifiez votre email".
3. Cliquez sur le lien de confirmation présent dans l'email.
4. Vous serez redirigé vers le site : votre compte est désormais **vérifié** et pleinement fonctionnel.

## 3. Gestion des inscriptions aux événements

Une fois connecté et votre email vérifié, vous pouvez gérer votre participation.

### Rejoindre un événement

1. Parcourez la liste des événements via l'onglet **"Events"** ou la **"Carte"**.
2. Cliquez sur un événement pour afficher ses détails (date, lieu, description).
3. Si l'événement est à venir, cliquez sur le bouton **"Rejoindre l'événement"**.
4. Un message de succès confirmera votre inscription et vous apparaitrez dans la liste des participants.

> **Attention :**
> - Vous ne pouvez pas rejoindre un événement dont la date de fin est passée.
> - Si un message d'erreur indique "Votre email n'est pas vérifié", référez-vous à la section 2 de ce guide.

### Se désinscrire

En cas d'imprévu, vous pouvez libérer votre place :

1. Retournez sur la page de l'événement auquel vous êtes inscrit.
2. Le bouton affichera désormais **"Quitter l'événement"**.
3. Cliquez dessus pour annuler votre participation.

### Retrouver mes événements

Pour voir rapidement votre agenda :

1. Cliquez sur votre pseudo dans le menu (en haut à droite).
2. Sélectionnez **"Mon profil"**.
3. Vous y trouverez la liste des événements auxquels vous participez.

## 4. Système d'annonces

Les annonces permettent aux administrateurs de communiquer avec les utilisateurs. Il existe un système de ciblage pour restreindre la visibilité de certaines informations.

### Types de visibilité

Lors de la création d'une annonce, deux niveaux de confidentialité sont disponibles (définis dans l'enum `Target`) :

1. **Tout le monde (`ALL`)** :
    - **Cible :** grand public.
    - **Accès :** visible par les visiteurs non connectés et les membres.
    - **Usage :** informations générales, promotion de l'association.
2. **Uniquement les membres (`MEMBERS`)** :
    - **Cible :** utilisateurs possédant un compte et connectés.
    - **Accès :** restreint. Si un visiteur tente d'accéder à une annonce réservée, il est automatiquement **redirigé vers la page d'accueil**.
    - **Usage :** codes promo, informations internes, organisation des AG.

### Comportement sur le site

- **Page liste (`/annonces`)** : affiche les annonces paginées (6 par page).
- **Page détail (`/annonces/{id}`)** : le site vérifie automatiquement vos droits d'accès avant d'afficher le contenu.
