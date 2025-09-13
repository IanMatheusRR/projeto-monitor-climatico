# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema para o PostgreSQL.
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar a extensão do PHP para conectar com o PostgreSQL.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: (NOVA ETAPA) Copiar nosso arquivo de configuração customizado do Apache.
# Isso garante que requisições diretas para arquivos .php funcionem.
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# PASSO 5: Copiar todos os arquivos do seu projeto para a pasta do servidor.
COPY . /var/www/html/

# PASSO 6: Ajustar as permissões da pasta para o usuário do servidor Apache.
RUN chown -R www-data:www-data /var/www/html