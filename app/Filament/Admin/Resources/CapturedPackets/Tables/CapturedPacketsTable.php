<?php

namespace App\Filament\Admin\Resources\CapturedPackets\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class CapturedPacketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->poll('3s')          // szybszy refresh — pakiety lecą często
            ->columns([
                TextColumn::make('created_at')
                    ->label('Czas')
                    ->dateTime('H:i:s.v')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('protocol')
                    ->label('Protokół')
                    ->badge()
                    ->color(fn (string $state): string => match (strtoupper($state)) {
                        'HTTP'   => 'warning',
                        'HTTPS'  => 'success',
                        'DNS'    => 'info',
                        'TCP'    => 'primary',
                        'UDP'    => 'warning',
                        'ICMP'   => 'danger',
                        'ARP'    => 'gray',
                        default  => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('src_ip')
                    ->label('Źródło')
                    ->state(fn ($record): string =>
                        ($record->src_ip ?? '?') . ($record->src_port ? ":{$record->src_port}" : '')
                    )
                    ->copyable()
                    ->searchable(query: fn ($query, string $search) =>
                        $query->where('src_ip', 'like', "%{$search}%")
                    ),

                TextColumn::make('dst_ip')
                    ->label('Cel')
                    ->state(fn ($record): string =>
                        ($record->dst_ip ?? '?') . ($record->dst_port ? ":{$record->dst_port}" : '')
                    )
                    ->copyable()
                    ->searchable(query: fn ($query, string $search) =>
                        $query->where('dst_ip', 'like', "%{$search}%")
                    ),

                TextColumn::make('summary')
                    ->label('Opis')
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->summary),

                TextColumn::make('packet_size')
                    ->label('Rozmiar')
                    ->state(fn ($record): string => $record->packet_size . ' B')
                    ->sortable(),

                TextColumn::make('flags')
                    ->label('Flagi TCP')
                    ->badge()
                    ->color('danger')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ttl')
                    ->label('TTL')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('interface')
                    ->label('Interfejs')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('protocol')
                    ->label('Protokół')
                    ->options([
                        'HTTP'  => 'HTTP',
                        'HTTPS' => 'HTTPS',
                        'DNS'   => 'DNS',
                        'TCP'   => 'TCP',
                        'UDP'   => 'UDP',
                        'ICMP'  => 'ICMP',
                        'ARP'   => 'ARP',
                    ]),
            ])
            ->recordActions([
                Action::make('view_raw')
                    ->label('Raw')
                    ->icon('heroicon-o-code-bracket')
                    ->color('gray')
                    ->modalHeading(fn ($record) => "Pakiet #{$record->id} — {$record->protocol}")
                    ->modalContent(fn ($record) => view('filament.modals.captured-packet-detail', ['record' => $record]))
                    ->modalWidth('3xl'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
