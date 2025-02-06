#!/bin/bash

# エラーハンドリング: スクリプトが失敗した場合に即座に終了
set -e

# デバッグ用: スクリプトの実行内容をログに出力（必要に応じて有効化）
# set -x

echo "Starting entrypoint script..."

# Laravelのキャッシュディレクトリを準備
echo "Preparing Laravel cache directories..."
php artisan config:clear || echo "Warning: Failed to clear config cache (might not be required)."
php artisan config:cache || echo "Warning: Failed to cache config."
php artisan route:cache || echo "Warning: Failed to cache routes."
php artisan view:cache || echo "Warning: Failed to cache views."

# Supervisorの起動
echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisord.conf