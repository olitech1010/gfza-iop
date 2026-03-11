<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreItemResource\Pages;
use App\Models\StoreItem;
use App\Models\StoreTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StoreItemResource extends Resource
{
    protected static ?string $model = StoreItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Stores Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\Select::make('store_category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->maxLength(255),
                        Forms\Components\Select::make('unit_of_measure')
                            ->options([
                                'Pieces' => 'Pieces',
                                'Boxes' => 'Boxes',
                                'Reams' => 'Reams',
                                'Liters' => 'Liters',
                                'Kilograms' => 'Kilograms',
                                'Packs' => 'Packs',
                                'Cartons' => 'Cartons',
                                'Pairs' => 'Pairs',
                            ])
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('reorder_level')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Get alerted when stock falls below this level.'),
                        Forms\Components\TextInput::make('unit_cost')
                            ->numeric()
                            ->default(0.00)
                            ->prefix('GHS')
                            ->helperText('Standard or average cost per unit'),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Current Stock')
                    ->schema([
                        Forms\Components\TextInput::make('current_stock')
                            ->disabled()
                            ->numeric()
                            ->default(0)
                            ->helperText('This is automatically calculated via stock transactions.'),
                    ])->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (StoreItem $record): string => $record->sku ?? ''),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_of_measure')
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_stock')
                    ->sortable()
                    ->badge()
                    ->color(function (StoreItem $record): string {
                        if ($record->current_stock <= 0) {
                            return 'danger';
                        }
                        if ($record->current_stock <= $record->reorder_level) {
                            return 'warning';
                        }
                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('reorder_level')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('unit_cost')
                    ->money('GHS')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('store_category_id')
                    ->relationship('category', 'name')
                    ->label('Category'),
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock Items')
                    ->query(fn (Builder $query): Builder => $query->whereColumn('current_stock', '<=', 'reorder_level')),
            ])
            ->actions([
                // RECEIVE STOCK ACTION
                Tables\Actions\Action::make('receive_stock')
                    ->label('Receive')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->label('Quantity Received'),
                        Forms\Components\Select::make('supplier_id')
                            ->relationship('transactions.supplier', 'name')
                            ->label('Supplier')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('unit_price')
                            ->numeric()
                            ->prefix('GHS')
                            ->label('Unit Price (Optional)'),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number'),
                        Forms\Components\TextInput::make('sra_number')
                            ->label('SRA Number (Store Receipt Voucher)'),
                    ])
                    ->action(function (StoreItem $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $newStock = $record->current_stock + $data['quantity'];
                            
                            StoreTransaction::create([
                                'store_item_id' => $record->id,
                                'type' => 'receipt',
                                'transaction_date' => $data['transaction_date'],
                                'quantity' => $data['quantity'],
                                'unit_price' => $data['unit_price'] ?? $record->unit_cost,
                                'balance_after' => $newStock,
                                'supplier_id' => $data['supplier_id'] ?? null,
                                'invoice_number' => $data['invoice_number'] ?? null,
                                'sra_number' => $data['sra_number'] ?? null,
                            ]);
                            
                            $record->update(['current_stock' => $newStock]);
                            
                            if (isset($data['unit_price']) && $data['unit_price'] > 0) {
                                $record->update(['unit_cost' => $data['unit_price']]);
                            }
                        });
                    })
                    ->successNotificationTitle('Stock received successfully.'),
                
                // ISSUE STOCK ACTION
                Tables\Actions\Action::make('issue_stock')
                    ->label('Issue')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->form([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->label('Quantity to Issue'),
                        Forms\Components\Select::make('department_id')
                            ->relationship('transactions.department', 'name')
                            ->label('Issued to Department')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('transactions.user', 'name')
                            ->label('Issued to Person')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('requisition_number')
                            ->label('Requisition Number (Request Note)'),
                        Forms\Components\TextInput::make('siv_number')
                            ->label('SIV Number (Store Issue Voucher)'),
                    ])
                    ->action(function (StoreItem $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $newStock = $record->current_stock - $data['quantity'];
                            
                            StoreTransaction::create([
                                'store_item_id' => $record->id,
                                'type' => 'issue',
                                'transaction_date' => $data['transaction_date'],
                                'quantity' => -$data['quantity'], // negative for issues
                                'balance_after' => $newStock,
                                'department_id' => $data['department_id'],
                                'user_id' => $data['user_id'] ?? null,
                                'requisition_number' => $data['requisition_number'] ?? null,
                                'siv_number' => $data['siv_number'] ?? null,
                            ]);
                            
                            $record->update(['current_stock' => $newStock]);
                        });
                    })
                    ->visible(fn (StoreItem $record): bool => $record->current_stock > 0)
                    ->successNotificationTitle('Stock issued successfully.'),

                // ADJUST STOCK ACTION
                Tables\Actions\Action::make('adjust_stock')
                    ->label('Adjust')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('gray')
                    ->form([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('quantity')
                            ->required()
                            ->numeric()
                            ->label('Adjustment Quantity')
                            ->helperText('Use positive numbers to add stock, negative to remove.'),
                        Forms\Components\Textarea::make('notes')
                            ->required()
                            ->label('Reason for Adjustment')
                            ->placeholder('e.g., Found extra during audit, item damaged.'),
                    ])
                    ->action(function (StoreItem $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            $newStock = $record->current_stock + $data['quantity'];
                            
                            StoreTransaction::create([
                                'store_item_id' => $record->id,
                                'type' => 'adjustment',
                                'transaction_date' => $data['transaction_date'],
                                'quantity' => $data['quantity'],
                                'balance_after' => $newStock,
                                'notes' => $data['notes'],
                            ]);
                            
                            $record->update(['current_stock' => $newStock]);
                        });
                    })
                    ->successNotificationTitle('Stock adjusted successfully.'),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListStoreItems::route('/'),
            'create' => Pages\CreateStoreItem::route('/create'),
            'edit' => Pages\EditStoreItem::route('/{record}/edit'),
        ];
    }
}
