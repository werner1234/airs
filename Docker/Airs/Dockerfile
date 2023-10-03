# build environment
FROM php:5.6-apache

# APT
RUN apt-get update -y \
  && apt-get install jq libzip-dev libgmp-dev libc-client-dev libkrb5-dev libbz2-dev libicu-dev libonig-dev libpng-dev libxslt-dev libmcrypt-dev wget nano mc -y
RUN rm -rf /var/lib/apt/lists/* \
  && ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
# DOCKER
RUN docker-php-ext-install gd \
  && docker-php-ext-install calendar \
  && docker-php-ext-install exif \
  && docker-php-ext-install gettext \
  && docker-php-ext-install mysqli \
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-install shmop \
  && docker-php-ext-install sockets \
  && docker-php-ext-install sysvmsg \
  && docker-php-ext-install gmp \
  && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
  && docker-php-ext-install imap \
  && docker-php-ext-install intl \
  && docker-php-ext-install mbstring \
  && docker-php-ext-install zip \
  && docker-php-ext-install bz2 \
  && docker-php-ext-install xsl \
  && docker-php-ext-install wddx \
  && docker-php-ext-install mcrypt \
  && docker-php-ext-install mysql

RUN echo "ServerName airs" >> /etc/apache2/apache2.conf

WORKDIR /var/www
# The line below is for kubernetes deploy, which requires a copy of the source
#COPY /src /var/www
COPY /Docker/Airs/php.ini /usr/local/etc/php
RUN mkdir -p temp
RUN chmod 777 temp
RUN ls /var/www
RUN cat /etc/hosts

# apache mod ip
COPY /Docker/Airs/apache/remoteip.conf /etc/apache2/mods-enabled/

RUN a2enmod remoteip