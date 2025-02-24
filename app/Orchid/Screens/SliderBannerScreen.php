<?php

namespace App\Orchid\Screens;

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
                    ->render(fn (SliderBanner $banner) => e($banner->index_code)),
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
                        ->value(fn ($banner) => is_string($banner) ? $banner : '')
                        ->required(),
                ]),
            ])->title('Редактирование заставки')->applyButton('Сохранить'),

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
                        ->value(fn ($banner) => is_string($banner) ? $banner : '')
                        ->required(),
                ]),
            ])->async('asyncGetSliderBanner')->title('Редактирование заставки')->applyButton('Сохранить'),
        ];
    }

    public function create(Request $request)
    {
        SliderBanner::create($request->get('banner'));

        Toast::info('Заставка успешно добавлена.');
    }

    public function update(Request $request, int $id)
    {
        $banner = SliderBanner::findOrFail($id);
        $banner->update($request->get('banner'));

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
                'image_path' => $banner->image_path ?? '',
            ]),
        ];
    }
}
