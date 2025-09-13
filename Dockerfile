# PASSO 1: Usar a imagem oficial do PHP com Apache.
FROM php:8.2-apache

# PASSO 2: Instalar as dependências de sistema necessárias.
RUN apt-get update && apt-get install -y \
    build-essential \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PASSO 3: Instalar as extensões PDO e PostgreSQL.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 4: (NOVA ETAPA CRÍTICA) Forçar a HABILITAÇÃO da extensão.
# Este comando garante que a linha "extension=pdo_pgsql.so" seja adicionada ao php.ini
RUN docker-php-ext-enable pdo_pgsql

# PASSO 5: (DEBUG) Listar todos os módulos PHP instalados e habilitados.
# Isso vai aparecer no log do deploy e nos dará a prova final.
RUN php -m

# PASSO 6: Habilitar o módulo 'rewrite' do Apache (boa prática).
RUN a2enmod rewrite

# PASSO 7: Copiar todos os arquivos do seu projeto.
COPY . /var/www/html/

# PASSO 8: Ajustar as permissões da pasta.
RUN chown -R www-data:www-data /var/www/html