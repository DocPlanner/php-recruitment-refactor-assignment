FROM php:8.1-cli
RUN apt-get update -y && apt-get install -y \
    git \
    libzip-dev \
    unzip \
    libicu-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install zip \
&& docker-php-ext-configure intl \
&& docker-php-ext-install intl

WORKDIR /app

EXPOSE 2137
CMD bash -c "composer install"
CMD bash -c "bin/VendorApi_linux"
