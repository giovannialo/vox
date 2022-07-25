# Vox

Integração com serviços da redesim.

## Dependências

* Repositório [Nginx Proxy + Let's Encrypt](https://github.com/giovannialo/nginx-proxy-letsencrypt).

#### Observação

É necessário realizar os procedimentos de instalação do repositório acima antes de iniciar o processo de instalação abaixo.

## Instalação

Siga as etapas abaixo para um correto funcionamento do sistema.

### Container: Variáveis de ambiente

Na raiz do projeto crie um arquivo com o nome **.env**, copie e cole o bloco de código abaixo e configure as variáveis.

```dotenv
# ### ### ### ### ### ### ### ### ### ###
# PHP
# ### ### ### ### ### ### ### ### ### ###

# Não altere esta linha
API_PHP_INI_DIR=/usr/local/etc/php

# Configuração (development ou production)
API_PHP_INI=development

# Versão recomendada >= 8.1.8
API_PHP_VERSION=8.1.8

# ### ### ### ### ### ### ### ### ### ###
# Servidor
# ### ### ### ### ### ### ### ### ### ###

# Container
API_TIMEZONE=America/Maceio

# Nginx
API_VIRTUAL_HOST=vox.meudominio.com.br
API_VIRTUAL_PORT=80

# Certificação SSL
API_LETSENCRYPT_HOST=vox.meudominio.com.br
API_LETSENCRYPT_EMAIL=email@exemplo.com
```

### Api: Variáveis de ambiente

Dentro da pasta **api** crie um arquivo com o nome **.env**, copie e cole o bloco de código abaixo e configure as variáveis.

```dotenv
#
# Domínio
#
HOST=vox.meudominio.com.br

#
# Banco de dados
# Para utilizar mais de um banco de dados, separe os valores com dois ponto-vírgula (;;).
# Todas as variáveis, exceto DATABASE_OPTIONS, são de preenchimento obrigatório.
#
DATABASE_KEY=default
DATABASE_DRIVER=mysql
DATABASE_HOST=localhost
DATABASE_PORT=3306
DATABASE_DBNAME=example
DATABASE_USERNAME=user
DATABASE_PASSWORD=user
DATABASE_OPTIONS=1002=SET NAMES utf8mb4&3=2&19=5&8=0

#
# Token cliente-servidor para web services
# Para utilizar mais de um token, separe os valores com dois ponto-vírgula (;;).
#
# Token do cliente: é utilizado para validar a comunicação recebida dos web services.
# Token do servidor: é utilizado para validar a comunicação enviada para os web services.
#
WEB_SERVICE_TOKEN_CLIENT=secret
WEB_SERVICE_TOKEN_SERVER=secret
```

### Container

Execute o comando abaixo para criar e iniciar o container.

```docker
docker-compose up -d
```

## Credits

* [Giovanni Alves de Lima Oliveira](https://github.com/giovannialo) (Developer)
