# Usamos la imagen oficial de PHP con Apache para facilitar la configuración
FROM php:8.2-apache

# 1. Instalamos dependencias del sistema y extensiones de PHP necesarias para Laravel y PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo pdo_pgsql pgsql gd zip

# 2. Habilitamos el módulo rewrite de Apache (vital para las rutas de Laravel)
RUN a2enmod rewrite

# 3. Configuramos el DocumentRoot de Apache hacia la carpeta /public de Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 4. Instalamos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Copiamos los archivos del proyecto al contenedor
COPY . /var/www/html

# 6. Instalamos las dependencias de PHP (sin las de desarrollo para que pese menos)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 7. Ajustamos permisos para que Apache pueda escribir en storage y cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# --- EL FIX PARA CLOUD RUN ---
# Le decimos a Apache que escuche en el puerto dinámico que Google Cloud Run le asigne
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Comando para iniciar Apache
CMD ["apache2-foreground"]