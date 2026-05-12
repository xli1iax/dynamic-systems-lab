FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    octave \
    octave-control \
    && a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]