# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema necessárias para compilar a extensão.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar as extensões do PHP para o banco de dados.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: (A MUDANÇA CRÍTICA E DEFINITIVA)
# Ativar o php.ini de produção e injetar nossa extensão diretamente nele.
# 1. Copia o arquivo de configuração recomendado para produção para o local onde o PHP o lê.
# 2. Usa o comando 'echo' para adicionar a linha 'extension=pdo_pgsql' no final do php.ini.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && echo "extension=pdo_pgsql" >> "$PHP_INI_DIR/php.ini"

# PASSO 5: Copiar todos os arquivos do seu projeto para a pasta CORRETA do servidor.
COPY . /var/www/html/

# PASSO 6: Ajustar as permissões na pasta CORRETA.
RUN chown -R www-data:www-data /var/www/html