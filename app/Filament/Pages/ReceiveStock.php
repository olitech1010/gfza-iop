<?php

namespace App\Filament\Pages;

use App\Models\StoreItem;
use App\Models\StoreTransaction;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ReceiveStock extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Stores Management';

    protected static ?string $navigationLabel = 'Receive Stock';

    protected static ?string $title = 'Receive Stock';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.receive-stock';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['super_admin', 'stores_manager']);
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'transaction_date' => now()->format('Y-m-d'),
            'items' => [
                ['store_item_id' => null, 'quantity' => null, 'unit_price' => null],
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Receipt Details')
                    ->description('Enter the supplier and invoice information for this delivery.')
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now())
                            ->label('Date Received'),
                        Forms\Components\Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('contact_person')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel()
                                    ->maxLength(255),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                return Supplier::create($data)->id;
                            }),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number'),
                        Forms\Components\TextInput::make('sra_number')
                            ->label('SRA Number (Store Receipt Voucher)'),
                    ])->columns(2),

                Forms\Components\Section::make('Items to Receive')
                    ->description('Add the items and quantities being received in this delivery.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->schema([
                                Forms\Components\Select::make('store_item_id')
                                    ->label('Item')
                                    ->options(StoreItem::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->label('Quantity')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->numeric()
                                    ->prefix('GHS')
                                    ->label('Unit Price')
                                    ->columnSpan(1),
                            ])
                            ->columns(4)
                            ->addActionLabel('+ Add Another Item')
                            ->minItems(1)
                            ->defaultItems(1)
                            ->reorderable(false),
                    ]),
            ])
            ->statePath('data');
    }

    public function receive(): void
    {
        $data = $this->form->getState();

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $line) {
                $item = StoreItem::findOrFail($line['store_item_id']);
                $newStock = $item->current_stock + $line['quantity'];

                StoreTransaction::create([
                    'store_item_id' => $item->id,
                    'type' => 'receipt',
                    'transaction_date' => $data['transaction_date'],
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'] ?? $item->unit_cost,
                    'balance_after' => $newStock,
                    'supplier_id' => $data['supplier_id'],
                    'invoice_number' => $data['invoice_number'] ?? null,
                    'sra_number' => $data['sra_number'] ?? null,
                ]);

                $item->update(['current_stock' => $newStock]);

                if (isset($line['unit_price']) && $line['unit_price'] > 0) {
                    $item->update(['unit_cost' => $line['unit_price']]);
                }
            }
        });

        $count = count($data['items']);

        Notification::make()
            ->success()
            ->title('Stock Received Successfully')
            ->body("{$count} item(s) received and stock updated.")
            ->send();

        $this->redirect(StoresOverview::getUrl());
    }
}
