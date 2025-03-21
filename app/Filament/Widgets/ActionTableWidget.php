<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\AccountMeta;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActionTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Actions';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Add Action')
                    ->form([
                        ToggleButtons::make('key')
                            ->icons([
                                'call' => 'heroicon-o-phone',
                                'event' => 'heroicon-o-exclamation-circle',
                            ])
                            ->colors([
                                'call' => 'success',
                                'event' => 'warning',
                            ])
                            ->default('call')
                            ->inline()
                            ->options([
                                'call' => 'Call',
                                'event' => 'Event',
                            ])
                            ->label('Action')
                            ->required(),
                        ToggleButtons::make('response')
                            ->colors([
                                'ok' => 'success',
                                'no-response' => 'danger',
                            ])
                            ->icons([
                                'ok' => 'heroicon-o-check-circle',
                                'no-response' => 'heroicon-o-x-circle',
                            ])
                            ->default('ok')
                            ->inline()
                            ->options([
                                'ok' => 'Ok',
                                'no-response' => 'No Response',
                            ])
                            ->label('Response')
                            ->required(),
                        Textarea::make('key_value')->label('Description'),
                        DatePicker::make('date')->default(now()->toDateString()),
                        TimePicker::make('time')->default(now()->toTimeString()),
                    ])
                    ->action(function (array $data): void {
                        $account = Account::query()->find(session('model_id'));
                        if ($account) {
                            $account->accountMeta()->create([
                                'type' => 'action',
                                'user_id' => auth()->user()->id,
                                'key' => $data['key'],
                                'response' => $data['response'],
                                'date' => $data['date'],
                                'time' => $data['time'],
                            ]);
                        }

                        Notification::make()
                            ->title('Success')
                            ->body('Data saved successfully!')
                            ->success()
                            ->send();
                    }),
            ])
            ->query(
                AccountMeta::query()->where('type', 'action')->where('account_id', session('model_id'))
            )
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make('edit')
                    ->tooltip(__('filament-actions::edit.single.label'))
                    ->iconButton()
                    ->form([
                        ToggleButtons::make('key')
                            ->icons([
                                'call' => 'heroicon-o-phone',
                                'event' => 'heroicon-o-exclamation-circle',
                            ])
                            ->colors([
                                'call' => 'success',
                                'event' => 'warning',
                            ])
                            ->default('call')
                            ->inline()
                            ->options([
                                'call' => 'Call',
                                'event' => 'Event',
                            ])
                            ->label('Action')
                            ->required(),
                        ToggleButtons::make('response')
                            ->colors([
                                'ok' => 'success',
                                'no-response' => 'danger',
                            ])
                            ->icons([
                                'ok' => 'heroicon-o-check-circle',
                                'no-response' => 'heroicon-o-x-circle',
                            ])
                            ->default('ok')
                            ->inline()
                            ->options([
                                'ok' => 'Ok',
                                'no-response' => 'No Response',
                            ])
                            ->label('Response')
                            ->required(),
                        Textarea::make('value')->label('Description'),
                        DatePicker::make('date')->default(now()->toDateString()),
                        TimePicker::make('time')->default(now()->toTimeString()),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'key' => $data['key'],
                            'value' => $data['value'],
                            'response' => $data['response'],
                            'date' => $data['date'],
                            'time' => $data['time'],
                        ]);

                        Notification::make()
                            ->title('Success')
                            ->body('Data saved successfully!')
                            ->success()
                            ->send();
                    })
            ])
            ->columns([
                Tables\Columns\TextColumn::make('date')->sortable(),
                Tables\Columns\TextColumn::make('time')->sortable(),
                Tables\Columns\TextColumn::make('key_value')->label('Description')->searchable()
                    ->description(fn ($record) => $record->value),
                Tables\Columns\TextColumn::make('key')
                    ->label('Action')
                    ->formatStateUsing(fn ($state): string => str($state)->title())
                    ->color(fn ($record) => match ($record->key) {
                        'call' => 'success',
                        'event' => 'warning',
                        default => 'primary'
                    })
                    ->icon(fn ($record) => match ($record->key) {
                        'call' => 'bxs-phone-call',
                        'event' => 'heroicon-o-exclamation-circle',
                        default => 'heroicon-o-information-circle'
                    })
                    ->badge(),
                Tables\Columns\TextColumn::make('response')
                    ->sortable()
                    ->formatStateUsing(fn ($state): string => str($state)->title())
                    ->color(fn ($record) => match ($record->response) {
                        'ok' => 'success',
                        'no-response' => 'danger',
                        default => 'primary'
                    })
                    ->icon(fn ($record) => match ($record->response) {
                        'ok' => 'heroicon-o-check-circle',
                        'no-response' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-information-circle'
                    })
                    ->badge(),
            ]);
    }
}
