<?php

namespace App\Orchid\Screens;

use App\Models\RegionIndex;
use Illuminate\Http\Request;
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
        return [
            'regions' => RegionIndex::paginate(),
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
                TD::make('index_code', 'Индекс')->sort(),
                TD::make('name', 'Название')->sort(),
                TD::make('created_at', 'Создано')
                    ->render(fn (RegionIndex $region) => $region->created_at->format('d.m.Y H:i')),
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
        RegionIndex::create($request->get('region'));
        Toast::info('Регион успешно добавлен.');
    }

    public function update(Request $request, int $id)
    {
        $region = RegionIndex::findOrFail($id);
        $region->update($request->get('region'));
        Toast::info('Регион успешно обновлён.');
    }

    public function delete(int $id)
    {
        RegionIndex::findOrFail($id)->delete();
        Toast::info('Регион удалён.');
    }

    public function asyncGetRegion(int $id): array
    {
        return [
            'region' => RegionIndex::findOrFail($id)->toArray(),
        ];
    }
}
