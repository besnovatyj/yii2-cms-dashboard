<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\repositories;

use Besnovatyj\Dashboard\entities\DashboardWidgetSetting;
use RuntimeException;
use Throwable;

/**
 * Доступ к сохранённой раскладке плиток дашборда.
 *
 * Работает с одной раскладкой за раз (по `user_id`; сейчас всегда {@see DashboardWidgetSetting::GLOBAL_USER}).
 * Каталог доступных плиток здесь НЕ участвует — это лишь пользовательские оверрайды поверх дескрипторов.
 */
final class DashboardSettingsRepository
{
    /**
     * Карта сохранённых настроек: widget_id => {enabled, sort_order}.
     *
     * @return array<string, array{enabled: bool, sort_order: int}>
     */
    public function loadMap(int $userId = DashboardWidgetSetting::GLOBAL_USER): array
    {
        $rows = DashboardWidgetSetting::find()
            ->where(['user_id' => $userId])
            ->asArray()
            ->all();

        $map = [];
        foreach ($rows as $row) {
            $map[(string)$row['widget_id']] = [
                'enabled' => (bool)$row['enabled'],
                'sort_order' => (int)$row['sort_order'],
            ];
        }
        return $map;
    }

    /**
     * Сохранить раскладку целиком (upsert по каждой плитке в одной транзакции).
     *
     * Ключи `$layout` уже провалидированы вызывающим по каталогу (сохраняются только известные плитки).
     *
     * @param array<string, array{enabled: bool, sort_order: int}> $layout widget_id => состояние
     * @throws Throwable
     */
    public function save(array $layout, int $userId = DashboardWidgetSetting::GLOBAL_USER): void
    {
        $db = DashboardWidgetSetting::getDb();
        $tx = $db->beginTransaction();
        try {
            foreach ($layout as $widgetId => $state) {
                $model = DashboardWidgetSetting::findOne(['widget_id' => (string)$widgetId, 'user_id' => $userId])
                    ?? new DashboardWidgetSetting(['widget_id' => (string)$widgetId, 'user_id' => $userId]);
                $model->enabled = $state['enabled'] ? 1 : 0;
                $model->sort_order = $state['sort_order'];
                if (!$model->save()) {
                    throw new RuntimeException("Не удалось сохранить настройку плитки '{$widgetId}': "
                        . implode('; ', $model->getFirstErrors()));
                }
            }
            $tx->commit();
        } catch (Throwable $e) {
            $tx->rollBack();
            throw $e;
        }
    }
}
