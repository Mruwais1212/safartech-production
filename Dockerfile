# FROM php:8.2-fpm

# # Copy composer.lock and composer.json
# COPY composer.lock composer.json /var/www/html/

# # Set working directory
# WORKDIR /var/www/html

# # Install dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev\
#     libfreetype6-dev \
#     locales \
#     zip \
#     vim \
#     unzip \
#     git \
#     libzip-dev \
#     curl 
# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# RUN set -e ; \
#     pecl install xdebug-3.3.1; \
#     docker-php-ext-enable xdebug;

# # Install extensions
# RUN docker-php-ext-install pdo pdo_mysql bcmath zip calendar
# RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
# RUN docker-php-ext-install gd


# # Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Add user for laravel application
# RUN groupadd -g 1000 www
# RUN useradd -u 1000 -ms /bin/bash -g www www

# # Copy existing application directory contents
# COPY . /var/www/html

# # Copy existing application directory permissions
# COPY --chown=www:www . /var/www/html

# # Copy and set permissions for entrypoint script
# COPY docker-entrypoint.sh /usr/local/bin/
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# # Change current user to www
# USER www

# # Expose port 9000 and start php-fpm server
# EXPOSE 9000
# CMD ["/usr/local/bin/docker-entrypoint.sh"]


# FROM php:8.2-fpm

# # Install system dependencies including Nginx
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     locales \
#     zip \
#     unzip \
#     git \
#     libzip-dev \
#     curl \
#     nginx

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions
# RUN docker-php-ext-install pdo pdo_mysql bcmath zip calendar
# RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/
# RUN docker-php-ext-install gd

# # Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Create working directory
# WORKDIR /var/www/html

# # Copy only composer files first for better caching
# COPY composer.lock composer.json /var/www/html/

# # Install dependencies (without running scripts)
# RUN composer install --no-dev --no-scripts --no-autoloader

# # Copy the rest of the application files
# COPY . /var/www/html/

# # Now run composer scripts with full application available
# RUN composer dump-autoload --optimize && \
#     composer run-script post-root-package-install && \
#     composer run-script post-autoload-dump && \
#     composer run-script post-create-project-cmd

 


# # Configure Nginx
# COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# # Alternative simpler version:
# # COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Copy entrypoint script
# COPY docker-entrypoint.sh /usr/local/bin/
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# EXPOSE 8080

# CMD ["/usr/local/bin/docker-entrypoint.sh"]


# FROM php:8.2-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     locales \
#     zip \
#     unzip \
#     git \
#     libzip-dev \
#     curl \
#     nginx

# # Clear cache
# RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions (ADD calendar here)
# RUN docker-php-ext-install pdo pdo_mysql bcmath zip calendar gd
# RUN docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/

# # Install composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Configure working directory
# WORKDIR /var/www/html

# # Copy composer files first for caching
# COPY composer.lock composer.json /var/www/html/

# # Install dependencies (with platform check ignore if needed)
# RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-calendar || \
#     (docker-php-ext-install calendar && composer install --no-dev --optimize-autoloader --no-scripts)

# # Copy application files
# COPY . /var/www/html/

# # Configure Nginx
# COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
# RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Copy entrypoint script
# COPY docker-entrypoint.sh /usr/local/bin/
# RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# EXPOSE 8080

# CMD ["/usr/local/bin/docker-entrypoint.sh"]

# FROM php:8.2-fpm

# # Install system dependencies
# RUN apt-get update && apt-get install -y \
#     build-essential \
#     libpng-dev \
#     libjpeg-dev \
#     libfreetype6-dev \
#     locales \
#     zip \
#     unzip \
#     git \
#     libzip-dev \
#     curl \
#     nginx \
#     netcat-openbsd && \
#     apt-get clean && rm -rf /var/lib/apt/lists/*

# # Install PHP extensions (including calendar)
# RUN docker-php-ext-install pdo pdo_mysql bcmath zip calendar gd && \
#     docker-php-ext-configure gd --with-jpeg=/usr/include/ --with-freetype=/usr/include/

# # Install Composer
# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# # Configure working directory
# WORKDIR /var/www/html

# # Copy composer files first for caching
# COPY composer.lock composer.json ./

# # Install dependencies (with platform check ignore if needed)
# RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=ext-calendar || \
#     (docker-php-ext-install calendar && composer install --no-dev --optimize-autoloader --no-scripts)

# # Copy application files
# COPY . .

# # Configure Nginx
# COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# # Set permissions
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
#     chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# # Generate Laravel key if not set
# RUN if [ ! -f ".env" ]; then \
#     cp .env.example .env && \
#     php artisan key:generate; \
#     fi

# EXPOSE 8080

# # Startup command
# CMD sh -c "\
#     if [ -n \"\$DB_HOST\" ]; then \
#     echo 'Waiting for database...'; \
#     while ! nc -z \$DB_HOST \$DB_PORT; do sleep 0.5; done; \
#     echo 'Database ready!'; \
#     fi && \
#     php artisan migrate --force && \
#     php-fpm -D && \
#     nginx -g 'daemon off;'"

FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    xml \
    zip \
    gd \
    opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

# Rest of your Dockerfile remains the same...