<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('theme.{themeId}', function ($user, $themeId) {
    // 認可ロジックを追加します。すべてのユーザーに許可する場合はtrueを返す
    return true;
});
