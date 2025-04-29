FROM php:8.3-fpm

RUN apt update && apt install -y git zip unzip wget
RUN wget https://get.symfony.com/cli/installer -O - | bash

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
