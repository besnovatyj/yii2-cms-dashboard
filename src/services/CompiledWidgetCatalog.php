<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\services;

use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;

/**
 * Каталог доступных плиток — читает компилируемый modman артефакт `dashboardWidgets.php`.
 *
 * Артефакт содержит плитки ТОЛЬКО активных модулей (registry-gated на этапе компиляции), поэтому здесь
 * нет ни сканирования пакетов, ни инстанцирования модулей — только чтение готового массива и обратная
 * сборка типизированных дескрипторов. Отсутствие файла (свежая установка до recompile) — не ошибка:
 * каталог просто пуст.
 */
final class CompiledWidgetCatalog
{
    public function __construct(private readonly string $artifactPath) {}

    /**
     * Все доступные дескрипторы плиток, ключ — id плитки.
     *
     * @return array<string, DashboardWidgetDescriptor>
     */
    public function all(): array
    {
        if (!is_file($this->artifactPath)) {
            return [];
        }

        /** @var mixed $raw */
        $raw = require $this->artifactPath;
        if (!is_array($raw)) {
            return [];
        }

        $descriptors = [];
        foreach ($raw as $data) {
            if (!is_array($data)) {
                continue;
            }
            $descriptor = DashboardWidgetDescriptor::fromArray($data);
            $descriptors[$descriptor->id] = $descriptor;
        }
        return $descriptors;
    }
}
