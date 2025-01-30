# ベースイメージ
FROM php:8.2-fpm

# 必要なライブラリのインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    lsof \
    zip \
    unzip \
    git \
    procps \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pcntl

# 作業ディレクトリの設定
WORKDIR /var/www/html

# PHP-FPM の設定ファイルをコンテナにコピー
COPY php-fpm.conf /usr/local/etc/php-fpm.conf
COPY php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf
RUN mkdir -p /var/log && touch /var/log/php-fpm.log /var/log/php-fpm.slow.log /var/log/php-fpm.access.log \
    && chown www-data:www-data /var/log/php-fpm.log /var/log/php-fpm.slow.log /var/log/php-fpm.access.log

# コンポーザーのインストール
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

# Entrypointスクリプトのコピーと実行権限の付与
#COPY entrypoint.sh /usr/local/bin/entrypoint.sh
#RUN chmod +x /usr/local/bin/entrypoint.sh
RUN chown root:root /var/www/html/.env.production


COPY supervisord.conf /etc/supervisord.conf
#CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


# Entrypointスクリプトをエントリーポイントに設定
#ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Entrypointスクリプトをコピー
COPY supervisord_init.sh /usr/local/bin/supervisord_init.sh
RUN chmod +x /usr/local/bin/supervisord_init.sh

# Entrypointを設定
ENTRYPOINT ["/usr/local/bin/supervisord_init.sh"]

