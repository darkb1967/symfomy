# Créer un projet d'API Rest avec Symfony 

## Étapes 
- Créer le container Docker
    - Serveur Web avec PHP et certains outils préinstallés
    - Base de données
- Installer Symfony dans le container
- Tester l'installation
- Installer les dépendances nécessaires à un projet de type API Rest
- Configurer l'API
- Tester l'API
- Créer une application frontend utilisant l'API
- Tester l'application frontend

## Préparation

1. En local, créer un répertoire **vide**
2. Dans ce répertoire, copier les fichiers suivants :
    - [docker-compose.yml](./docker-compose.yml)
    - [Dockerfile](./Dockerfile)
    - [conf/000-default.conf](./conf/000-default.conf)
    - Structure de fichiers attendue : 
        - VotreRepertoire/
            - conf/
                - 000-default.conf
            - docker-compose.yml
            - Dockerfile
3. Dans un terminal Se positionner dans le répertoire
4. Exécuter la commande `docker compose up`

## Dockerfile 

```dockerfile
# Image de base : Apache avec PHP 8.4
FROM php:8.4-apache

# Nécessaire au bon fonctionnement de Composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Port d'écoute
EXPOSE 80

# Répertoire de travail pour le script courant
WORKDIR /var/www/html

# Installation de git, unzip & zip et composer
RUN apt-get update -qq && \
    apt-get install -qy \
    git \
    gnupg \
    unzip \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Installation d'extensions PHP
RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql

# Copie le fichier de configuration d'Apache
COPY conf/000-default.conf /etc/apache2/sites-available/000-default.conf

# Activation de modules Apache
RUN a2enmod rewrite remoteip
```

## docker-compose.yml 

```yaml
# Nom du container "principal"
name: myapi-symfony-2503
# Déclaration des services
services: 
  # Base de données (nommé db)
  db:
    # Nom de l'image utilisée pour ce service
    image: mariadb:11
    # Nom du container hébergeant ce service
    container_name: myapi-symfony-2503-mysql
    # Redémarer automatiquement le service
    restart: always
    # Port d'écoute 
    ports: ['3306:3306']
    # Volumes volume_name:/container/path
    # db_data pointe sur le répertoire de travail de mysql
    volumes:
      - db_data:/var/lib/mysql
    # Nom de la base de données et identifiants
    environment:
      MYSQL_DATABASE: db_myapi
      MYSQL_USER: user 
      MYSQL_PASSWORD: user
      MYSQL_ALLOW_EMPTY_PASSWORD: 'no'
      MYSQL_ROOT_PASSWORD: root
  # Serveur web Apache avec PHP (nommé web)
  web:
    # image: "php:8.4-apache"
    container_name: myapi-symfony-2503-apache2
    # Ce container sera crée à partir du Dockerfile présent dans le répertoire courant
    build: .
    restart: always
    # Le service db doit être démarré
    depends_on: ['db']
    # Port d'écoute ['hote:container']
    ports: ['8000:80']
    # Permet la communication avec le service db (bientôt deprecated)
    links: ['db:db']
    # Le répertoire local "myapi" pointe sur le répertoire de travail du serveur Apache
    # il sera créé à la création du container
    # C'est le répertoire dans lequel on installera symfony
    volumes:
      - './myapi/:/var/www/html'
    environment:
      MYSQL_DB_HOST: db
      MYSQL_DATABASE: db_myapi
      MYSQL_USER: user 
      MYSQL_PASSWORD: user
  # Accéder à la base de données via le navigateur
  phpmyadmin:
    image: phpmyadmin/phpmyadmin:5.2
    container_name: myapi-symfony-2503-phpmyadmin
    restart: always
    depends_on: ['db']
    ports: ['1200:80']
    links: ['db:db']
    environment:
      - MYSQL_DB_HOST=db
      - MYSQL_USER=user 
      - MYSQL_PASSWORD=user

# Création d'un volume db_data pour la persistance des données
volumes:
  db_data: {}

```

## conf/000-default.conf

```apache
<VirtualHost *:80>
    # Nom du serbeur (typiquement le nom de domaine)
    ServerName myapi
    # Email de l'admin du serveur
	ServerAdmin contact@example.com
    # Répertoire racine du serveur web
    # Tout ce qui est en dehors de ce répertoire n'as pas accessible depuis le navigateur
    # Il pointe sur le dossier public de notre future installation Symfony
	DocumentRoot /var/www/html/public

	<Directory /var/www/html/public>
    AllowOverride None
    Require all granted
    FallbackResource /index.php
  </Directory>
</VirtualHost>
```

Après avoir exécuté la commande `docker compose up`, le container est créé et lancé.

1. Accéder au terminal du container web (myapi-symfony-2503-apache2)
2. Se positionner sur le chemin '/var/www/html'
    - Bien vérifier qu'il est vide (le vider si nécessaire)
3. Lancer l'installation de Symfony
    - `composer create-project symfony/skeleton:"8.0.*" .`
    - (Pensez à bien mettre le . à la fin de la commande (. = répertoire courant))
4. Patienter...

L'installation de Symfony est terminée

- Accéder à l'url http://127.0.0.1:8000
- Vous devriez voir la page par défaut de Symfony.

## Installation des dépendances Symfony

```sh
composer require api 
composer require symfony/apache-pack
``` 

Cette commande va installer les dépendances nécessaires pour un projet d'API Rest.

Une fois les dépendances installées, accéder à l'url [http://localhost:8000/index.php/api/](http://localhost:8000/index.php/api/). Vous devriez voir une page ayant pour titre "Hello API Platform.

Le projet étant destiné à n'accueillir qu'une API, nous allons le configurer pour que l'adresse de base [http://localhost:8000/](http://localhost:8000/) pointe directement sur l'API.

Ouvrir le fichier `myapi/config/routes/api_platform.yaml`

Puis commenter la ligne `prefix: /api` en la prefixant avec un hashtag comme ceci : `# prefix: /api`.

# Configurer et créer la base de données

Ouvrir le fichier `myapi/.env`

Commenter la ligne `DATABASE_URL="postgre.....

et ajouter en dessous la ligne suivante : 

`DATABASE_URL="mysql://user:user@db:3306/db_myapi?serverVersion=11.8.5-MariaDB&charset=utf8mb4"`

Direction le terminal du conteneur Web :

```bash
cd /var/www/html
php bin/console doctrine:database:create
```

## Créer la 1ère entité.


```bash
cd /var/www/html
composer require symfony/maker-bundle --dev
php bin/console make:entity
```

## Sauvegarder les changements

```bash
cd /var/www/html
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

# Autres commandes de migrations : 


```bash
# Afficher la version de la migration en cours
php bin/console doctrine:migrations:current   
# Afficher la version de la dernière migration  
php bin/console doctrine:migrations:latest   
# Afficher la liste de toutes les migrations et leurs statuts  
php bin/console doctrine:migrations:list     
# Afficher des informations sur l'état actuel des migrations et autres   
php bin/console doctrine:migrations:status      
```
