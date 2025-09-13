# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: ATUALIZAÇÃO IMPORTANTE
# Instalar as dependências de sistema necessárias para compilar a extensão do PostgreSQL.
# 'apt-get update' atualiza a lista de pacotes.
# 'apt-get install -y' instala os pacotes sem pedir confirmação.
# 'libpq-dev' é o pacote que contém o arquivo 'libpq-fe.h' que estava faltando.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Agora que a dependência está instalada, este comando vai funcionar.
# Instalar a extensão do PHP para conectar com o banco de dados PostgreSQL (Neon).
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: Copiar todos os arquivos do seu projeto para a pasta do servidor.
COPY . /var/www/html/

# PASSO 5: Ajustar as permissões da pasta para o usuário do servidor Apache.
RUN chown -R www-data:www-data /var/www/html