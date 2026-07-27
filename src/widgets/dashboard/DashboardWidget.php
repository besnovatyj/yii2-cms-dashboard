<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\widgets\dashboard;

use Besnovatyj\Dashboard\services\DashboardService;
use Yii;
use yii\bootstrap5\Widget;

/**
 * Панель плиток на главной странице админки.
 *
 * Ставится в представление главной (`backend/views/site/index.php`) как `DashboardWidget::widget()`.
 * Тянет включённые плитки из {@see DashboardService} (каталог из артефакта × сохранённая раскладка) и
 * рисует общий «каркас» карточек, вкладывая внутрь тело каждой плитки — её собственный виджет.
 */
class DashboardWidget extends Widget
{
    public function run(): string
    {
        /** @var DashboardService $service */
        $service = Yii::$container->get(DashboardService::class);

        return $this->render('index', [
            'tiles' => $service->visibleTiles(),
        ]);
    }
}
