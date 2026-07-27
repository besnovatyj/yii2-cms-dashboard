<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Dashboard\services\ResolvedWidget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Управление раскладкой плиток. Работает без JS: чекбокс включённости + числовой порядок на строку.
 * Drag-and-drop можно добавить поверх своими ассетами (порядок хранится в поле sort_order).
 *
 * @var yii\web\View $this
 * @var ResolvedWidget[] $rows все доступные плитки с текущим состоянием
 */

$this->title = 'Панель админки — плитки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dashboard-manage">
    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">
        Включите нужные плитки и задайте порядок (меньше — выше и левее). Плитки предоставляются
        установленными модулями; набор обновляется при установке/удалении модулей.
    </p>

    <?php if ($rows === []): ?>
        <div class="alert alert-info">Установленные модули пока не предоставили плиток для панели.</div>
    <?php else: ?>
        <?= Html::beginForm(Url::to(['save']), 'post') ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th style="width:1%">Вкл.</th>
                    <th>Плитка</th>
                    <th>Идентификатор</th>
                    <th style="width:9rem">Порядок</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $item): $d = $item->descriptor; ?>
                    <tr>
                        <td>
                            <div class="form-check form-switch mb-0">
                                <?= Html::checkbox("widgets[{$d->id}][enabled]", $item->enabled, [
                                    'class' => 'form-check-input',
                                    'value' => 1,
                                ]) ?>
                            </div>
                        </td>
                        <td>
                            <i class="<?= Html::encode($d->iconClass) ?> me-2"></i>
                            <?= Html::encode($d->title) ?>
                        </td>
                        <td><code class="small"><?= Html::encode($d->id) ?></code></td>
                        <td>
                            <?= Html::input('number', "widgets[{$d->id}][sort_order]", $item->sortOrder, [
                                'class' => 'form-control form-control-sm',
                                'step' => 1,
                            ]) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?= Html::submitButton('<i class="bi bi-save me-1"></i>Сохранить раскладку', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>
</div>
