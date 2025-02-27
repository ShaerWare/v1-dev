<?php

namespace App\Orchid\Screens;

use App\Models\RegionIndex;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class RegionIndexScreen extends Screen
{
    public function name(): string
    {
        return 'Области/Индексы';
    }

    public function query(): array
    {
        $regions = RegionIndex::paginate(10);
        \Log::info('Regions data:', $regions->toArray());

        return [
            'regions' => $regions,
        ];
    }

    public function commandBar(): array
    {
        return [
            ModalToggle::make('Добавить регион')
                ->modal('createRegionModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::table('regions', [
                TD::make('id', 'ID')->sort(),
                TD::make('index_code', 'Индекс')
                    ->sort()
                    ->render(fn (RegionIndex $region) => $region->index_code ?? 'Нет индекса'),
                TD::make('name', 'Название')
                    ->sort()
                    ->render(fn (RegionIndex $region) => $region->name ?? 'Нет названия'),
                TD::make('created_at', 'Создано')
                    ->render(fn (RegionIndex $region) => $region->created_at?->format('d.m.Y H:i') ?? 'Нет даты'),
                TD::make('Действия')
                    ->align(TD::ALIGN_CENTER)
                    ->render(fn (RegionIndex $region) => ModalToggle::make('Редактировать')
                            ->modal('editRegionModal')
                            ->method('update', ['id' => $region->id])
                            ->icon('pencil')
                            ->async('asyncGetRegion', ['id' => $region->id])
                        .
                        Button::make('Удалить')
                            ->icon('trash')
                            ->method('delete', ['id' => $region->id])
                            ->confirm('Вы уверены, что хотите удалить этот регион?')
                    ),
            ]),

            Layout::modal('createRegionModal', [
                Layout::rows([
                    Input::make('region.index_code')
                        ->title('Индекс')
                        ->type('number')
                        ->required(),
                    Input::make('region.name')
                        ->title('Название')
                        ->required(),
                ]),
            ])->title('Добавить регион')->applyButton('Сохранить'),

            Layout::modal('editRegionModal', [
                Layout::rows([
                    Input::make('region.index_code')
                        ->title('Индекс')
                        ->type('number')
                        ->required(),
                    Input::make('region.name')
                        ->title('Название')
                        ->required(),
                ]),
            ])->async('asyncGetRegion')->title('Редактирование региона')->applyButton('Сохранить'),
        ];
    }

    public function create(Request $request)
    {
        $data = $request->get('region');

        // Валидация данных
        $validator = Validator::make($data, [
            'index_code' => 'required|numeric|unique:region_indices,index_code',
            'name' => 'required|string|max:255',
        ], [
            'index_code.unique' => 'Регион с таким индексом уже существует.',
            'index_code.required' => 'Поле "Индекс" обязательно.',
            'name.required' => 'Поле "Название" обязательно.',
        ]);

        // Если валидация не прошла, выбрасываем исключение с сообщением
        if ($validator->fails()) {
            Toast::error($validator->errors()->first());
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Если валидация прошла, создаем запись
        RegionIndex::create($data);
        Toast::info('Регион успешно добавлен.');
    }

    public function update(Request $request, int $id)
    {
        $region = RegionIndex::findOrFail($id);
        $data = $request->get('region');

        // Валидация с учетом текущего ID (чтобы не конфликтовать с самим собой)
        $validator = Validator::make($data, [
            'index_code' => "required|numeric|unique:region_indices,index_code,{$id}",
            'name' => 'required|string|max:255',
        ], [
            'index_code.unique' => 'Регион с таким индексом уже существует.',
            'index_code.required' => 'Поле "Индекс" обязательно.',
            'name.required' => 'Поле "Название" обязательно.',
        ]);

        if ($validator->fails()) {
            Toast::error($validator->errors()->first());
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $region->update($data);
        Toast::info('Регион успешно обновлён.');
    }

    public function delete(int $id)
    {
        RegionIndex::findOrFail($id)->delete();
        Toast::info('Регион удалён.');
    }

    public function asyncGetRegion(int $id): array
    {
        $region = RegionIndex::findOrFail($id);

        return [
            'region' => $region->toArray(),
        ];
    }
}
