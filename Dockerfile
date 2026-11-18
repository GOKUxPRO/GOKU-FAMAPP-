FROM dunglas/frankenphp:php8.4-alpine

RUN install-php-extensions imap openssl mbstring curl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /app

COPY composer.json ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

COPY . .
RUN chmod +x start-web.sh start-worker.sh

EXPOSE 80
