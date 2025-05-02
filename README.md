# Clyptor - Plateforme de services en ligne

Clyptor est une plateforme web pour la location de voitures, de maisons et le covoiturage, développée avec PHP et MySQL en utilisant l'architecture MVC.

## Structure du projet

```
clyptor/
├── Model/            # Classes représentant les données
├── View/             # Interfaces utilisateur
│   ├── backoffice/   # Interface d'administration
│   ├── frontoffice/  # Interface utilisateur principale
│   └── includes/     # Éléments d'interface communs
├── Controller/       # Logique métier
├── config.php        # Configuration globale
├── index.php         # Point d'entrée de l'application
└── database.sql      # Script de création de la base de données
```

## Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache, Nginx, etc.)

## Installation

1. Clonez le dépôt dans votre répertoire web :
   ```
   git clone https://github.com/votre-compte/clyptor.git
   ```

2. Créez la base de données et importez le schéma :
   ```
   mysql -u root -p < clyptor/database.sql
   ```

3. Configurez la connexion à la base de données dans `clyptor/config.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'clyptor_db');
   define('DB_USER', 'votre_utilisateur');
   define('DB_PASS', 'votre_mot_de_passe');
   ```

4. Configurez l'URL de base dans `clyptor/config.php` :
   ```php
   define('BASE_URL', 'http://localhost/chemin/vers/clyptor');
   ```

5. Assurez-vous que le serveur web a les permissions nécessaires pour les dossiers du projet.

## Utilisation

### Espace utilisateur (FrontOffice)

- Page d'accueil : `index.php`
- Connexion : `index.php?page=user&action=login`
- Inscription : `index.php?page=user&action=register`
- Location de voitures : `index.php?page=car`
- Location de maisons : `index.php?page=home`
- Covoiturage : `index.php?page=covoiturage`
- Contact : `index.php?page=contact`

### Espace administrateur (BackOffice)

- Tableau de bord admin : `index.php?page=admin`
- Gestion des utilisateurs : `index.php?page=user&action=list`

## Comptes par défaut

- Admin : 
  - Identifiant : admin
  - Mot de passe : admin123

- Utilisateur régulier :
  - Identifiant : user
  - Mot de passe : user123

## Développement

Pour ajouter de nouvelles fonctionnalités :

1. Créez un modèle dans le dossier `Model/`
2. Créez un contrôleur dans le dossier `Controller/`
3. Créez les vues nécessaires dans le dossier `View/frontoffice/` ou `View/backoffice/`
4. Mettez à jour le routage dans `index.php`

## Licence

Tous droits réservés. 
