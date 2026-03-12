<?php

namespace App\Filament\Pages;

use App\Models\CustomPublicHoliday;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;

class CustomPublicHolidayPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Custom Public Holiday';
    protected static ?string $title = 'Custom Public Holiday';
    protected static string $view = 'filament.pages.custom-public-holiday';
    protected static ?string $slug = 'custom-public-holiday';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 30;

    public function table(Table $table): Table
    {
        return $table
            ->query(CustomPublicHoliday::query()->orderBy('date', 'asc'))
            ->columns([
                TextColumn::make('id')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('date')
                    ->label('Date')
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('day_of_week')
                    ->label('Day')
                    ->formatStateUsing(fn ($state) => match ((int) $state) {
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                        default => '-',
                    }),
                TextColumn::make('name')
                    ->label('Holiday Name')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([])
            ->headerActions([
                Action::make('addHolidays')
                    ->label('Add Holiday')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Repeater::make('holidays')
                            ->label('Holidays')
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Date')
                                    ->required(),
                                TextInput::make('name')
                                    ->label('Holiday Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->extraAlpineAttributes([
                                        'x-on:input' => '
                                            const start = $el.selectionStart;
                                            const end = $el.selectionEnd;
                                            const value = $el.value;
                                            $el.value = value.toUpperCase();
                                            $el.setSelectionRange(start, end);
                                        '
                                    ])
                                    ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                            ])
                            ->reorderable(false)
                            ->deletable(false)
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Add Another Holiday'),
                    ])
                    ->modalHeading('Add Public Holidays')
                    ->modalSubmitActionLabel('Save')
                    ->modalWidth('lg')
                    ->action(function (array $data): void {
                        $count = 0;
                        foreach ($data['holidays'] as $holiday) {
                            CustomPublicHoliday::create([
                                'date' => $holiday['date'],
                                'name' => $holiday['name'],
                                'day_of_week' => Carbon::parse($holiday['date'])->dayOfWeekIso,
                            ]);
                            $count++;
                        }

                        Notification::make()
                            ->title($count . ' holiday(s) added successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->form([
                        DatePicker::make('date')
                            ->label('Date')
                            ->required(),
                        TextInput::make('name')
                            ->label('Holiday Name')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['day_of_week'] = Carbon::parse($data['date'])->dayOfWeekIso;
                        return $data;
                    }),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
