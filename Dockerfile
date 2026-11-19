FROM dunglas/frankenphp:php8.4-alpine

# Install required PHP extensions
RUN install-php-extensions imap openssl mbstring curl zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Composer install
COPY composer.json ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Copy all files
COPY . .

# 🔥 આ લાઇન ખૂબ જ જરૂરી છે (પરમિશન માટે)
RUN chmod +x start-web.sh

EXPOSE 80

# Fallback command (Railway માં Custom Start Command હશે તો પણ આ કામ કરશે)
CMD ["./start-web.sh"]
