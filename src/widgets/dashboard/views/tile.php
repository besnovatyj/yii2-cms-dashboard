<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Тело одной плитки: рендер виджета плитки, предоставленного модулем.
 *
 * Рендер каждой плитки ИЗОЛИРОВАН: сбой одного модуля (напр. ошибка запроса-счётчика) не должен гасить
 * всю главную. Ошибка показывается в самой плитке и пишется в лог — без «тихого» проглатывания.
 *
 * @var yii\web\View $this
 * @var DashboardWidgetDescriptor $tile
 */

$class = $tile->tileClass;

if (!class_exists($class) || !is_subclass_of($class, Widget::class)) {
    echo Html::tag(
        'div',
        'Виджет плитки недоступен: ' . Html::encode($class),
        ['class' => 'text-danger small']
    );
    return;
}

try {
    /** @var class-string<Widget> $class */
    echo $class::widget();
} catch (\Throwable $e) {
    Yii::error(
        "Ошибка рендера плитки '{$tile->id}' ({$class}): {$e->getMessage()}",
        'dashboard'
    );
    echo Html::tag(
        'div',
        '<i class="bi bi-exclamation-triangle me-1"></i>Не удалось отрисовать плитку.',
        ['class' => 'text-danger small']
    );
}
