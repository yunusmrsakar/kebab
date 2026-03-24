FROM php:8.3-apache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Install SQLite extension
RUN apt-get update && apt-get install -y libsqlite3-dev && \
    docker-php-ext-install pdo_sqlite && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Set document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Allow .htaccess overrides
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Copy project files
COPY . /var/www/html/

# Create writable directories
RUN mkdir -p /var/www/html/uploads/sliders \
    /var/www/html/uploads/menu \
    /var/www/html/uploads/marketing \
    /var/www/html/uploads/logos && \
    chown -R www-data:www-data /var/www/html/uploads /var/www/html/

# Expose port (Render uses PORT env variable)
EXPOSE 10000

# Update Apache to listen on Render's port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

CMD ["apache2-foreground"]
