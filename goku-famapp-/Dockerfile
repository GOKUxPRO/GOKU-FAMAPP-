FROM dunglas/frankenphp:php8.4-alpine

# 1. જરૂરી PHP એક્સટેન્શન ઇન્સ્ટોલ કરો (IMAP ખૂબ જરૂરી છે)
RUN install-php-extensions imap openssl mbstring curl zip

# 2. Composer ઇન્સ્ટોલ કરો
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. વર્કિંગ ડિરેક્ટરી સેટ કરો
WORKDIR /app

# 4. Composer ડિપેન્ડન્સી ઇન્સ્ટોલ કરો
COPY composer.json ./
RUN composer install --no-interaction --no-dev --optimize-autoloader

# 5. બાકીની બધી ફાઇલો કોપી કરો
COPY . .

# 6. 🔥 સ્ક્રિપ્ટ ફાઇલોને એક્ઝિક્યુટેબલ બનાવો (આ લાઇન ખૂટતી હતી)
RUN chmod +x start-web.sh start-worker.sh

# 7. પોર્ટ એક્સપોઝ કરો
EXPOSE 80

# 8. ડિફોલ્ટ કમાન્ડ (Railway માં આપણે Custom Command વાપરીશું)
CMD ["./start-web.sh"]
