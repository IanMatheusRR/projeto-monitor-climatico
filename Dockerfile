# Usa a imagem oficial do PHP 8.2 com Apache
FROM php:8.2-apache

# Instala as dependências de sistema necessárias para compilar a extensão do PostgreSQL
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões do PHP para o banco de dados
RUN docker-php-ext-install pdo pdo_pgsql

# Ativa o php.ini de produção e injeta nossa extensão diretamente nele
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "extension=pdo_pgsql" >> "$PHP_INI_DIR/php.ini"

# Copia todos os arquivos do projeto para a pasta raiz do servidor
COPY . /var/www/html/

# Ajusta as permissões da pasta
RUN chown -R www-data:www-data /var/www/html