# Use PHP with Apache (so it serves .m3u8/.ts files)
FROM php:8.1-apache

# Install FFmpeg
RUN apt-get update && apt-get install -y ffmpeg curl unzip

# Enable Apache modules if needed
RUN a2enmod rewrite

# Set working directory to Apache root
WORKDIR /var/www/html

# Copy app files into web root
COPY . .

# Expose port 80 (Render does this automatically)
EXPOSE 80