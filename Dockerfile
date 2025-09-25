# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema necessárias para compilar a extensão.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar a extensão PDO e a extensão PDO para PostgreSQL.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: (A MUDANÇA CRÍTICA) Copiar nosso arquivo de configuração manual.
# Isso força o PHP do Apache a carregar a extensão pdo_pgsql.
COPY custom-php.ini /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini

# PASSO 5: Habilitar o módulo 'rewrite' do Apache (boa prática).
RUN a2enmod rewrite

# PASSO 6: Copiar todos os arquivos do seu projeto.
COPY . /var/lib/www/html/

# PASSO 7: Ajustar as permissões da pasta.
RUN chown -R www-data:www-data /var/lib/www/html