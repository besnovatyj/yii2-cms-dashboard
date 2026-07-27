<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

return [[
    'label' => 'Панель админки',
    'iconClass' => 'bi bi-grid-1x2-fill me-1',
    'url' => ['/Dashboard/backend/index/index'],
    'active' => static function (): bool {
        return str_contains(\Yii::$app->request->url, 'Dashboard/backend');
    },
    '_meta' => [
        'placements' => [
            [
                'location' => 'right-sidebar',
                'group' => 'Service',
                'groupIcon' => 'bi bi-sliders',
                'priority' => 110,
                'groupPriority' => 100,
            ],
        ],
    ],
]];
