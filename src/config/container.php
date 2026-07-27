<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Dashboard\repositories\DashboardSettingsRepository;
use Besnovatyj\Dashboard\services\CompiledWidgetCatalog;
use Besnovatyj\Dashboard\services\DashboardService;
use yii\di\Container;

/**
 * DI-проводка модуля дашборда.
 *
 * Загружается ГЛОБАЛЬНО из {@see \Besnovatyj\Dashboard\Bootstrap} (registry-gated L2), а не только
 * способом A при инициализации модуля: {@see DashboardWidget} рендерится на главной админки, которая
 * не относится к маршрутам модуля, — сервисы должны быть доступны в контейнере в любом запросе.
 */
return function (Container $container): void {
    // Каталог доступных плиток — из компилируемого modman артефакта (без рантайм-сканирования модулей).
    $container->setSingleton(CompiledWidgetCatalog::class, static fn(): CompiledWidgetCatalog
        => new CompiledWidgetCatalog(Yii::getAlias('@config-dyn-gen/dashboardWidgets.php')));

    $container->setSingleton(DashboardSettingsRepository::class, DashboardSettingsRepository::class);

    $container->setSingleton(DashboardService::class, static fn(Container $c): DashboardService
        => new DashboardService(
            $c->get(CompiledWidgetCatalog::class),
            $c->get(DashboardSettingsRepository::class),
        ));
};
