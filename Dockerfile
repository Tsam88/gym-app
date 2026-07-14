#########################################################
# Build stage: compile frontend assets with Node
#########################################################
FROM node:18 AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --silent
COPY . .
# Build production assets (adjust script name if needed)
RUN npm run production

#########################################################
# Runtime stage: PHP FPM image without Node installed
#########################################################
FROM php:8.0-fpm

# Install system deps and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        curl \
        git \
        zip \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libonig-dev \
        libxml2-dev \
        libicu-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy compiled frontend assets from builder
COPY --from=node_builder /app/public /var/www/html/public
COPY --from=node_builder /app/mix-manifest.json /var/www/html/mix-manifest.json

# Copy application source
COPY . /var/www/html

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
