# Makefile

.PHONY: up down build rebuild logs

# エイリアスの定義
dco = docker compose

# Docker Composeを使用してコンテナを起動
up:
	$(dco) up -d

# Docker Composeを使用してコンテナを停止
down:
	$(dco) down

# Docker Composeを使用してイメージをビルド
build:
	$(dco) build

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