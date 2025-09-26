# Dockerfile für den PHP-Dienst der Galerie-App
FROM php:8.2-apache

# Installiere die pdo_mysql-Erweiterung für die Datenbankverbindung
RUN docker-php-ext-install pdo_mysql

# Aktiviere das Apache Rewrite-Modul (für Clean URLs, falls später benötigt)
RUN a2enmod rewrite

# Installiere GD-Bibliothek für Bildbearbeitung (z.B. Thumbnails), falls später benötigt
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libwebp-dev && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# Setze das Arbeitsverzeichnis im Container
WORKDIR /var/www/html