# Symfony Quiz

## Installation

- Cloner le projet.

- Naviguer dans le dossier du projet avec la commande `cd`.

- Installer les dépendances avec `composer install`.

- Configurer la connexion à la base de données dans le fichier **.env** en remplaçant la version du serveur par `5.7` pour un serveur MySQL et `mariadb-10.4.0` pour un serveur MariaDB.

- Créer la base de données avec `symfony console doctrine:database:create`.

- Créer une migration avec `symfony console make:migration`.

- Exécuter la migration avec `symfony console doctrine:migrations:migrate`.

Vous devriez obtenir un message vous disant que votre base de données est synchronisée avec les fichiers de mapping en exécutant la commande `symfony console doctrine:schema:validate`.

- _(facultatif)_ Exécuter les fixtures avec `symfony console doctrine:fixtures:load`.

## Exécution

- Naviguer dans le dossier du projet avec la commande `cd`.

- Lancer le serveur local avec `symfony serve`.

Vous devriez avoir accès à la page d'accueil de l'application en naviguant sur [`https://localhost:8000`](https://localhost:8000).
