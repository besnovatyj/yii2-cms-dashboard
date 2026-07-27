# Dashboard — панель главной страницы админки

Собирает плитки-виджеты установленных модулей, даёт админке UI для их включения/выключения и
упорядочивания и рендерит выбранные плитки на главной.

## Как модуль поставляет плитку

1. Класс модуля реализует контракт `Besnovatyj\Contracts\dashboard\ProvidesDashboardWidgets` и
   возвращает массив `DashboardWidgetDescriptor` (чистые метаданные + FQCN виджета-плитки).
2. Виджет-плитка — обычный `yii\base\Widget` (или `yii\bootstrap5\Widget`), рендерящий ТОЛЬКО тело
   карточки (число, кнопку и т.п.). Общий «каркас» карточки рисует дашборд.
3. modman при установке модуля компилирует плитки активных модулей в артефакт
   `@config-dyn-gen/dashboardWidgets.php` (registry-gated, без рантайм-сканирования).

```php
public static function dashboardWidgets(): array
{
    return [
        new DashboardWidgetDescriptor(
            id: 'Catalog.productsCount',
            title: 'Товары',
            tileClass: \Vendor\Module\widgets\dashboard\ProductsCountTile::class,
            iconClass: 'bi bi-box-seam',
            priority: 200,
        ),
    ];
}
```

## Архитектура

- **Каталог** (`CompiledWidgetCatalog`) — читает артефакт (что доступно).
- **Настройки** (`DashboardSettingsRepository` + таблица `dashboard_widget`) — что включено и в каком
  порядке. `user_id = 0` — глобальная раскладка; ненулевой зарезервирован под персональные.
- **Сведение** (`DashboardService`) — каталог × настройки → упорядоченные плитки; настройка
  перекрывает дефолты дескриптора, новая плитка появляется сама по `enabledByDefault`/`priority`.
- **Рендер** (`DashboardWidget`) — ставится в `backend/views/site/index.php`.

Рендер каждой плитки изолирован: сбой одного модуля не гасит всю главную.
