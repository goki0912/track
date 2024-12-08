<?php

use Illuminate\Support\Facades\Broadcast;

// 普通のChannelならrouteを書く必要ない?

//Broadcast::channel('theme.{themeId}', static function ($user, $themeId) {
//    // 認可ロジックを追加します。すべてのユーザーに許可する場合はtrueを返す
//    return true;
//});
