<?php

namespace App\Orchid\Screens;

use App\Models\RegionIndex;
use App\Models\SliderBanner;
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

class SliderBannerScreen extends Screen
{
    public function name(): string
    {
        return 'Заставки слайдера';
    }

    public function query(): array
    {
        return [
            'banners' => SliderBanner::paginate(),
        ];
    }

    public function commandBar(): array
    {
        return [
            ModalToggle::make('Добавить заставку')
                ->modal('createSliderBannerModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('banners', [
                TD::make('id', 'ID')->sort(),
                TD::make('title', 'Заголовок')
                    ->sort()
                    ->render(fn (SliderBanner $banner) => e($banner->title)),
                TD::make('access_type', 'Доступ')
                    ->render(fn (SliderBanner $banner) => $banner->access_type === 'all' ? 'Все' : 'Только зарегистрированные'),
                TD::make('index_code', 'Индекс')
                    ->render(fn (SliderBanner $banner) => RegionIndex::where('index_code', $banner->index_code)->first()?->name ?? e($banner->index_code)
                    ),
                TD::make('image_path', 'Изображение')
                    ->render(fn (SliderBanner $banner) => $banner->image_path
                        ? "<img src='{$banner->image_url}' width='100' />"
                        : 'Нет изображения'),
                TD::make('created_at', 'Создано')
                    ->render(fn (SliderBanner $banner) => $banner->created_at->format('d.m.Y H:i')),
                TD::make('updated_at', 'Обновлено')
                    ->render(fn (SliderBanner $banner) => $banner->updated_at->format('d.m.Y H:i')),
                TD::make('Действия')
                    ->render(fn (SliderBanner $banner) => ModalToggle::make('Редактировать')
                            ->modal('editSliderBannerModal')
                            ->method('update', ['id' => $banner->id])
                            ->icon('pencil')
                            ->async('asyncGetSliderBanner', ['id' => $banner->id])
                        .
                        Button::make('Удалить')
                            ->icon('trash')
                            ->method('delete', ['id' => $banner->id])
                            ->confirm('Вы уверены, что хотите удалить эту заставку?')
                    ),
            ]),

            Layout::modal('createSliderBannerModal', [
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
                        ->fromModel(RegionIndex::class, 'name', 'index_code')
                        ->required()
                        ->empty('Выберите регион'),

                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->title('Добавить заставку')->applyButton('Сохранить'),

            Layout::modal('editSliderBannerModal', [
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
                        ->fromModel(RegionIndex::class, 'name', 'index_code')
                        ->required()
                        ->empty('Выберите регион'),

                    Picture::make('banner.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->async('asyncGetSliderBanner')->title('Редактирование заставки')->applyButton('Сохранить'),
        ];
    }

    public function create(Request $request)
    {
        $data = $request->get('banner');

        // Обработка пути к изображению
        if (isset($data['image_path']) && is_array($data['image_path'])) {
            $data['image_path'] = $data['image_path'][0] ?? null;
        }

        SliderBanner::create($data);
        Toast::info('Заставка успешно добавлена.');
    }

    public function update(Request $request, int $id)
    {
        $banner = SliderBanner::findOrFail($id);
        $data = $request->get('banner');

        // Обработка пути к изображению
        if (isset($data['image_path']) && is_array($data['image_path'])) {
            $data['image_path'] = $data['image_path'][0] ?? null;
        } elseif (!isset($data['image_path'])) {
            unset($data['image_path']); // Оставляем старое значение, если изображение не обновляется
        }

        $banner->update($data);
        Toast::info('Заставка успешно обновлена.');
    }

    public function delete(int $id)
    {
        SliderBanner::findOrFail($id)->delete();
        Toast::info('Заставка удалена.');
    }

    public function asyncGetSliderBanner(int $id): array
    {
        $banner = SliderBanner::findOrFail($id);

        return [
            'banner' => array_merge($banner->toArray(), [
                'image_path' => is_string($banner->image_path) ? $banner->image_path : '',
            ]),
        ];
    }
}
