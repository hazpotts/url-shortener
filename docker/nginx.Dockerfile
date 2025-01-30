FROM node:20-alpine AS assets-build
WORKDIR /var/www/html
COPY . /var/www/html/

FROM url-shortener-fpm:latest AS fpm

FROM nginx:1.25-alpine AS nginx
COPY /docker/vhost.conf /etc/nginx/conf.d/default.conf
COPY --from=fpm /var/www/html/public /var/www/html/public