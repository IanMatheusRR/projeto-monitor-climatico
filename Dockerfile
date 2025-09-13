# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar TODAS as dependências de sistema necessárias.
# 'build-essential' inclui as ferramentas de compilação (como 'make', 'gcc').
# 'libpq-dev' é a biblioteca de desenvolvimento do PostgreSQL.
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: AGORA, com as ferramentas instaladas, este comando vai funcionar.
# Instalar a extensão PDO e a extensão PDO para PostgreSQL.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: Habilitar o módulo 'rewrite' do Apache (boa prática).
RUN a2enmod rewrite

# PASSO 5: Copiar todos os arquivos do seu projeto.
COPY . /var/www/html/

# PASSO 6: Ajustar as permissões da pasta.
RUN chown -R www-data:www-data /var/www/html