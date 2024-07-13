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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# 作業ディレクトリの設定
WORKDIR /var/www/html

# コンポーザーのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ソースコードのコピーと依存関係のインストール
COPY . .
RUN composer install

# パーミッションの設定
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Entrypointスクリプトのコピーと実行権限の付与
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Entrypointスクリプトをエントリポイントに設定
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
