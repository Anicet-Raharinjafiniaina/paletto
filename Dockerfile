FROM php:8.4-apache

# =========================
# System dependencies
# =========================
RUN apt update && apt install -y \
    libicu-dev \
    libpq-dev \
    libldap2-dev \
    libsasl2-dev \
    ldap-utils \
    libzip-dev \
    unixodbc-dev \
    curl \
    gnupg2 \
    ca-certificates \
    unzip \
    git \
    apt-transport-https \
    pkg-config \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git

# ZIP (IMPORTANT)
RUN docker-php-ext-install zip    

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# PHP extensions GD
# =========================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# =========================
# PHP extensions (PostgreSQL + intl)
# =========================
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    intl

# =========================
# LDAP extension
# =========================
RUN docker-php-ext-configure ldap \
    && docker-php-ext-install ldap

# =========================
# Microsoft ODBC Driver (FIX MODERN - NO apt-key)
# =========================
RUN curl -sSL https://packages.microsoft.com/keys/microsoft.asc \
    | gpg --dearmor \
    | tee /usr/share/keyrings/microsoft.gpg > /dev/null

RUN echo "deb [signed-by=/usr/share/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" \
    > /etc/apt/sources.list.d/mssql-release.list

RUN apt update && ACCEPT_EULA=Y apt install -y msodbcsql18

# =========================
# SQL Server PHP drivers
# =========================
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# =========================
# Apache config (CodeIgniter 4)
# =========================
RUN a2enmod rewrite

RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data /var/www/html