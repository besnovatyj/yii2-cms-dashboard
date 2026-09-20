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

/*
 * Выравнивание «подвала» плитки — ответственность каркаса, а не модуля-поставщика.
 *
 * У плиток разный объём: где-то одно число, где-то два счётчика и пара предупреждений. Без этого
 * правила кнопки и ссылки всплывают сразу за текстом и в одном ряду стоят на разной высоте.
 * Правило прижимает ПОСЛЕДНИЙ элемент тела ко дну карточки — им во всех плитках идёт блок
 * действий. Плитка, завёрнутая в один контейнер (ей нужен корневой узел для своего JS), тоже
 * учтена: единственный потомок сам становится колонкой и растягивается, и правило применяется
 * уже внутри него.
 *
 * `!important` здесь вынужденный: утилиты отступов Bootstrap (`mt-3`, которым плитки отделяют свой
 * блок действий) объявлены с `!important`, и без него `margin-top: auto` до элемента не доходит.
 * Селекторы при этом специфичнее утилиты, так что переопределение однозначное.
 *
 * Стиль инлайновый, без ассет-бандла: модуль не тянет ассетов вообще, а три правила не стоят
 * отдельного пакета и подключения в раскладке админки.
 */
$this->registerCss(<<<CSS
.dashboard-tile > :only-child { display: flex; flex-direction: column; flex: 1 1 auto; }
.dashboard-tile > :last-child,
.dashboard-tile > :only-child > :last-child { margin-top: auto !important; }
CSS);
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
                    <div class="card-body dashboard-tile d-flex flex-column">
                        <?= $this->render('tile', ['tile' => $tile]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
