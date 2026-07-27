<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard;

use Yii;
use yii\base\BootstrapInterface;

/**
 * Глобальный bootstrap дашборда (L2, гейт modman).
 *
 * Регистрирует DI-проводку сервисов дашборда в общий контейнер — чтобы {@see widgets\dashboard\DashboardWidget}
 * мог получить {@see services\DashboardService} при рендере на главной админки, которая не входит в
 * маршруты модуля (иначе способ A из {@see \Besnovatyj\Kernel\module\CmsModule} не сработал бы).
 */
final class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        (require __DIR__ . '/config/container.php')(Yii::$container);
    }
}
