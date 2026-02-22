FROM php:8.2-apache

LABEL maintainer="SAW Development Team"
LABEL description="SAW Web Application with PHP 8.2 and Apache"

# ========================================
# 1. Instalar dependências necessárias
# ========================================
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
    build-essential \
    autoconf \
    automake \
    libtool \
    pkg-config \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    curl \
    git \
    unzip \
    && apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# ========================================
# 2. Instalar extensões PHP
# ========================================
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg && \
    docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip \
    bcmath

# ========================================
# 2.5. Remover dependências de build (reduz tamanho)
# ========================================
RUN apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false \
    build-essential \
    autoconf \
    automake \
    libtool \
    pkg-config \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    zlib1g-dev

# ========================================
# 3. Habilitar módulos Apache necessários
# ========================================
RUN a2enmod rewrite headers expires deflate remoteip ssl proxy proxy_http

# ========================================
# 4. Configuração Apache principal
# ========================================
RUN cat > /etc/apache2/sites-available/000-default.conf <<'EOF'
<VirtualHost *:80>
    ServerAdmin admin@saw.local
    DocumentRoot /var/www/html

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/saw_error.log
    CustomLog ${APACHE_LOG_DIR}/saw_access.log combined

    # Security Headers
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"

    <Directory /var/www/html>
        Options -MultiViews +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Gzip Compression
    <IfModule mod_deflate.c>
        AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
    </IfModule>

    # Cache Control
    <IfModule mod_expires.c>
        ExpiresActive On
        ExpiresDefault "access plus 0 seconds"
        ExpiresByType text/html "access plus 0 seconds"
        ExpiresByType image/gif "access plus 1 year"
        ExpiresByType image/jpeg "access plus 1 year"
        ExpiresByType image/png "access plus 1 year"
        ExpiresByType text/css "access plus 1 month"
        ExpiresByType application/javascript "access plus 1 month"
    </IfModule>
</VirtualHost>
EOF

# Supress Apache warnings
RUN echo "ServerSignature Off" >> /etc/apache2/apache2.conf && \
    echo "ServerTokens Prod" >> /etc/apache2/apache2.conf

# ========================================
# 5. Configurações PHP
# ========================================
RUN echo "display_errors=On" > /usr/local/etc/php/conf.d/app.ini && \
    echo "error_reporting=E_ALL" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "log_errors=On" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "memory_limit=256M" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "upload_max_filesize=50M" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "session.cookie_httponly=1" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "default_charset=utf-8" >> /usr/local/etc/php/conf.d/app.ini && \
    echo "date.timezone=America/Sao_Paulo" >> /usr/local/etc/php/conf.d/app.ini

# ========================================
# 6. Criar diretórios necessários
# ========================================
RUN mkdir -p /var/www/html/logs \
    /var/www/html/tmp \
    /var/www/html/uploads \
    /var/www/html/cache

# ========================================
# 7. Copiar código
# ========================================
COPY . /var/www/html/

# ========================================
# 8. Permissões
# ========================================
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type d -exec chmod 755 {} \; && \
    chmod -R 775 /var/www/html/logs \
    /var/www/html/tmp \
    /var/www/html/uploads \
    /var/www/html/cache

# ========================================
# 9. Healthcheck
# ========================================
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# ========================================
# 10. Entrada do container
# ========================================
RUN echo '#!/bin/bash' > /etc/apache2/docker-entrypoint.sh && \
    echo 'set -e' >> /etc/apache2/docker-entrypoint.sh && \
    echo 'exec apache2-foreground' >> /etc/apache2/docker-entrypoint.sh && \
    chmod +x /etc/apache2/docker-entrypoint.sh

ENTRYPOINT ["/etc/apache2/docker-entrypoint.sh"]
EXPOSE 80

CMD ["apache2-foreground"]
