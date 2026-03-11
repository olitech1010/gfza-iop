<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreTransactionResource\Pages;
use App\Models\StoreTransaction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StoreTransactionResource extends Resource
{
    protected static ?string $model = StoreTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Stores Management';

    protected static ?string $navigationLabel = 'Inventory Ledger';

    protected static ?string $pluralModelLabel = 'Inventory Ledger';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false; // Transactions are created via Actions on the Item Resource
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'receipt' => 'success',
                        'issue' => 'warning',
                        'adjustment' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('Balance')
                    ->sortable(),
                // Contextual Column for Source / Destination
                Tables\Columns\TextColumn::make('source_destination')
                    ->getStateUsing(function (StoreTransaction $record) {
                        if ($record->type === 'receipt') {
                            return 'From: ' . ($record->supplier->name ?? 'Unknown Supplier');
                        } elseif ($record->type === 'issue') {
                            $dept = $record->department->name ?? 'Unknown Dept';
                            $user = $record->user->name ?? '';
                            return "To: $dept" . ($user ? " ($user)" : '');
                        }
                        return 'Adjustment';
                    })
                    ->label('Source / Destination'),
                // Contextual Column for Reference Docs
                Tables\Columns\TextColumn::make('reference_docs')
                    ->getStateUsing(function (StoreTransaction $record) {
                        if ($record->type === 'receipt') {
                            $sra = $record->sra_number ? "SRA: {$record->sra_number}" : "";
                            $inv = $record->invoice_number ? "INV: {$record->invoice_number}" : "";
                            return trim("$sra $inv");
                        } elseif ($record->type === 'issue') {
                            $siv = $record->siv_number ? "SIV: {$record->siv_number}" : "";
                            $req = $record->requisition_number ? "REQ: {$record->requisition_number}" : "";
                            return trim("$siv $req");
                        }
                        return reset($record->notes);
                    })
                    ->label('Reference Docs'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('store_item_id')
                    ->relationship('item', 'name')
                    ->label('Filter by Item'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'receipt' => 'Receipt (In)',
                        'issue' => 'Issue (Out)',
                        'adjustment' => 'Adjustment',
                    ]),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Usually ledgers aren't deleted in bulk, but we can leave export if we had a plugin
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStoreTransactions::route('/'),
        ];
    }
}
