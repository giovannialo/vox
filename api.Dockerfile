# Definir argumentos
ARG PHP_VERSION=${PHP_VERSION}

# Definir imagem
FROM php:${PHP_VERSION}-apache

# Definir variáveis
ENV TZ=${TZ}

# Definir timezone do servidor
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Atualizar pacotes
RUN apt-get update

# Instalar unzip
RUN apt-get install -y unzip

# Instalar extensões do php
RUN docker-php-ext-install pdo pdo_mysql

# Instalar módulo rewrite
RUN a2enmod rewrite

# Instalar composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir pasta de trabalho
WORKDIR /var/www/html
