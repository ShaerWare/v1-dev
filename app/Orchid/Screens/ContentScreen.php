<?php

namespace App\Orchid\Screens;

use App\Models\Content;
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

class ContentScreen extends Screen
{
    public function name(): string
    {
        return 'Контент/Новости';
    }

    public function query(): array
    {
        return [
            'contents' => Content::paginate(),
        ];
    }

    public function commandBar(): array
    {
        return [
            ModalToggle::make('Добавить контент')
                ->modal('editContentModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    public function create(Request $request)
    {
        $contentData = $request->get('content');

        Content::create($contentData);

        Toast::info('Контент успешно добавлен.');
    }

    public function layout(): array
    {
        return [
            Layout::table('contents', [
                TD::make('id', 'ID')->sort(),
                TD::make('title', 'Заголовок')->sort()
                    ->render(fn (Content $content) => e($content->title)),
                TD::make('access_type', 'Доступ')
                    ->render(fn (Content $content) => $content->access_type === 'all' ? 'Все' : 'Только зарегистрированные'),
                TD::make('index_code', 'Индекс')
                    ->render(fn (Content $content) => e($content->index_code)),
                TD::make('region', 'Регион')
                    ->render(fn (Content $content) => e($content->region)),
                TD::make('image_path', 'Изображение')
                    ->render(fn (Content $content) => "<img src='{$content->image_url}' width='100' />"),
                TD::make('created_at', 'Создано')
                    ->render(fn (Content $content) => $content->created_at->format('d.m.Y H:i')),
                TD::make('updated_at', 'Обновлено')
                    ->render(fn (Content $content) => $content->updated_at->format('d.m.Y H:i')),
                TD::make('Действия')
                    ->render(fn (Content $content) => Button::make('Удалить')
                        ->icon('trash')
                        ->method('delete', ['id' => $content->id])
                        ->confirm('Вы уверены?')
                    ),
            ]),

            Layout::modal('editContentModal', [
                Layout::rows([
                    Input::make('content.title')
                        ->title('Заголовок')
                        ->required(),

                    Input::make('content.description')
                        ->title('Описание'),

                    Select::make('content.access_type')
                        ->title('Доступ')
                        ->options([
                            'all' => 'Все',
                            'registered' => 'Только зарегистрированные',
                        ])
                        ->required(),

                    Select::make('content.index_code')
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

                    Input::make('content.region')
                        ->title('Текст')
                        ->required(),

                    Picture::make('content.image_path')
                        ->title('Изображение')
                        ->required(),
                ]),
            ])->title('Добавить контент')->applyButton('Сохранить'),
        ];
    }
}
