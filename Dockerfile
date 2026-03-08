# Usamos la imagen oficial de PHP con Apache
FROM php:8.2-apache

# 1. Instalamos dependencias, Postgres, Zip y ahora Node.js (con NPM)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip

# 2. Habilitamos el módulo rewrite de Apache
RUN a2enmod rewrite

# 3. Configuramos el DocumentRoot de Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copiamos los archivos del proyecto al contenedor
COPY . /var/www/html

# 6. Instalamos las dependencias de PHP
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 7. Compilamos el Frontend (Tailwind y Vite) - ESTA ES LA MAGIA
RUN npm install
RUN npm run build

# 8. Ajustamos permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 9. Le decimos a Apache que escuche en el puerto dinámico de Google
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Comando para iniciar
CMD ["apache2-foreground"]