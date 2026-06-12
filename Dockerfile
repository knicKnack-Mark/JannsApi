FROM richarvey/nginx-php-fpm:3.1.6

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

RUN chmod -R 775 storage bootstrap/cache

ENV WEBROOT=/var/www/html/public

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]