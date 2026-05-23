<?php

namespace App\Filament\Admin\Resources\CapturedRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CapturedRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('5s')          // auto-refresh co 5 sekund
            ->columns([
                TextColumn::make('created_at')
                    ->label('Czas')
                    ->dateTime('H:i:s d.m.Y')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('source')
                    ->label('Źródło')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discord'  => 'indigo',
                        'facebook' => 'info',
                        'steam'    => 'success',
                        'uczelnia' => 'warning',
                        'fingerprint' => 'purple',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->searchable(),

                TextColumn::make('ip_address')
                    ->label('IP Adres')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-globe-alt'),

                TextColumn::make('user_agent')
                    ->label('User-Agent')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->user_agent),

                TextColumn::make('payload_preview')
                    ->label('Payload (podgląd)')
                    ->state(fn ($record) => $record->payload_preview)
                    ->color('danger')
                    ->limit(60),

                TextColumn::make('referer')
                    ->label('Referer')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('request_method')
                    ->label('Metoda')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'GET' ? 'info' : 'warning')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('Źródło')
                    ->options([
                        'discord'  => 'Discord',
                        'facebook' => 'Facebook',
                        'steam'    => 'Steam',
                        'uczelnia' => 'Uczelnia',
                        'fingerprint' => 'Fingerprint',
                        'unknown'  => 'Nieznane',
                    ]),
            ])
            ->recordActions([
                Action::make('view_full')
                    ->label('Podgląd')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn ($record) => "Przechwycony Request #{$record->id} — {$record->source}")
                    ->modalContent(fn ($record) => view('filament.modals.captured-request-detail', ['record' => $record]))
                    ->modalWidth('4xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
