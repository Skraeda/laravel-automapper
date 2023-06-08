FROM php:8.0-cli

# Install dependencies
RUN apt-get update && apt-get install -y \
  build-essential \
  libpng-dev \
  libjpeg62-turbo-dev \
  libxml2-dev \
  libfreetype6-dev \
  libzip-dev \
  libonig-dev \
  locales \
  zip \
  jpegoptim optipng pngquant gifsicle \
  vim \
  unzip \
  git \
  curl

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install extensions
RUN docker-php-ext-install zip exif pcntl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
