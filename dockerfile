FROM php:7.2-fpm

# ...

# Install PHP Redis extension
RUN pecl install -o -f redis \  
  &&  rm -rf /tmp/pear \
  &&  docker-php-ext-enable redis