<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\services;

use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;

/**
 * Дескриптор плитки с применённой раскладкой (включённость и порядок из настроек либо из дефолтов).
 *
 * Промежуточная модель между {@see DashboardService} и представлениями: несёт и статические метаданные
 * плитки, и разрешённое состояние. Используется и на главной (фильтр по {@see $enabled}), и в UI
 * управления (полный список с текущими значениями).
 */
final readonly class ResolvedWidget
{
    public function __construct(
        public DashboardWidgetDescriptor $descriptor,
        public bool $enabled,
        public int $sortOrder,
    ) {}
}
