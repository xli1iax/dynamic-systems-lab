FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    octave \
    octave-control \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && a2enmod rewrite

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]