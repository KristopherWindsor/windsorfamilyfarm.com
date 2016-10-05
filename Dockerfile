FROM php:7.0-apache
RUN apt-get update && apt-get install -y
RUN echo $ADMIN_PASSWORD_HASH > ./src/summer/data/admin-password.txt
COPY ./src /var/www/html/
RUN chown -R www-data:www-data /var/www/html/
