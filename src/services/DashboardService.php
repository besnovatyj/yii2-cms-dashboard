<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\services;

use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;
use Besnovatyj\Dashboard\entities\DashboardWidgetSetting;
use Besnovatyj\Dashboard\repositories\DashboardSettingsRepository;
use Yii;

/**
 * Сведение каталога доступных плиток с сохранённой раскладкой.
 *
 * Единственное место, где дескрипторы из артефакта соединяются с пользовательскими настройками:
 *  - {@see resolveAll()} — полный упорядоченный список (для UI управления);
 *  - {@see visibleTiles()} — только включённые и доступные по правам (для главной админки).
 *
 * Правило разрешения: настройка перекрывает дефолт дескриптора; нет настройки — берём
 * `enabledByDefault`/`priority`. Так новая плитка только что установленного модуля появляется сама,
 * согласно своим дефолтам, ещё до первого захода в настройки.
 */
final class DashboardService
{
    public function __construct(
        private readonly CompiledWidgetCatalog $catalog,
        private readonly DashboardSettingsRepository $settings,
    ) {}

    /**
     * Все доступные плитки с применённой раскладкой, отсортированные (порядок, затем заголовок).
     *
     * @return ResolvedWidget[]
     */
    public function resolveAll(int $userId = DashboardWidgetSetting::GLOBAL_USER): array
    {
        $map = $this->settings->loadMap($userId);

        $resolved = [];
        foreach ($this->catalog->all() as $id => $descriptor) {
            $setting = $map[$id] ?? null;
            $resolved[] = new ResolvedWidget(
                descriptor: $descriptor,
                enabled: $setting['enabled'] ?? $descriptor->enabledByDefault,
                sortOrder: $setting['sort_order'] ?? $descriptor->priority,
            );
        }

        usort($resolved, static fn(ResolvedWidget $a, ResolvedWidget $b): int
            => $a->sortOrder !== $b->sortOrder
                ? $a->sortOrder <=> $b->sortOrder
                : strcmp($a->descriptor->title, $b->descriptor->title));

        return $resolved;
    }

    /**
     * Плитки для показа на главной: включённые и разрешённые текущему пользователю.
     *
     * @return DashboardWidgetDescriptor[]
     */
    public function visibleTiles(): array
    {
        $tiles = [];
        foreach ($this->resolveAll() as $item) {
            if ($item->enabled && $this->isPermitted($item->descriptor)) {
                $tiles[] = $item->descriptor;
            }
        }
        return $tiles;
    }

    /**
     * Доступ к плитке по её RBAC-разрешению (если объявлено). Fail-closed: при заявленном разрешении
     * без компонента `user` плитка скрывается.
     */
    private function isPermitted(DashboardWidgetDescriptor $descriptor): bool
    {
        if ($descriptor->permission === null || $descriptor->permission === '') {
            return true;
        }
        return Yii::$app->has('user') && Yii::$app->user->can($descriptor->permission);
    }
}
