FROM php:8.2-apache

WORKDIR /var/www/html 
COPY . /var/www/html/

RUN apt-get update && apt-get install -y && docker-php-ext-install pdo pdo_mysql

EXPOSE 5050 

CMD [ "apache2-foreground" ]