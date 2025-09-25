# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema para o PostgreSQL.
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar as extensões do PHP para o banco de dados.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: Copiar o arquivo de configuração manual para ativar a extensão.
COPY custom-php.ini /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini

# PASSO 5: Habilitar o módulo 'rewrite' do Apache (boa prática).
RUN a2enmod rewrite

# PASSO 6: (CORRIGIDO) Copiar todos os arquivos para a pasta CORRETA do servidor.
COPY . /var/www/html/

# PASSO 7: (CORRIGIDO) Ajustar as permissões na pasta CORRETA.
RUN chown -R www-data:www-data /var/www/html