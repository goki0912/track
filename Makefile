# Makefile

.PHONY: up down build rebuild logs

# エイリアスの定義
dco = docker compose

init:
	build up migrate seed

build:
	$(dco) build

up:
	$(dco) up -d

migrate:
	$(dco) exec app php artisan migrate

seed:
	$(dco) exec app php artisan db:seed

down:
	$(dco) down


# Docker Composeを使用してキャッシュを使わずに再ビルド
#rebuild:
#	$(dco) down --rmi all --volumes --remove-orphans
#	$(dco) build --no-cache
#	$(dco) up -d

# Docker Composeのログを表示
logs:
	$(dco) logs -f

cacheclear:
	$(dco) exec app php artisan optimize:clear

format:
	$(dco) exec vue npm run format