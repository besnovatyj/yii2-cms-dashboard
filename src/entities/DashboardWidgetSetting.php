<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\entities;

use yii\db\ActiveRecord;

/**
 * Настройка одной плитки в раскладке дашборда (включённость и порядок).
 *
 * @property int    $id
 * @property string $widget_id  id дескриптора плитки из артефакта
 * @property int    $user_id    0 — глобальная раскладка; >0 — персональная (задел под per-user)
 * @property int    $enabled    1|0 — показывать плитку
 * @property int    $sort_order порядок (меньше — выше/левее)
 */
class DashboardWidgetSetting extends ActiveRecord
{
    /** Сентинел «глобальной» раскладки (не привязанной к пользователю). */
    public const int GLOBAL_USER = 0;

    public static function tableName(): string
    {
        return '{{%dashboard_widget}}';
    }
}
