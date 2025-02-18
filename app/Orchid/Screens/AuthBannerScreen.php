<?php

namespace App\Orchid\Screens;

use App\Models\AuthBanner;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class AuthBannerScreen extends Screen
{
    /**
     * Название экрана.
     */
    public function name(): string
    {
        return 'Заставки при авторизации и регистрации';
    }

    /**
     * Доступ к экрану.
     */
    public function query(): array
    {
        return [
            'banners' => AuthBanner::paginate(),
        ];
    }

    /**
     * Кнопки в шапке экрана.
     */
    public function commandBar(): array
    {
        return [
            ModalToggle::make('Добавить заставку')
                ->modal('editBannerModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * Определение структуры экрана.
     */
    public function layout(): array
    {
        return [
            Layout::table('banners', [
                TD::make('id', 'ID')->sort(),
                TD::make('title', 'Заголовок')->sort(),
                TD::make('access_type', 'Доступ')
                    ->render(fn(AuthBanner $banner) => $banner->access_type === 'all' ? 'Все' : 'Только зарегистрированные'),
                TD::make('index_code', 'Индекс')->sort(),
                TD::make('image_path', 'Изображение')
                    ->render(fn(AuthBanner $banner) => "<img src='{$banner->image_url}' width='100' />"),
                TD::make('created_at', 'Создано')->sort(),
                TD::make('updated_at', 'Обновлено')->sort(),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn(AuthBanner $banner) =>
                        Button::make('Удалить')
                            ->icon('trash')
                            ->method('delete', ['id' => $banner->id])
                            ->confirm('Вы уверены, что хотите удалить эту заставку?')
                    ),
            ]),

            Layout::modal('editBannerModal', [
                Layout::rows([
                    Input::make('banner.title')
                        ->title('Заголовок')
                        ->required(),

                    Input::make('banner.description')
                        ->title('Описание'),

                    Select::make('banner.access_type')
                        ->title('Доступ')
                        ->options([
                            'all' => 'Все',
                            'registered' => 'Только зарегистрированные'
                        ])
                        ->required(),

                    Input::make('banner.index_code')
                        ->title('Индекс')
                        ->required(),

                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ])
            ])->title('Редактирование заставки')->applyButton('Сохранить'),
        ];
    }

    /**
     * Создание новой записи.
     */
    public function create(array $data)
    {
        // Сохраняем новую заставку
        $bannerData = $data['banner'] ?? [];
        AuthBanner::create($bannerData);
        Toast::info('Заставка успешно добавлена.');
    }

    /**
     * Удаление записи.
     */
    public function delete(int $id)
    {
        AuthBanner::findOrFail($id)->delete();
        Toast::info('Заставка удалена.');
    }
}
