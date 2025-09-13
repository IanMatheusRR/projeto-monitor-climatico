# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema para o PostgreSQL.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar a extensão do PHP para conectar com o PostgreSQL.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: Habilitar o módulo 'rewrite' do Apache, necessário para o .htaccess
RUN a2enmod rewrite

# PASSO 5: Copiar todos os arquivos do seu projeto para a pasta do servidor.
COPY . /var/www/html/

# PASSO 6: Ajustar as permissões da pasta para o usuário do servidor Apache.
RUN chown -R www-data:www-data /var/www/html

# A imagem base 'php:8.2-apache' já inicia o Apache automaticamente.
# Não precisamos de um CMD ou Start Command customizado.