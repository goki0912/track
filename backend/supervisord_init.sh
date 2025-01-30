#!/bin/bash

# エラーハンドリング: スクリプトが失敗した場合に即座に終了
set -e

# デバッグ用: スクリプトの実行内容をログに出力（必要に応じて有効化）
# set -x

echo "Starting entrypoint script..."

# 必要なディレクトリの作成と権限設定
echo "Setting up directories and permissions..."
mkdir -p /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/run/php /var/log/php /var/run/supervisor /var/www/html/storage /var/www/html/bootstrap/cache
touch /var/run/supervisor.sock
chmod 777 /var/run/supervisor.sock
chown root:root /var/run/supervisor


# 環境変数の確認とログ出力
echo "Environment variables:"
env | grep -E 'APP_ENV|DB_HOST|DB_DATABASE|DB_USERNAME|DB_PASSWORD|REVERB_' || echo "No relevant environment variables found."

# Laravelのキャッシュディレクトリを準備
echo "Preparing Laravel cache directories..."
php artisan config:clear || echo "Failed to clear config cache (might not be required)."
php artisan config:cache || echo "Failed to cache config."
php artisan route:cache || echo "Failed to cache routes."
php artisan view:cache || echo "Failed to cache views."

# Supervisorの設定ファイルチェック
#echo "Validating Supervisor configuration..."
#if ! /usr/bin/supervisord -n --test; then
#  echo "Supervisor configuration test failed. Exiting."
#  exit 1
#fi

# Supervisorの起動
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf

