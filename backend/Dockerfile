# ======== 共通ベースイメージ ========
FROM php:8.2-fpm-bullseye AS base

# 必要なライブラリのインストール
RUN apt-get update \
    && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    lsof \
    zip \
    unzip \
    git \
    procps \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pcntl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 作業ディレクトリの設定
WORKDIR /var/www/html

# PHP-FPM の設定ファイルをコンテナにコピー
COPY php-fpm.conf /usr/local/etc/php-fpm.conf
COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN mkdir -p /var/log && touch /var/log/php-fpm.log /var/log/php-fpm.slow.log /var/log/php-fpm.access.log \
    && chown www-data:www-data /var/log/php-fpm.log /var/log/php-fpm.slow.log /var/log/php-fpm.access.log

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 依存関係のキャッシュ最適化のため、composerファイルだけ先にコピー
COPY composer.json composer.lock ./

# 依存関係をインストール
RUN composer install --no-autoloader

# ソースコード全体のコピー
COPY . .

# オートローダーの最適化
RUN composer dump-autoload --optimize

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache


# ======== 開発環境用ビルド ========
FROM base AS dev

# Entrypointスクリプトをコピー
COPY dev_entrypoint.sh /usr/local/bin/dev_entrypoint.sh
RUN chmod +x /usr/local/bin/dev_entrypoint.sh

# 開発環境用のエントリーポイント設定
ENTRYPOINT ["/usr/local/bin/dev_entrypoint.sh"]

# ======== 本番環境用ビルド ========
FROM base AS prod

# 必要なライブラリのインストール（supervisorを含む）
RUN apt-get update \
    && apt-get install -y supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# .env.productionのパーミッションを変更
RUN chown root:root /var/www/html/.env.production

# 必要なディレクトリの作成と権限設定
RUN mkdir -p /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache \
    && touch /var/run/supervisor.sock \
    && chmod 660 /var/run/supervisor.sock \
    && chown root:root /var/run/supervisor.sock

# supervisordの設定をコピー
COPY supervisord.conf /etc/supervisord.conf

# Entrypointスクリプトをコピー
COPY prod_entrypoint.sh /usr/local/bin/prod_entrypoint.sh
RUN chmod +x /usr/local/bin/prod_entrypoint.sh

# 本番環境用のエントリーポイント設定
ENTRYPOINT ["/usr/local/bin/prod_entrypoint.sh"]
