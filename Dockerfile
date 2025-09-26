FROM php:8.2-apache

# Habilitar mod_rewrite de Apache
RUN a2enmod rewrite

# Instalar extensiones necesarias para MySQL
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql mysqli


# Copiar tu proyecto al contenedor
COPY ./src /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html