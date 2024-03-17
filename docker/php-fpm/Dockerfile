FROM php:8.3-fpm
WORKDIR /application

RUN apt-get update \
        && apt-get install -y --no-install-recommends \
        libicu-dev \
        zlib1g-dev \
        libpq-dev \
        libzip-dev \
        libpcre3-dev \
        ssmtp vim git cron zip \
        && docker-php-ext-install \
        intl \
        mbstring \
        pdo_pgsql \
        pdo \
        pgsql \
        zip; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && docker-php-ext-install pgsql pdo_pgsql sockets

RUN mkdir -p /application/src/Infra/Api/Public/logs.txt

#CMD ["php", "/application/src/Infra/Services/EmailService.php"]