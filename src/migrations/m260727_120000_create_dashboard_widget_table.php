<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;

/**
 * Таблица раскладки плиток главной панели админки.
 *
 * Хранит пользовательский выбор поверх дескрипторов из артефакта: включённость и порядок каждой плитки.
 * Строки с `user_id = 0` — глобальная раскладка (текущий режим). Ненулевой `user_id` зарезервирован под
 * персональные раскладки без переделки схемы (задел): резолвер дашборда отдаст персональную строку с
 * откатом на глобальную. NOT NULL с сентинелом 0 (вместо NULL) — чтобы UNIQUE(widget_id, user_id)
 * реально запрещал дубли глобальных строк (в MySQL NULL-значения в UNIQUE не считаются равными).
 *
 * 'm<YYMMDD_HHMMSS>_<Name>'
 */
class m260727_120000_create_dashboard_widget_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%dashboard_widget}}';

    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'widget_id' => $this->string(191)->notNull()
                ->comment('Идентификатор плитки (id дескриптора из артефакта dashboardWidgets)'),
            'user_id' => $this->integer(10)->unsigned()->notNull()->defaultValue(0)
                ->comment('0 — глобальная раскладка; >0 — персональная (задел под per-user)'),
            'enabled' => $this->smallInteger(1)->notNull()->defaultValue(1)
                ->comment('Показывать плитку на главной'),
            'sort_order' => $this->integer(10)->notNull()->defaultValue(0)
                ->comment('Порядок плитки (меньше — выше/левее)'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Раскладка плиток главной панели админки');

        // Одна строка на (плитку × раскладку): исключает дубли настройки.
        $this->createIndexes(static::TABLE_NAME, ['widget_id', 'user_id'], isPK: false, unique: true);
    }
}
