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
                ->modal('createContentModal')
                ->method('create')
                ->icon('plus'),
        ];
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
                TD::make('region', 'Текст')
                    ->render(fn (Content $content) => e($content->region)),
                TD::make('image_path', 'Изображение')
                    ->render(fn (Content $content) => $content->image_path
                        ? "<img src='{$content->image_url}' width='100' />"
                        : 'Нет изображения'),
                TD::make('created_at', 'Создано')
                    ->render(fn (Content $content) => $content->created_at->format('d.m.Y H:i')),
                TD::make('updated_at', 'Обновлено')
                    ->render(fn (Content $content) => $content->updated_at->format('d.m.Y H:i')),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn (Content $content) => ModalToggle::make('Редактировать')
                            ->modal('editContentModal')
                            ->method('update', ['id' => $content->id])
                            ->icon('pencil')
                            ->async('asyncGetContent', ['id' => $content->id])
                        .
                        Button::make('Удалить')
                            ->icon('trash')
                            ->method('delete', ['id' => $content->id])
                            ->confirm('Вы уверены, что хотите удалить этот контент?')
                    ),
            ]),

            // Модалка для создания контента (без async)
            Layout::modal('createContentModal', [
                Layout::rows([
                    Input::make('content.title')->title('Заголовок')->required(),
                    Input::make('content.description')->title('Описание'),
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
                    Input::make('content.region')->title('Текст')->required(),
                    Picture::make('content.image_path')->title('Изображение'),
                ]),
            ])->title('Добавить контент')->applyButton('Сохранить'),

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
                        ->value(fn ($content) => is_string($content ?? null) ? $content : '')
                    // ->required()
                    ,
                ]),
            ])->async('asyncGetContent')->title('Редактирование контента')->applyButton('Сохранить'),
        ];
    }

    public function create(Request $request)
    {
        $contentData = $request->get('content');

        // Проверяем изображение и сохраняем путь
        if (isset($contentData['image_path']) && is_array($contentData['image_path'])) {
            $contentData['image_path'] = $contentData['image_path'][0] ?? null;
        }

        Content::create($contentData);
        Toast::info('Контент успешно добавлен.');
    }

    public function update(Request $request, int $id)
    {
        $content = Content::findOrFail($id);
        $contentData = $request->get('content');

        // Проверяем изображение
        if (isset($contentData['image_path']) && is_array($contentData['image_path'])) {
            $contentData['image_path'] = $contentData['image_path'][0] ?? null;
        } elseif (!isset($contentData['image_path'])) {
            // Если поле image_path отсутствует в запросе, оставляем старое значение
            unset($contentData['image_path']);
        }

        $content->update($contentData);
        Toast::info('Контент успешно обновлён.');
    }

    public function delete(int $id)
    {
        Content::findOrFail($id)->delete();
        Toast::info('Контент удалён.');
    }

    public function asyncGetContent(int $id): array
    {
        $content = Content::findOrFail($id);

        return [
            'content' => array_merge($content->toArray(), [
                'image_path' => is_string($content->image_path) ? $content->image_path : '',
            ]),
        ];
    }
}
