<?php

namespace App\Orchid\Screens;

use App\Models\AuthBanner;
use App\Models\RegionIndex;
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
                ->modal('createBannerModal')
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
                    ->render(fn (AuthBanner $banner) => e($banner->title)),
                TD::make('access_type', 'Доступ')
                    ->render(fn (AuthBanner $banner) => $banner->access_type === 'all' ? 'Все' : 'Только зарегистрированные'),
                TD::make('index_code', 'Индекс')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => $this->getRegionName($banner->index_code)), // Выводим название региона
                TD::make('image_path', 'Изображение')
                    ->render(fn (AuthBanner $banner) => $banner->image_path
                        ? "<img src='{$banner->image_url}' width='100' />"
                        : 'Нет изображения'),
                TD::make('created_at', 'Создано')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => $banner->created_at->format('d.m.Y H:i')),
                TD::make('updated_at', 'Обновлено')
                    ->sort()
                    ->render(fn (AuthBanner $banner) => $banner->updated_at->format('d.m.Y H:i')),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn (AuthBanner $banner) => ModalToggle::make('Редактировать')
                        ->modal('editBannerModal')
                        ->method('update', ['id' => $banner->id])
                        ->icon('pencil')
                        ->async('asyncGetBanner', ['id' => $banner->id])
                    .
                    Button::make('Удалить')
                            ->icon('trash')
                            ->method('delete', ['id' => $banner->id])
                            ->confirm('Вы уверены, что хотите удалить эту заставку?')
                    ),
            ]),

            Layout::modal('createBannerModal', [
                Layout::rows([
                    Input::make('banner.title')
                        ->title('Заголовок')
                        ->required(),
                    Input::make('banner.description')
                        ->title('Описание')
                        ->required(),
                    Select::make('banner.access_type')
                        ->title('Доступ')
                        ->options([
                            'all' => 'Все',
                            'registered' => 'Только зарегистрированные',
                        ])
                        ->required(),
                    Select::make('banner.index_code')
                        ->title('Индекс')
                        ->fromModel(RegionIndex::class, 'name', 'index_code') // Динамическая загрузка из RegionIndex
                        ->required(),
                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->title('Добавить заставку')->applyButton('Сохранить'),

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
                        ->fromModel(RegionIndex::class, 'name', 'index_code') // Динамическая загрузка из RegionIndex
                        ->required(),
                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->async('asyncGetBanner')->title('Редактирование заставки')->applyButton('Сохранить'),
        ];
    }

    /**
     * Создание новой записи.
     */
    public function create(Request $request)
    {
        $bannerData = $request->get('banner');

        // Проверяем и обрабатываем путь к изображению
        if (isset($bannerData['image_path']) && is_array($bannerData['image_path'])) {
            $bannerData['image_path'] = $bannerData['image_path'][0] ?? null;
        }

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

    /**
     * Обновление записи.
     */
    public function update(Request $request, int $id)
    {
        $banner = AuthBanner::findOrFail($id);
        $bannerData = $request->get('banner');

        // Проверяем и обрабатываем путь к изображению
        if (isset($bannerData['image_path']) && is_array($bannerData['image_path'])) {
            $bannerData['image_path'] = $bannerData['image_path'][0] ?? null;
        } elseif (!isset($bannerData['image_path'])) {
            unset($bannerData['image_path']); // Оставляем старое значение, если поле не отправлено
        }

        $banner->update($bannerData);
        Toast::info('Заставка успешно обновлена.');
    }

    /**
     * Асинхронная загрузка данных для редактирования.
     */
    public function asyncGetBanner(int $id): array
    {
        $banner = AuthBanner::findOrFail($id);

        return [
            'banner' => array_merge($banner->toArray(), [
                'image_path' => $banner->image_path ?? '',
            ]),
        ];
    }

    /**
     * Вспомогательный метод для получения названия региона по index_code.
     */
    private function getRegionName($indexCode): string
    {
        $region = RegionIndex::where('index_code', $indexCode)->first();

        return $region ? "{$region->index_code}. {$region->name}" : "Регион не найден ($indexCode)";
    }
}
