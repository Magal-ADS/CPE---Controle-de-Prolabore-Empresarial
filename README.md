# CPE - Controle de Prolabore Empresarial

Portal financeiro em Laravel para transparencia total entre socios. O sistema centraliza entradas, saidas, comprovantes, usuarios com acesso ao portal e atualizacao de perfil do usuario autenticado.

## Padrao de execucao

Este projeto deve ser operado em modo Docker-only.

- Rode `composer`, `npm` e `php artisan` sempre dentro do container `app`
- Nao rode `php artisan ...` direto no PowerShell/host
- O host do banco no Laravel e `db`, que so existe dentro da rede do Docker Compose

## Stack

- Laravel 13
- Blade Templates
- Tailwind CSS 4
- PostgreSQL 17
- Docker + Docker Compose
- Nginx + PHP-FPM

## Funcionalidades base

- Dashboard com cards de saldo, entradas e saidas
- Grafico de barras para comparativo financeiro por periodo
- CRUD de movimentacoes com upload de comprovantes
- Gestao de usuarios com cadastro, edicao e inativacao
- Edicao do perfil do usuario logado
- Tema claro e escuro com persistencia local

## Como subir a infraestrutura

1. Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

2. Suba os containers:

```bash
docker compose up -d --build
```

3. Instale dependencias do backend dentro do container:

```bash
docker compose exec app composer install
```

4. Gere os assets do frontend:

```bash
docker compose exec app npm install
docker compose exec app npm run build
```

5. Execute migrations e seeds:

```bash
docker compose exec app php artisan migrate --seed
```

6. Crie o link publico do storage:

```bash
docker compose exec app php artisan storage:link
```

7. Acesse a aplicacao:

```text
http://localhost:8080
```

Se voce alterar o mapeamento de porta no `docker-compose.yml`, acesse pela nova porta publicada.

## Deploy no Dokploy

Para publicar esse projeto via Dokploy usando o `docker-compose.yml` da raiz:

1. Use o servico `nginx` como servico web publicado no dominio.
2. No dominio, aponte para a porta interna `80` do `nginx`, nao para o servico `app`.
3. Mantenha o `app` apenas como PHP-FPM interno na rede Docker.
4. Defina no ambiente de producao:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cpe.weagles.com.br
```

Se o dominio estiver ligado ao servico `app` em vez do `nginx`, o Dokploy responde com `404 page not found` antes de chegar no Laravel.

Em producao no Dokploy, nao use bind mounts como `./:/var/www/html`. Como `vendor/` nao fica versionado no Git, esse mount sobrescreve o codigo construido na imagem e faz o Artisan falhar com erro de `vendor/autoload.php` ausente.

## Comandos obrigatoriamente no container

Use sempre este formato:

```bash
docker compose exec app composer install
docker compose exec app npm install
docker compose exec app npm run build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
docker compose exec app php artisan route:list
```

Para abrir um shell no container:

```bash
docker compose exec app sh
```

## Usando o scripts_docker.sh

O projeto inclui um atalho de automacao na raiz.

```bash
chmod +x scripts_docker.sh
```

Comandos principais:

```bash
./scripts_docker.sh up
./scripts_docker.sh composer-install
./scripts_docker.sh first-run
./scripts_docker.sh shell
./scripts_docker.sh npm-build
./scripts_docker.sh migrate-seed
./scripts_docker.sh storage-link
./scripts_docker.sh down
```

Para a primeira subida completa do ambiente, use:

```bash
./scripts_docker.sh first-run
```

Tambem e possivel executar qualquer comando Artisan:

```bash
./scripts_docker.sh artisan route:list
```

## Credenciais padrao

- E-mail: `admin@wtech.com`
- Senha: `123`

## Estrutura principal

- `app/Http/Controllers`: controllers do dashboard, autenticacao, usuarios, perfil e movimentacoes
- `app/Models`: modelos `User` e `Transaction`
- `database/migrations`: estrutura das tabelas `users` e `transactions`
- `database/seeders/DatabaseSeeder.php`: usuario administrador inicial
- `resources/views`: telas Blade mobile-first com dark mode
- `docker-compose.yml`: servicos `app`, `nginx` e `db`
- `Dockerfile`: imagem PHP-FPM com Composer, Node e extensoes necessarias

## Observacoes

- Os comprovantes sao armazenados no disco `public`.
- Todos os usuarios autenticados possuem visao integral das movimentacoes.
- A inativacao de usuario bloqueia novos logins, sem apagar historico.
- Se o sistema apresentar erro de conexao com banco dentro do Docker, confirme que o `.env` usa `DB_HOST=db`.
