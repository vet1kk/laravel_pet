ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libz-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    autoconf \
    automake \
    libtool \
    make \
    gcc \
    supervisor

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install zip extension
RUN pecl install zlib zip && docker-php-ext-install zip

# Install redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install PHP extensions
RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql mbstring exif pcntl bcmath opcache

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration files
ADD infra/docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/php.ini
ADD infra/docker/php/conf.d/php-fpm.conf /usr/local/etc/
ADD infra/docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
ADD infra/docker/php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
ADD infra/docker/php/conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
ADD infra/docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
ADD infra/docker/nginx/default.conf /etc/nginx/sites-available/default
ADD infra/docker/start-container.sh /usr/local/bin/
ADD infra/docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Set working directory
WORKDIR /var/www

# Copy project
COPY --chown=www-data:www-data . .

# Install packages
RUN composer install --no-scripts --no-autoloader

RUN chmod +x artisan
RUN chmod -R guo+w ./bootstrap/cache ./storage

RUN composer dump-autoload --optimize

ENTRYPOINT ["start-container.sh"]
