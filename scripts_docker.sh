#!/usr/bin/env bash

set -euo pipefail

COMPOSE="docker compose"

run_cmd() {
  echo "+ $*"
  "$@"
}

up() {
  run_cmd docker compose up -d
}

down() {
  run_cmd docker compose down
}

composer_install() {
  run_cmd docker compose exec app composer install
}

shell_app() {
  run_cmd docker compose exec app sh
}

first_run() {
  up
  composer_install
  npm_build
  migrate_seed
  storage_link
}

npm_build() {
  run_cmd docker compose exec app npm install
  run_cmd docker compose exec app npm run build
}

migrate_seed() {
  run_cmd docker compose exec app php artisan migrate --seed
}

storage_link() {
  run_cmd docker compose exec app php artisan storage:link
}

artisan() {
  run_cmd docker compose exec app php artisan "$@"
}

case "${1:-}" in
  up)
    up
    ;;
  down)
    down
    ;;
  composer-install)
    composer_install
    ;;
  first-run)
    first_run
    ;;
  shell)
    shell_app
    ;;
  npm-build)
    npm_build
    ;;
  migrate-seed)
    migrate_seed
    ;;
  storage-link)
    storage_link
    ;;
  artisan)
    shift
    artisan "$@"
    ;;
  *)
    cat <<'EOF'
Uso: ./scripts_docker.sh <comando>

Comandos disponiveis:
  up                Sobe os containers em background
                    Terminal: docker compose up -d
  down              Derruba os containers
                    Terminal: docker compose down
  composer-install  Instala dependencias do Composer no container app
                    Terminal: docker compose exec app composer install
  first-run         Executa a subida inicial completa do projeto
                    Terminal: docker compose up -d
                              docker compose exec app composer install
                              docker compose exec app npm install
                              docker compose exec app npm run build
                              docker compose exec app php artisan migrate --seed
                              docker compose exec app php artisan storage:link
  shell             Abre um terminal dentro do container app
                    Terminal: docker compose exec app sh
  npm-build         Instala dependencias frontend e gera assets
                    Terminal: docker compose exec app npm install
                              docker compose exec app npm run build
  migrate-seed      Executa php artisan migrate --seed
                    Terminal: docker compose exec app php artisan migrate --seed
  storage-link      Executa php artisan storage:link
                    Terminal: docker compose exec app php artisan storage:link
  artisan ...       Executa qualquer comando artisan
                    Terminal: docker compose exec app php artisan <comando>
EOF
    exit 1
    ;;
esac
