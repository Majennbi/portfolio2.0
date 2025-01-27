# Utiliser une image PHP avec Composer
FROM php:8.2-fpm

# Installer des outils nécessaires et extensions PHP pour Symfony et MySQL
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libzip-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /app

# Copier les fichiers du projet
COPY . .

# Installer les dépendances Symfony
RUN composer install --no-interaction --optimize-autoloader

# Exposer le port pour le serveur Symfony
EXPOSE 8000

# Commande par défaut pour démarrer le serveur Symfony
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
