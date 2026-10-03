<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Dashboard\Module;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Объявляется через `extra.config-plugin`, собирается modman в merge-plan и мёржится в рантайме.
 * Регистрирует модуль и его L2-bootstrap (registry-gated: попадает в конфиг только когда модуль
 * активен). Меню админки — `adminMenu.php` (группа `admin-menu`), миграции — вклад modman.
 * Значения — из статических методов {@see Module}, без дублирования.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    // L2-bootstrap: глобальная DI-проводка сервисов дашборда (нужна и вне маршрутов модуля —
    // плитки рендерятся виджетом прямо на главной админки). См. Bootstrap.
    'bootstrap' => array_values(Module::bootstrapClasses()),
];
