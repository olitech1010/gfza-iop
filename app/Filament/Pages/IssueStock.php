<?php

namespace App\Filament\Pages;

use App\Models\Department;
use App\Models\StoreItem;
use App\Models\StoreTransaction;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class IssueStock extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static ?string $navigationGroup = 'Stores Management';

    protected static ?string $navigationLabel = 'Issue Stock';

    protected static ?string $title = 'Issue Stock';

    protected static ?int $navigationSort = 6;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.issue-stock';

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
                ['store_item_id' => null, 'quantity' => null],
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Issue Details')
                    ->description('Enter the person and department receiving these items.')
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->required()
                            ->default(now())
                            ->label('Date'),
                        Forms\Components\Select::make('department_id')
                            ->label('Department')
                            ->options(Department::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live(),
                        Forms\Components\Select::make('user_id')
                            ->label('Issued To (Person)')
                            ->options(fn (Get $get) => User::where('department_id', $get('department_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->reactive(),
                        Forms\Components\TextInput::make('requisition_number')
                            ->label('Requisition Number'),
                        Forms\Components\TextInput::make('siv_number')
                            ->label('SIV Number (Store Issue Voucher)'),
                    ])->columns(2),

                Forms\Components\Section::make('Items to Issue')
                    ->description('Add the items and quantities to issue.')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->schema([
                                Forms\Components\Select::make('store_item_id')
                                    ->label('Item')
                                    ->options(
                                        StoreItem::where('current_stock', '>', 0)
                                            ->get()
                                            ->mapWithKeys(fn (StoreItem $item) => [
                                                $item->id => "{$item->name} ({$item->current_stock} available)",
                                            ])
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->columnSpan(3),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->label('Quantity')
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

    public function issue(): void
    {
        $data = $this->form->getState();

        // Validate stock availability before processing
        foreach ($data['items'] as $index => $line) {
            $item = StoreItem::findOrFail($line['store_item_id']);
            if ($line['quantity'] > $item->current_stock) {
                Notification::make()
                    ->danger()
                    ->title('Insufficient Stock')
                    ->body("Not enough stock for \"{$item->name}\". Available: {$item->current_stock}, Requested: {$line['quantity']}")
                    ->send();

                return;
            }
        }

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $line) {
                $item = StoreItem::findOrFail($line['store_item_id']);
                $newStock = $item->current_stock - $line['quantity'];

                StoreTransaction::create([
                    'store_item_id' => $item->id,
                    'type' => 'issue',
                    'transaction_date' => $data['transaction_date'],
                    'quantity' => -$line['quantity'],
                    'balance_after' => $newStock,
                    'department_id' => $data['department_id'],
                    'user_id' => $data['user_id'] ?? null,
                    'requisition_number' => $data['requisition_number'] ?? null,
                    'siv_number' => $data['siv_number'] ?? null,
                ]);

                $item->update(['current_stock' => $newStock]);
            }
        });

        $count = count($data['items']);

        Notification::make()
            ->success()
            ->title('Stock Issued Successfully')
            ->body("{$count} item(s) issued and stock updated.")
            ->send();

        $this->redirect(StoresOverview::getUrl());
    }
}
