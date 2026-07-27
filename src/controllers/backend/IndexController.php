<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Dashboard\controllers\backend;

use Besnovatyj\Dashboard\repositories\DashboardSettingsRepository;
use Besnovatyj\Dashboard\services\DashboardService;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * Управление раскладкой плиток главной панели админки.
 *
 * Показывает все доступные плитки установленных модулей с их текущим состоянием и сохраняет выбор
 * (включённость + порядок) в таблицу настроек. Каталог из {@see DashboardService::resolveAll()} —
 * единственный источник допустимых id: сохраняются только известные плитки, чужой ввод отбрасывается.
 */
class IndexController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly DashboardService $service,
        private readonly DashboardSettingsRepository $settings,
        array $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['save' => ['POST']],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index', [
            'rows' => $this->service->resolveAll(),
        ]);
    }

    /**
     * Сохранить раскладку. Итерируем по каталогу (whitelist), беря отправленное состояние по каждой
     * известной плитке; неотмеченный чекбокс → выключена, пустой порядок → текущий.
     */
    public function actionSave(): Response
    {
        $submitted = (array)Yii::$app->request->post('widgets', []);

        $layout = [];
        foreach ($this->service->resolveAll() as $item) {
            $id = $item->descriptor->id;
            $row = (array)($submitted[$id] ?? []);
            $layout[$id] = [
                'enabled' => !empty($row['enabled']),
                'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : $item->sortOrder,
            ];
        }

        $this->settings->save($layout);
        Yii::$app->session->setFlash('success', 'Раскладка панели сохранена.');

        return $this->redirect(['index']);
    }
}
