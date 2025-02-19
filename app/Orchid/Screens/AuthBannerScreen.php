<?php

namespace App\Orchid\Screens;

use App\Models\AuthBanner;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
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
                TD::make('title', 'Заголовок')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => e($banner->title)), // Безопасный вывод
                TD::make('access_type', 'Доступ')
                    ->render(fn (AuthBanner $banner) => $banner->access_type === 'all' ? 'Все' : 'Только зарегистрированные'),
                TD::make('index_code', 'Индекс')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => e($banner->index_code)),
                TD::make('image_path', 'Изображение')
                    ->render(fn (AuthBanner $banner) => $banner->image_path
                        ? "<img src='{$banner->image_url}' width='100' />"
                        : 'Нет изображения'),
                // ->html(), // Позволяет отображать HTML
                // ->render(fn (AuthBanner $banner) => e("<img src='{$banner->image_url}' width='100' />")),//"<img src='{$banner->image_url}' width='100' />"),
                TD::make('created_at', 'Создано')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => $banner->created_at->format('d.m.Y H:i')),
                TD::make('updated_at', 'Обновлено')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => $banner->updated_at->format('d.m.Y H:i')),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn (AuthBanner $banner) => Button::make('Удалить')
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
                            'registered' => 'Только зарегистрированные',
                        ])
                        ->required(),

                    Select::make('banner.index_code')
                        ->title('Индекс')
                        ->options([
                            1 => '1. Ярославль и обл.',
                            2 => '2. Казань и Татарстан',
                            3 => '3. Екатеринбург и обл.',
                            4 => '4. Москва и Московская обл.',
                            5 => '5. Владимир и обл.',
                            6 => '6. Тверь и обл.',
                            7 => '7. Калуга и обл.',
                            8 => '8. Тула и обл.',
                            9 => '9. Санкт-Петербург и обл.',
                            10 => '10. Великий Новгород и обл.',
                        ])
                        ->required(),

                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->title('Редактирование заставки')->applyButton('Сохранить'),
        ];
    }

    /**
     * Создание новой записи.
     */
    public function create(Request $request)
    {
        // Получаем данные из запроса
        $bannerData = $request->get('banner');

        // Создаем новую запись в базе
        AuthBanner::create($bannerData);

        // Выводим уведомление
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
