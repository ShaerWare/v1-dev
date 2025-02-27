<?php

declare(strict_types=1);

namespace App\Orchid;

use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;
use Orchid\Platform\OrchidServiceProvider;
use Orchid\Screen\Actions\Menu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Dashboard $dashboard): void
    {
        parent::boot($dashboard);

        // ...
    }

    /**
     * Register the application menu.
     *
     * @return Menu[]
     */
    public function menu(): array
    {
        return [
            Menu::make('Заставки')
                ->icon('image')
                ->title('Некий контент')
                ->route('platform.auth-banners'),

            Menu::make('Области и регионы')
                ->icon('globe') // Выбираем подходящий значок
                ->route('platform.region.indexes'),

            // Добавляем пункт для слайдеров
            Menu::make('Слайдеры')
            ->icon('bs.sliders')
            ->route('platform.slider-banners') // Указание маршрута для слайдеров
            ,

            // Добавляем пункт для контента
            Menu::make('Новости/Материалы')
                ->icon('bs.file-earmark-text')
                ->route('platform.contents') // Указание маршрута для контента
            ,

            Menu::make(__('Пользователи'))
                ->icon('bs.people')
                ->route('platform.systems.users')
                ->permission('platform.systems.users')
                ->title(__('Раздел администратора')),

            Menu::make(__('Роли'))
                ->icon('bs.shield')
                ->route('platform.systems.roles')
                ->permission('platform.systems.roles')
                ->divider(),
        ];
    }

    /**
     * Register permissions for the application.
     *
     * @return ItemPermission[]
     */
    public function permissions(): array
    {
        return [
            ItemPermission::group(__('System'))
                ->addPermission('platform.systems.roles', __('Roles'))
                ->addPermission('platform.systems.users', __('Users')),
        ];
    }
}
