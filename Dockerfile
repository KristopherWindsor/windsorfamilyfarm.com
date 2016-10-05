FROM php:7.0-apache
COPY ./src /var/www/html/
RUN echo $ADMIN_PASSWORD_HASH > /var/www/html/summer/data/admin-password.txt
RUN chown -R www-data:www-data /var/www/html/
