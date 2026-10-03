<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard;

use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesBootstrap;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Kernel\module\CmsModule;

/**
 * Модуль панели главной страницы админки.
 *
 * Собирает плитки-виджеты установленных модулей (артефакт `dashboardWidgets`, компилируемый modman),
 * даёт админке UI для их включения/выключения и упорядочивания и рендерит выбранные плитки на главной
 * через {@see \Besnovatyj\Dashboard\widgets\dashboard\DashboardWidget}.
 *
 * Раскладка (включённость/порядок) хранится в собственной таблице модуля `dashboard_widget`.
 */
class Module extends CmsModule implements
    DeclaresModule, 
    ProvidesBootstrap, ProvidesMigrations
{
    public const bool EDITABLE = true;
    public const string VERSION = '1.0.0';
    public const string MODULE_ID = 'Dashboard';

    public static function moduleId(): string { return self::MODULE_ID; }
    public static function moduleVersion(): string { return self::VERSION; }
    public static function isEditable(): bool { return self::EDITABLE; }
    public static function moduleConfig(): array { return require __DIR__ . '/config/config.php'; }
    public static function bootstrapClasses(): array { return [Bootstrap::class]; }
    public static function migrationPath(): string { return __DIR__ . '/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__ . '\\migrations'; }
}
