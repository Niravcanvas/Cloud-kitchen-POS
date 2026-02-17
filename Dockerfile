FROM php:8.2-apache

# Install mysqli extension (required for your app)
RUN docker-php-ext-install mysqli

# Copy all project files into the web root
COPY . /var/www/html/

# Make sure the invoices folder is writable (for PDF generation)
RUN mkdir -p /var/www/html/invoices \
    && chown -R www-data:www-data /var/www/html/