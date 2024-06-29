# Use the official PHP image as a base image
FROM php:8.1-apache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy the composer.json file to the working directory
COPY composer.json .

# Install PHP dependencies (if any)
RUN composer install

# Copy the rest of the application code to the container
COPY . .

# Expose port 80
EXPOSE 80

# Set the entrypoint to the Apache server
CMD ["apache2-foreground"]
