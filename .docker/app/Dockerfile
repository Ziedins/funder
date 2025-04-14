FROM php:8.4-fpm
RUN apt-get update && \
    apt-get install -y zip curl libcurl3-dev libzip-dev libpng-dev libonig-dev libxml2-dev

RUN docker-php-ext-install curl gd mbstring mysqli pdo pdo_mysql xml

# Install Composer so it's available
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

CMD ["php-fpm"]

EXPOSE 9000
