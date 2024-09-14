<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unique(['track_id', 'theme_id'], 'track_theme_unique');
            $table->unique(['user_id', 'theme_id'], 'user_theme_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // 外部キー制約を削除
            $table->dropForeign('posts_track_id_foreign');
            $table->dropForeign('posts_theme_id_foreign');
            $table->dropForeign('posts_user_id_foreign');

            // ユニークインデックスを削除
            $table->dropUnique('track_theme_unique');
            $table->dropUnique('user_theme_unique');
        });
    }
};
