# PASSO 1: Usar uma imagem oficial do PHP que já vem com o servidor Apache.
# Usaremos a versão 8.2 do PHP.
FROM php:8.2-apache

# PASSO 2: Instalar a extensão do PHP necessária para conectar com o banco de dados PostgreSQL (Neon).
# O comando 'docker-php-ext-install' é uma ferramenta que vem na imagem oficial para facilitar isso.
RUN docker-php-ext-install pdo pdo_pgsql

# PASSO 3: Copiar todos os arquivos do seu projeto (o '.' significa o diretório atual)
# para dentro da pasta raiz do servidor web na imagem (/var/www/html).
COPY . /var/www/html/

# PASSO 4: (Opcional, mas boa prática) Ajustar as permissões da pasta para o usuário do servidor Apache.
RUN chown -R www-data:www-data /var/www/html