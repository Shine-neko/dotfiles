FROM php:8.1-fpm

RUN apt update && apt install -y git zip unzip wget
RUN wget https://get.symfony.com/cli/installer -O - | bash  
