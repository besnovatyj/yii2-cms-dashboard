<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;
use yii\helpers\Html;

/**
 * Каркас панели: сетка карточек-плиток. Тело каждой плитки рендерит вложенное представление `tile`.
 *
 * @var yii\web\View $this
 * @var DashboardWidgetDescriptor[] $tiles включённые и доступные плитки в порядке показа
 */

// Ширина плитки в сетке Bootstrap по подсказке размера дескриптора.
$sizeMap = [
    'sm' => 'col-12 col-sm-6 col-xl-3',
    'md' => 'col-12 col-md-6 col-xl-4',
    'lg' => 'col-12 col-xl-6',
];
?>
<?php if ($tiles === []): ?>
    <div class="alert alert-light border">
        Плитки панели не настроены. Включите нужные в разделе
        <i class="bi bi-grid-1x2-fill"></i> «Панель админки».
    </div>
<?php else: ?>
    <div class="row g-3 dashboard-tiles">
        <?php foreach ($tiles as $tile): ?>
            <div class="<?= $sizeMap[$tile->size] ?? $sizeMap['md'] ?>">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex align-items-center bg-white">
                        <i class="<?= Html::encode($tile->iconClass) ?> me-2"></i>
                        <span class="fw-semibold"><?= Html::encode($tile->title) ?></span>
                    </div>
                    <div class="card-body">
                        <?= $this->render('tile', ['tile' => $tile]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
