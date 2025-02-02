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
    
# Instalamos la extensión de Redis para PHP (si quieres usar la extensión nativa php-redis)
RUN apt-get install -y libssl-dev \
    && pecl install redis \
    && docker-php-ext-enable redis

# Instalamos Composer (gestor de dependencias de PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo
WORKDIR /var/www

# Copiamos el proyecto Laravel en el contenedor
COPY src/ /var/www

# Exponemos el puerto 9000
EXPOSE 9000

# Comando para ejecutar PHP-FPM
CMD ["php-fpm"]
