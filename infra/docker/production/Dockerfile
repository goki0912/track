# 軽そうだからalpineを使ってみる(理由はない)
FROM php:8.2-fpm-alpine

# 必要なパッケージをインストール
RUN apk add --no-cache \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libxml2-dev \
    libzip-dev \
    oniguruma-dev \
    zip \
    unzip \
    supervisor

# PHP拡張をインストール
RUN docker-php-ext-configure gd \
    --with-jpeg \
    --with-webp \
    --with-xpm \
 && docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip
# 公式のComposerイメージを使用
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# アプリケーションをコピー
COPY ./backend /var/www/html

# Composer install（開発用パッケージを除く）
RUN composer install --optimize-autoloader --no-dev

# Nginx設定ファイルをコピー
COPY ./infra/docker/production/nginx/default.conf /etc/nginx/conf.d/default.conf

# Supervisordを使用してNginxとPHP-FPMを管理
COPY ./infra/docker/production/supervisord.conf /etc/supervisord.conf

# NginxとPHP-FPMをSupervisordで起動
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

# ポートを公開
EXPOSE 8000
