<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeeklyMenuResource\Pages;
use App\Filament\Resources\WeeklyMenuResource\RelationManagers;
use App\Models\MealItem;
use App\Models\WeeklyMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WeeklyMenuResource extends Resource
{
    protected static ?string $model = WeeklyMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Meal Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Weekly Menu';

    public static function form(Form $form): Form
    {
        $mealItems = MealItem::where('is_active', true)->pluck('name', 'id')->toArray();

        return $form
            ->schema([
                Forms\Components\Section::make('Menu Information')
                    ->schema([
                        Forms\Components\Select::make('caterer_id')
                            ->label('Caterer')
                            ->relationship('caterer', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('phone')
                                    ->tel(),
                            ]),

                        Forms\Components\DatePicker::make('week_start')
                            ->label('Week Start (Monday)')
                            ->required()
                            ->native(false)
                            ->displayFormat('D, M j, Y')
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state) {
                                    $weekStart = \Carbon\Carbon::parse($state);
                                    // Set to Monday if not already
                                    if ($weekStart->dayOfWeek !== 1) {
                                        $weekStart = $weekStart->startOfWeek();
                                        $set('week_start', $weekStart->format('Y-m-d'));
                                    }
                                    // Auto-calculate week end (Friday)
                                    $weekEnd = $weekStart->copy()->addDays(4);
                                    $set('week_end', $weekEnd->format('Y-m-d'));
                                    // Auto-generate week label
                                    $label = $weekStart->format('jS').' - '.$weekEnd->format('jS F Y');
                                    $set('week_label', $label);
                                }
                            }),

                        Forms\Components\DatePicker::make('week_end')
                            ->label('Week End (Friday)')
                            ->required()
                            ->native(false)
                            ->displayFormat('D, M j, Y')
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('week_label')
                            ->label('Week Label')
                            ->placeholder('e.g., 1st - 4th December 2025')
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'closed' => 'Closed',
                            ])
                            ->default('draft')
                            ->required()
                            ->native(false),

                        Forms\Components\CheckboxList::make('available_days')
                            ->label('Available Days')
                            ->options([
                                'monday' => 'Monday',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                            ])
                            ->default(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
                            ->columns(5)
                            ->helperText('Uncheck days that are holidays or not working days'),
                    ])
                    ->columns(2),

                // Daily Menu Items - Fast Click Interface
                Forms\Components\Section::make('Monday')
                    ->schema([
                        Forms\Components\CheckboxList::make('monday_meals')
                            ->label('')
                            ->options($mealItems)
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Tuesday')
                    ->schema([
                        Forms\Components\CheckboxList::make('tuesday_meals')
                            ->label('')
                            ->options($mealItems)
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Wednesday')
                    ->schema([
                        Forms\Components\CheckboxList::make('wednesday_meals')
                            ->label('')
                            ->options($mealItems)
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Thursday')
                    ->schema([
                        Forms\Components\CheckboxList::make('thursday_meals')
                            ->label('')
                            ->options($mealItems)
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Friday')
                    ->schema([
                        Forms\Components\CheckboxList::make('friday_meals')
                            ->label('')
                            ->options($mealItems)
                            ->columns(3)
                            ->gridDirection('row')
                            ->bulkToggleable(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('week_label')
                    ->label('Week')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('caterer.name')
                    ->label('Caterer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('week_start')
                    ->label('Start')
                    ->date('M j')
                    ->sortable(),

                Tables\Columns\TextColumn::make('week_end')
                    ->label('End')
                    ->date('M j')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'danger' => 'closed',
                    ]),

                Tables\Columns\TextColumn::make('menu_items_count')
                    ->label('Items')
                    ->counts('menuItems')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('week_start', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'closed' => 'Closed',
                    ]),

                Tables\Filters\SelectFilter::make('caterer')
                    ->relationship('caterer', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (WeeklyMenu $record): bool => $record->status === 'draft')
                    ->action(fn (WeeklyMenu $record) => $record->update(['status' => 'published'])),

                Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (WeeklyMenu $record): bool => $record->status === 'published')
                    ->action(fn (WeeklyMenu $record) => $record->update(['status' => 'closed'])),

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
            RelationManagers\MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWeeklyMenus::route('/'),
            'create' => Pages\CreateWeeklyMenu::route('/create'),
            'edit' => Pages\EditWeeklyMenu::route('/{record}/edit'),
        ];
    }
}
