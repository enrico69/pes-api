FROM php:7.2.10-fpm

ARG ENABLE_X_DEBUG=true

# Get repository and install a lot of tools (some of them are probably already installed)
# Note to myself: I probably don't need them at all. I could remove this overhead on image creation.
RUN apt-get update && apt-get install --no-install-recommends -y \
        vim \
        nano \
        curl \
        wget \
        git \
        zip \
        unzip \
        ssh

# Install various PHP extensions
RUN docker-php-ext-install pdo_mysql

# Install xdebug (if enabled) with log. An old version, otherwise it is bugged.
RUN if [ "$ENABLE_X_DEBUG" = "true" ]; then pecl install xdebug-2.6.1 && docker-php-ext-enable xdebug; fi
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN if [ "$ENABLE_X_DEBUG" = "true" ]; then echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini; fi

RUN touch /var/log/xdebug_remote.log
RUN chmod 777 /var/log/xdebug_remote.log

# Expose the port xx = listen to the port xx
# It is useless because containers communicate between them through the Docker network
# However exposing port could be usefull for external connexions. For instance, if I want to connect to the MySQL with Sequel or Workbench
#EXPOSE 9000
#EXPOSE 80

# PHP.ini
COPY php.ini /usr/local/etc/php/