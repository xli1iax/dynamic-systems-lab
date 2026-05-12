FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    octave \
    octave-control \
    && a2enmod rewrite


RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]