#  image de base PHP 8.3 avec FPM, version Alpine (légère ~30MB)
FROM php:8.3-fpm-alpine

# Installation des extensions PHP nécessaires
# extensions pour se connecter à MySQL et pour améliorer les performances avec opcache
RUN docker-php-ext-install pdo pdo_mysql opcache

# Copie de Composer depuis son image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# port PHP-FPM 
EXPOSE 9000