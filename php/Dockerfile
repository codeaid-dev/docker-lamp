FROM php:8.0-apache
COPY ./php.ini /usr/local/etc/php/
RUN apt-get update &&\
  #PHP GD Multibyte String
  apt-get install -y zlib1g-dev libpng-dev libwebp-dev libjpeg62-turbo-dev libonig-dev &&\
  docker-php-ext-configure gd --with-jpeg --with-webp &&\
  docker-php-ext-install -j$(nproc) gd exif &&\
  #PHP PDO MySQL
  docker-php-ext-install pdo_mysql mysqli
#mod_rewrite有効化
RUN a2enmod rewrite
