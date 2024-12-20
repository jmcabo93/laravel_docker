# Usamos una imagen base de PHP con Nginx y PHP-FPM
FROM php:8.1-fpm

# Instalamos las dependencias necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Instalamos Composer (gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo
WORKDIR /var/www

# Copiamos el proyecto Laravel en el contenedor
COPY src/ /var/www

# Instalamos las dependencias de Laravel
RUN composer install

# Cambiamos los permisos de los directorios de almacenamiento
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Exponemos el puerto 9000
EXPOSE 9000

# Comando para ejecutar PHP-FPM
CMD ["php-fpm"]
